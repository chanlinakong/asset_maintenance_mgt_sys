<?php

namespace App\Http\Controllers;

use App\Enums\MaintenanceStatus;
use App\Models\MaintenanceRecord;
use App\Models\MaintenanceSchedule;
use App\Models\Vehicle;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('dashboard.index', [
            ...$this->vehicleStatistics(),
            ...$this->maintenanceStatistics(),
            ...$this->scheduleStatistics(),
            ...$this->chartStatistics(),
        ]);
    }

    /* Vehicle Statistics */

    private function vehicleStatistics(): array
    {
        return [
            'totalVehicles' => Vehicle::count(),

            'activeVehicles' => Vehicle::where(
                'status',
                'active'
            )->count(),

            'vehiclesUnderMaintenance' => Vehicle::where(
                'status',
                'maintenance'
            )->count(),

            'vehiclesOutOfService' => Vehicle::where(
                'status',
                'out_of_service'
            )->count(),
        ];
    }

    /* Maintenance Statistics */

    private function maintenanceStatistics(): array
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        $monthlyRecords = MaintenanceRecord::query()
            ->whereBetween('reported_at', [
                $startOfMonth,
                $endOfMonth,
            ])
            ->get();

        $pendingStatus = MaintenanceStatus::Pending->value;

        return [
            /* KPI */

            'totalMaintenanceRecords' =>
                MaintenanceRecord::count(),

            'monthlyMaintenanceCount' =>
                $monthlyRecords->count(),

            'monthlyMaintenanceCost' =>
                $monthlyRecords->sum(
                    fn(MaintenanceRecord $record) =>
                        (float) $record->cost
                ),

            /* Pending maintenance */
            'pendingMaintenanceCount' =>
                MaintenanceRecord::query()
                    ->where('status', $pendingStatus)
                    ->count(),

            'pendingMaintenance' =>
                MaintenanceRecord::query()
                    ->with('vehicle')
                    ->where('status', $pendingStatus)
                    ->oldest('reported_at')
                    ->take(3)
                    ->get(),

            /* Recent maintenance */

            'recentMaintenance' =>
                MaintenanceRecord::query()
                    ->with('vehicle')
                    ->latest('reported_at')
                    ->take(10)
                    ->get(),
        ];
    }

    /* Maintenance Schedule Statistics */
    private function scheduleStatistics(): array
    {
        $activeSchedules = MaintenanceSchedule::query()
            ->with([
                'vehicle',
                'maintenanceRecords' => function ($query) {
                    $query
                        ->whereIn('status', [
                            MaintenanceStatus::Pending->value,
                            MaintenanceStatus::InProgress->value,
                        ])
                        ->latest('reported_at');
                },
            ])
            ->where('is_active', true)
            ->get();

        $overdue = $activeSchedules
            ->filter(function (MaintenanceSchedule $schedule) {
                return $schedule->dueStatus(
                    $schedule->vehicle?->current_kilometers
                ) === 'overdue';
            })
            ->values();

        $dueSoon = $activeSchedules
            ->filter(function (MaintenanceSchedule $schedule) {
                return $schedule->dueStatus(
                    $schedule->vehicle?->current_kilometers
                ) === 'due_soon';
            })
            ->values();

        /*
         * Schedules that are due soon but do not already
         * have Pending/InProgress maintenance.
         */
        $upcoming = $dueSoon
            ->filter(function (MaintenanceSchedule $schedule) {
                return $schedule->activeMaintenance() === null;
            })
            ->sortBy(function (MaintenanceSchedule $schedule) {
                return $schedule->next_due_date?->timestamp
                    ?? PHP_INT_MAX;
            })
            ->take(7)
            ->values();

        return [
            'totalSchedules' => $activeSchedules->count(),

            'overdueSchedules' => $overdue->count(),

            'dueSoonSchedules' => $dueSoon->count(),

            'overdueMaintenanceSchedules' => $overdue
                ->sortBy(function (MaintenanceSchedule $schedule) {
                    return $schedule->next_due_date?->timestamp
                        ?? PHP_INT_MAX;
                })
                ->take(3)
                ->values(),

            'dueSoonMaintenanceSchedules' => $dueSoon
                ->sortBy(function (MaintenanceSchedule $schedule) {
                    return $schedule->next_due_date?->timestamp
                        ?? PHP_INT_MAX;
                })
                ->take(3)
                ->values(),

            'upcomingMaintenanceSchedules' => $upcoming,
        ];
    }

    /* Chart Statistics */

    private function chartStatistics(): array
    {
        return [
            'monthlyMaintenanceCostTrend' =>
                $this->monthlyMaintenanceCost(),

            'monthlyMaintenanceActivity' =>
                $this->monthlyMaintenanceActivity(),

            'vehicleStatusDistribution' =>
                $this->vehicleStatusDistribution(),
        ];
    }

    /* Monthly Maintenance Cost */

    private function monthlyMaintenanceCost(): array
    {
        $startDate = now()->copy()->startOfMonth()->subMonths(11);
        $endDate = now()->copy()->endOfMonth();

        $results = MaintenanceRecord::query()
            ->select(
                DB::raw('YEAR(reported_at) as year'),
                DB::raw('MONTH(reported_at) as month'),
                DB::raw('SUM(cost) as total')
            )
            ->whereBetween('reported_at', [$startDate, $endDate])
            ->groupBy(
                DB::raw('YEAR(reported_at)'),
                DB::raw('MONTH(reported_at)')
            )
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        /*
         * Create all 12 months first.
         * This guarantees that months with no records
         * still appear in the chart with value 0.
         */
        $months = [];

        for ($i = 0; $i < 12; $i++) {
            $date = $startDate->copy()->addMonths($i);

            $months[$date->format('M Y')] = 0;
        }

        /*
         * Replace zero with the actual database total
         * when maintenance records exist for that month.
         */
        foreach ($results as $row) {
            $date = Carbon::create(
                (int) $row->year,
                (int) $row->month,
                1
            );

            $months[$date->format('M Y')] = round(
                (float) $row->total,
                2
            );
        }

        return $months;
    }

    /*
    | Monthly Maintenance Activity
    */

    private function monthlyMaintenanceActivity(): array
    {
        $startDate = now()->copy()->startOfMonth()->subMonths(11);
        $endDate = now()->copy()->endOfMonth();

        $results = MaintenanceRecord::query()
            ->select(
                DB::raw('YEAR(reported_at) as year'),
                DB::raw('MONTH(reported_at) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->whereBetween('reported_at', [$startDate, $endDate])
            ->groupBy(
                DB::raw('YEAR(reported_at)'),
                DB::raw('MONTH(reported_at)')
            )
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        /*
         * Create all 12 months with zero activity.
         */
        $months = [];

        for ($i = 0; $i < 12; $i++) {
            $date = $startDate->copy()->addMonths($i);

            $months[$date->format('M Y')] = 0;
        }

        /*
         * Replace zero with the actual maintenance count.
         */
        foreach ($results as $row) {
            $date = Carbon::create(
                (int) $row->year,
                (int) $row->month,
                1
            );

            $months[$date->format('M Y')] = (int) $row->total;
        }

        return $months;
    }

    /* Vehicle Status Distribution */

    private function vehicleStatusDistribution(): array
    {
        return Vehicle::query()
            ->select(
                'status',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('status')
            ->orderBy('status')
            ->get()
            ->mapWithKeys(function ($row) {
                return [
                    $row->status->value => (int) $row->total,
                ];
            })
            ->toArray();
    }
}