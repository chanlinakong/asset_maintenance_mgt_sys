<?php

namespace App\Services;

use App\Models\MaintenancePart;
use App\Models\MaintenanceRecord;
use App\Models\Part;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class MaintenancePartService
{
    public function addPart(
        MaintenanceRecord $maintenance,
        Part $part,
        int $quantity,
        float $unitCost
    ): MaintenancePart {

        if ($quantity <= 0) {
            throw new InvalidArgumentException(
                'Quantity must be greater than zero.'
            );
        }

        if ($unitCost < 0) {
            throw new InvalidArgumentException(
                'Unit cost cannot be negative.'
            );
        }

        return DB::transaction(function () use (
            $maintenance,
            $part,
            $quantity,
            $unitCost
        ) {

            $totalCost =
                round(
                    $quantity * $unitCost,
                    2
                );

            return MaintenancePart::create([
                'maintenance_record_id' =>
                    $maintenance->id,

                'part_id' =>
                    $part->id,

                'quantity' =>
                    $quantity,

                'unit_cost' =>
                    $unitCost,

                'total_cost' =>
                    $totalCost,
            ]);
        });
    }

    public function removePart(
        MaintenancePart $maintenancePart
    ): void {

        DB::transaction(function () use (
            $maintenancePart
        ) {

            $maintenancePart->delete();
        });
    }

    public function recalculateMaintenanceCost(
        MaintenanceRecord $maintenance
    ): void {

        $partsTotal =
            $maintenance
                ->maintenanceParts()
                ->sum('total_cost');

        $labor =
            (float) ($maintenance->labor_cost ?? 0);

        $other =
            (float) ($maintenance->other_cost ?? 0);

        $total =
            $partsTotal
            + $labor
            + $other;

        $maintenance->update([
            'cost' => round($total, 2),
        ]);
    }
}