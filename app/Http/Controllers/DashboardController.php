<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRecord;
use App\Models\Vehicle;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalVehicles = Vehicle::count();

        $activeVehicles = Vehicle::where(
            'status',
            'active'
        )->count();

        $vehiclesUnderMaintenance = Vehicle::where(
            'status',
            'maintenance'
        )->count();

        $vehiclesOutOfService = Vehicle::where(
            'status',
            'out_of_service'
        )->count();

        $recentMaintenance = MaintenanceRecord::with('vehicle')
            ->latest('reported_at')
            ->take(5)
            ->get();

        return view('dashboard.index', [
            'totalVehicles' => $totalVehicles,
            'activeVehicles' => $activeVehicles,
            'vehiclesUnderMaintenance' => $vehiclesUnderMaintenance,
            'vehiclesOutOfService' => $vehiclesOutOfService,
            'recentMaintenance' => $recentMaintenance,
        ]);
    }
}