<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceSchedule;
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

        $totalSchedules = MaintenanceSchedule::where(
            'is_active',
            true
        )->count();

        //by date not km
        // $overdueSchedules = MaintenanceSchedule::where(
        //     'is_active',
        //     true
        // )
        //     ->whereNotNull('next_due_date')
        //     ->whereDate('next_due_date', '<', today())
        //     ->count();

        // $dueSoonSchedules = MaintenanceSchedule::where(
        //     'is_active',
        //     true
        // )
        //     ->whereNotNull('next_due_date')
        //     ->whereBetween('next_due_date', [
        //         today(),
        //         today()->addDays(30),
        //     ])
        //     ->count();

        $activeSchedules = MaintenanceSchedule::query()
            ->with('vehicle')
            ->where('is_active', true)
            ->get();

        $overdueSchedules = $activeSchedules
            ->filter(function ($schedule) {
                return $schedule->dueStatus(
                    $schedule->vehicle?->current_kilometers
                ) === 'overdue';
            })
            ->count();

        $dueSoonSchedules = $activeSchedules
            ->filter(function ($schedule) {
                return $schedule->dueStatus(
                    $schedule->vehicle?->current_kilometers
                ) === 'due_soon';
            })
            ->count();

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
            fn($record) => (float) $record->cost
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

        $overdueMaintenanceSchedules =
            MaintenanceSchedule::query()
                ->with('vehicle')
                ->where('is_active', true)
                ->whereNotNull('next_due_date')
                ->whereDate('next_due_date', '<', today())
                ->orderBy('next_due_date')
                ->limit(10)
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
            'totalSchedules' => $totalSchedules,
            'overdueSchedules' => $overdueSchedules,
            'dueSoonSchedules' => $dueSoonSchedules,
            'overdueMaintenanceSchedules' => $overdueMaintenanceSchedules,
            'activeSchedules' => $activeSchedules,
        ]);
    }
}