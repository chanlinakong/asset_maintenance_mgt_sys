<?php

namespace App\Http\Controllers;

use App\Enums\MaintenanceStatus;
use App\Enums\MaintenanceType;
use App\Models\MaintenanceRecord;
use App\Models\Vehicle;
use App\Http\Requests\StoreMaintenanceRecordRequest;
use App\Http\Requests\UpdateMaintenanceRecordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Services\MaintenanceService;
use Illuminate\Support\Facades\Gate;
use App\Models\Part;
use App\Enums\VehicleStatus;
use App\Models\AssetDefect;
use App\Models\DefectType;
use App\Models\MaintenanceVisit;
use Illuminate\Support\Facades\DB;
use App\Models\MaintenanceRecordDefect;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class MaintenanceRecordController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->toString();

        $status = $request->string('status')->trim()->toString();

        $type = $request->string('type')->trim()->toString();

        $vehicleId = $request->integer('vehicle_id');

        $period = $request->input('period');

        $from = $request->input('from');

        $to = $request->input('to');

        $maintenanceRecords = MaintenanceRecord::query()
            ->with('vehicle')

            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere(
                            'description',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'service_provider',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhereHas('vehicle', function ($query) use ($search) {
                            $query->where(
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

            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })

            ->when($type, function ($query) use ($type) {
                $query->where('type', $type);
            })

            ->when($vehicleId, function ($query) use ($vehicleId) {
                $query->where('vehicle_id', $vehicleId);
            })
            ->when($period === 'today', function ($query) {
                $query->whereDate('reported_at', today());
            })
            ->when($period === 'yesterday', function ($query) {
                $query->whereDate('reported_at', today()->subDay());
            })
            ->when($period === 'this_month', function ($query) {
                $query->whereBetween('reported_at', [
                    now()->startOfMonth(),
                    now()->endOfMonth(),
                ]);
            })
            ->when($period === 'last_month', function ($query) {
                $query->whereBetween('reported_at', [
                    now()->subMonthNoOverflow()->startOfMonth(),
                    now()->subMonthNoOverflow()->endOfMonth(),
                ]);
            })
            ->when($period === 'this_year', function ($query) {
                $query->whereBetween('reported_at', [
                    now()->startOfYear(),
                    now()->endOfYear(),
                ]);
            })
            ->when($period === 'last_year', function ($query) {
                $query->whereBetween('reported_at', [
                    now()->subYearNoOverflow()->startOfYear(),
                    now()->subYearNoOverflow()->endOfYear(),
                ]);
            })
            // Custom date range
            ->when($from, function ($query) use ($from) {
                $query->whereDate('reported_at', '>=', $from);
            })
            ->when($to, function ($query) use ($to) {
                $query->whereDate('reported_at', '<=', $to);
            })
            ->latest('reported_at')
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax()) {
            return view('maintenance.partials.table', compact('maintenanceRecords'));
        }

        return view('maintenance.index', [
            'maintenanceRecords' => $maintenanceRecords,
            'search' => $search,
            'status' => $status,
            'type' => $type,
            'vehicleId' => $vehicleId,
            'statuses' => MaintenanceStatus::cases(),
            'types' => MaintenanceType::cases(),
            'vehicles' => Vehicle::orderBy('vehicle_code')->get(),
        ]);
    }

    public function create(Request $request): View
    {
        $selectedVehicle = null;
        $selectedSchedule = null;
        $schedules = collect();

        /* Vehicle selection */

        if ($request->filled('vehicle_id')) {
            $selectedVehicle = Vehicle::find(
                $request->integer('vehicle_id')
            );

            if ($selectedVehicle) {
                $schedules = $selectedVehicle
                    ->maintenanceSchedules()
                    ->where('is_active', true)
                    ->orderBy('title')
                    ->get();
            }
        }

        /*
        | Schedule selectio
        | The dashboard will send schedule_id.
        | Example:
        | /maintenance/create?vehicle_id=1&schedule_id=5
        */

        if ($request->filled('schedule_id')) {
            $selectedSchedule =
                \App\Models\MaintenanceSchedule::query()
                    ->with('vehicle')
                    ->where('id', $request->integer('schedule_id'))
                    ->where('is_active', true)
                    ->first();

            if (!$selectedSchedule) {
                abort(404);
            }

            /* If no vehicle was supplied, get it from the schedule. */

            $selectedVehicle ??= $selectedSchedule->vehicle;

            /* Make sure the selected schedule belongs to the selected vehicle. */

            if (
                !$selectedVehicle
                || $selectedSchedule->vehicle_id
                !== $selectedVehicle->id
            ) {
                abort(404);
            }

            /*
            | Load schedules for the selected vehicle.
            | This is important when the user arrives from the dashboard
            | using only schedule_id.
            */

            $schedules = $selectedVehicle
                ->maintenanceSchedules()
                ->where('is_active', true)
                ->orderBy('title')
                ->get();
        }

        return view('maintenance.create', [
            'vehicles' => Vehicle::where('status', '!=', VehicleStatus::OutOfService->value)
                ->orderBy('vehicle_code')
                ->get(),
            'selectedVehicle' => $selectedVehicle,

            'selectedSchedule' => $selectedSchedule,

            'schedules' => $schedules,

            'types' => MaintenanceType::cases(),

            'statuses' => MaintenanceStatus::cases(),

            'defectTypes' => DefectType::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }

    public function store(
        StoreMaintenanceRecordRequest $request,
        MaintenanceService $maintenanceService
    ): RedirectResponse {
        $data = $request->validated();

        $data['status'] = MaintenanceStatus::Pending;

        $maintenance = $maintenanceService->create($data);

        return redirect()
            ->route('maintenance.index')
            ->with(
                'success',
                'Maintenance record created successfully.'
            );
    }

    public function storeVisit(Request $request, MaintenanceRecord $maintenance)
    {
        Gate::authorize('update', $maintenance);

        $validated = $request->validate([
            'remarks' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($maintenance->status !== MaintenanceStatus::InProgress) {
            return back()->with(
                'error',
                'Visits can only be added to maintenance records that are In Progress.'
            );
        }

        DB::transaction(function () use ($maintenance, $validated) {

            // 1. Lock the parent maintenance report.
            $lockedMaintenance = MaintenanceRecord::query()
                ->lockForUpdate()
                ->findOrFail($maintenance->id);

            // 2. Prevent creating another visit while one is active.
            $activeVisitExists = $lockedMaintenance->visits()
                ->where('status', 'in_progress')
                ->exists();

            if ($activeVisitExists) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'visit' => 'Please complete the current visit before creating another visit.',
                ]);
            }

            // 3. Calculate the next visit number.
            $nextVisitNumber = (int) $lockedMaintenance->visits()
                ->max('visit_number') + 1;

            // 4. Create the visit and store the created model in $visit.
            $visit = $lockedMaintenance->visits()->create([
                'visit_number' => $nextVisitNumber,
                'status' => 'in_progress',
                'technician_id' => auth()->id(),
                'started_at' => now(),
                'remarks' => $validated['remarks'] ?? null,
            ]);

            // 5. Find all unresolved defect occurrences in this maintenance report.
            $unresolvedDefects = MaintenanceRecordDefect::query()
                ->where('maintenance_record_id', $lockedMaintenance->id)
                ->where(function ($query) {
                    $query->whereNull('repair_outcome')
                        ->orWhere('repair_outcome', '!=', 'resolved');
                })
                ->get();

            // 6. Assign those defects to the newly created visit.
            $visit->recordDefects()->sync(
                $unresolvedDefects->modelKeys()
            );
        });

        return redirect()
            ->route('maintenance.show', $maintenance)
            ->with('success', 'Maintenance visit created successfully.');
    }

    public function completeVisit(
        MaintenanceRecord $maintenance,
        MaintenanceVisit $visit
    ) {
        Gate::authorize('update', $maintenance);

        // Ensure this visit belongs to the selected maintenance report.
        abort_unless(
            $visit->maintenance_record_id === $maintenance->id,
            404
        );

        if ($maintenance->status !== MaintenanceStatus::InProgress) {
            return back()->with(
                'error',
                'Visits cannot be completed because this maintenance record is not In Progress.'
            );
        }

        if ($visit->status !== 'in_progress') {
            return back()->with(
                'error',
                'This visit is already completed or is no longer active.'
            );
        }

        $visit->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return redirect()
            ->route('maintenance.show', $maintenance)
            ->with('success', 'Maintenance visit completed successfully.');
    }

    public function show(
        MaintenanceRecord $maintenance
    ): View {
        $maintenance->load([
            'vehicle',
            'audits.user',
            'attachments.uploader',
            'maintenanceParts.part',

            'recordDefects.assetDefect.defectType',
            'recordDefects.repairWorks.maintenanceParts.part',

            'visits.technician',
            'visits.repairWorks.maintenanceRecordDefect.assetDefect',
            'visits.repairWorks.maintenanceParts.part',

            'visits.recordDefects.assetDefect.defectType',
            'visits.recordDefects.repairWorks.maintenanceParts.part',
        ]);

        $maintenance->loadMissing([
            'visits.attachments.uploader',
        ]);

        $parts = Part::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $hasActiveVisit = $maintenance->visits()
            ->where('status', 'in_progress')
            ->exists();

        return view(
            'maintenance.show',
            compact('maintenance', 'parts', 'hasActiveVisit')
        );
    }

    public function edit(
        MaintenanceRecord $maintenance
    ): View {
        return view('maintenance.edit', [
            'maintenance' => $maintenance,
            'vehicles' => Vehicle::orderBy('vehicle_code')->get(),
            'types' => MaintenanceType::cases(),
            'statuses' => $maintenance->status
                ->allowedNextStatuses(),
        ]);
    }

    public function update(
        UpdateMaintenanceRecordRequest $request,
        MaintenanceRecord $maintenance,
        MaintenanceService $maintenanceService
    ): RedirectResponse {
        $maintenanceService->update(
            $maintenance,
            $request->validated()
        );

        return redirect()
            ->route('maintenance.show', $maintenance)
            ->with(
                'success',
                'Maintenance record updated successfully.'
            );
    }

    public function destroy(
        MaintenanceRecord $maintenance,
        MaintenanceService $maintenanceService
    ): RedirectResponse {
        Gate::authorize('delete', $maintenance);
        $maintenanceService->delete($maintenance);

        return redirect()
            ->route('maintenance.index')
            ->with(
                'success',
                'Maintenance record deleted successfully.'
            );
    }

    public function availableDefects(Vehicle $vehicle)
    {
        $defects = AssetDefect::query()
            ->where('vehicle_id', $vehicle->id)

            // Include all registered defects.
            // Do not hide defects with unresolved occurrences.
            ->with('defectType:id,name')

            // Count every occurrence in maintenance records.
            ->withCount([
                'maintenanceRecordDefects as occurrences_count',
            ])

            // Count unresolved occurrences in active maintenance records.
            ->withCount([
                'maintenanceRecordDefects as unresolved_occurrences_count' => function ($query) {
                    $query
                        ->where(function ($query) {
                            $query->whereNull('repair_outcome')
                                ->orWhere('repair_outcome', '!=', 'resolved');
                        })
                        ->whereHas('maintenanceRecord', function ($maintenanceQuery) {
                            $maintenanceQuery->whereIn('status', [
                                MaintenanceStatus::Pending->value,
                                MaintenanceStatus::InProgress->value,
                            ]);
                        });
                },
            ])

            ->orderBy('defect_type_id')
            ->orderBy('title')
            ->get([
                'id',
                'vehicle_id',
                'defect_type_id',
                'title',
                'description',
                'status',
                'reported_at',
            ]);

        return response()->json(
            $defects->map(fn($defect) => [
                'id' => $defect->id,
                'title' => $defect->title,
                'description' => $defect->description,
                'status' => $defect->status,
                'reported_at' => $defect->reported_at?->format('d M Y'),

                'defect_type_id' => $defect->defect_type_id,
                'defect_type' => $defect->defectType?->name,

                'occurrences_count' => (int) $defect->occurrences_count,
                'unresolved_occurrences_count' => (int) $defect->unresolved_occurrences_count,

                'has_unresolved_occurrence' => $defect->unresolved_occurrences_count > 0,
            ])
        );
    }

    //update the repair outcome of a defect occurrence and synchronize the status of the registered asset defect.
    public function updateDefectOutcome(
        Request $request,
        MaintenanceRecord $maintenance,
        MaintenanceVisit $visit,
        MaintenanceRecordDefect $recordDefect
    ) {
        Gate::authorize('update', $maintenance);

        $validated = $request->validate([
            'repair_outcome' => [
                'required',
                Rule::in(['in_progress', 'resolved', 'unable_to_repair']),
            ],
            'outcome_remarks' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($maintenance, $visit, $recordDefect, $validated) {
            // 1. Lock the maintenance report.
            $lockedMaintenance = MaintenanceRecord::query()
                ->lockForUpdate()
                ->findOrFail($maintenance->id);

            // 2. Confirm this visit belongs to this maintenance report.
            $lockedVisit = MaintenanceVisit::query()
                ->where('maintenance_record_id', $lockedMaintenance->id)
                ->lockForUpdate()
                ->findOrFail($visit->id);

            // 3. Confirm the defect occurrence belongs to this report.
            $lockedDefect = MaintenanceRecordDefect::query()
                ->where('maintenance_record_id', $lockedMaintenance->id)
                ->lockForUpdate()
                ->findOrFail($recordDefect->id);

            // 4. Only allow updating defects assigned to this visit.
            $isAssignedToVisit = $lockedVisit->recordDefects()
                ->whereKey($lockedDefect->id)
                ->exists();

            if (!$isAssignedToVisit) {
                throw ValidationException::withMessages([
                    'repair_outcome' => 'This defect is not assigned to the selected visit.',
                ]);
            }

            // 5. Only allow outcome changes during an active visit.
            if (
                $lockedMaintenance->status !== MaintenanceStatus::InProgress ||
                $lockedVisit->status !== 'in_progress'
            ) {
                throw ValidationException::withMessages([
                    'repair_outcome' => 'Defect outcomes can only be updated during an active maintenance visit.',
                ]);
            }

            // 6. Update the occurrence outcome.
            $lockedDefect->update([
                'repair_outcome' => $validated['repair_outcome'],
                'outcome_remarks' => $validated['outcome_remarks'] ?? null,
                'outcome_recorded_at' => now(),
            ]);

            // 7. Lock the reusable registered vehicle defect.
            $assetDefect = AssetDefect::query()
                ->lockForUpdate()
                ->findOrFail($lockedDefect->asset_defect_id);

            // 8. Check whether ANY occurrence of this asset defect
            // is still unresolved in any maintenance report.
            $unresolvedOccurrences = $assetDefect
                ->maintenanceRecordDefects()
                ->where(function ($query) {
                    $query->whereNull('repair_outcome')
                        ->orWhere('repair_outcome', '!=', 'resolved');
                });

            $hasUnresolvedOccurrences = (clone $unresolvedOccurrences)->exists();

            $hasInProgressOccurrences = (clone $unresolvedOccurrences)
                ->where('repair_outcome', 'in_progress')
                ->exists();

            // 9. Synchronize the registered asset defect status.
            if (!$hasUnresolvedOccurrences) {
                $assetDefect->update([
                    'status' => 'resolved',
                    'resolved_at' => now(),
                ]);
            } else {
                $assetDefect->update([
                    'status' => $hasInProgressOccurrences
                        ? 'in_progress'
                        : 'open',
                    'resolved_at' => null,
                ]);
            }
        });

        return redirect()
            ->route('maintenance.show', $maintenance)
            ->with('success', 'Defect outcome and registered asset defect status updated successfully.');
    }


}