<?php

namespace App\Http\Controllers;

use App\Enums\MaintenanceStatus;
use App\Models\MaintenanceRecord;
use App\Models\MaintenanceRecordDefect;
use App\Models\MaintenanceRepairWork;
use App\Models\MaintenanceVisit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class MaintenanceRepairWorkController extends Controller
{
    private function verifyOwnership(
        MaintenanceRecord $maintenance,
        MaintenanceVisit $visit,
        MaintenanceRecordDefect $recordDefect
    ): void {
        Gate::authorize('update', $maintenance);

        abort_unless(
            $maintenance->status === MaintenanceStatus::InProgress,
            403,
            'This maintenance report is not in progress.'
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
    }

    private function verifyRepairWork(
        MaintenanceRepairWork $repairWork,
        MaintenanceVisit $visit,
        MaintenanceRecordDefect $recordDefect
    ): void {
        abort_unless(
            (int) $repairWork->maintenance_record_defect_id ===
            (int) $recordDefect->id
            && (int) $repairWork->maintenance_visit_id ===
            (int) $visit->id,
            404
        );
    }

    public function store(
        Request $request,
        MaintenanceRecord $maintenance,
        MaintenanceVisit $visit,
        MaintenanceRecordDefect $recordDefect
    ) {
        $this->verifyOwnership($maintenance, $visit, $recordDefect);

        $data = $request->validate([
            'work_description' => ['required', 'string', 'max:10000'],
            'quantity' => ['nullable', 'numeric', 'gt:0'],
            'unit' => ['nullable', 'string', 'max:30'],
            'remarks' => ['nullable', 'string', 'max:5000'],
        ]);

        DB::transaction(function () use ($data, $visit, $recordDefect) {
            $visit->repairWorks()->create([
                'maintenance_record_defect_id' => $recordDefect->id,
                'work_description' => $data['work_description'],
                'quantity' => $data['quantity'] ?? null,
                'unit' => $data['unit'] ?? null,
                'remarks' => $data['remarks'] ?? null,
            ]);
        });

        return back()->with('success', 'Repair activity added successfully.');
    }

    public function update(
        Request $request,
        MaintenanceRecord $maintenance,
        MaintenanceVisit $visit,
        MaintenanceRecordDefect $recordDefect,
        MaintenanceRepairWork $repairWork
    ) {
        $this->verifyOwnership($maintenance, $visit, $recordDefect);
        $this->verifyRepairWork($repairWork, $visit, $recordDefect);

        $data = $request->validate([
            'work_description' => ['required', 'string', 'max:10000'],
            'quantity' => ['nullable', 'numeric', 'gt:0'],
            'unit' => ['nullable', 'string', 'max:30'],
            'remarks' => ['nullable', 'string', 'max:5000'],
        ]);

        $repairWork->update($data);

        return back()->with('success', 'Repair activity updated successfully.');
    }

    public function destroy(
        MaintenanceRecord $maintenance,
        MaintenanceVisit $visit,
        MaintenanceRecordDefect $recordDefect,
        MaintenanceRepairWork $repairWork
    ) {
        $this->verifyOwnership($maintenance, $visit, $recordDefect);
        $this->verifyRepairWork($repairWork, $visit, $recordDefect);

        // Associated part rows are deleted by their foreign-key cascade.
        //$repairWork->delete();
        DB::transaction(function () use ($repairWork, $maintenance) {
            $repairWork->delete();

            $this->refreshMaintenanceCosts($maintenance->id);
        });

        return back()->with('success', 'Repair activity deleted successfully.');
    }

    private function refreshMaintenanceCosts(int $maintenanceId): void
    {
        $maintenance = MaintenanceRecord::query()
            ->lockForUpdate()
            ->findOrFail($maintenanceId);

        $partsCost = (float) \App\Models\MaintenancePart::query()
            ->where('maintenance_record_id', $maintenanceId)
            ->sum('total_cost');

        $laborCost = (float) ($maintenance->labor_cost ?? 0);
        $otherCost = (float) ($maintenance->other_cost ?? 0);

        $maintenance->update([
            'parts_cost' => round($partsCost, 2),
            'cost' => round($partsCost + $laborCost + $otherCost, 2),
        ]);
    }
}
