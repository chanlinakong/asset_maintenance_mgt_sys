<?php

namespace App\Services;

use App\Enums\MaintenanceStatus;
use App\Models\MaintenanceRecord;
use App\Models\MaintenanceSchedule;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ReportService
{
    public function generate(array $filters): array
    {
        [$from, $to] = $this->resolveDates($filters);

        $report = match ($filters['report_type']) {
            'vehicle_status' => $this->vehicleStatusReport($filters),
            'maintenance_history' => $this->maintenanceHistoryReport($filters, $from, $to),
            'monthly_maintenance' => $this->monthlyMaintenanceReport($filters, $from, $to),
            'maintenance_schedule' => $this->maintenanceScheduleReport($filters, $from, $to),
            'maintenance_cost' => $this->maintenanceCostReport($filters, $from, $to),
        };

        return [
            ...$report,
            'period_label' => $this->periodLabel($filters, $from, $to),
            'vehicle_status_label' => $this->vehicleStatusLabel(
                $filters['vehicle_status'] ?? ''
            ),
            'generated_at' => now()->format('d M Y H:i A'),
            'generated_by' => auth()->user()?->name ?? 'System',
        ];
    }

    /**
     * Resolve the selected reporting period.
     *
     * @return array{0: Carbon|null, 1: Carbon|null}
     */
    private function resolveDates(array $filters): array
    {
        $period = $filters['period'] ?? 'all';
        $now = now();

        return match ($period) {
            'today' => [
                $now->copy()->startOfDay(),
                $now->copy()->endOfDay(),
            ],

            'yesterday' => [
                $now->copy()->subDay()->startOfDay(),
                $now->copy()->subDay()->endOfDay(),
            ],

            'this_month' => [
                $now->copy()->startOfMonth(),
                $now->copy()->endOfMonth(),
            ],

            'last_month' => [
                $now->copy()->subMonthNoOverflow()->startOfMonth(),
                $now->copy()->subMonthNoOverflow()->endOfMonth(),
            ],

            'last_3_months' => [
                $now->copy()->subMonths(2)->startOfMonth(),
                $now->copy()->endOfMonth(),
            ],

            'this_year' => [
                $now->copy()->startOfYear(),
                $now->copy()->endOfYear(),
            ],

            'last_year' => [
                $now->copy()->subYear()->startOfYear(),
                $now->copy()->subYear()->endOfYear(),
            ],

            'custom' => [
                Carbon::parse($filters['from'])->startOfDay(),
                Carbon::parse($filters['to'])->endOfDay(),
            ],

            default => [null, null],
        };
    }

    private function applyDateRange(
        Builder $query,
        string $column,
        ?Carbon $from,
        ?Carbon $to
    ): Builder {
        return $query
            ->when(
                $from !== null,
                fn(Builder $q) => $q->where($column, '>=', $from)
            )
            ->when(
                $to !== null,
                fn(Builder $q) => $q->where($column, '<=', $to)
            );
    }

    private function applyVehicleStatus(
        Builder $query,
        string $relation,
        string $status
    ): Builder {
        if ($status === '') {
            return $query;
        }

        return $query->whereHas(
            $relation,
            fn(Builder $vehicleQuery) => $vehicleQuery->where(
                'status',
                $status
            )
        );
    }

    private function maintenanceQuery(
        array $filters,
        ?Carbon $from,
        ?Carbon $to
    ): Builder {
        $query = MaintenanceRecord::query()
            ->with('vehicle')
            ->when(
                $filters['maintenance_status'] ?? '',
                fn(Builder $q, string $status) => $q->where(
                    'status',
                    $status
                )
            )
            ->when(
                $filters['maintenance_type'] ?? '',
                fn(Builder $q, string $type) => $q->where(
                    'type',
                    $type
                )
            );

        $this->applyDateRange($query, 'reported_at', $from, $to);

        return $this->applyVehicleStatus(
            $query,
            'vehicle',
            $filters['vehicle_status'] ?? ''
        );
    }


    private function vehicleStatusReport(array $filters): array
    {
        $vehicles = Vehicle::query()
            ->withCount(['maintenanceRecords', 'maintenanceSchedules'])
            ->when(
                $filters['vehicle_status'] ?? '',
                fn(Builder $q, string $status) => $q->where(
                    'status',
                    $status
                )
            )
            ->orderBy('vehicle_code')
            ->get();

        $rows = $vehicles->map(
            fn(Vehicle $vehicle) => [
                $vehicle->vehicle_code,
                $vehicle->name,
                $vehicle->type,
                $vehicle->brand,
                $vehicle->model,
                $vehicle->registration_number,
                $this->formatLabel($vehicle->status),
                $vehicle->current_kilometers,
                $vehicle->purchase_date?->format('Y-m-d'),
                $vehicle->maintenance_records_count,
                $vehicle->maintenance_schedules_count,
            ]
        )->all();

        // Append total vehicle count.
        $rows[] = [
            'TOTAL VEHICLES: ' . $vehicles->count(),
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
        ];

        return [
            'title' => 'Vehicle Status Report',
            'headers' => [
                'Vehicle Code',
                'Vehicle Name',
                'Vehicle Type',
                'Brand',
                'Model',
                'Registration Number',
                'Vehicle Status',
                'Current Kilometers',
                'Purchase Date',
                'Maintenance Records',
                'Maintenance Schedules',
            ],
            'rows' => $rows,
        ];
    }

    private function formatLabel(mixed $value): string
    {
        $value = $this->enumValue($value);

        return str($value)
            ->replace('_', ' ')
            ->replace('-', ' ')
            ->squish()
            ->title()
            ->toString();
    }

    private function maintenanceHistoryReport(
        array $filters,
        ?Carbon $from,
        ?Carbon $to
    ): array {
        $records = $this->maintenanceQuery($filters, $from, $to)
            ->latest('reported_at')
            ->get();

        $rows = $records->map(
            fn(MaintenanceRecord $record) => [
                $record->reported_at?->format('Y-m-d'),
                $record->vehicle?->vehicle_code,
                $record->vehicle?->name,
                $record->title,
                $this->formatLabel($record->type),
                $this->formatLabel($record->status),
                (float) $record->parts_cost,
                (float) $record->labor_cost,
                (float) $record->other_cost,
                (float) $record->cost,
            ]
        )->all();

        // Calculate totals.
        $totalPartsCost = $records->sum('parts_cost');
        $totalLaborCost = $records->sum('labor_cost');
        $totalOtherCost = $records->sum('other_cost');
        $totalReportedCost = $records->sum('cost');

        // Add the total row below the records.
        $rows[] = [
            '',                 // Reported Date
            '',                 // Vehicle Code
            '',                 // Vehicle Name
            '',                 // Maintenance Title
            '',                 // Maintenance Type
            'TOTAL',            // Maintenance Status
            (float) $totalPartsCost,
            (float) $totalLaborCost,
            (float) $totalOtherCost,
            (float) $totalReportedCost,
        ];

        return [
            'title' => 'Maintenance History Report',
            'headers' => [
                'Reported Date',
                'Vehicle Code',
                'Vehicle Name',
                'Maintenance Title',
                'Maintenance Type',
                'Maintenance Status',
                'Parts Cost',
                'Labor Cost',
                'Other Cost',
                'Reported Cost',
            ],
            'rows' => $rows,
        ];
    }
    private function monthlyMaintenanceReport(
        array $filters,
        ?Carbon $from,
        ?Carbon $to
    ): array {
        $records = $this->maintenanceQuery($filters, $from, $to)
            ->whereNotNull('reported_at')
            ->orderBy('reported_at')
            ->get();

        $groups = $records->groupBy(
            fn(MaintenanceRecord $record) => $record->reported_at->format('Y-m')
        );

        $rows = $groups->map(
            function (Collection $monthRecords, string $month): array {
                return [
                    Carbon::createFromFormat('!Y-m', $month)->format('F Y'),
                    $monthRecords->count(),
                    $monthRecords->filter(
                        fn(MaintenanceRecord $record) =>
                            $this->enumValue($record->status)
                            === MaintenanceStatus::Completed->value
                    )->count(),
                    $monthRecords->filter(
                        fn(MaintenanceRecord $record) =>
                            $this->enumValue($record->status)
                            === MaintenanceStatus::InProgress->value
                    )->count(),
                    $monthRecords->filter(
                        fn(MaintenanceRecord $record) =>
                            $this->enumValue($record->status)
                            === MaintenanceStatus::Pending->value
                    )->count(),
                    $monthRecords->sum(
                        fn(MaintenanceRecord $record) => (float) $record->cost
                    ),
                ];
            }
        )->values()->all();

        // Calculate grand totals across all months.
        $totalRecords = $records->count();

        $totalCompleted = $records->filter(
            fn(MaintenanceRecord $record) =>
                $this->enumValue($record->status)
                === MaintenanceStatus::Completed->value
        )->count();

        $totalInProgress = $records->filter(
            fn(MaintenanceRecord $record) =>
                $this->enumValue($record->status)
                === MaintenanceStatus::InProgress->value
        )->count();

        $totalPending = $records->filter(
            fn(MaintenanceRecord $record) =>
                $this->enumValue($record->status)
                === MaintenanceStatus::Pending->value
        )->count();

        $totalCost = $records->sum(
            fn(MaintenanceRecord $record) => (float) $record->cost
        );

        // Append the TOTAL row.
        $rows[] = [
            'TOTAL',
            $totalRecords,
            $totalCompleted,
            $totalInProgress,
            $totalPending,
            $totalCost,
        ];

        return [
            'title' => 'Monthly Maintenance Report',
            'headers' => [
                'Month',
                'Maintenance Records',
                'Completed',
                'In Progress',
                'Pending',
                'Total Cost',
            ],
            'rows' => $rows,
        ];
    }

    private function maintenanceScheduleReport(
        array $filters,
        ?Carbon $from,
        ?Carbon $to
    ): array {
        $query = MaintenanceSchedule::query()
            ->with('vehicle')
            ->when(
                $filters['vehicle_status'] ?? '',
                fn(Builder $q, string $status) => $q->whereHas(
                    'vehicle',
                    fn(Builder $v) => $v->where('status', $status)
                )
            );

        $this->applyDateRange($query, 'next_due_date', $from, $to);

        $schedules = $query
            ->orderBy('next_due_date')
            ->get();

        return [
            'title' => 'Maintenance Schedule Report',
            'headers' => [
                'Vehicle Code',
                'Vehicle Name',
                'Schedule Title',
                'Active',
                'Due Status',
                'Last Service Date',
                'Next Due Date',
                'Last Service Kilometers',
                'Next Due Kilometers',
                'Current Kilometers',
                'Interval Days',
                'Interval Kilometers',
            ],
            'rows' => $schedules->map(
                function (MaintenanceSchedule $schedule): array {
                    $vehicle = $schedule->vehicle;
                    $currentKm = $vehicle?->current_kilometers;

                    return [
                        $vehicle?->vehicle_code,
                        $vehicle?->name,
                        $schedule->title,
                        $schedule->is_active ? 'Yes' : 'No',
                        str($schedule->dueStatus($currentKm))
                            ->replace('_', ' ')
                            ->title()
                            ->toString(),
                        $schedule->last_service_date?->format('Y-m-d'),
                        $schedule->next_due_date?->format('Y-m-d'),
                        $schedule->last_service_kilometers,
                        $schedule->next_due_kilometers,
                        $currentKm,
                        $schedule->interval_days,
                        $schedule->interval_kilometers,
                    ];
                }
            )->all(),
        ];
    }
    private function maintenanceCostReport(
        array $filters,
        ?Carbon $from,
        ?Carbon $to
    ): array {
        $records = $this->maintenanceQuery($filters, $from, $to)->get();

        $groups = $records->groupBy(
            fn(MaintenanceRecord $record) => $record->vehicle_id
        );

        $rows = $groups->map(
            function (Collection $vehicleRecords): array {
                $vehicle = $vehicleRecords->first()?->vehicle;

                $totalCost = $vehicleRecords->sum(
                    fn(MaintenanceRecord $record) => (float) $record->cost
                );

                $recordCount = $vehicleRecords->count();

                return [
                    $vehicle?->vehicle_code,
                    $vehicle?->name,
                    $vehicle?->type,
                    $recordCount,
                    $totalCost,
                    $recordCount > 0 ? $totalCost / $recordCount : 0,
                ];
            }
        )->sortByDesc(
                fn(array $row) => $row[4]
            )->values()->all();

        // Calculate grand totals across all vehicles.
        $totalRecords = $records->count();
        $grandTotalCost = $records->sum(
            fn(MaintenanceRecord $record) => (float) $record->cost
        );

        // Append the TOTAL row.
        $rows[] = [
            '',
            '',
            'TOTAL',
            $totalRecords,
            $grandTotalCost,
            $totalRecords > 0 ? $grandTotalCost / $totalRecords : 0,
        ];

        return [
            'title' => 'Maintenance Cost Report',
            'headers' => [
                'Vehicle Code',
                'Vehicle Name',
                'Vehicle Type',
                'Maintenance Records',
                'Total Cost',
                'Average Cost per Record',
            ],
            'rows' => $rows,
        ];
    }

    private function enumValue(mixed $value): string
    {
        if ($value instanceof \BackedEnum) {
            return (string) $value->value;
        }

        if ($value instanceof \UnitEnum) {
            return $value->name;
        }

        if (is_object($value) && property_exists($value, 'value')) {
            return (string) $value->value;
        }

        return (string) ($value ?? '');
    }
    private function periodLabel(
        array $filters,
        ?Carbon $from,
        ?Carbon $to
    ): string {
        $period = $filters['period'] ?? 'all';

        // Custom date range.
        if ($period === 'custom') {
            if ($from === null || $to === null) {
                return 'Custom Period';
            }

            return $from->format('d M Y') . ' - ' . $to->format('d M Y');
        }

        // All dates: show the actual range of the report data.
        if ($period === 'all') {
            return ($from?->format('d M Y') ?? 'Beginning')
                . ' - '
                . ($to?->format('d M Y') ?? now()->format('d M Y'));
        }

        // Use the resolved dates instead of descriptive labels.
        if ($from !== null && $to !== null) {
            if ($from->isSameDay($to)) {
                return $from->format('d M Y');
            }

            return $from->format('d M Y') . ' - ' . $to->format('d M Y');
        }

        return match ($period) {
            'today' => now()->format('d M Y'),
            'yesterday' => now()->subDay()->format('d M Y'),
            'this_month' => now()->startOfMonth()->format('d M Y')
                . ' - ' . now()->format('d M Y'),
            'last_month' => now()->subMonthNoOverflow()->startOfMonth()->format('d M Y')
                . ' - ' . now()->subMonthNoOverflow()->endOfMonth()->format('d M Y'),
            'last_3_months' => now()->subMonths(2)->startOfMonth()->format('d M Y')
                . ' - ' . now()->format('d M Y'),
            'this_year' => now()->startOfYear()->format('d M Y')
                . ' - ' . now()->endOfYear()->format('d M Y'),
            'last_year' => now()->subYear()->startOfYear()->format('d M Y')
                . ' - ' . now()->subYear()->endOfYear()->format('d M Y'),
            default => 'All Dates',
        };
    }


    private function vehicleStatusLabel(string $status): string
    {
        if ($status === '') {
            return 'All Vehicle Statuses';
        }

        return str($status)->replace('_', ' ')->title()->toString();
    }
}