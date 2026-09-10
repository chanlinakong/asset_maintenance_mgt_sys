<?php

namespace App\Http\Controllers;

use App\Enums\MaintenanceStatus;
use App\Enums\MaintenanceType;
use App\Models\MaintenanceRecord;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $from = $request->date('from');
        $to = $request->date('to');
        $status = $request->string('status')->trim()->toString();
        $type = $request->string('type')->trim()->toString();

        $query = MaintenanceRecord::query()
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
            });

        $records = $query
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

        return view('reports.index', [
            'records' => $records,
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