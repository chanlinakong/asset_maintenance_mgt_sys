<?php

namespace App\Services;

use App\Models\MaintenanceRecord;
use Illuminate\Support\Facades\DB;
use App\Models\MaintenanceAudit;
use Illuminate\Support\Facades\Auth;
use App\Enums\MaintenanceStatus;
use App\Enums\MaintenanceType;

class MaintenanceService
{
    public function create(array $data): MaintenanceRecord
    {
        return DB::transaction(function () use ($data) {

            $data['parts_cost'] = $data['parts_cost'] ?? 0;

            $data['labor_cost'] = $data['labor_cost'] ?? 0;

            $data['other_cost'] = $data['other_cost'] ?? 0;

            $data['cost'] = $data['parts_cost'] + $data['labor_cost'] + $data['other_cost'];

            $maintenance = MaintenanceRecord::create($data);

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

            $data['parts_cost'] = $data['parts_cost'] ?? 0;

            $data['labor_cost'] = $data['labor_cost'] ?? 0;

            $data['other_cost'] = $data['other_cost'] ?? 0;

            $data['cost'] = $data['parts_cost'] + $data['labor_cost'] + $data['other_cost'];


            $oldStatus = $maintenance->status;

            $maintenance->update($data);

            $maintenance->load([
                'vehicle',
                'maintenanceSchedule',
            ]);

            $newStatus = $maintenance->status;

            if (
                $newStatus === MaintenanceStatus::Completed &&
                $maintenance->service_kilometers !== null
                && (
                    $maintenance->vehicle->current_kilometers === null
                    || $maintenance->service_kilometers
                    > $maintenance->vehicle->current_kilometers
                )
            ) {
                $maintenance->vehicle->update([
                    'current_kilometers' =>
                        $maintenance->service_kilometers,
                ]);
            }

            $maintenance->vehicle->syncMaintenanceStatus();

            if (
                $oldStatus !== MaintenanceStatus::Completed
                && $newStatus === MaintenanceStatus::Completed
                && $maintenance->type === MaintenanceType::Preventive
                && $maintenance->maintenanceSchedule
            ) {
                $maintenance
                    ->maintenanceSchedule
                    ->markServiced(
                        $maintenance->completed_at,
                        $maintenance->service_kilometers
                    );
            }

            if ($oldStatus !== $newStatus) {

                MaintenanceAudit::create([
                    'maintenance_record_id' => $maintenance->id,
                    'user_id' => Auth::id(),
                    'action' => 'status_changed',
                    'old_status' => $oldStatus->value,
                    'new_status' => $newStatus->value,
                    'description' =>
                        'Maintenance status changed.',
                ]);
            }

            return $maintenance->fresh();
        });
    }

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