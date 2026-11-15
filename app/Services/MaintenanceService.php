<?php

namespace App\Services;

use App\Models\MaintenanceRecord;
use Illuminate\Support\Facades\DB;
use App\Models\MaintenanceAudit;
use Illuminate\Support\Facades\Auth;
use App\Enums\MaintenanceStatus;
use App\Enums\MaintenanceType;
use App\Models\AssetDefect;
use App\Models\MaintenanceRecordDefect;
use Illuminate\Validation\ValidationException;

class MaintenanceService
{
    public function create(array $data): MaintenanceRecord
    {
        return DB::transaction(function () use ($data) {

            $defectIds = $data['defect_ids'] ?? [];

            $newDefects = $data['new_defects'] ?? [];

            unset(
                $data['defect_ids'],
                $data['new_defects']
            );

            $data['parts_cost'] = $data['parts_cost'] ?? 0;

            $data['labor_cost'] = $data['labor_cost'] ?? 0;

            $data['other_cost'] = $data['other_cost'] ?? 0;

            $data['cost'] = $data['parts_cost'] + $data['labor_cost'] + $data['other_cost'];

            $maintenance = MaintenanceRecord::create($data);

            // Link existing defects to this maintenance report.
            foreach ($defectIds as $defectId) {

                MaintenanceRecordDefect::create([
                    'maintenance_record_id' => $maintenance->id,
                    'asset_defect_id' => $defectId,
                ]);
            }


            // Create new defects and link them to this report.
            foreach ($newDefects as $newDefectData) {

                $defect = AssetDefect::create([
                    'vehicle_id' => $maintenance->vehicle_id,
                    'defect_type_id' => $newDefectData['defect_type_id'],
                    'title' => $newDefectData['title'],
                    'description' => $newDefectData['description'] ?? null,
                    'status' => 'open',
                    'reported_at' => now(),
                ]);

                MaintenanceRecordDefect::create([
                    'maintenance_record_id' => $maintenance->id,
                    'asset_defect_id' => $defect->id,
                ]);
            }

            $maintenance->load('vehicle');

            $maintenance->vehicle->syncMaintenanceStatus();

            MaintenanceAudit::create([
                'maintenance_record_id' => $maintenance->id,
                'user_id' => Auth::id(),
                'action' => 'created',
                'old_status' => null,
                'new_status' => $maintenance->status->value,
                'description' => 'Maintenance record created.',
            ]);

            return $maintenance;
        });
    }

    public function update(
        MaintenanceRecord $maintenance,
        array $data
    ): MaintenanceRecord {
        return DB::transaction(function () use ($maintenance, $data) {

            // Lock the maintenance report to prevent concurrent updates.
            $lockedMaintenance = MaintenanceRecord::query()
                ->lockForUpdate()
                ->findOrFail($maintenance->id);

            // Determine the requested status.
            $requestedStatus = $data['status'] ?? $lockedMaintenance->status;

            $requestedStatusValue = $requestedStatus instanceof MaintenanceStatus
                ? $requestedStatus->value
                : $requestedStatus;

            // Only apply these checks when completing maintenance.
            if ($requestedStatusValue === MaintenanceStatus::Completed->value) {

                // 1. Check whether an active visit exists.
                $hasActiveVisit = $lockedMaintenance->visits()
                    ->where('status', 'in_progress')
                    ->exists();

                if ($hasActiveVisit) {
                    throw ValidationException::withMessages([
                        'status' => 'Cannot complete maintenance while a visit is still in progress. Please complete all active visits first.',
                    ]);
                }

                // 2. Check whether any defect occurrence remains unresolved.
                $hasUnresolvedDefects = $lockedMaintenance->recordDefects()
                    ->where(function ($query) {
                        $query->whereNull('repair_outcome')
                            ->orWhere('repair_outcome', '!=', 'resolved');
                    })
                    ->exists();

                if ($hasUnresolvedDefects) {
                    throw ValidationException::withMessages([
                        'status' => 'Cannot complete maintenance. Please resolve all outstanding defects first.',
                    ]);
                }
            }

            // 3. Calculate maintenance costs.
            $data['parts_cost'] = $data['parts_cost'] ?? 0;
            $data['labor_cost'] = $data['labor_cost'] ?? 0;
            $data['other_cost'] = $data['other_cost'] ?? 0;

            $data['cost'] = $data['parts_cost']
                + $data['labor_cost']
                + $data['other_cost'];

            // 4. Keep the old status for audit logging.
            $oldStatus = $lockedMaintenance->status;

            // 5. Update the maintenance report.
            $lockedMaintenance->update($data);

            $lockedMaintenance->load([
                'vehicle',
                'maintenanceSchedule',
            ]);

            $newStatus = $lockedMaintenance->status;

            // 6. Update vehicle kilometers when maintenance is completed.
            if (
                $newStatus === MaintenanceStatus::Completed &&
                $lockedMaintenance->service_kilometers !== null &&
                (
                    $lockedMaintenance->vehicle->current_kilometers === null ||
                    $lockedMaintenance->service_kilometers >
                    $lockedMaintenance->vehicle->current_kilometers
                )
            ) {
                $lockedMaintenance->vehicle->update([
                    'current_kilometers' =>
                        $lockedMaintenance->service_kilometers,
                ]);
            }

            // 7. Synchronize vehicle maintenance status.
            $lockedMaintenance->vehicle->syncMaintenanceStatus();

            // 8. Update preventive maintenance schedule.
            if (
                $oldStatus !== MaintenanceStatus::Completed &&
                $newStatus === MaintenanceStatus::Completed &&
                $lockedMaintenance->type === MaintenanceType::Preventive &&
                $lockedMaintenance->maintenanceSchedule
            ) {
                $lockedMaintenance->maintenanceSchedule->markServiced(
                    $lockedMaintenance->completed_at,
                    $lockedMaintenance->service_kilometers
                );
            }

            // 9. Record status changes in the audit history.
            if ($oldStatus !== $newStatus) {
                MaintenanceAudit::create([
                    'maintenance_record_id' => $lockedMaintenance->id,
                    'user_id' => Auth::id(),
                    'action' => 'status_changed',
                    'old_status' => $oldStatus->value,
                    'new_status' => $newStatus->value,
                    'description' => 'Maintenance status changed.',
                ]);
            }

            return $lockedMaintenance->fresh();
        });
    }

