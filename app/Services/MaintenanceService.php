<?php

namespace App\Services;

use App\Models\MaintenanceRecord;
use Illuminate\Support\Facades\DB;

class MaintenanceService
{
    public function create(array $data): MaintenanceRecord
    {
        return DB::transaction(function () use ($data) {

            $maintenance = MaintenanceRecord::create($data);

            $maintenance->load('vehicle');

            $maintenance->vehicle->syncMaintenanceStatus();

            return $maintenance;
        });
    }

    public function update(
        MaintenanceRecord $maintenance,
        array $data
    ): MaintenanceRecord {
        return DB::transaction(function () use (
            $maintenance,
            $data
        ) {

            $maintenance->update($data);

            $maintenance->load('vehicle');

            $maintenance->vehicle->syncMaintenanceStatus();

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