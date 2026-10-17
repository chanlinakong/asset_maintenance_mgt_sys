<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceSchedule;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use App\Http\Requests\StoreMaintenanceScheduleRequest;
use App\Http\Requests\UpdateMaintenanceScheduleRequest;

class MaintenanceScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->toString();
        $status = $request->string('status')->trim()->toString();
        $dueStatus = $request->string('due_status')->trim()->toString();

        $today = today()->toDateString();
        $warningDate = today()->addDays(30)->toDateString();

        $schedules = MaintenanceSchedule::query()
            ->with('vehicle')

            // Search by schedule or vehicle
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhereHas('vehicle', function ($query) use ($search) {
                            $query->where('vehicle_code', 'like', "%{$search}%")
                                ->orWhere('name', 'like', "%{$search}%");
                        });
                });
            })

            // Existing Active / Inactive filter
            ->when($status === 'active', function ($query) {
                $query->where('is_active', true);
            })
            ->when($status === 'inactive', function ($query) {
                $query->where('is_active', false);
            })

            // Due Status filter
            ->when(
                in_array($dueStatus, ['overdue', 'due_soon', 'up_to_date'], true),
                function ($query) use ($dueStatus, $today, $warningDate) {
                    $query->where('is_active', true);

                    if ($dueStatus === 'overdue') {
                        $query->where(function ($query) use ($today) {
                            $query->whereDate('next_due_date', '<=', $today)
                                ->orWhereHas('vehicle', function ($vehicle) {
                                    $vehicle->whereNotNull('current_kilometers')
                                        ->whereNotNull('maintenance_schedules.next_due_kilometers')
                                        ->whereColumn(
                                            'vehicles.current_kilometers',
                                            '>=',
                                            'maintenance_schedules.next_due_kilometers'
                                        );
                                });
                        });
                    }

                    if ($dueStatus === 'due_soon') {
                        $query->where(function ($query) use ($today, $warningDate) {
                            $query->whereBetween('next_due_date', [$today, $warningDate])
                                ->orWhereHas('vehicle', function ($vehicle) {
                                    $vehicle->whereNotNull('current_kilometers')
                                        ->whereColumn(
                                            'vehicles.current_kilometers',
                                            '<',
                                            'maintenance_schedules.next_due_kilometers'
                                        )
                                        ->whereRaw(
                                            'maintenance_schedules.next_due_kilometers - vehicles.current_kilometers <= ?',
                                            [500]
                                        );
                                });
                        });

                        // Exclude schedules already overdue by date or mileage.
                        $query->where(function ($query) use ($today) {
                            $query->whereNull('next_due_date')
                                ->orWhereDate('next_due_date', '>', $today);
                        });

                        $query->whereDoesntHave('vehicle', function ($vehicle) {
                            $vehicle->whereNotNull('current_kilometers')
                                ->whereNotNull('maintenance_schedules.next_due_kilometers')
                                ->whereColumn(
                                    'vehicles.current_kilometers',
                                    '>=',
                                    'maintenance_schedules.next_due_kilometers'
                                );
                        });
                    }

                    if ($dueStatus === 'up_to_date') {
                        $query->where(function ($query) use ($warningDate) {
                            $query->whereNull('next_due_date')
                                ->orWhereDate('next_due_date', '>', $warningDate);
                        });

                        $query->whereDoesntHave('vehicle', function ($vehicle) {
                            $vehicle->whereNotNull('current_kilometers')
                                ->whereNotNull('maintenance_schedules.next_due_kilometers')
                                ->where(function ($query) {
                                    $query->whereColumn(
                                        'vehicles.current_kilometers',
                                        '>=',
                                        'maintenance_schedules.next_due_kilometers'
                                    )->orWhereRaw(
                                            'maintenance_schedules.next_due_kilometers - vehicles.current_kilometers <= ?',
                                            [500]
                                        );
                                });
                        });
                    }
                }
            )

            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('maintenance-schedules.index', [
            'schedules' => $schedules,
            'search' => $search,
            'status' => $status,
            'dueStatus' => $dueStatus,
        ]);
    }

    public function create(): View
    {
        return view('maintenance-schedules.create', [
            'vehicles' => Vehicle::orderBy('vehicle_code')->get(),
        ]);
    }


    public function store(
        StoreMaintenanceScheduleRequest $request
    ): RedirectResponse {

        $data = $request->validated();

        if (
            empty($data['interval_days'])
            && empty($data['interval_kilometers'])
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'interval_days' =>
                        'At least one maintenance interval is required.',
                ]);
        }

        // No known service history for a new schedule.
        $nextDueDate = !empty($data['interval_days'])
            ? today()->addDays((int) $data['interval_days'])
            : null;

        MaintenanceSchedule::create([
            ...$data,
            'last_service_date' => null,
            'next_due_date' => $nextDueDate,
            'last_service_kilometers' => null,
            'next_due_kilometers' => null,
            'is_active' => true,
        ]);

        return redirect()
            ->route('maintenance-schedules.index')
            ->with('success', 'Maintenance schedule created successfully.');
    }


    public function show(
        MaintenanceSchedule $maintenanceSchedule
    ): View {
        $maintenanceSchedule->load([
            'vehicle',
            'maintenanceRecords' => function ($query) {
                $query->latest('reported_at');
            },
        ]);

        return view(
            'maintenance-schedules.show',
            compact('maintenanceSchedule')
        );
    }

    public function edit(
        MaintenanceSchedule $maintenanceSchedule
    ): View {
        return view('maintenance-schedules.edit', [
            'maintenanceSchedule' => $maintenanceSchedule,
            'vehicles' => Vehicle::orderBy('vehicle_code')->get(),
        ]);
    }


    public function update(
        UpdateMaintenanceScheduleRequest $request,
        MaintenanceSchedule $maintenanceSchedule
    ): RedirectResponse {

        $data = $request->validated();

        // Calculate next due date.
        $data['next_due_date'] = null;

        if (
            !is_null($data['last_service_date'] ?? null)
            && !is_null($data['interval_days'] ?? null)
        ) {
            $data['next_due_date'] = \Carbon\Carbon::parse(
                $data['last_service_date']
            )->addDays((int) $data['interval_days']);
        }

        // Calculate next due kilometers.
        $data['next_due_kilometers'] = null;

        if (
            !is_null($data['last_service_kilometers'] ?? null)
            && !is_null($data['interval_kilometers'] ?? null)
        ) {
            $data['next_due_kilometers'] =
                (int) $data['last_service_kilometers']
                + (int) $data['interval_kilometers'];
        }

        $maintenanceSchedule->update($data);

        return redirect()
            ->route('maintenance-schedules.show', $maintenanceSchedule)
            ->with('success', 'Maintenance schedule updated successfully.');
    }


    public function toggleActive(
        MaintenanceSchedule $maintenanceSchedule
    ): RedirectResponse {
        Gate::authorize('update', $maintenanceSchedule);

        $newStatus = !$maintenanceSchedule->is_active;

        $maintenanceSchedule->update([
            'is_active' => $newStatus,
        ]);

        return redirect()
            ->route('maintenance-schedules.show', $maintenanceSchedule)
            ->with(
                'success',
                $newStatus
                ? 'Maintenance schedule reactivated successfully.'
                : 'Maintenance schedule deactivated successfully.'
            );
    }

    public function destroy(
        MaintenanceSchedule $maintenanceSchedule
    ): RedirectResponse {
        Gate::authorize('delete', $maintenanceSchedule);

        $maintenanceSchedule->delete();

        return redirect()
            ->route('maintenance-schedules.index')
            ->with(
                'success',
                'Maintenance schedule deleted successfully.'
            );
    }

    //restrive vehicle schedule when user load new vehicle in maintenance record form
    public function forVehicle(
        Vehicle $vehicle
    ): \Illuminate\Http\JsonResponse {
        $schedules = $vehicle
            ->maintenanceSchedules()
            ->where('is_active', true)
            ->orderBy('title')
            ->get([
                'id',
                'title',
                'interval_days',
                'interval_kilometers',
                'next_due_date',
                'next_due_kilometers',
            ]);

        return response()->json($schedules);
    }
}