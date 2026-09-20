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
        $search = $request
            ->string('search')
            ->trim()
            ->toString();

        $status = $request
            ->string('status')
            ->trim()
            ->toString();

        $schedules = MaintenanceSchedule::query()
            ->with('vehicle')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query
                        ->where('title', 'like', "%{$search}%")
                        ->orWhereHas('vehicle', function ($query) use ($search) {
                            $query
                                ->where(
                                    'vehicle_code',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'name',
                                    'like',
                                    "%{$search}%"
                                );
                        });
                });
            })
            ->when($status === 'active', function ($query) {
                $query->where('is_active', true);
            })
            ->when($status === 'inactive', function ($query) {
                $query->where('is_active', false);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('maintenance-schedules.index', [
            'schedules' => $schedules,
            'search' => $search,
            'status' => $status,
        ]);
    }

    public function create(): View
    {
        return view('maintenance-schedules.create', [
            'vehicles' => Vehicle::orderBy('vehicle_code')->get(),
        ]);
    }

    public function store(StoreMaintenanceScheduleRequest $request): RedirectResponse
    {
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

        $nextDueDate = null;

        if (
            !empty($data['last_service_date'])
            && !empty($data['interval_days'])
        ) {
            $nextDueDate = \Carbon\Carbon::parse(
                $data['last_service_date']
            )->addDays(
                    (int) $data['interval_days']
                );
        } elseif (!empty($data['interval_days'])) {
            $nextDueDate = today()->addDays((int) $data['interval_days']);
        }

        $schedule = MaintenanceSchedule::create([
            ...$data,
            'last_service_date' => null,
            'next_due_date' => $nextDueDate,
            'last_service_kilometers' => null,
            'next_due_kilometers' => null,
            'is_active' => true,
        ]);

        return redirect()
            ->route('maintenance-schedules.index')
            ->with(
                'success',
                'Maintenance schedule created successfully.'
            );
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

        if (
            !empty($data['last_service_date'])
            && !empty($data['interval_days'])
        ) {
            $data['next_due_date'] = \Carbon\Carbon::parse(
                $data['last_service_date']
            )->addDays(
                    (int) $data['interval_days']
                );
        } else {
            $data['next_due_date'] = null;
        }

        if (
            !empty($data['last_service_kilometers'])
            && !empty($data['interval_kilometers'])
        ) {
            $data['next_due_kilometers'] =
                (int) $data['last_service_kilometers']
                + (int) $data['interval_kilometers'];
        } else {
            $data['next_due_kilometers'] = null;
        }

        $maintenanceSchedule->update($data);

        return redirect()
            ->route(
                'maintenance-schedules.show',
                $maintenanceSchedule
            )
            ->with(
                'success',
                'Maintenance schedule updated successfully.'
            );
    }

    public function destroy(
        MaintenanceSchedule $maintenanceSchedule
    ): RedirectResponse {
        Gate::authorize(
            'delete',
            $maintenanceSchedule
        );

        $maintenanceSchedule->update([
            'is_active' => false,
        ]);

        return redirect()
            ->route('maintenance-schedules.index')
            ->with(
                'success',
                'Maintenance schedule deactivated successfully.'
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