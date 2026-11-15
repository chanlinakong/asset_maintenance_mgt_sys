<?php

namespace App\Http\Controllers;

use App\Enums\MaintenanceStatus;
use App\Models\MaintenancePart;
use App\Models\MaintenanceRecord;
use App\Models\MaintenanceRecordDefect;
use App\Models\MaintenanceRepairWork;
use App\Models\MaintenanceVisit;
use App\Models\Part;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MaintenancePartController extends Controller
{
    use AuthorizesRequests;
    private function verifyOwnership(
        MaintenanceRecord $maintenance,
        MaintenanceVisit $visit,
        MaintenanceRecordDefect $recordDefect,
        MaintenanceRepairWork $repairWork
    ): void {
        $this->authorize('update', $maintenance);

        abort_unless(
            $maintenance->status === MaintenanceStatus::InProgress,
            403
        );

        abort_unless(
            (int) $visit->maintenance_record_id === (int) $maintenance->id
            && $visit->status === 'in_progress',
            404
        );

        abort_unless(
            (int) $recordDefect->maintenance_record_id ===
                (int) $maintenance->id,
            404
        );

        abort_unless(
            (int) $repairWork->maintenance_record_defect_id ===
                (int) $recordDefect->id
            && (int) $repairWork->maintenance_visit_id ===
                (int) $visit->id,
            404
        );
    }

    private function refreshMaintenanceCosts(int $maintenanceId): void
    {
        $maintenance = MaintenanceRecord::query()
            ->lockForUpdate()
            ->findOrFail($maintenanceId);

        $partsCost = (float) MaintenancePart::query()
            ->where('maintenance_record_id', $maintenanceId)
            ->sum('total_cost');

        $laborCost = (float) ($maintenance->labor_cost ?? 0);
        $otherCost = (float) ($maintenance->other_cost ?? 0);

        $maintenance->update([
            'parts_cost' => round($partsCost, 2),
            'cost' => round($partsCost + $laborCost + $otherCost, 2),
        ]);
    }

    public function store(
        Request $request,
        MaintenanceRecord $maintenance,
        MaintenanceVisit $visit,
        MaintenanceRecordDefect $recordDefect,
        MaintenanceRepairWork $repairWork
    ) {
        $this->verifyOwnership(
            $maintenance, $visit, $recordDefect, $repairWork
        );

        $data = $request->validate([
            'part_id' => [
                'required',
                'integer',
                Rule::exists('parts', 'id')->where('is_active', true),
                Rule::unique('maintenance_parts', 'part_id')
                    ->where(fn ($query) =>
                        $query->where(
                            'maintenance_repair_work_id',
                            $repairWork->id
                        )
                    ),
            ],
            'quantity' => ['required', 'numeric', 'gt:0'],
            'unit_cost' => ['required', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use (
            $data, $maintenance, $repairWork
        ) {
            $part = Part::query()
                ->where('is_active', true)
                ->findOrFail($data['part_id']);

            $quantity = (float) $data['quantity'];
            $unitCost = (float) $data['unit_cost'];

            MaintenancePart::create([
                'maintenance_record_id' => $maintenance->id,
                'maintenance_repair_work_id' => $repairWork->id,
                'part_id' => $part->id,

                // Snapshot the catalogue details at the time of use.
                'part_name' => $part->name,
                'part_code' => $part->part_code,
                'unit' => $part->unit,

                'quantity' => $quantity,
                'unit_cost' => $unitCost,
                'total_cost' => round($quantity * $unitCost, 2),
            ]);

            $this->refreshMaintenanceCosts($maintenance->id);
        });

        return back()->with('success', 'Spare part added successfully.');
    }

    public function update(
        Request $request,
        MaintenanceRecord $maintenance,
        MaintenanceVisit $visit,
        MaintenanceRecordDefect $recordDefect,
        MaintenanceRepairWork $repairWork,
        MaintenancePart $maintenancePart
    ) {
        $this->verifyOwnership(
            $maintenance, $visit, $recordDefect, $repairWork
        );

        abort_unless(
            (int) $maintenancePart->maintenance_record_id ===
                (int) $maintenance->id
            && (int) $maintenancePart->maintenance_repair_work_id ===
                (int) $repairWork->id,
            404
        );

        $data = $request->validate([
            'quantity' => ['required', 'numeric', 'gt:0'],
            'unit_cost' => ['required', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use (
            $data, $maintenance, $maintenancePart
        ) {
            $quantity = (float) $data['quantity'];
            $unitCost = (float) $data['unit_cost'];

            $maintenancePart->update([
                'quantity' => $quantity,
                'unit_cost' => $unitCost,
                'total_cost' => round($quantity * $unitCost, 2),
            ]);

            $this->refreshMaintenanceCosts($maintenance->id);
        });

        return back()->with('success', 'Spare part updated successfully.');
    }

    public function destroy(
        MaintenanceRecord $maintenance,
        MaintenanceVisit $visit,
        MaintenanceRecordDefect $recordDefect,
        MaintenanceRepairWork $repairWork,
        MaintenancePart $maintenancePart
    ) {
        $this->verifyOwnership(
            $maintenance, $visit, $recordDefect, $repairWork
        );

        abort_unless(
            (int) $maintenancePart->maintenance_record_id ===
                (int) $maintenance->id
            && (int) $maintenancePart->maintenance_repair_work_id ===
                (int) $repairWork->id,
            404
        );

        DB::transaction(function () use (
            $maintenance, $maintenancePart
        ) {
            $maintenancePart->delete();

            $this->refreshMaintenanceCosts($maintenance->id);
        });

        return back()->with('success', 'Spare part removed successfully.');
    }
}
