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

class MaintenanceRecordController extends Controller
{
    public function index(): View
    {
        $maintenanceRecords = MaintenanceRecord::with('vehicle')
            ->latest('reported_at')
            ->paginate(10);

        return view(
            'maintenance.index',
            compact('maintenanceRecords')
        );
    }

    public function create(): View
    {
        return view('maintenance.create', [
            'vehicles' => Vehicle::orderBy('vehicle_code')->get(),
            'types' => MaintenanceType::cases(),
            'statuses' => MaintenanceStatus::cases(),
        ]);
    }

    public function store(
        StoreMaintenanceRecordRequest $request
    ): RedirectResponse {
        MaintenanceRecord::create(
            $request->validated()
        );

        return redirect()
            ->route('maintenance.index')
            ->with(
                'success',
                'Maintenance record created successfully.'
            );
    }

    public function show(
        MaintenanceRecord $maintenance
    ): View {
        $maintenance->load('vehicle');

        return view(
            'maintenance.show',
            compact('maintenance')
        );
    }

    public function edit(
        MaintenanceRecord $maintenance
    ): View {
        return view('maintenance.edit', [
            'maintenance' => $maintenance,
            'vehicles' => Vehicle::orderBy('vehicle_code')->get(),
            'types' => MaintenanceType::cases(),
            'statuses' => MaintenanceStatus::cases(),
        ]);
    }

    public function update(
        UpdateMaintenanceRecordRequest $request,
        MaintenanceRecord $maintenance
    ): RedirectResponse {
        $maintenance->update(
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
        MaintenanceRecord $maintenance
    ): RedirectResponse {
        $maintenance->delete();

        return redirect()
            ->route('maintenance.index')
            ->with(
                'success',
                'Maintenance record deleted successfully.'
            );
    }
}