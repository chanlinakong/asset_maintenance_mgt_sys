@extends('layouts.app')

@section('title', $maintenanceSchedule->title)

@section('content')

@php
    $activeMaintenance =
        $maintenanceSchedule->activeMaintenance();
@endphp

    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">

            <div>

                <div class="flex items-center gap-2">

                    <a href="{{ route('maintenance-schedules.index') }}" class="text-sm text-gray-500 hover:text-gray-900">
                        ← Schedules
                    </a>

                </div>

                <h1 class="mt-3 text-2xl font-bold text-gray-900">
                    {{ $maintenanceSchedule->title }}
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    {{ $maintenanceSchedule->vehicle->vehicle_code }}
                    ·
                    {{ $maintenanceSchedule->vehicle->name }}
                </p>

            </div>

            <div class="flex gap-2">

                @can('update', $maintenanceSchedule)

                            <a href="{{ route(
                        'maintenance-schedules.edit',
                        $maintenanceSchedule
                    ) }}"
                                class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Edit
                            </a>

                @endcan

                @if(
                                $maintenanceSchedule->is_active
                                && !$maintenanceSchedule->activeMaintenance()
                            )

                            <a href="{{ route('maintenance.create', [
                        'vehicle_id' => $maintenanceSchedule->vehicle_id,
                        'schedule_id' => $maintenanceSchedule->id,
                        'type' => 'preventive',
                    ]) }}" class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">
                                + Create Maintenance
                            </a>

                @endif

            </div>

        </div>


        {{-- Status --}}
        <div class="rounded-xl bg-white p-6 shadow-sm">

            <div class="flex flex-wrap items-center gap-3">

                <span class="text-sm font-medium text-gray-500">
                    Schedule Status:
                </span>

                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $maintenanceSchedule->dueStatusClasses() }}">
                    {{ $maintenanceSchedule->dueStatusLabel($maintenanceSchedule->vehicle->current_kilometers) }}
                </span>

            </div>

        </div>

        @if($maintenanceSchedule->dueStatus($maintenanceSchedule->vehicle->current_kilometers) === 'overdue')

            <div class="rounded-xl border border-red-200 bg-red-50 p-5">

                <div class="flex gap-3">

                    <div class="text-xl">
                        ⚠️
                    </div>

                    <div>

                        <h2 class="font-semibold text-red-800">
                            Maintenance is overdue
                        </h2>

                        <p class="mt-1 text-sm text-red-700">
                            This preventive maintenance should be scheduled as soon as possible.
                        </p>

                    </div>

                </div>

            </div>

        @elseif($maintenanceSchedule->dueStatus($maintenanceSchedule->vehicle->current_kilometers) === 'due_soon')

            <div class="rounded-xl border border-yellow-200 bg-yellow-50 p-5">

                <div class="flex gap-3">

                    <div class="text-xl">
                        ⏰
                    </div>

                    <div>

                        <h2 class="font-semibold text-yellow-800">
                            Maintenance is due soon
                        </h2>

                        <p class="mt-1 text-sm text-yellow-700">
                            Consider scheduling this maintenance before the due date.
                        </p>

                    </div>

                </div>

            </div>

        @endif

        {{-- Active maintenance warning --}}
        @if($activeMaintenance)

            <div class="rounded-xl border border-blue-200 bg-blue-50 p-5">

                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                    <div>

                        <h2 class="font-semibold text-blue-800">
                            🔧 Maintenance already being handled
                        </h2>

                        <p class="mt-1 text-sm text-blue-700">
                            {{ $activeMaintenance->title }}
                        </p>

                        <p class="mt-1 text-xs text-blue-600">
                            Status:
                            {{ str(
                $activeMaintenance->status->value
            )->replace('_', ' ')->title() }}
                        </p>

                    </div>

                    <a href="{{ route(
                'maintenance.show',
                $activeMaintenance
            ) }}"
                        class="rounded-lg border border-blue-300 bg-white px-4 py-2 text-sm font-medium text-blue-700 hover:bg-blue-100">
                        View Maintenance
                    </a>

                </div>

            </div>

        @endif

        {{-- Schedule information --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

            <div class="rounded-xl bg-white p-5 shadow-sm">

                <p class="text-sm text-gray-500">
                    Last Service
                </p>

                <p class="mt-2 text-lg font-semibold text-gray-900">
                    {{ $maintenanceSchedule->last_service_date?->format('d M Y') ?? '—' }}
                </p>

            </div>


            <div class="rounded-xl bg-white p-5 shadow-sm">

                <p class="text-sm text-gray-500">
                    Next Due Date
                </p>

                <p class="mt-2 text-lg font-semibold text-gray-900">
                    {{ $maintenanceSchedule->next_due_date?->format('d M Y') ?? '—' }}
                </p>

            </div>


            <div class="rounded-xl bg-white p-5 shadow-sm">

                <p class="text-sm text-gray-500">
                    Last Service KM
                </p>

                <p class="mt-2 text-lg font-semibold text-gray-900">
                    {{ $maintenanceSchedule->last_service_kilometers !== null
        ? number_format($maintenanceSchedule->last_service_kilometers) . ' km'
        : '—'
                                    }}
                </p>

            </div>


            <div class="rounded-xl bg-white p-5 shadow-sm">

                <p class="text-sm text-gray-500">
                    Next Due KM
                </p>

                <p class="mt-2 text-lg font-semibold text-gray-900">
                    {{ $maintenanceSchedule->next_due_kilometers !== null
        ? number_format($maintenanceSchedule->next_due_kilometers) . ' km'
        : '—'
                                    }}
                </p>

            </div>

        </div>

        {{-- Mileage Status --}}
        @if(
                $maintenanceSchedule->next_due_kilometers !== null
                && $maintenanceSchedule->vehicle->current_kilometers !== null
            )

            @php
                $remainingKm = $maintenanceSchedule->kilometersRemaining(
                    $maintenanceSchedule->vehicle->current_kilometers
                );
            @endphp

            <div class="rounded-xl bg-white p-5 shadow-sm">

                <p class="text-sm text-gray-500">
                    Mileage Status
                </p>

                @if($remainingKm < 0)

                    <p class="mt-2 font-semibold text-red-700">
                        {{ number_format(abs($remainingKm)) }} km overdue
                    </p>

                @else

                    <p class="mt-2 font-semibold text-gray-900">
                        {{ number_format($remainingKm) }} km remaining
                    </p>

                @endif

            </div>
        @endif

        {{-- Interval --}}
        <div class="rounded-xl bg-white p-6 shadow-sm">

            <h2 class="text-lg font-semibold text-gray-900">
                Maintenance Interval
            </h2>

            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">

                <div class="rounded-lg bg-gray-50 p-4">

                    <p class="text-sm text-gray-500">
                        Time Interval
                    </p>

                    <p class="mt-1 font-medium text-gray-900">

                        @if($maintenanceSchedule->interval_days)
                            Every
                            {{ $maintenanceSchedule->interval_days }}
                            days
                        @else
                            Not configured
                        @endif

                    </p>

                </div>


                <div class="rounded-lg bg-gray-50 p-4">

                    <p class="text-sm text-gray-500">
                        Kilometer Interval
                    </p>

                    <p class="mt-1 font-medium text-gray-900">

                        @if($maintenanceSchedule->interval_kilometers)
                                            Every
                                            {{ number_format(
                                $maintenanceSchedule->interval_kilometers
                            ) }}
                                            km
                        @else
                            Not configured
                        @endif

                    </p>

                </div>

            </div>

        </div>


        {{-- Description --}}
        @if($maintenanceSchedule->description)

            <div class="rounded-xl bg-white p-6 shadow-sm">

                <h2 class="text-lg font-semibold text-gray-900">
                    Description
                </h2>

                <p class="mt-3 whitespace-pre-line text-sm text-gray-600">
                    {{ $maintenanceSchedule->description }}
                </p>

            </div>

        @endif


        {{-- Maintenance history --}}
        <div class="rounded-xl bg-white shadow-sm">

            <div class="border-b border-gray-100 p-6">

                <h2 class="text-lg font-semibold text-gray-900">
                    Maintenance History
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Previous maintenance records related to this vehicle.
                </p>

            </div>


            <div class="divide-y divide-gray-100">

                @forelse(
                                $maintenanceSchedule->maintenanceRecords
                                as $record
                            )

                            <a href="{{ route('maintenance.show', $record) }}" class="block p-5 hover:bg-gray-50">

                                <div class="flex items-start justify-between gap-4">

                                    <div>

                                        <p class="font-medium text-gray-900">
                                            {{ $record->title }}
                                        </p>

                                        <p class="mt-1 text-sm text-gray-500">
                                            {{ $record->reported_at->format('d M Y') }}
                                        </p>

                                    </div>


                                    <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600">

                                        {{ str(
                        $record->status->value
                    )->replace('_', ' ')->title() }}

                                    </span>

                                </div>

                            </a>

                @empty

                    <div class="p-8 text-center text-sm text-gray-500">
                        No maintenance history found.
                    </div>

                @endforelse

            </div>

        </div>

    </div>

@endsection