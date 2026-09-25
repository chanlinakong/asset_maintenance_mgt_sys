@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

    <div class="space-y-6 px-4 py-6 sm:px-6 lg:px-8">

        {{-- PAGE HEADER- --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">
                    Dashboard
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    Asset and maintenance overview.
                </p>
            </div>

            <div class="text-sm text-gray-500">
                {{ now()->format('d M Y') }}
            </div>

        </div>


        {{-- VEHICLE KPI CARDS --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

            {{-- Total Vehicles --}}
            <a href="{{ route('vehicles.index') }}"
                class="group rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-md">
                <div class="flex items-start justify-between">

                    <div>
                        <p class="text-sm font-medium text-gray-500">
                            Total Vehicles
                        </p>

                        <p class="mt-2 text-3xl font-bold text-gray-900">
                            {{ $totalVehicles }}
                        </p>
                    </div>

                    <div class="rounded-lg bg-blue-50 p-3 text-blue-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17h6m-7 4h8M5 17h14l1-6H4l1 6zm2-6 1-4h8l1 4M7 21h10" />
                        </svg>
                    </div>

                </div>

                <p class="mt-4 text-xs font-medium text-blue-600">
                    View vehicles →
                </p>
            </a>


            {{-- Active Vehicles --}}
            <a href="{{ route('vehicles.index') }}"
                class="group rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-green-200 hover:shadow-md">
                <div class="flex items-start justify-between">

                    <div>
                        <p class="text-sm font-medium text-gray-500">
                            Active Vehicles
                        </p>

                        <p class="mt-2 text-3xl font-bold text-gray-900">
                            {{ $activeVehicles }}
                        </p>
                    </div>

                    <div class="rounded-lg bg-green-50 p-3 text-green-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>

                </div>

                <p class="mt-4 text-xs font-medium text-green-600">
                    Operational vehicles
                </p>
            </a>


            {{-- Under Maintenance --}}
            <a href="{{ route('maintenance.index', ['status' => \App\Enums\MaintenanceStatus::InProgress->value]) }}"
                class="group rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-amber-200 hover:shadow-md">
                <div class="flex items-start justify-between">

                    <div>
                        <p class="text-sm font-medium text-gray-500">
                            Under Maintenance
                        </p>

                        <p class="mt-2 text-3xl font-bold text-gray-900">
                            {{ $vehiclesUnderMaintenance }}
                        </p>
                    </div>

                    <div class="rounded-lg bg-amber-50 p-3 text-amber-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4v16m-7-8h14" />
                        </svg>
                    </div>

                </div>

                <p class="mt-4 text-xs font-medium text-amber-600">
                    View maintenance →
                </p>
            </a>


            {{-- Out of Service --}}
            <a href="{{ route('vehicles.index') }}"
                class="group rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-red-200 hover:shadow-md">
                <div class="flex items-start justify-between">

                    <div>
                        <p class="text-sm font-medium text-gray-500">
                            Out of Service
                        </p>

                        <p class="mt-2 text-3xl font-bold text-gray-900">
                            {{ $vehiclesOutOfService }}
                        </p>
                    </div>

                    <div class="rounded-lg bg-red-50 p-3 text-red-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>

                </div>

                <p class="mt-4 text-xs font-medium text-red-600">
                    Requires attention
                </p>
            </a>

        </div>


        {{-- MAINTENANCE KPI CARDS- --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

            {{-- Total Maintenance --}}
            <a href="{{ route('maintenance.index') }}"
                class="group rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-md">
                <p class="text-sm font-medium text-gray-500">
                    Total Maintenance Records
                </p>

                <p class="mt-2 text-3xl font-bold text-gray-900">
                    {{ $totalMaintenanceRecords }}
                </p>

                <p class="mt-4 text-xs font-medium text-blue-600">
                    View records →
                </p>
            </a>


            {{-- Monthly Maintenance --}}
            <a href="{{ route('maintenance.index') }}"
                class="group rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-amber-200 hover:shadow-md">
                <p class="text-sm font-medium text-gray-500">
                    This Month's Maintenance
                </p>

                <p class="mt-2 text-3xl font-bold text-gray-900">
                    {{ $monthlyMaintenanceCount }}
                </p>

                <p class="mt-4 text-xs font-medium text-amber-600">
                    Current month activity
                </p>
            </a>


            {{-- Monthly Cost --}}
            <a href="{{ route('reports.index') }}"
                class="group rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-green-200 hover:shadow-md">
                <p class="text-sm font-medium text-gray-500">
                    This Month's Cost
                </p>

                <p class="mt-2 text-3xl font-bold text-gray-900">
                    ${{ number_format((float) $monthlyMaintenanceCost, 2) }}
                </p>

                <p class="mt-4 text-xs font-medium text-green-600">
                    View reports →
                </p>
            </a>


            {{-- Overdue --}}
            <a href="{{ route('maintenance-schedules.index') }}"
                class="group rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-red-200 hover:shadow-md">
                <p class="text-sm font-medium text-gray-500">
                    Overdue Maintenance
                </p>

                <p class="mt-2 text-3xl font-bold text-red-600">
                    {{ $overdueSchedules }}
                </p>

                <p class="mt-4 text-xs font-medium text-red-600">
                    Review overdue work →
                </p>
            </a>

        </div>


        {{-- CHARTS- --}}
        <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">

            {{-- Monthly Cost --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">

                <div class="flex items-start justify-between">

                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">
                            Monthly Maintenance Cost
                        </h2>

                        <p class="mt-1 text-sm text-gray-500">
                            Maintenance spending over the last 12 months.
                        </p>
                    </div>

                    <span class="rounded-lg bg-green-50 px-2.5 py-1 text-xs font-medium text-green-700">
                        Cost
                    </span>

                </div>

                <div class="relative h-72">
                    <canvas id="monthlyMaintenanceCostChart"></canvas>
                </div>

            </div>


            {{-- Vehicle Status --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">

                <div class="flex items-start justify-between">

                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">
                            Vehicle Status
                        </h2>

                        <p class="mt-1 text-sm text-gray-500">
                            Current distribution of vehicles.
                        </p>
                    </div>

                    <span class="rounded-lg bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700">
                        Fleet
                    </span>

                </div>

                <div class="relative h-72">
                    <canvas id="vehicleStatusChart"></canvas>
                </div>

            </div>

        </div>


        {{-- MONTHLY ACTIVITY- --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">

            <div class="flex items-start justify-between">

                <div>
                    <h2 class="text-lg font-semibold text-gray-900">
                        Monthly Maintenance Activity
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Number of maintenance records reported each month.
                    </p>
                </div>

                <span class="rounded-lg bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700">
                    Activity
                </span>

            </div>

            <div class="relative h-72">
                <canvas id="monthlyMaintenanceActivityChart"></canvas>
            </div>

        </div>


        {{-- OPERATIONAL SECTION- --}}
        <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">

            {{--             ATTENTION REQUIRED
             --}}
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">

                <div class="border-b border-gray-100 p-5">

                    <div class="flex items-center justify-between">

                        <div>
                            <h2 class="font-semibold text-gray-900">
                                Attention Required
                            </h2>

                            <p class="mt-1 text-sm text-gray-500">
                                Maintenance work that needs action.
                            </p>
                        </div>

                        @if(
                                $overdueSchedules > 0 ||
                                $dueSoonSchedules > 0 ||
                                $pendingMaintenance->count() > 0
                            )
                            <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700">
                                Action needed
                            </span>
                        @endif

                    </div>

                </div>

                {{-- Overdue --}}
                <div class="border-b border-gray-100">

                    <div class="flex items-center justify-between px-5 py-3">

                        <div class="flex items-center gap-2">
                            <span class="h-2.5 w-2.5 rounded-full bg-red-500"></span>

                            <span class="text-sm font-semibold text-gray-900">
                                Overdue
                            </span>
                        </div>

                        @if($overdueSchedules > 3)
                            <a href="{{ route('maintenance-schedules.index') }}"
                                class="mt-4 block text-center text-sm font-medium text-blue-600 hover:text-blue-800">
                                See all {{ $overdueSchedules }} over-due schedules →
                            </a>
                        @else
                            <span class="text-sm font-semibold text-red-600">
                                {{ $overdueSchedules }}
                            </span>
                        @endif
                    </div>

                    <div class="divide-y divide-gray-100">

                        @if($overdueMaintenanceSchedules->isNotEmpty())


                            @forelse($overdueMaintenanceSchedules as $schedule)

                                @php
                                    $currentKm = $schedule->vehicle?->current_kilometers;
                                @endphp

                                <div class="flex items-start justify-between gap-4 px-5 py-4">

                                    {{-- LEFT CONTENT --}}
                                    <div class="min-w-0 flex-1">

                                        <p class="font-medium text-gray-900">
                                            {{ $schedule->title }}
                                        </p>

                                        <p class="mt-1 text-sm text-gray-500">
                                            {{ $schedule->vehicle?->vehicle_code }}
                                            —
                                            {{ $schedule->vehicle?->name }}
                                        </p>

                                        <div class="mt-2 flex flex-wrap gap-2">

                                            <span class="rounded-full bg-red-100 px-2.5 py-1 text-xs font-medium text-red-700">
                                                Overdue
                                            </span>

                                            <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs text-gray-600">
                                                {{ $schedule->dueReasonLabel($currentKm) }}
                                            </span>

                                        </div>

                                    </div>

                                    {{-- REVIEW BUTTON --}}
                                    <a href="{{ route('maintenance-schedules.show', $schedule) }}"
                                        class="shrink-0 text-sm font-medium text-blue-600 hover:text-blue-800">
                                        Review
                                    </a>

                                </div>

                            @empty

                                <div class="px-5 py-4 text-sm text-gray-500">
                                    No overdue maintenance.
                                </div>

                            @endforelse

                        @else

                            <div class="py-8 text-center">
                                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-green-100">
                                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>

                                <p class="mt-3 font-medium text-gray-900">
                                    No overdue maintenance
                                </p>

                                <p class="mt-1 text-sm text-gray-500">
                                    All active maintenance schedules are up to date.
                                </p>
                            </div>

                        @endif

                    </div>

                </div>

                {{-- Due Soon --}}
                <div class="border-b border-gray-100">

                    <div class="flex items-center justify-between px-5 py-3">

                        <div class="flex items-center gap-2">
                            <span class="h-2.5 w-2.5 rounded-full bg-amber-500"></span>

                            <span class="text-sm font-semibold text-gray-900">
                                Due Soon
                            </span>
                        </div>

                        @if($dueSoonSchedules > 3)
                            <a href="{{ route('maintenance-schedules.index') }}"
                                class="mt-4 block text-center text-sm font-medium text-blue-600 hover:text-blue-800">
                                See all {{ $dueSoonSchedules }} due-soon schedules →
                            </a>
                        @else
                            <span class="text-sm font-semibold text-amber-600">
                                {{ $dueSoonSchedules }}
                            </span>
                        @endif
                    </div>


                    <div class="divide-y divide-gray-100">
                        @if($dueSoonMaintenanceSchedules->isNotEmpty())

                            @forelse($dueSoonMaintenanceSchedules as $schedule)

                                @php
                                    $currentKm = $schedule->vehicle?->current_kilometers;
                                @endphp

                                <div class="flex items-start justify-between gap-4 px-5 py-4">

                                    {{-- LEFT CONTENT --}}
                                    <div class="min-w-0 flex-1">

                                        <p class="font-medium text-gray-900">
                                            {{ $schedule->title }}
                                        </p>

                                        <p class="mt-1 text-sm text-gray-500">
                                            {{ $schedule->vehicle?->vehicle_code }}
                                            —
                                            {{ $schedule->vehicle?->name }}
                                        </p>


                                        {{-- STATUS BADGES --}}
                                        <div class="mt-2 flex flex-wrap gap-2">

                                            <span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-medium text-amber-700">
                                                Due Soon
                                            </span>

                                            <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs text-gray-600">
                                                {{ $schedule->dueReasonLabel($currentKm) }}
                                            </span>

                                        </div>


                                        {{-- KILOMETER INFORMATION --}}
                                        @if($schedule->next_due_kilometers !== null)

                                            <p class="mt-2 text-xs text-gray-500">

                                                Due at:
                                                {{ number_format($schedule->next_due_kilometers) }} km

                                                @if($currentKm !== null)
                                                    · Current:
                                                    {{ number_format($currentKm) }} km
                                                @endif

                                            </p>

                                        @endif

                                    </div>


                                    {{-- REVIEW BUTTON --}}
                                    <a href="{{ route('maintenance-schedules.show', $schedule) }}"
                                        class="shrink-0 text-sm font-medium text-blue-600 hover:text-blue-800">
                                        Review
                                    </a>

                                </div>

                            @empty

                                <div class="px-5 py-4 text-sm text-gray-500">
                                    No maintenance due soon.
                                </div>

                            @endforelse
                        @else

                            <div class="py-8 text-center">
                                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-green-100">
                                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>

                                <p class="mt-3 font-medium text-gray-900">
                                    No maintenance due soon
                                </p>

                                <p class="mt-1 text-sm text-gray-500">
                                    There are no schedules approaching their due point.
                                </p>
                            </div>

                        @endif
                    </div>

                </div>

                {{-- Pending --}}
                <div>

                    <div class="flex items-center justify-between px-5 py-3">

                        <div class="flex items-center gap-2">

                            <span class="h-2.5 w-2.5 rounded-full bg-yellow-500"></span>

                            <span class="text-sm font-semibold text-gray-900">
                                Pending Maintenance
                            </span>

                        </div>

                        @if($pendingMaintenanceCount > 3)
                                            <a href="{{ route('maintenance.index', [
                                'status' => \App\Enums\MaintenanceStatus::Pending->value,
                            ]) }}"
                                                class="mt-4 block text-center text-sm font-medium text-blue-600 hover:text-blue-800">
                                                See all {{ $pendingMaintenanceCount }} pending records →
                                            </a>
                        @else
                            <span class="text-sm font-semibold text-yellow-600">
                                {{ $pendingMaintenance->count() }}
                            </span>
                        @endif

                    </div>

                    @if($pendingMaintenance->isNotEmpty())
                        @forelse($pendingMaintenance->take(3) as $maintenance)

                                    <div class="border-t border-gray-100 px-5 py-4">

                                        <div class="flex items-start justify-between gap-4">

                                            <div class="min-w-0">

                                                <p class="truncate text-sm font-medium text-gray-900">
                                                    {{ $maintenance->title }}
                                                </p>

                                                <p class="mt-1 text-xs text-gray-500">
                                                    {{ $maintenance->vehicle?->vehicle_code ?? 'Unknown vehicle' }}
                                                </p>

                                            </div>

                                            <a href="{{ route(
                                'maintenance.show',
                                $maintenance
                            ) }}"
                                                class="shrink-0 rounded-lg bg-yellow-50 px-3 py-2 text-xs font-semibold text-yellow-700 transition hover:bg-yellow-100">
                                                Review
                                            </a>

                                        </div>

                                    </div>

                        @empty

                            <div class="px-5 py-4 text-sm text-gray-500">
                                No pending maintenance.
                            </div>

                        @endforelse
                    @else

                        <div class="py-8 text-center">
                            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-green-100">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>

                            <p class="mt-3 font-medium text-gray-900">
                                No pending maintenance
                            </p>

                            <p class="mt-1 text-sm text-gray-500">
                                There are no maintenance records waiting to be started.
                            </p>
                        </div>

                    @endif

                </div>

            </div>


            {{-- UPCOMING MAINTENANCE --}}
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">

                <div class="border-b border-gray-100 p-5">

                    <div class="flex items-center justify-between">

                        <div>
                            <h2 class="font-semibold text-gray-900">
                                Upcoming Maintenance
                            </h2>

                            <p class="mt-1 text-sm text-gray-500">
                                Scheduled work that can be started next.
                            </p>
                        </div>

                        <a href="{{ route('maintenance-schedules.index') }}"
                            class="text-xs font-semibold text-blue-600 hover:text-blue-700">
                            View all
                        </a>

                    </div>

                </div>


                <div class="divide-y divide-gray-100">
                    @if($upcomingMaintenanceSchedules->isNotEmpty())

                        @forelse($upcomingMaintenanceSchedules as $schedule)

                                    @php
                                        $currentKm =
                                            $schedule->vehicle?->current_kilometers;

                                        $remainingKm =
                                            $schedule->kilometersRemaining($currentKm);

                                        $dueReason =
                                            $schedule->dueReason($currentKm);
                                    @endphp

                                    <div class="p-5">

                                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                                            <div class="min-w-0">

                                                <div class="flex items-center gap-2">

                                                    <span class="h-2.5 w-2.5 rounded-full bg-amber-500"></span>

                                                    <p class="truncate font-medium text-gray-900">
                                                        {{ $schedule->title }}
                                                    </p>

                                                </div>

                                                <p class="mt-1 text-sm text-gray-500">
                                                    {{ $schedule->vehicle?->vehicle_code ?? 'Unknown vehicle' }}
                                                    @if($schedule->vehicle?->name)
                                                        · {{ $schedule->vehicle->name }}
                                                    @endif
                                                </p>


                                                <div class="mt-2 flex flex-wrap gap-2 text-xs">

                                                    @if($schedule->next_due_date)
                                                        <span class="rounded-full bg-gray-100 px-2.5 py-1 text-gray-600">
                                                            Due {{ $schedule->next_due_date->format('d M Y') }}
                                                        </span>
                                                    @endif

                                                    @if($remainingKm !== null)

                                                        @if($remainingKm <= 0)

                                                            <span class="rounded-full bg-red-50 px-2.5 py-1 font-medium text-red-700">
                                                                {{ number_format(abs($remainingKm)) }} km overdue
                                                            </span>

                                                        @else

                                                            <span class="rounded-full bg-blue-50 px-2.5 py-1 font-medium text-blue-700">
                                                                {{ number_format($remainingKm) }} km remaining
                                                            </span>

                                                        @endif

                                                    @endif

                                                    @if($dueReason === 'both')
                                                        <span class="rounded-full bg-amber-50 px-2.5 py-1 font-medium text-amber-700">
                                                            Date + mileage
                                                        </span>
                                                    @elseif($dueReason === 'date')
                                                        <span class="rounded-full bg-amber-50 px-2.5 py-1 font-medium text-amber-700">
                                                            Date
                                                        </span>
                                                    @elseif($dueReason === 'kilometers')
                                                        <span class="rounded-full bg-amber-50 px-2.5 py-1 font-medium text-amber-700">
                                                            Mileage
                                                        </span>
                                                    @endif

                                                </div>

                                            </div>


                                            <div class="flex shrink-0 flex-col gap-2 sm:flex-row">

                                                <a href="{{ route(
                                'maintenance-schedules.show',
                                $schedule
                            ) }}"
                                                    class="rounded-lg border border-gray-300 px-3 py-2 text-center text-xs font-semibold text-gray-700 transition hover:bg-gray-50">
                                                    View Schedule
                                                </a>

                                                <a href="{{ route('maintenance.create', [
                                'vehicle_id' => $schedule->vehicle_id,
                                'schedule_id' => $schedule->id,
                            ]) }}"
                                                    class="rounded-lg bg-blue-600 px-3 py-2 text-center text-xs font-semibold text-white transition hover:bg-blue-700">
                                                    Start Maintenance
                                                </a>

                                            </div>

                                        </div>

                                    </div>

                        @empty

                            <div class="p-10 text-center">

                                <div
                                    class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-green-50 text-green-600">

                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>

                                </div>

                                <p class="mt-3 text-sm font-medium text-gray-900">
                                    No upcoming maintenance requires action.
                                </p>

                                <p class="mt-1 text-xs text-gray-500">
                                    The maintenance schedule is currently up to date.
                                </p>

                            </div>

                        @endforelse
                    @else

                        <div class="py-8 text-center">
                            <p class="font-medium text-gray-900">
                                No upcoming maintenance
                            </p>

                            <p class="mt-1 text-sm text-gray-500">
                                No maintenance schedules currently require planning.
                            </p>
                        </div>

                    @endif
                </div>

            </div>

        </div>

        {{-- RECENT MAINTENANCE- --}}
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">

            <div class="flex flex-col gap-3 border-b border-gray-100 p-5 sm:flex-row sm:items-center sm:justify-between">

                <div>
                    <h2 class="font-semibold text-gray-900">
                        Recent Maintenance
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Latest maintenance activities.
                    </p>
                </div>

                <a href="{{ route('maintenance.index') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">
                    View all →
                </a>

            </div>

            <div class="overflow-x-auto">

                <table class="min-w-full text-left text-sm">

                    <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500">

                        <tr>

                            <th class="whitespace-nowrap px-5 py-3 font-semibold">
                                Vehicle
                            </th>

                            <th class="whitespace-nowrap px-5 py-3 font-semibold">
                                Maintenance
                            </th>

                            <th class="whitespace-nowrap px-5 py-3 font-semibold">
                                Type
                            </th>

                            <th class="whitespace-nowrap px-5 py-3 font-semibold">
                                Status
                            </th>

                            <th class="whitespace-nowrap px-5 py-3 text-right font-semibold">
                                Cost
                            </th>

                            <th class="whitespace-nowrap px-5 py-3 font-semibold">
                                Date
                            </th>

                            <th class="whitespace-nowrap px-5 py-3 text-right font-semibold">
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-gray-100">

                        @if ($recentMaintenance->isNotEmpty())
                            @forelse($recentMaintenance as $maintenance)

                                            @php

                                                $statusValue =
                                                    $maintenance->status?->value
                                                    ?? (string) $maintenance->status;

                                                $statusLabel =
                                                    str($statusValue)
                                                        ->replace('_', ' ')
                                                        ->title();

                                                $statusClasses = match ($statusValue) {
                                                    'completed' =>
                                                        'bg-green-50 text-green-700',

                                                    'in_progress' =>
                                                        'bg-blue-50 text-blue-700',

                                                    'pending' =>
                                                        'bg-amber-50 text-amber-700',

                                                    'cancelled' =>
                                                        'bg-gray-100 text-gray-600',

                                                    default =>
                                                        'bg-gray-100 text-gray-700',
                                                };

                                            @endphp

                                            <tr class="transition hover:bg-gray-50">

                                                <td class="whitespace-nowrap px-5 py-4">

                                                    <div class="font-medium text-gray-900">
                                                        {{ $maintenance->vehicle?->vehicle_code ?? '—' }}
                                                    </div>

                                                    <div class="mt-0.5 text-xs text-gray-500">
                                                        {{ $maintenance->vehicle?->name ?? 'Unknown vehicle' }}
                                                    </div>

                                                </td>


                                                <td class="max-w-xs px-5 py-4">

                                                    <p class="truncate font-medium text-gray-900">
                                                        {{ $maintenance->title }}
                                                    </p>

                                                </td>


                                                <td class="whitespace-nowrap px-5 py-4 text-gray-600">

                                                    {{ $maintenance->type?->value
                                    ? str($maintenance->type->value)
                                        ->replace('_', ' ')
                                        ->title()
                                    : '—'
                                                                                                                                                                                                                                                                                                                                                                                                                                                        }}

                                                </td>


                                                <td class="whitespace-nowrap px-5 py-4">

                                                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClasses }}">
                                                        {{ $statusLabel }}
                                                    </span>

                                                </td>


                                                <td class="whitespace-nowrap px-5 py-4 text-right font-medium text-gray-900">

                                                    ${{ number_format(
                                    (float) $maintenance->cost,
                                    2
                                ) }}

                                                </td>


                                                <td class="whitespace-nowrap px-5 py-4 text-gray-500">

                                                    {{ $maintenance->reported_at?->format('d M Y') ?? '—' }}

                                                </td>


                                                <td class="whitespace-nowrap px-5 py-4 text-right">

                                                    <a href="{{ route(
                                    'maintenance.show',
                                    $maintenance
                                ) }}" class="font-semibold text-blue-600 hover:text-blue-700">
                                                        View
                                                    </a>

                                                </td>

                                            </tr>

                            @empty

                                <tr>

                                    <td colspan="7" class="px-5 py-10 text-center text-sm text-gray-500">
                                        No maintenance records found.
                                    </td>

                                </tr>

                            @endforelse
                        @else

                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <p class="font-medium text-gray-900">
                                        No maintenance records
                                    </p>

                                    <p class="mt-1 text-sm text-gray-500">
                                        Maintenance activity will appear here once records are created.
                                    </p>
                                </td>
                            </tr>

                        @endif
                    </tbody>

                </table>

            </div>

        </div>

    </div>


    {{-- CHART DAT --}}
    @php

        $costLabels =
            array_keys($monthlyMaintenanceCostTrend);

        $costValues =
            array_values($monthlyMaintenanceCostTrend);

        $activityLabels =
            array_keys($monthlyMaintenanceActivity);

        $activityValues =
            array_values($monthlyMaintenanceActivity);

        $vehicleStatusLabels =
            array_keys($vehicleStatusDistribution);

        $vehicleStatusValues =
            array_values($vehicleStatusDistribution);

    @endphp


    {{-- CHART --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            /* Monthly Maintenance Cost */

            const costCanvas =
                document.getElementById('monthlyMaintenanceCostChart');

            if (costCanvas) {

                new Chart(costCanvas, {
                    type: 'line',

                    data: {
                        labels: @json($costLabels),

                        datasets: [{
                            label: 'Maintenance Cost',

                            data: @json($costValues),

                            borderWidth: 2,

                            tension: 0.35,

                            fill: true,

                            pointRadius: 3,

                            pointHoverRadius: 5
                        }]
                    },

                    options: {
                        responsive: true,

                        maintainAspectRatio: false,

                        plugins: {
                            legend: {
                                display: false
                            },

                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        return '$' +
                                            Number(context.raw)
                                                .toLocaleString(
                                                    undefined,
                                                    {
                                                        minimumFractionDigits: 2,
                                                        maximumFractionDigits: 2
                                                    }
                                                );
                                    }
                                }
                            }
                        },

                        scales: {
                            y: {
                                beginAtZero: true,

                                ticks: {
                                    callback: function (value) {
                                        return '$' +
                                            Number(value)
                                                .toLocaleString();
                                    }
                                }
                            }
                        }
                    }
                });
            }


            /* Vehicle Status */

            const vehicleStatusCanvas =
                document.getElementById('vehicleStatusChart');

            if (vehicleStatusCanvas) {

                new Chart(vehicleStatusCanvas, {
                    type: 'doughnut',

                    data: {
                        labels: @json($vehicleStatusLabels),

                        datasets: [{
                            data: @json($vehicleStatusValues),

                            borderWidth: 2
                        }]
                    },

                    options: {
                        responsive: true,

                        maintainAspectRatio: false,

                        cutout: '65%',

                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }


            /* Monthly Maintenance Activity */

            const activityCanvas =
                document.getElementById(
                    'monthlyMaintenanceActivityChart'
                );

            if (activityCanvas) {

                new Chart(activityCanvas, {
                    type: 'bar',

                    data: {
                        labels: @json($activityLabels),

                        datasets: [{
                            label: 'Maintenance Records',

                            data: @json($activityValues),

                            borderRadius: 6,

                            borderSkipped: false
                        }]
                    },

                    options: {
                        responsive: true,

                        maintainAspectRatio: false,

                        plugins: {
                            legend: {
                                display: false
                            }
                        },

                        scales: {
                            y: {
                                beginAtZero: true,

                                ticks: {
                                    precision: 0
                                }
                            }
                        }
                    }
                });
            }

        });
    </script>

@endsection