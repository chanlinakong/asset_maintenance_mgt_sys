<?php

namespace App\Http\Controllers;

use App\Models\MaintenancePart;
use App\Models\MaintenanceRecord;
use App\Models\Part;
use App\Services\MaintenancePartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MaintenancePartController extends Controller
{
    public function store(
        Request $request,
        MaintenanceRecord $maintenance,
        MaintenancePartService $service
    ): RedirectResponse {

        // $this->authorize(
        //     'update',
        //     $maintenance
        // );

        $data = $request->validate([
            'part_id' => [
                'required',
                'integer',
                'exists:parts,id',
            ],

            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],

            'unit_cost' => [
                'required',
                'numeric',
                'min:0',
            ],
        ]);

        $part = Part::findOrFail(
            $data['part_id']
        );

        $service->addPart(
            $maintenance,
            $part,
            (int) $data['quantity'],
            (float) $data['unit_cost']
        );

        $service->recalculateMaintenanceCost(
            $maintenance
        );

        return back()->with(
            'success',
            'Part added successfully.'
        );
    }

    public function destroy(
        MaintenanceRecord $maintenance,
        MaintenancePart $maintenancePart,
        MaintenancePartService $service
    ): RedirectResponse {

        // $this->authorize(
        //     'update',
        //     $maintenance
        // );

        abort_unless(
            $maintenancePart->maintenance_record_id
                === $maintenance->id,
            404
        );

        $service->removePart(
            $maintenancePart
        );

        $service->recalculateMaintenanceCost(
            $maintenance
        );

        return back()->with(
            'success',
            'Part removed successfully.'
        );
    }
}