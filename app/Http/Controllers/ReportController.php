<?php

namespace App\Http\Controllers;

use App\Enums\MaintenanceStatus;
use App\Enums\MaintenanceType;
use App\Models\MaintenanceRecord;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Pagination\LengthAwarePaginator;

class ReportController extends Controller
{

    public function index(Request $request): View
    {
        $from = $request->date('from');
        $to = $request->date('to');
        $status = $request->string('status')->trim()->toString();
        $type = $request->string('type')->trim()->toString();
        $period = $request->input('period', 'all');

        $query = MaintenanceRecord::query()
            ->with('vehicle')
            ->when(
                $from,
                fn($query) =>
                    $query->whereDate('reported_at', '>=', $from)
            )
            ->when(
                $to,
                fn($query) =>
                    $query->whereDate('reported_at', '<=', $to)
            )
            ->when(
                $status,
                fn($query) =>
                    $query->where('status', $status)
            )
            ->when(
                $type,
                fn($query) =>
                    $query->where('type', $type)
            )
            ->when($period === 'today', function ($query) {
                $query->whereDate('reported_at', today());
            })
            ->when($period === 'yesterday', function ($query) {
                $query->whereDate('reported_at', today()->subDay());
            })
            ->when($period === 'this_month', function ($query) {
                $query->whereBetween('reported_at', [
                    now()->startOfMonth(),
                    now()->endOfMonth(),
                ]);
            })
            ->when($period === 'last_month', function ($query) {
                $date = now()->subMonthNoOverflow();

                $query->whereBetween('reported_at', [
                    $date->copy()->startOfMonth(),
                    $date->copy()->endOfMonth(),
                ]);
            })
            ->when($period === 'this_year', function ($query) {
                $query->whereBetween('reported_at', [
                    now()->startOfYear(),
                    now()->endOfYear(),
                ]);
            })
            ->when($period === 'last_year', function ($query) {
                $date = now()->subYear();

                $query->whereBetween('reported_at', [
                    $date->copy()->startOfYear(),
                    $date->copy()->endOfYear(),
                ]);
            });

        // Keep all filtered records for summary calculations
        $records = (clone $query)
            ->latest('reported_at')
            ->get();

        $totalRecords = $records->count();
        $totalCost = $records->sum(
            fn($record) => (float) $record->cost
        );

        $completedCount = $records->where(
            'status',
            MaintenanceStatus::Completed
        )->count();

        $inProgressCount = $records->where(
            'status',
            MaintenanceStatus::InProgress
        )->count();

        $pendingCount = $records->where(
            'status',
            MaintenanceStatus::Pending
        )->count();

        $vehiclesUnderMaintenance = Vehicle::where(
            'status',
            'maintenance'
        )->count();

        // Cost by Vehicle (grouped summary)
        $costByVehicle = $records
            ->groupBy('vehicle_id')
            ->map(function ($records) {
                return [
                    'vehicle' => $records->first()->vehicle,
                    'count' => $records->count(),
                    'cost' => $records->sum(
                        fn($record) => (float) $record->cost
                    ),
                ];
            })
            ->sortByDesc('cost')
            ->values();

        // Paginate Cost by Vehicle
        $perPage = 10;
        $vehiclePage = LengthAwarePaginator::resolveCurrentPage('vehicle_page');

        $costByVehicle = new LengthAwarePaginator(
            $costByVehicle->forPage($vehiclePage, $perPage)->values(),
            $costByVehicle->count(),
            $perPage,
            $vehiclePage,
            [
                'path' => $request->url(),
                'pageName' => 'vehicle_page',
                'query' => $request->query(),
            ]
        );

        // Cost by Type
        $costByType = $records
            ->groupBy('type')
            ->map(function ($records) {
                return [
                    'type' => $records->first()->type,
                    'count' => $records->count(),
                    'cost' => $records->sum(
                        fn($record) => (float) $record->cost
                    ),
                ];
            })
            ->sortByDesc('cost');

        // Paginate Maintenance Detail independently
        $maintenancePage = LengthAwarePaginator::resolveCurrentPage('maintenance_page');

        $maintenanceRecords = (clone $query)
            ->latest('reported_at')
            ->paginate($perPage, ['*'], 'maintenance_page')
            ->withQueryString();

        return view('reports.index', [
            'records' => $maintenanceRecords,
            'totalRecords' => $totalRecords,
            'totalCost' => $totalCost,
            'completedCount' => $completedCount,
            'inProgressCount' => $inProgressCount,
            'pendingCount' => $pendingCount,
            'vehiclesUnderMaintenance' => $vehiclesUnderMaintenance,
            'costByVehicle' => $costByVehicle,
            'costByType' => $costByType,
            'statuses' => MaintenanceStatus::cases(),
            'types' => MaintenanceType::cases(),
            'from' => $from?->format('Y-m-d'),
            'to' => $to?->format('Y-m-d'),
            'status' => $status,
            'type' => $type,
        ]);
    }


    public function pdf(Request $request)
    {
        $from = $request->date('from');
        $to = $request->date('to');
        $status = $request->string('status')->trim()->toString();
        $type = $request->string('type')->trim()->toString();

        $records = MaintenanceRecord::query()
            ->with('vehicle')
            ->when($from, function ($query) use ($from) {
                $query->whereDate('reported_at', '>=', $from);
            })
            ->when($to, function ($query) use ($to) {
                $query->whereDate('reported_at', '<=', $to);
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($type, function ($query) use ($type) {
                $query->where('type', $type);
            })
            ->latest('reported_at')
            ->get();

        $totalRecords = $records->count();

        $totalCost = $records->sum(
            fn($record) => (float) $record->cost
        );

        $completedCount = $records->where(
            'status',
            MaintenanceStatus::Completed
        )->count();

        $inProgressCount = $records->where(
            'status',
            MaintenanceStatus::InProgress
        )->count();

        $pendingCount = $records->where(
            'status',
            MaintenanceStatus::Pending
        )->count();

        $costByVehicle = $records
            ->groupBy('vehicle_id')
            ->map(function ($records) {
                return [
                    'vehicle' => $records->first()->vehicle,
                    'count' => $records->count(),
                    'cost' => $records->sum(
                        fn($record) => (float) $record->cost
                    ),
                ];
            })
            ->sortByDesc('cost');

        $costByType = $records
            ->groupBy('type')
            ->map(function ($records) {
                return [
                    'type' => $records->first()->type,
                    'count' => $records->count(),
                    'cost' => $records->sum(
                        fn($record) => (float) $record->cost
                    ),
                ];
            })
            ->sortByDesc('cost');

        $pdf = Pdf::loadView('reports.pdf', [
            'records' => $records,
            'totalRecords' => $totalRecords,
            'totalCost' => $totalCost,
            'completedCount' => $completedCount,
            'inProgressCount' => $inProgressCount,
            'pendingCount' => $pendingCount,
            'costByVehicle' => $costByVehicle,
            'costByType' => $costByType,
            'from' => $from?->format('Y-m-d'),
            'to' => $to?->format('Y-m-d'),
        ]);

        return $pdf->download(
            'vehicle-maintenance-report.pdf'
        );
    }
}