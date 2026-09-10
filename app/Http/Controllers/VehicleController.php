<?php

namespace App\Http\Controllers;

use App\Enums\VehicleStatus;
use App\Http\Requests\StoreVehicleRequest;
use App\Http\Requests\UpdateVehicleRequest;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->toString();

        $status = $request->string('status')->trim()->toString();

        $vehicles = Vehicle::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('vehicle_code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('type', 'like', "%{$search}%")
                        ->orWhere('brand', 'like', "%{$search}%")
                        ->orWhere('model', 'like', "%{$search}%")
                        ->orWhere(
                            'registration_number',
                            'like',
                            "%{$search}%"
                        );
                });
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('vehicles.index', [
            'vehicles' => $vehicles,
            'search' => $search,
            'status' => $status,
            'statuses' => VehicleStatus::cases(),
        ]);
    }

    public function create(): View
    {
        return view('vehicles.create', [
            'statuses' => VehicleStatus::cases(),
        ]);
    }

    public function store(StoreVehicleRequest $request): RedirectResponse
    {
        Vehicle::create($request->validated());

        return redirect()
            ->route('vehicles.index')
            ->with('success', 'Vehicle created successfully.');
    }

    public function show(Vehicle $vehicle): View
    {
        $vehicle->load([
            'maintenanceRecords' => function ($query) {
                $query->latest('reported_at');
            },
        ]);

        return view('vehicles.show', compact('vehicle'));
    }

    public function edit(Vehicle $vehicle): View
    {
        return view('vehicles.edit', [
            'vehicle' => $vehicle,
            'statuses' => VehicleStatus::cases(),
        ]);
    }

    public function update(
        UpdateVehicleRequest $request,
        Vehicle $vehicle
    ): RedirectResponse {
        $vehicle->update($request->validated());

        return redirect()
            ->route('vehicles.show', $vehicle)
            ->with('success', 'Vehicle updated successfully.');
    }

    public function destroy(Vehicle $vehicle): RedirectResponse
    {
        $vehicle->delete();

        return redirect()
            ->route('vehicles.index')
            ->with('success', 'Vehicle removed successfully.');
    }
}