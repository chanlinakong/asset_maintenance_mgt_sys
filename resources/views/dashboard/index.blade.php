@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

    <div class="space-y-6">

        {{-- Page heading --}}
        <div>
            <h1 class="text-2xl font-bold">
                Dashboard
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                Overview of vehicles and maintenance activities.
            </p>
        </div>


        {{-- Statistics --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

            <x-stat-card title="Total Vehicles" :value="$totalVehicles" icon="🚛" />

            <x-stat-card title="Active Vehicles" :value="$activeVehicles" icon="✅" />

            <x-stat-card title="Under Maintenance" :value="$vehiclesUnderMaintenance" icon="🔧" />

            <x-stat-card title="Out of Service" :value="$vehiclesOutOfService" icon="⚠️" />

        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">

            <x-stat-card title="Active Schedules" :value="$totalSchedules" icon="📅" />

            <x-stat-card title="Due Soon" :value="$dueSoonSchedules" icon="⏰" />

            <x-stat-card title="Overdue" :value="$overdueSchedules" icon="⚠️" />

        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">

            <x-stat-card title="Total Maintenance Records" :value="$totalMaintenanceRecords" icon="🔧" />

            <x-stat-card title="This Month's Maintenance" :value="$monthlyMaintenanceCount" icon="📅" />

            <x-stat-card title="This Month's Cost" :value="'$' . number_format($monthlyMaintenanceCost, 2)" icon="💰" />

        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

            {{-- Maintenance by Type --}}
            <div class="rounded-xl bg-white p-6 shadow-sm">

                <h2 class="text-lg font-semibold text-gray-900">
                    Maintenance by Type
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Number of maintenance records by type.
                </p>

                <div class="mt-6 space-y-4">

                    @forelse($maintenanceByType as $item)

                                @php
                                    $label = str($item->type->value)
                                        ->replace('_', ' ')
                                        ->title();
                                @endphp

                                <div>

                                    <div class="mb-1 flex justify-between">

                                        <span class="text-sm font-medium text-gray-700">
                                            {{ $label }}
                                        </span>

                                        <span class="text-sm font-semibold text-gray-900">
                                            {{ $item->total }}
                                        </span>

                                    </div>

                                    <div class="h-2 overflow-hidden rounded-full bg-gray-100">

                                        <div class="h-full rounded-full bg-gray-900" style="
                                                                                                                width:
                                                                                                                {{ $maintenanceByType->max('total') > 0
                        ? ($item->total / $maintenanceByType->max('total')) * 100
                        : 0
                                                                                                                }}%
                                                                                                            "></div>

                                    </div>

                                </div>

                    @empty

                        <p class="text-sm text-gray-500">
                            No maintenance data available.
                        </p>

                    @endforelse

                </div>

            </div>


            {{-- Maintenance by Status --}}
            <div class="rounded-xl bg-white p-6 shadow-sm">

                <h2 class="text-lg font-semibold text-gray-900">
                    Maintenance Status
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Current maintenance record distribution.
                </p>

                <div class="mt-6 space-y-4">

                    @forelse($maintenanceByStatus as $item)

                        @php
                            $label = str($item->status->value)
                                ->replace('_', ' ')
                                ->title();
                        @endphp

                        <div class="flex items-center justify-between">

                            <span class="text-sm text-gray-700">
                                {{ $label }}
                            </span>

                            <span class="rounded-full bg-gray-100 px-3 py-1 text-sm font-semibold text-gray-700">
                                {{ $item->total }}
                            </span>

                        </div>

                    @empty

                        <p class="text-sm text-gray-500">
                            No maintenance data available.
                        </p>

                    @endforelse

                </div>

            </div>

        </div>

        
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

            <div class="overflow-hidden rounded-xl bg-white shadow-sm">

                <div class="border-b border-gray-100 p-5">
                    <h2 class="font-semibold text-gray-900">
                        Overdue Preventive Maintenance
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Maintenance schedules that require attention.
                    </p>
                </div>

                <div class="divide-y divide-gray-100">

                    @forelse($overdueMaintenanceSchedules as $schedule)

                        <a href="{{ route('maintenance-schedules.show', $schedule) }}" class="block p-5 hover:bg-gray-50">

                            <div class="flex items-center justify-between gap-4">

                                <div>
                                    <p class="font-medium text-gray-900">
                                        {{ $schedule->title }}
                                    </p>

                                    <p class="mt-1 text-sm text-gray-500">
                                        {{ $schedule->vehicle->vehicle_code }}
                                        ·
                                        {{ $schedule->vehicle->name }}
                                    </p>
                                </div>

                                <div class="text-right">

                                    <p class="text-sm font-semibold text-red-600">
                                        {{ $schedule->next_due_date?->format('d M Y') ?? '—'  }}
                                    </p>

                                    <p class="text-xs text-gray-500">
                                        Overdue
                                    </p>

                                </div>

                            </div>

                        </a>

                    @empty

                        <div class="p-8 text-center text-sm text-gray-500">
                            No overdue preventive maintenance.
                        </div>

                    @endforelse

                </div>

            </div>

        {{-- Recent maintenance --}}
        <div class="rounded-xl bg-white shadow-sm">

            <div class="border-b px-6 py-4">

                <h2 class="font-semibold">
                    Recent Maintenance
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Latest maintenance activities.
                </p>

            </div>

            <div class="overflow-x-auto">

                <table class="min-w-full text-left text-sm">

                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 font-medium">
                                Vehicle
                            </th>

                            <th class="px-6 py-3 font-medium">
                                Maintenance
                            </th>

                            <th class="px-6 py-3 font-medium">
                                Status
                            </th>

                            <th class="px-6 py-3 font-medium">
                                Cost
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y">

                        @forelse($recentMaintenance as $maintenance)

                            <tr>
                                <td class="px-6 py-4">
                                    {{ $maintenance->vehicle->vehicle_code }}
                                </td>

                                <td class="px-6 py-4">
                                    {{ $maintenance->title }}
                                </td>

                                <td class="px-6 py-4">
                                    {{ str($maintenance->status->value)->replace('_', ' ')->title() }}
                                </td>

                                <td class="px-6 py-4">
                                    ${{ number_format((float) $maintenance->cost, 2) }}
                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                    No maintenance records found.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

@endsection