    // public function update(
    //     MaintenanceRecord $maintenance,
    //     array $data
    // ): MaintenanceRecord {
    //     return DB::transaction(function () use ($maintenance, $data) {

    //         $data['parts_cost'] = $data['parts_cost'] ?? 0;

    //         $data['labor_cost'] = $data['labor_cost'] ?? 0;

    //         $data['other_cost'] = $data['other_cost'] ?? 0;

    //         $data['cost'] = $data['parts_cost'] + $data['labor_cost'] + $data['other_cost'];


    //         $oldStatus = $maintenance->status;

    //         $maintenance->update($data);

    //         $maintenance->load([
    //             'vehicle',
    //             'maintenanceSchedule',
    //         ]);

    //         $newStatus = $maintenance->status;

    //         if (
    //             $newStatus === MaintenanceStatus::Completed &&
    //             $maintenance->service_kilometers !== null
    //             && (
    //                 $maintenance->vehicle->current_kilometers === null
    //                 || $maintenance->service_kilometers
    //                 > $maintenance->vehicle->current_kilometers
    //             )
    //         ) {
    //             $maintenance->vehicle->update([
    //                 'current_kilometers' =>
    //                     $maintenance->service_kilometers,
    //             ]);
    //         }

    //         $maintenance->vehicle->syncMaintenanceStatus();

    //         if (
    //             $oldStatus !== MaintenanceStatus::Completed
    //             && $newStatus === MaintenanceStatus::Completed
    //             && $maintenance->type === MaintenanceType::Preventive
    //             && $maintenance->maintenanceSchedule
    //         ) {
    //             $maintenance
    //                 ->maintenanceSchedule
    //                 ->markServiced(
    //                     $maintenance->completed_at,
    //                     $maintenance->service_kilometers
    //                 );
    //         }

    //         if ($oldStatus !== $newStatus) {

    //             MaintenanceAudit::create([
    //                 'maintenance_record_id' => $maintenance->id,
    //                 'user_id' => Auth::id(),
    //                 'action' => 'status_changed',
    //                 'old_status' => $oldStatus->value,
    //                 'new_status' => $newStatus->value,
    //                 'description' =>
    //                     'Maintenance status changed.',
    //             ]);
    //         }

    //         return $maintenance->fresh();
    //     });
    // }

    public function delete(
        MaintenanceRecord $maintenance
    ): void {
        DB::transaction(function () use ($maintenance) {

            $vehicle = $maintenance->vehicle;

            $maintenance->delete();

            $vehicle->syncMaintenanceStatus();
        });
    }
}