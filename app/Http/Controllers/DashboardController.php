<?php

namespace App\Http\Controllers;

use App\Enums\MaintenanceStatus;
use App\Enums\MaintenanceType;
use App\Models\MaintenanceRecord;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;
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

        $totalMaintenanceRecords = MaintenanceRecord::count();

        $recentMaintenance = MaintenanceRecord::with('vehicle')
            ->latest('reported_at')
            ->take(5)
            ->get();

    
        //Current Month Statistics

        $monthlyRecords = MaintenanceRecord::whereMonth(
            'reported_at',
            now()->month
        )->whereYear(
            'reported_at',
            now()->year
        )->get();

        $monthlyMaintenanceCount = $monthlyRecords->count();

        $monthlyMaintenanceCost = $monthlyRecords->sum(
            fn ($record) => (float) $record->cost
        );

        //Maintenance By Type
        
        $maintenanceByType = MaintenanceRecord::query()
            ->select(
                'type',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('type')
            ->orderByDesc('total')
            ->get();

        //Maintenance By Status

        $maintenanceByStatus = MaintenanceRecord::query()
            ->select(
                'status',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('status')
            ->orderByDesc('total')
            ->get();

        return view('dashboard.index', [
            'totalVehicles' => $totalVehicles,
            'activeVehicles' => $activeVehicles,
            'vehiclesUnderMaintenance' => $vehiclesUnderMaintenance,
            'vehiclesOutOfService' => $vehiclesOutOfService,
            'totalMaintenanceRecords' => $totalMaintenanceRecords,
            'recentMaintenance' => $recentMaintenance,

            'monthlyMaintenanceCount' =>
                $monthlyMaintenanceCount,

            'monthlyMaintenanceCost' =>
                $monthlyMaintenanceCost,

            'maintenanceByType' =>
                $maintenanceByType,

            'maintenanceByStatus' =>
                $maintenanceByStatus,
        ]);
    }
}