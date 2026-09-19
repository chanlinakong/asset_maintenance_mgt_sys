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
use App\Enums\VehicleStatus;
use Illuminate\Support\Facades\DB;

class MaintenanceRecordController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->toString();

        $status = $request->string('status')->trim()->toString();

        $type = $request->string('type')->trim()->toString();

        $vehicleId = $request->integer('vehicle_id');

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

            ->latest('reported_at')
            ->paginate(10)
            ->withQueryString();

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
        $vehicleId = $request->integer('vehicle_id');

        $vehicle = $vehicleId
            ? Vehicle::find($vehicleId)
            : null;

        return view('maintenance.create', [
            'vehicles' => Vehicle::orderBy('vehicle_code')->get(),
            'types' => MaintenanceType::cases(),
            'statuses' => MaintenanceStatus::cases(),
            'selectedVehicle' => $vehicle,
        ]);
    }

    public function store(
        StoreMaintenanceRecordRequest $request
    ): RedirectResponse {
        $data = $request->validated();

        DB::transaction(function () use ($data, &$maintenance) {
            $maintenance = MaintenanceRecord::create($data);

            $maintenance->vehicle->syncMaintenanceStatus();
        });

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
        $data = $request->validated();

        DB::transaction(function () use ($data, $maintenance) {

            $maintenance->update($data);

            $maintenance->load('vehicle');

            $maintenance->vehicle->syncMaintenanceStatus();

        });

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