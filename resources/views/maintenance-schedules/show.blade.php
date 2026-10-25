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

            <!-- Overdue Maintenance -->

            <div class="rounded-xl border border-red-200 bg-red-50 p-5">
                <div class="flex items-start gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-red-100 text-red-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v4m0 4h.01M10.3 3.86L1.82 18.5A2 2 0 003.55 21h16.9a2 2 0 001.73-2.5L13.7 3.86a2 2 0 00-3.4 0z" />
                        </svg>
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

            <!-- Maintenance Due Soon -->

            <div class="rounded-xl border border-yellow-200 bg-yellow-50 p-5">
                <div class="flex items-start gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-yellow-100 text-yellow-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.8">
                            <circle cx="12" cy="12" r="9" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 2" />
                        </svg>
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
                $maintenanceSchedule->vehicle->current_kilometers !== null &&
                $maintenanceSchedule->next_due_kilometers !== null
            )
            @php
                $currentKm = $maintenanceSchedule->vehicle->current_kilometers;

                $remainingKm = $maintenanceSchedule->kilometersRemaining($currentKm);
            @endphp

            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">

                {{-- Header --}}
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.64 18.36a9 9 0 1112.72 0" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 13l4-4" />
                            <circle cx="12" cy="13" r="1" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 21h16" />
                        </svg>
                    </div>

                    <div>
                        <h3 class="font-semibold text-gray-900">
                            Mileage Information
                        </h3>
                        <p class="text-sm text-gray-500">
                            Current vehicle mileage
                        </p>
                    </div>
                </div>

                {{-- Current Mileage --}}
                <div class="mt-5 rounded-lg bg-gray-50 p-4">
                    <p class="text-sm text-gray-500">
                        Current Mileage
                    </p>

                    <p class="mt-1 text-2xl font-bold tracking-tight text-gray-900">
                        {{ number_format($currentKm) }}
                        <span class="text-sm font-medium text-gray-500">km</span>
                    </p>
                </div>

                {{-- Mileage Status --}}
                <div class="mt-4">
                    <p class="text-sm text-gray-500">
                        Mileage Status
                    </p>

                    @if($remainingKm < 0)
                        <p class="mt-1 font-semibold text-red-700">
                            {{ number_format(abs($remainingKm)) }} km overdue
                        </p>
                    @elseif($remainingKm == 0)
                        <p class="mt-1 font-semibold text-amber-700">
                            Service due now
                        </p>
                    @else
                        <p class="mt-1 font-semibold text-green-700">
                            {{ number_format($remainingKm) }} km remaining
                        </p>
                    @endif
                </div>

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


    @if (auth()->user()->role->value === 'admin')
        <div class="mt-6 rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <h2 class="font-semibold text-gray-900">Schedule Management</h2>
            <p class="mt-1 text-sm text-gray-500">
                Activate, deactivate, or delete this schedule.
            </p>

            <div class="mt-4 grid gap-4 sm:grid-cols-2">

                {{-- Activate / Deactivate --}}
                <div class="rounded-lg border border-gray-200 p-4">
                    <h3 class="text-sm font-semibold text-gray-900">
                        {{ $maintenanceSchedule->is_active ? 'Deactivate Schedule' : 'Reactivate Schedule' }}
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        {{ $maintenanceSchedule->is_active
                ? 'Stop future use while preserving history.'
                : 'Allow future maintenance planning.' }}
                    </p>

                    <form method="POST" action="{{ route('maintenance-schedules.toggle-active', $maintenanceSchedule) }}"
                        class="mt-3" onsubmit="return confirm('Are you sure?')">
                        @csrf
                        @method('PATCH')

                        <button type="submit" class="w-full rounded-lg px-4 py-2 text-sm font-medium text-white
                                                                        {{ $maintenanceSchedule->is_active
                ? 'bg-amber-600 hover:bg-amber-700'
                : 'bg-emerald-600 hover:bg-emerald-700' }}">
                            {{ $maintenanceSchedule->is_active ? 'Deactivate' : 'Reactivate' }}
                        </button>
                    </form>
                </div>

                {{-- Soft Delete --}}
                <div class="rounded-lg border border-gray-200 p-4">
                    <h3 class="text-sm font-semibold text-gray-900">Delete Schedule</h3>

                    <p class="mt-1 text-sm text-gray-500">
                        Remove this schedule from normal listings.
                    </p>

                    <form method="POST" action="{{ route('maintenance-schedules.destroy', $maintenanceSchedule) }}" class="mt-3"
                        onsubmit="return confirm('Delete this schedule?')">
                        @csrf
                        @method('DELETE')

                        <button type="submit"
                            class="w-full rounded-lg px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700">
                            Delete
                        </button>
                    </form>
                </div>

            </div>
        </div>
    @endif


@endsection