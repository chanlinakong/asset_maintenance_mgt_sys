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
        $vehicles = $this->vehicleStatistics();
        $records = $this->recordStatistics();
        $schedules = $this->scheduleStatistics();

        return view('dashboard.index', [
            ...$vehicles,
            ...$records,
            ...$schedules,
        ]);
    }

    private function vehicleStatistics(): array
    {
        return [
            'totalVehicles' => Vehicle::count(),
            'activeVehicles' => Vehicle::where('status', 'active')->count(),
            'vehiclesUnderMaintenance' => Vehicle::where('status', 'maintenance')->count(),
            'vehiclesOutOfService' => Vehicle::where('status', 'out_of_service')->count(),
        ];
    }

    private function recordStatistics(): array
    {
        $monthlyRecords = MaintenanceRecord::whereMonth('reported_at', now()->month)
            ->whereYear('reported_at', now()->year)
            ->get();

        return [
            'totalMaintenanceRecords' => MaintenanceRecord::count(),
            'recentMaintenance' => MaintenanceRecord::with('vehicle')
                ->latest('reported_at')->take(5)->get(),
            'monthlyMaintenanceCount' => $monthlyRecords->count(),
            'monthlyMaintenanceCost' => $monthlyRecords->sum(fn ($record) => (float) $record->cost),
            'maintenanceByType' => $this->recordsGroupedBy('type'),
            'maintenanceByStatus' => $this->recordsGroupedBy('status'),
        ];
    }

    private function recordsGroupedBy(string $column)
    {
        return MaintenanceRecord::query()
            ->select($column, DB::raw('COUNT(*) as total'))
            ->groupBy($column)
            ->orderByDesc('total')
            ->get();
    }

    private function scheduleStatistics(): array
    {
        $activeSchedules = MaintenanceSchedule::with('vehicle')
            ->where('is_active', true)->get();
        $overdue = $activeSchedules->filter(fn (MaintenanceSchedule $schedule) =>
            $schedule->dueStatus($schedule->vehicle?->current_kilometers) === 'overdue'
        );

        return [
            'totalSchedules' => $activeSchedules->count(),
            'overdueSchedules' => $overdue->count(),
            'dueSoonSchedules' => $activeSchedules->filter(fn (MaintenanceSchedule $schedule) =>
                $schedule->dueStatus($schedule->vehicle?->current_kilometers) === 'due_soon'
            )->count(),
            'overdueMaintenanceSchedules' => $overdue->sortBy('next_due_date')->take(10)->values(),
            'activeSchedules' => $activeSchedules,
        ];
    }
}