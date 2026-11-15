@extends('layouts.app')

@section('title', 'Maintenance Details')

@section('content')

    @php
        use App\Enums\MaintenanceStatus;

        /* Safe values*/

        $maintenanceType = $maintenance->type;

        $maintenanceTypeValue = $maintenanceType instanceof \BackedEnum
            ? $maintenanceType->value
            : $maintenanceType;

        $maintenanceStatus = $maintenance->status;

        $maintenanceStatusValue = $maintenanceStatus instanceof \BackedEnum
            ? $maintenanceStatus->value
            : $maintenanceStatus;

        /* Cost calculation */

        $partsTotal = $maintenance->maintenanceParts->sum(
            fn($part) => (float) $part->total_cost
        );

        $laborCost = (float) ($maintenance->labor_cost ?? 0);

        $otherCost = (float) ($maintenance->other_cost ?? 0);

        /*
        | The maintenance record cost should normally already be calculated
        | by MaintenanceService / MaintenancePartService.
        |
        | We use the stored cost as the final authoritative value.
        */

        $totalCost = (float) ($maintenance->cost ?? 0);

        /* Workflow */

        $workflow = [
            MaintenanceStatus::Pending,
            MaintenanceStatus::InProgress,
            MaintenanceStatus::Completed,
        ];

        /* Status helpers */

        $isPending = $maintenance->status === MaintenanceStatus::Pending;

        $isInProgress = $maintenance->status === MaintenanceStatus::InProgress;

        $isCompleted = $maintenance->status === MaintenanceStatus::Completed;

        $isCancelled = $maintenance->status === MaintenanceStatus::Cancelled;

        $canUpdate = auth()->user()->can('update', $maintenance);

        $canDelete = auth()->user()->role->value === 'admin'
            && !$isInProgress
            && !$isCompleted
            && !$isCancelled;
    @endphp

    <!-- @error('status')
        <div class="mt-2 rounded-lg border border-red-200 bg-red-50 p-3">
            <p class="text-sm text-red-700">
                {{ $message }}
            </p>
        </div>
    @enderror -->

    <div class="space-y-6">

        {{--PAGE HEADER --}}

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>

                <h1 class="text-2xl font-bold text-gray-900">
                    {{ $maintenance->title }}
                </h1>

                <p class="mt-1 text-sm text-gray-500">

                    {{ $maintenance->vehicle?->vehicle_code ?? '-' }}

                    @if($maintenance->vehicle?->name)
                        — {{ $maintenance->vehicle->name }}
                    @endif

                </p>

            </div>

            <div class="flex flex-wrap gap-2">

                {{-- Edit --}}

                @if($canUpdate)

                    <a href="{{ route('maintenance.edit', $maintenance) }}"
                        class="rounded-lg border border-gray-300 px-4 py-2 text-gray-700 transition-colors hover:border-gray-400 hover:bg-gray-200 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Edit
                    </a>

                @endif

                {{-- Delete --}}

                @if($canDelete)

                    <form method="POST" action="{{ route('maintenance.destroy', $maintenance) }}"
                        onsubmit="return confirm('Are you sure you want to delete this maintenance record?')">

                        @csrf

                        @method('DELETE')

                        <button type="submit"
                            class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">
                            Delete
                        </button>

                    </form>

                @endif

                {{-- Back --}}

                <a href="{{ route('maintenance.index') }}"
                    class="rounded-lg bg-gray-900 px-4 py-2 text-white transition-colors hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2">
                    Back
                </a>

            </div>

        </div>

        {{--SUCCESS MESSAGE --}}

        @if(session('success'))

            <div class="rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>

        @endif

        {{--ERROR MESSAGE --}}

        @if(session('error'))

            <div class="rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ session('error') }}
            </div>

        @endif

        {{--VALIDATION ERRORS --}}

        @if($errors->any())

            <div class="rounded-lg bg-red-50 p-4">

                <p class="font-medium text-red-800">
                    Please correct the following errors:
                </p>

                <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-700">

                    @foreach($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        @endif

        {{--MAINTENANCE WORKFLOW --}}

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">

            <h2 class="text-lg font-semibold text-gray-900">
                Maintenance Workflow
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                Current progress of this maintenance record.
            </p>

            <div class="mt-6 flex flex-wrap items-center gap-2">

                @foreach($workflow as $status)

                        @php
                            $isCurrent = $maintenance->status === $status;

                            $isCompletedStep =
                                $isCompleted
                                && in_array(
                                    $status,
                                    [
                                        MaintenanceStatus::Pending,
                                        MaintenanceStatus::InProgress,
                                        MaintenanceStatus::Completed
                                    ],
                                    true
                                );

                            $isPastStep =
                                $isCompleted
                                && $status !== MaintenanceStatus::Completed;
                        @endphp

                        <div @class([
                            'rounded-full px-4 py-2 text-sm font-medium',

                            'bg-gray-900 text-white'
                            => $isCurrent,

                            'bg-green-100 text-green-700'
                            => $isPastStep,

                            'bg-gray-100 text-gray-500'
                            => !$isCurrent && !$isPastStep,
                        ])>

                            {{ str($status->value)
                    ->replace('_', ' ')
                    ->title()
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    }}

                        </div>

                        @if(!$loop->last)

                            <span class="text-gray-400">
                                →
                            </span>

                        @endif

                @endforeach

            </div>

            {{-- Cancelled --}}

            @if($isCancelled)

                <div class="mt-5 rounded-lg border border-red-200 bg-red-50 p-4">

                    <p class="font-medium text-red-800">
                        Maintenance Cancelled
                    </p>

                    <p class="mt-1 text-sm text-red-700">
                        This maintenance record was cancelled and can no longer
                        continue through the normal workflow.
                    </p>

                </div>

            @endif

            {{-- Completed --}}

            @if($isCompleted)

                <div class="mt-5 rounded-lg border border-green-200 bg-green-50 p-4">

                    <p class="font-medium text-green-800">
                        Maintenance Completed
                    </p>

                    <p class="mt-1 text-sm text-green-700">
                        This maintenance record has been completed.
                    </p>

                </div>

            @endif

        </div>

        {{--MAINTENANCE INFORMATION --}}

        <div class="rounded-xl bg-white p-6 shadow-sm">

            <h2 class="mb-6 text-lg font-semibold text-gray-900">
                Maintenance Information
            </h2>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">

                {{-- Vehicle --}}

                <div>

                    <p class="text-sm text-gray-500">
                        Vehicle
                    </p>

                    <p class="mt-1 font-medium text-gray-900">

                        {{ $maintenance->vehicle?->vehicle_code ?? '-' }}

                        @if($maintenance->vehicle?->name)
                            — {{ $maintenance->vehicle->name }}
                        @endif

                    </p>

                </div>

                {{-- Type --}}

                <div>
                    <p class="text-sm text-gray-500">
                        Type
                    </p>

                    <p class="mt-1">
                        <span @class([
                            'inline-flex rounded-full px-3 py-1 text-xs font-semibold',

                            'bg-purple-100 text-purple-700' =>
                                $maintenanceTypeValue === 'preventive',

                            'bg-orange-100 text-orange-700' =>
                                $maintenanceTypeValue === 'corrective',

                            'bg-indigo-100 text-indigo-700' =>
                                $maintenanceTypeValue === 'inspection',
                        ])>
                            {{ $maintenanceTypeValue
        ? str($maintenanceTypeValue)
            ->replace('_', ' ')
            ->title()
        : '-' }}
                        </span>
                    </p>
                </div>

                {{-- Status --}}

                <div>
                    <p class="text-sm text-gray-500">
                        Status
                    </p>

                    <p class="mt-1">
                        @if ($maintenanceStatusValue)
                                        <span @class([
                                            'inline-flex rounded-full px-3 py-1 text-xs font-semibold',

                                            'bg-yellow-100 text-yellow-700' =>
                                                $maintenanceStatusValue === 'pending',

                                            'bg-blue-100 text-blue-700' =>
                                                $maintenanceStatusValue === 'in_progress',

                                            'bg-green-100 text-green-700' =>
                                                $maintenanceStatusValue === 'completed',

                                            'bg-red-100 text-red-700' =>
                                                $maintenanceStatusValue === 'cancelled',
                                        ])>
                                            {{ str($maintenanceStatusValue)
                            ->replace('_', ' ')
                            ->title() }}
                                        </span>
                        @else
                            <span class="text-gray-900">-</span>
                        @endif
                    </p>
                </div>

                {{-- Schedule --}}

                <div>

                    <p class="text-sm text-gray-500">
                        Preventive Schedule
                    </p>

                    <p class="mt-1 font-medium text-gray-900">

                        {{ $maintenance->maintenanceSchedule?->title ?? 'Not linked' }}

                    </p>

                </div>

                {{-- Reported At --}}

                <div>

                    <p class="text-sm text-gray-500">
                        Reported At
                    </p>

                    <p class="mt-1 font-medium text-gray-900">

                        {{ $maintenance->reported_at
        ? $maintenance->reported_at->format('d M Y H:i')
        : '-'
                                                                                                                                                                                                            }}

                    </p>

                </div>

                {{-- Started At --}}

                <div>

                    <p class="text-sm text-gray-500">
                        Started At
                    </p>

                    <p class="mt-1 font-medium text-gray-900">

                        {{ $maintenance->started_at
        ? $maintenance->started_at->format('d M Y H:i')
        : '-'
                                                                                                                                                                                                            }}

                    </p>

                </div>

                {{-- Completed At --}}

                <div>

                    <p class="text-sm text-gray-500">
                        Completed At
                    </p>

                    <p class="mt-1 font-medium text-gray-900">

                        {{ $maintenance->completed_at
        ? $maintenance->completed_at->format('d M Y H:i')
        : '-'
                                                                                                                                                                                                            }}

                    </p>

                </div>

                {{-- Service Kilometers --}}

                <div>

                    <p class="text-sm text-gray-500">
                        Service Kilometers
                    </p>

                    <p class="mt-1 font-medium text-gray-900">

                        {{ $maintenance->service_kilometers !== null
        ? number_format($maintenance->service_kilometers) . ' km'
        : '-'
                                                                                                                                                                                                            }}

                    </p>

                </div>

                {{-- Service Provider --}}

                <div>

                    <p class="text-sm text-gray-500">
                        Service Provider
                    </p>

                    <p class="mt-1 font-medium text-gray-900">
                        {{ $maintenance->service_provider ?: '-' }}
                    </p>

                </div>

            </div>

            {{-- Description --}}

            <div class="mt-6 border-t border-gray-200 pt-6">

                <h3 class="font-medium text-gray-900">
                    Description
                </h3>

                <p class="mt-2 whitespace-pre-line text-gray-600">
                    {{ $maintenance->description ?: '-' }}
                </p>

            </div>

            {{-- Notes --}}

            <div class="mt-6 border-t border-gray-200 pt-6">

                <h3 class="font-medium text-gray-900">
                    Notes
                </h3>

                <p class="mt-2 whitespace-pre-line text-gray-600">
                    {{ $maintenance->notes ?: '-' }}
                </p>

            </div>

        </div>


        {{-- =========================================================
        MAINTENANCE VISITS
        ========================================================= --}}

        <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

            {{-- SECTION HEADER --}}
            <div class="border-b border-gray-100 px-4 py-5 sm:px-6">
                <div class="flex items-start justify-between gap-3">

                    <div class="flex items-start gap-3">
                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8 7V3m8 4V3M4 11h16M5 5h14a1 1 0 0 1 1 1v14H4V6a1 1 0 0 1 1-1Z" />
                            </svg>
                        </div>

                        <div>
                            <h2 class="text-lg font-bold tracking-tight text-gray-900">
                                Maintenance Visits
                            </h2>
                            <p class="mt-1 text-sm leading-relaxed text-gray-500">
                                Track inspections, repairs, and testing activities across multiple visits.
                            </p>
                        </div>
                    </div>

                    <span class="shrink-0 rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600">
                        {{ $maintenance->visits->count() }}
                        {{ \Illuminate\Support\Str::plural('visit', $maintenance->visits->count()) }}
                    </span>
                </div>
            </div>


            <div class="space-y-6 p-4 sm:p-6">

                {{-- =====================================================
                REGISTER NEW VISIT
                ===================================================== --}}

                @if($canUpdate && $isInProgress && !$hasActiveVisit)

                    <div class="rounded-xl border border-dashed border-indigo-200 bg-indigo-50/40 p-4 sm:p-5">

                        <div class="mb-4 flex items-start gap-3">
                            <div
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white text-indigo-600 shadow-sm">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m-7-7h14" />
                                </svg>
                            </div>

                            <div>
                                <h3 class="text-sm font-semibold text-gray-900">
                                    Register a new visit
                                </h3>
                                <p class="mt-1 text-xs leading-relaxed text-gray-500">
                                    Start another inspection or repair session for this maintenance report.
                                </p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('maintenance.visits.store', $maintenance) }}" class="space-y-3">
                            @csrf

                            <div>
                                <label for="visit_remarks" class="mb-1.5 block text-sm font-medium text-gray-700">
                                    Initial visit remarks
                                    <span class="font-normal text-gray-400">(Optional)</span>
                                </label>

                                <textarea name="remarks" id="visit_remarks" rows="2" maxlength="2000"
                                    placeholder="Describe the purpose of this visit..."
                                    class="block w-full rounded-lg border-gray-300 bg-white text-sm shadow-sm
                                                                                                                                                                           placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500">{{ old('remarks') }}</textarea>

                                @error('remarks')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex justify-end">
                                <button type="submit"
                                    class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5
                                                                                                                                                                               text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700
                                                                                                                                                                               focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m-7-7h14" />
                                    </svg>
                                    Register Visit
                                </button>
                            </div>
                        </form>
                    </div>

                @elseif($hasActiveVisit)

                    <div class="flex items-start gap-3 rounded-xl border border-blue-200 bg-blue-50 p-4">
                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v3m0 4h.01M10.3 3.8 1.9 18.3A2 2 0 0 0 3.6 21h16.8a2 2 0 0 0 1.7-2.7L13.7 3.8a2 2 0 0 0-3.4 0Z" />
                        </svg>

                        <div>
                            <p class="text-sm font-semibold text-blue-900">
                                An active visit already exists
                            </p>
                            <p class="mt-1 text-sm leading-relaxed text-blue-800">
                                Complete the current visit before registering another one.
                            </p>
                        </div>
                    </div>

                @elseif($isPending)

                    <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                        <p class="text-sm font-semibold text-amber-900">
                            Maintenance not started
                        </p>
                        <p class="mt-1 text-sm text-amber-800">
                            Change the maintenance report status to In Progress before registering a visit.
                        </p>
                    </div>

                @else

                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                        <p class="text-sm font-medium text-gray-800">
                            Visit registration unavailable
                        </p>
                        <p class="mt-1 text-sm text-gray-600">
                            New visits cannot be registered because this maintenance report is
                            {{ $isCompleted ? 'completed' : 'cancelled' }}.
                        </p>
                    </div>

                @endif


                {{-- VISIT VALIDATION ERROR --}}
                @error('visit')
                    <div class="rounded-lg border border-red-200 bg-red-50 p-3">
                        <p class="text-sm text-red-700">{{ $message }}</p>
                    </div>
                @enderror


                {{-- =====================================================
                VISIT HISTORY
                ===================================================== --}}

                <div>
                    <div class="mb-4">
                        <h3 class="text-sm font-semibold text-gray-900">
                            Visit History
                        </h3>
                        <p class="mt-1 text-xs text-gray-500">
                            Inspection and repair records for this maintenance report.
                        </p>
                    </div>

                    <div class="space-y-4">

                        @forelse($maintenance->visits as $visit)

                            @php
                                $canManageVisit = $canUpdate
                                    && $isInProgress
                                    && $visit->status === 'in_progress';

                                $visitIsCompleted = $visit->status === 'completed';
                                $isActiveVisit = $visit->status === 'in_progress';
                                // All defects explicitly assigned to this visit through the pivot table.
                                $visitDefects = $visit->recordDefects;

                                $visitDefectCount = $visitDefects->count();

                                $visitRepairWorks = $maintenance->recordDefects
                                    ->flatMap(fn($defect) => $defect->repairWorks)
                                    ->where('maintenance_visit_id', $visit->id);

                                $visitRepairCount = $visitRepairWorks->count();

                                $visitPartCount = $visitRepairWorks
                                    ->sum(fn($work) => $work->maintenanceParts->count());
                            @endphp


                            {{-- =================================================
                            VISIT CARD
                            Alpine state belongs to the whole card.
                            ================================================= --}}

                            <article x-data="{ expanded: @js($isActiveVisit) }"
                                class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm transition hover:border-gray-300">

                                {{-- VISIT HEADER --}}
                                <div class="flex flex-col gap-3 bg-gray-50/70 px-4 py-4 sm:px-5">

                                    <div class="flex items-start justify-between gap-3">

                                        {{-- CLICKABLE VISIT SUMMARY --}}
                                        <button type="button" @click="expanded = !expanded" :aria-expanded="expanded.toString()"
                                            class="flex min-w-0 flex-1 items-start gap-3 text-left">
                                            <div @class([
                                                'flex h-10 w-10 shrink-0 items-center justify-center rounded-xl',
                                                'bg-green-100 text-green-700' => $visitIsCompleted,
                                                'bg-blue-100 text-blue-700' => $isActiveVisit,
                                                'bg-gray-100 text-gray-600' => !$visitIsCompleted && !$isActiveVisit,
                                            ])>
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                    stroke-width="1.8">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M8 7V3m8 4V3M4 11h16M5 5h14a1 1 0 0 1 1 1v14H4V6a1 1 0 0 1 1-1Z" />
                                                </svg>
                                            </div>

                                            <div class="min-w-0 flex-1">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <h4 class="text-sm font-bold text-gray-900">
                                                        Visit #{{ $visit->visit_number }}
                                                    </h4>

                                                    <span @class([
                                                        'inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold',
                                                        'bg-blue-100 text-blue-700' => $isActiveVisit,
                                                        'bg-green-100 text-green-700' => $visitIsCompleted,
                                                        'bg-gray-100 text-gray-600' => !$isActiveVisit && !$visitIsCompleted,
                                                    ])>
                                                        {{ str($visit->status)->replace('_', ' ')->title() }}
                                                    </span>
                                                </div>

                                                <p class="mt-1 text-xs text-gray-500">
                                                    {{ $visit->started_at?->format('d M Y, h:i A') ?? 'Date unavailable' }}
                                                </p>

                                                <div class="mt-2 flex flex-wrap gap-x-3 gap-y-1 text-xs text-gray-500">
                                                    <span>
                                                        {{ $visitDefectCount }}
                                                        {{ \Illuminate\Support\Str::plural('defect', $visitDefectCount) }}
                                                    </span>
                                                    <span>
                                                        {{ $visitRepairCount }}
                                                        {{ \Illuminate\Support\Str::plural('repair activity', $visitRepairCount) }}
                                                    </span>
                                                    <span>
                                                        {{ $visitPartCount }}
                                                        {{ \Illuminate\Support\Str::plural('spare part', $visitPartCount) }}
                                                    </span>
                                                </div>
                                            </div>

                                            <svg class="mt-2 h-5 w-5 shrink-0 text-gray-400 transition-transform duration-200"
                                                :class="expanded ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7" />
                                            </svg>
                                        </button>

                                        {{-- COMPLETE VISIT: OUTSIDE COLLAPSE BUTTON --}}
                                        @if($canManageVisit)
                                                                    <form method="POST" action="{{ route('maintenance.visits.complete', [
                                                'maintenance' => $maintenance,
                                                'visit' => $visit,
                                            ]) }}"
                                                                        onsubmit="return confirm('Are you sure you want to complete this visit?')"
                                                                        class="shrink-0">
                                                                        @csrf
                                                                        @method('PATCH')

                                                                        <button type="submit"
                                                                            class="inline-flex items-center justify-center gap-1.5 rounded-lg
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       border border-green-200 bg-white px-3 py-2 text-xs
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       font-semibold text-green-700 transition hover:bg-green-50
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                                                stroke-width="2">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6" />
                                                                            </svg>
                                                                            <span class="hidden sm:inline">Complete Visit</span>
                                                                            <span class="sm:hidden">Complete</span>
                                                                        </button>
                                                                    </form>
                                        @endif

                                    </div>
                                </div>


                                {{-- =================================================
                                VISIT DETAILS
                                ================================================= --}}

                                <div x-cloak x-show="expanded" x-collapse>
                                    <div class="space-y-6 border-t border-gray-100 p-4 sm:p-5">

                                        {{-- VISIT INFORMATION --}}
                                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">

                                            <div class="rounded-lg bg-gray-50 p-3">
                                                <p class="text-xs font-medium text-gray-500">
                                                    Technician
                                                </p>
                                                <p class="mt-1 text-sm font-semibold text-gray-800">
                                                    {{ $visit->technician?->name ?? 'Not assigned' }}
                                                </p>
                                            </div>

                                            <div class="rounded-lg bg-gray-50 p-3">
                                                <p class="text-xs font-medium text-gray-500">
                                                    Started At
                                                </p>
                                                <p class="mt-1 text-sm font-semibold text-gray-800">
                                                    {{ $visit->started_at?->format('d M Y, h:i A') ?? 'Not available' }}
                                                </p>
                                            </div>

                                            @if($visitIsCompleted)
                                                <div class="rounded-lg bg-green-50 p-3 sm:col-span-2">
                                                    <p class="text-xs font-medium text-green-700">
                                                        Completed At
                                                    </p>
                                                    <p class="mt-1 text-sm font-semibold text-green-900">
                                                        {{ $visit->completed_at?->format('d M Y, h:i A') ?? 'Not available' }}
                                                    </p>
                                                </div>
                                            @endif

                                        </div>


                                        {{-- VISIT REMARKS --}}
                                        @if($visit->remarks)
                                            <div class="rounded-lg border border-gray-200 bg-white p-3">
                                                <p class="text-xs font-semibold text-gray-500">
                                                    Visit Remarks
                                                </p>
                                                <p class="mt-1 whitespace-pre-line text-sm leading-6 text-gray-700">
                                                    {{ $visit->remarks }}
                                                </p>
                                            </div>
                                        @endif


                                        @include('maintenance.partials.visit-attachments')


                                        {{-- =================================================
                                        DEFECTS & REPAIR ACTIVITIES
                                        ================================================= --}}



                                        @php
                                            // Get defects assigned to this specific visit.
                                            // Do not filter by repair_outcome here.
                                            // Resolved defects must remain visible in their visit history.
                                            $visitDefects = $visit->recordDefects;

                                            $allDefectIds = $visitDefects
                                                ->pluck('id')
                                                ->all();
                                        @endphp

                                        <div class="border-t border-gray-100 pt-5"
                                            x-data="{
                                                                                                                                                                                openDefects: @js($allDefectIds),
                                                                                                                                                                                allDefectIds: @js($allDefectIds),

                                                                                                                                                                                toggleAll() {
                                                                                                                                                                                    this.openDefects =
                                                                                                                                                                                        this.openDefects.length === this.allDefectIds.length
                                                                                                                                                                                            ? []
                                                                                                                                                                                            : [...this.allDefectIds];
                                                                                                                                                                                },

                                                                                                                                                                                toggleDefect(id) {
                                                                                                                                                                                    this.openDefects = this.openDefects.includes(id)
                                                                                                                                                                                        ? this.openDefects.filter(item => item !== id)
                                                                                                                                                                                        : [...this.openDefects, id];
                                                                                                                                                                                },

                                                                                                                                                                                isOpen(id) {
                                                                                                                                                                                    return this.openDefects.includes(id);
                                                                                                                                                                                }
                                                                                                                                                                            }">

                                            {{-- DEFECT SECTION HEADER --}}
                                            <div class="flex flex-wrap items-center justify-between gap-3">
                                                <div>
                                                    <h3 class="text-sm font-bold text-gray-900">
                                                        Defects & Repair Activities
                                                    </h3>
                                                    <p class="mt-1 text-xs leading-relaxed text-gray-500">
                                                        Review defects and record the repair work and spare parts used during
                                                        this visit.
                                                    </p>
                                                </div>

                                                @if($allDefectIds)
                                                    <button type="button" @click="toggleAll()"
                                                        class="rounded-lg border border-gray-200 bg-white px-3 py-2
                                                                                                                                                                                                                                                                   text-xs font-semibold text-gray-600 transition hover:bg-gray-50">
                                                        <span
                                                            x-text="openDefects.length === allDefectIds.length
                                                                                                                                                                                                                                                                    ? 'Collapse all'
                                                                                                                                                                                                                                                                    : 'Expand all'"></span>
                                                    </button>
                                                @endif
                                            </div>


                                            {{-- NO DEFECTS --}}
                                            @if($visitDefects->isEmpty())
                                                <div
                                                    class="mt-4 rounded-xl border border-dashed border-gray-300 bg-gray-50 px-5 py-8 text-center">
                                                    <p class="text-sm font-semibold text-gray-800">
                                                        No defects recorded
                                                    </p>
                                                    <p class="mt-1 text-xs text-gray-500">
                                                        No defects are associated with this maintenance report.
                                                    </p>
                                                </div>
                                            @else

                                                <div class="mt-4 space-y-3">

                                                    @foreach($visitDefects as $recordDefect)

                                                        @php
                                                            $defectId = $recordDefect->id;
                                                            $assetDefect = $recordDefect->assetDefect;
                                                            $defectTypeName = $assetDefect?->defectType?->name ?? 'Unknown defect';

                                                            $visitRepairWorks = $recordDefect->repairWorks
                                                                ->where('maintenance_visit_id', $visit->id);

                                                            $repairWorkCount = $visitRepairWorks->count();

                                                            $sparePartCount = $visitRepairWorks
                                                                ->sum(fn($work) => $work->maintenanceParts->count());
                                                        @endphp


                                                        {{-- DEFECT CARD --}}
                                                        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">

                                                            {{-- DEFECT HEADER --}}
                                                            <button type="button" @click="toggleDefect(@js($defectId))"
                                                                :aria-expanded="isOpen(@js($defectId)).toString()"
                                                                class="flex w-full items-center justify-between gap-3 p-4 text-left transition hover:bg-gray-50">
                                                                <div class="flex min-w-0 items-center gap-3">
                                                                    <div
                                                                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
                                                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                                                            stroke="currentColor" stroke-width="1.8">
                                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                                d="M12 9v4m0 4h.01M10.3 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.7 3.86a2 2 0 0 0-3.4 0Z" />
                                                                        </svg>
                                                                    </div>

                                                                    <div class="min-w-0">
                                                                        <p class="truncate text-sm font-bold text-gray-900">
                                                                            {{ $defectTypeName }}
                                                                        </p>
                                                                        <p class="mt-1 text-xs text-gray-500">
                                                                            {{ $repairWorkCount }}
                                                                            {{ \Illuminate\Support\Str::plural('repair activity', $repairWorkCount) }}
                                                                            <span class="mx-1">·</span>
                                                                            {{ $sparePartCount }}
                                                                            {{ \Illuminate\Support\Str::plural('spare part', $sparePartCount) }}
                                                                        </p>
                                                                    </div>
                                                                </div>

                                                                <svg class="h-5 w-5 shrink-0 text-gray-400 transition-transform duration-200"
                                                                    :class="isOpen(@js($defectId)) ? 'rotate-180' : ''" fill="none"
                                                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        d="m19 9-7 7-7-7" />
                                                                </svg>
                                                            </button>


                                                            {{-- DEFECT CONTENT --}}
                                                            <div x-cloak x-show="isOpen(@js($defectId))" x-collapse
                                                                class="border-t border-gray-100 bg-gray-50/60 p-3 sm:p-4">


                                                                {{-- DEFECT OUTCOME | UPDATE Maintenance DEFECT STATE --}}
                                                                <div class="mb-5 rounded-xl border border-gray-200 bg-white p-4">

                                                                    <div
                                                                        class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">

                                                                        <div>
                                                                            <h4 class="text-sm font-bold text-gray-800">
                                                                                Defect Outcome
                                                                            </h4>

                                                                            <p class="mt-1 text-xs text-gray-500">
                                                                                Record the current result of inspecting or repairing
                                                                                this defect.
                                                                            </p>
                                                                        </div>

                                                                        @php
                                                                            $outcome = $recordDefect->repair_outcome;

                                                                            $outcomeLabel = match ($outcome) {
                                                                                'resolved' => 'Resolved',
                                                                                'in_progress' => 'In Progress',
                                                                                'unable_to_repair' => 'Unable to Repair',
                                                                                default => 'Not Assessed',
                                                                            };

                                                                            $outcomeClass = match ($outcome) {
                                                                                'resolved' => 'bg-green-100 text-green-700',
                                                                                'in_progress' => 'bg-blue-100 text-blue-700',
                                                                                'unable_to_repair' => 'bg-red-100 text-red-700',
                                                                                default => 'bg-gray-100 text-gray-600',
                                                                            };
                                                                        @endphp

                                                                        <span
                                                                            class="inline-flex w-fit rounded-full px-3 py-1 text-xs font-semibold {{ $outcomeClass }}">
                                                                            {{ $outcomeLabel }}
                                                                        </span>

                                                                    </div>

                                                                    @if($canManageVisit)

                                                                                                            <form method="POST" action="{{ route('maintenance.visits.defects.outcome', [
                                                                            $maintenance,
                                                                            $visit,
                                                                            $recordDefect,
                                                                        ]) }}" class="mt-4 space-y-3">

                                                                                                                @csrf
                                                                                                                @method('PATCH')

                                                                                                                <div>
                                                                                                                    <label
                                                                                                                        class="mb-1.5 block text-xs font-semibold text-gray-700">
                                                                                                                        Select Outcome <span class="text-red-500">*</span>
                                                                                                                    </label>

                                                                                                                    <select name="repair_outcome" required
                                                                                                                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                                                                                                                        <option value="in_progress"
                                                                                                                            @selected($outcome === 'in_progress')>
                                                                                                                            In Progress — Repair is ongoing
                                                                                                                        </option>

                                                                                                                        <option value="resolved"
                                                                                                                            @selected($outcome === 'resolved')>
                                                                                                                            Resolved — Defect successfully fixed
                                                                                                                        </option>

                                                                                                                        <option value="unable_to_repair"
                                                                                                                            @selected($outcome === 'unable_to_repair')>
                                                                                                                            Unable to Repair — Further action required
                                                                                                                        </option>

                                                                                                                    </select>

                                                                                                                    @error('repair_outcome')
                                                                                                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                                                                                    @enderror
                                                                                                                </div>

                                                                                                                <div>
                                                                                                                    <label
                                                                                                                        class="mb-1.5 block text-xs font-semibold text-gray-700">
                                                                                                                        Outcome Remarks
                                                                                                                        <span
                                                                                                                            class="font-normal text-gray-400">(Optional)</span>
                                                                                                                    </label>

                                                                                                                    <textarea name="outcome_remarks" rows="2" maxlength="2000"
                                                                                                                        placeholder="Describe the result, remaining problem, or reason it could not be repaired..."
                                                                                                                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ $recordDefect->outcome_remarks }}</textarea>

                                                                                                                    @error('outcome_remarks')
                                                                                                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                                                                                    @enderror
                                                                                                                </div>

                                                                                                                <div
                                                                                                                    class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">

                                                                                                                    @if($recordDefect->outcome_recorded_at)
                                                                                                                        <p class="text-xs text-gray-400">
                                                                                                                            Last recorded:
                                                                                                                            {{ $recordDefect->outcome_recorded_at->format('d M Y, h:i A') }}
                                                                                                                        </p>
                                                                                                                    @else
                                                                                                                        <p class="text-xs text-gray-400">
                                                                                                                            No outcome recorded yet.
                                                                                                                        </p>
                                                                                                                    @endif

                                                                                                                    <button type="submit"
                                                                                                                        class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-indigo-700">
                                                                                                                        Save Outcome
                                                                                                                    </button>

                                                                                                                </div>

                                                                                                            </form>

                                                                    @else

                                                                        @if($recordDefect->outcome_remarks)
                                                                            <p class="mt-3 whitespace-pre-line text-sm text-gray-600">
                                                                                {{ $recordDefect->outcome_remarks }}
                                                                            </p>
                                                                        @endif

                                                                        @if($recordDefect->outcome_recorded_at)
                                                                            <p class="mt-2 text-xs text-gray-400">
                                                                                Recorded:
                                                                                {{ $recordDefect->outcome_recorded_at->format('d M Y, h:i A') }}
                                                                            </p>
                                                                        @endif

                                                                    @endif

                                                                </div>


                                                                {{-- REPAIR ACTIVITY HEADING --}}
                                                                <div class="mb-3">
                                                                    <h4 class="text-sm font-bold text-gray-800">
                                                                        Repair Activities
                                                                    </h4>
                                                                    <p class="mt-1 text-xs text-gray-500">
                                                                        Work performed to address this defect during this visit.
                                                                    </p>
                                                                </div>


                                                                {{-- EXISTING REPAIR ACTIVITIES --}}
                                                                @forelse($visitRepairWorks as $repairWork)

                                                                    <div x-data="{ editing: false }"
                                                                        class="mb-3 overflow-hidden rounded-xl border border-gray-200 bg-white">
                                                                        <div class="p-4">

                                                                            <div
                                                                                class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">

                                                                                <div class="flex min-w-0 gap-3">
                                                                                    <div
                                                                                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                                                                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                                                                            stroke="currentColor" stroke-width="2">
                                                                                            <path stroke-linecap="round"
                                                                                                stroke-linejoin="round"
                                                                                                d="m5 12 4 4L19 6" />
                                                                                        </svg>
                                                                                    </div>

                                                                                    <div class="min-w-0">
                                                                                        <p
                                                                                            class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                                                                            Repair activity
                                                                                        </p>
                                                                                        <p
                                                                                            class="mt-1 whitespace-pre-line text-sm font-medium leading-6 text-gray-800">
                                                                                            {{ $repairWork->work_description }}
                                                                                        </p>

                                                                                        @if($repairWork->remarks)
                                                                                            <p
                                                                                                class="mt-2 whitespace-pre-line text-xs leading-5 text-gray-500">
                                                                                                {{ $repairWork->remarks }}
                                                                                            </p>
                                                                                        @endif
                                                                                    </div>
                                                                                </div>

                                                                                @if($canManageVisit)
                                                                                                                            <div
                                                                                                                                class="flex shrink-0 items-center gap-3 sm:justify-end">
                                                                                                                                <button type="button" @click="editing = !editing"
                                                                                                                                    class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                                                                                                                                    <span x-text="editing ? 'Cancel' : 'Edit'"></span>
                                                                                                                                </button>

                                                                                                                                <form method="POST" action="{{ route('maintenance.repair-works.destroy', [
                                                                                        $maintenance,
                                                                                        $visit,
                                                                                        $recordDefect,
                                                                                        $repairWork
                                                                                    ]) }}"
                                                                                                                                    onsubmit="return confirm('Are you sure you want to delete this repair activity?')">
                                                                                                                                    @csrf
                                                                                                                                    @method('DELETE')

                                                                                                                                    <button type="submit"
                                                                                                                                        class="text-xs font-semibold text-red-600 hover:text-red-800">
                                                                                                                                        Delete
                                                                                                                                    </button>
                                                                                                                                </form>
                                                                                                                            </div>
                                                                                @endif
                                                                            </div>


                                                                            {{-- EDIT REPAIR ACTIVITY --}}
                                                                            @if($canManageVisit)
                                                                                                                    <div x-cloak x-show="editing" x-collapse
                                                                                                                        class="mt-4 border-t border-gray-100 pt-4">

                                                                                                                        <form method="POST" action="{{ route('maintenance.repair-works.update', [
                                                                                    $maintenance,
                                                                                    $visit,
                                                                                    $recordDefect,
                                                                                    $repairWork
                                                                                ]) }}" class="space-y-3">
                                                                                                                            @csrf
                                                                                                                            @method('PUT')

                                                                                                                            <div>
                                                                                                                                <label
                                                                                                                                    class="mb-1 block text-xs font-semibold text-gray-700">
                                                                                                                                    Work Description
                                                                                                                                </label>
                                                                                                                                <textarea name="work_description" rows="3" required
                                                                                                                                    class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ $repairWork->work_description }}</textarea>
                                                                                                                            </div>

                                                                                                                            <div>
                                                                                                                                <label
                                                                                                                                    class="mb-1 block text-xs font-semibold text-gray-700">
                                                                                                                                    Remarks <span
                                                                                                                                        class="font-normal text-gray-400">(Optional)</span>
                                                                                                                                </label>
                                                                                                                                <textarea name="remarks" rows="2"
                                                                                                                                    class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ $repairWork->remarks }}</textarea>
                                                                                                                            </div>

                                                                                                                            <div class="flex justify-end">
                                                                                                                                <button type="submit"
                                                                                                                                    class="rounded-lg bg-indigo-600 px-4 py-2 text-xs font-semibold text-white hover:bg-indigo-700">
                                                                                                                                    Save Changes
                                                                                                                                </button>
                                                                                                                            </div>
                                                                                                                        </form>
                                                                                                                    </div>
                                                                            @endif
                                                                        </div>


                                                                        {{-- SPARE PARTS --}}
                                                                        <div class="border-t border-gray-100 bg-gray-50/70 p-4">

                                                                            <div class="flex items-center justify-between gap-3">
                                                                                <div>
                                                                                    <h5 class="text-xs font-bold text-gray-800">
                                                                                        Spare Parts Used
                                                                                    </h5>
                                                                                    <p class="mt-1 text-xs text-gray-500">
                                                                                        Parts consumed for this repair activity.
                                                                                    </p>
                                                                                </div>

                                                                                <span class="text-xs font-medium text-gray-500">
                                                                                    {{ $repairWork->maintenanceParts->count() }}
                                                                                    {{ \Illuminate\Support\Str::plural('part', $repairWork->maintenanceParts->count()) }}
                                                                                </span>
                                                                            </div>


                                                                            @if($repairWork->maintenanceParts->isNotEmpty())

                                                                                <div
                                                                                    class="mt-3 overflow-x-auto rounded-lg border border-gray-200 bg-white">
                                                                                    <table
                                                                                        class="min-w-full divide-y divide-gray-100 text-left">
                                                                                        <thead class="bg-gray-50">
                                                                                            <tr>
                                                                                                <th
                                                                                                    class="px-3 py-2.5 text-xs font-semibold text-gray-500">
                                                                                                    Part</th>
                                                                                                <th
                                                                                                    class="px-3 py-2.5 text-xs font-semibold text-gray-500">
                                                                                                    Quantity</th>
                                                                                                <th
                                                                                                    class="px-3 py-2.5 text-xs font-semibold text-gray-500">
                                                                                                    Unit Cost</th>
                                                                                                <th
                                                                                                    class="px-3 py-2.5 text-right text-xs font-semibold text-gray-500">
                                                                                                    Total</th>
                                                                                                @if($canManageVisit)
                                                                                                    <th
                                                                                                        class="px-3 py-2.5 text-right text-xs font-semibold text-gray-500">
                                                                                                        Actions</th>
                                                                                                @endif
                                                                                            </tr>
                                                                                        </thead>

                                                                                        <tbody class="divide-y divide-gray-100">
                                                                                            @foreach($repairWork->maintenanceParts as $maintenancePart)

                                                                                                <tr x-data="{ editingPart: false }">
                                                                                                    <td class="px-3 py-3">
                                                                                                        <p
                                                                                                            class="text-xs font-semibold text-gray-800">
                                                                                                            {{ $maintenancePart->part?->name ?? 'Unknown part' }}
                                                                                                        </p>
                                                                                                        @if($maintenancePart->part?->part_code)
                                                                                                            <p class="mt-0.5 text-xs text-gray-400">
                                                                                                                {{ $maintenancePart->part->part_code }}
                                                                                                            </p>
                                                                                                        @endif
                                                                                                    </td>

                                                                                                    <td
                                                                                                        class="whitespace-nowrap px-3 py-3 text-xs text-gray-600">
                                                                                                        {{ number_format($maintenancePart->quantity, 2) }}
                                                                                                        {{ $maintenancePart->part?->unit }}
                                                                                                    </td>

                                                                                                    <td
                                                                                                        class="whitespace-nowrap px-3 py-3 text-xs text-gray-600">
                                                                                                        {{ number_format($maintenancePart->unit_cost, 2) }}
                                                                                                    </td>

                                                                                                    <td
                                                                                                        class="whitespace-nowrap px-3 py-3 text-right text-xs font-semibold text-gray-800">
                                                                                                        {{ number_format($maintenancePart->total_cost, 2) }}
                                                                                                    </td>

                                                                                                    @if($canManageVisit)
                                                                                                                                                    <td
                                                                                                                                                        class="whitespace-nowrap px-3 py-3 text-right">
                                                                                                                                                        <div
                                                                                                                                                            class="flex items-center justify-end gap-2">
                                                                                                                                                            <button type="button"
                                                                                                                                                                @click="editingPart = !editingPart"
                                                                                                                                                                class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                                                                                                                                                                Edit
                                                                                                                                                            </button>

                                                                                                                                                            <form method="POST" action="{{ route('maintenance.parts.destroy', [
                                                                                                            $maintenance,
                                                                                                            $visit,
                                                                                                            $recordDefect,
                                                                                                            $repairWork,
                                                                                                            $maintenancePart
                                                                                                        ]) }}"
                                                                                                                                                                onsubmit="return confirm('Remove this spare part?')">
                                                                                                                                                                @csrf
                                                                                                                                                                @method('DELETE')

                                                                                                                                                                <button type="submit"
                                                                                                                                                                    class="text-xs font-semibold text-red-600 hover:text-red-800">
                                                                                                                                                                    Remove
                                                                                                                                                                </button>
                                                                                                                                                            </form>
                                                                                                                                                        </div>
                                                                                                                                                    </td>
                                                                                                    @endif

                                                                                                    {{-- EDIT PART FORM --}}
                                                                                                    @if($canManageVisit)
                                                                                                                                                    <td colspan="5" x-cloak x-show="editingPart"
                                                                                                                                                        class="bg-indigo-50/50 p-3">
                                                                                                                                                        <form method="POST" action="{{ route('maintenance.parts.update', [
                                                                                                            $maintenance,
                                                                                                            $visit,
                                                                                                            $recordDefect,
                                                                                                            $repairWork,
                                                                                                            $maintenancePart
                                                                                                        ]) }}"
                                                                                                                                                            class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                                                                                                                                                            @csrf
                                                                                                                                                            @method('PUT')

                                                                                                                                                            <div>
                                                                                                                                                                <label
                                                                                                                                                                    class="mb-1 block text-xs font-semibold text-gray-700">Spare
                                                                                                                                                                    Part</label>
                                                                                                                                                                <select name="part_id" required
                                                                                                                                                                    class="w-full rounded-lg border-gray-300 bg-white text-xs focus:border-indigo-500 focus:ring-indigo-500">
                                                                                                                                                                    @foreach($parts as $part)
                                                                                                                                                                        <option value="{{ $part->id }}"
                                                                                                                                                                            @selected($maintenancePart->part_id == $part->id)>
                                                                                                                                                                            {{ $part->name }}
                                                                                                                                                                        </option>
                                                                                                                                                                    @endforeach
                                                                                                                                                                </select>
                                                                                                                                                            </div>

                                                                                                                                                            <div>
                                                                                                                                                                <label
                                                                                                                                                                    class="mb-1 block text-xs font-semibold text-gray-700">Quantity</label>
                                                                                                                                                                <input type="number" name="quantity"
                                                                                                                                                                    min="0.01" step="0.01" required
                                                                                                                                                                    value="{{ $maintenancePart->quantity }}"
                                                                                                                                                                    class="w-full rounded-lg border-gray-300 text-xs focus:border-indigo-500 focus:ring-indigo-500">
                                                                                                                                                            </div>

                                                                                                                                                            <div>
                                                                                                                                                                <label
                                                                                                                                                                    class="mb-1 block text-xs font-semibold text-gray-700">Unit
                                                                                                                                                                    Cost</label>
                                                                                                                                                                <input type="number"
                                                                                                                                                                    name="unit_cost" min="0"
                                                                                                                                                                    step="0.01" required
                                                                                                                                                                    value="{{ $maintenancePart->unit_cost }}"
                                                                                                                                                                    class="w-full rounded-lg border-gray-300 text-xs focus:border-indigo-500 focus:ring-indigo-500">
                                                                                                                                                            </div>

                                                                                                                                                            <div
                                                                                                                                                                class="flex justify-end gap-2 sm:col-span-3">
                                                                                                                                                                <button type="button"
                                                                                                                                                                    @click="editingPart = false"
                                                                                                                                                                    class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs font-semibold text-gray-600">
                                                                                                                                                                    Cancel
                                                                                                                                                                </button>
                                                                                                                                                                <button type="submit"
                                                                                                                                                                    class="rounded-lg bg-indigo-600 px-3 py-2 text-xs font-semibold text-white hover:bg-indigo-700">
                                                                                                                                                                    Save Part
                                                                                                                                                                </button>
                                                                                                                                                            </div>
                                                                                                                                                        </form>
                                                                                                                                                    </td>
                                                                                                    @endif
                                                                                                </tr>

                                                                                            @endforeach
                                                                                        </tbody>
                                                                                    </table>
                                                                                </div>

                                                                            @else
                                                                                <div
                                                                                    class="mt-3 rounded-lg border border-dashed border-gray-200 bg-white px-4 py-4 text-center">
                                                                                    <p class="text-xs text-gray-500">
                                                                                        No spare parts recorded for this repair activity.
                                                                                    </p>
                                                                                </div>
                                                                            @endif


                                                                            {{-- ADD SPARE PART --}}
                                                                            @if($canManageVisit)

                                                                                                                    <div x-data="{
                                                                                                                                                                                                                                                                                                                                                                                                                                                            showForm: false,
                                                                                                                                                                                                                                                                                                                                                                                                                                                            open: false,
                                                                                                                                                                                                                                                                                                                                                                                                                                                            search: '',
                                                                                                                                                                                                                                                                                                                                                                                                                                                            selectedPartId: @js((string) old('part_id', request('new_part_id', ''))),
                                                                                                                                                                                                                                                                                                                                                                                                                                                            selectedName: '',
                                                                                                                                                                                                                                                                                                                                                                                                                                                            unitCost: @js((string) old('unit_cost', '')),

                                                                                                                                                                                                                                                                                                                                                                                                                                                            parts: @js($parts->map(fn($part) => [
                                                                                                                                                                                                                                                                                                                                                                                                                                                                'id' => $part->id,
                                                                                                                                                                                                                                                                                                                                                                                                                                                                'name' => $part->name,
                                                                                                                                                                                                                                                                                                                                                                                                                                                                'code' => $part->part_code,
                                                                                                                                                                                                                                                                                                                                                                                                                                                                'default_unit_cost' => $part->default_unit_cost,
                                                                                                                                                                                                                                                                                                                                                                                                                                                            ])->values()),

                                                                                                                                                                                                                                                                                                                                                                                                                                                            initializeSelectedPart() {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                if (!this.selectedPartId) return;

                                                                                                                                                                                                                                                                                                                                                                                                                                                                const part = this.parts.find(
                                                                                                                                                                                                                                                                                                                                                                                                                                                                    item => String(item.id) === String(this.selectedPartId)
                                                                                                                                                                                                                                                                                                                                                                                                                                                                );

                                                                                                                                                                                                                                                                                                                                                                                                                                                                if (!part) {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                    this.selectedPartId = '';
                                                                                                                                                                                                                                                                                                                                                                                                                                                                    return;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                }

                                                                                                                                                                                                                                                                                                                                                                                                                                                                this.selectedName = part.name +
                                                                                                                                                                                                                                                                                                                                                                                                                                                                    (part.code ? ' (' + part.code + ')' : '');

                                                                                                                                                                                                                                                                                                                                                                                                                                                                // Preserve old input after validation failure.
                                                                                                                                                                                                                                                                                                                                                                                                                                                                if (this.unitCost === '') {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                    this.unitCost = part.default_unit_cost ?? '0.00';
                                                                                                                                                                                                                                                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                                                                                                                                                                                                                                            },

                                                                                                                                                                                                                                                                                                                                                                                                                                                            get filteredParts() {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                const term = this.search.toLowerCase().trim();

                                                                                                                                                                                                                                                                                                                                                                                                                                                                if (!term) {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                    return this.parts;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                }

                                                                                                                                                                                                                                                                                                                                                                                                                                                                return this.parts.filter(part =>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                    part.name.toLowerCase().includes(term) ||
                                                                                                                                                                                                                                                                                                                                                                                                                                                                    (part.code && part.code.toLowerCase().includes(term))
                                                                                                                                                                                                                                                                                                                                                                                                                                                                );
                                                                                                                                                                                                                                                                                                                                                                                                                                                            },

                                                                                                                                                                                                                                                                                                                                                                                                                                                            updateCost() {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                const part = this.parts.find(
                                                                                                                                                                                                                                                                                                                                                                                                                                                                    item => String(item.id) === String(this.selectedPartId)
                                                                                                                                                                                                                                                                                                                                                                                                                                                                );

                                                                                                                                                                                                                                                                                                                                                                                                                                                                if (!part) {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                    this.unitCost = '';
                                                                                                                                                                                                                                                                                                                                                                                                                                                                    return;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                }

                                                                                                                                                                                                                                                                                                                                                                                                                                                                this.unitCost = part.default_unit_cost ?? '0.00';
                                                                                                                                                                                                                                                                                                                                                                                                                                                            },

                                                                                                                                                                                                                                                                                                                                                                                                                                                            selectPart(part) {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                this.selectedPartId = String(part.id);

                                                                                                                                                                                                                                                                                                                                                                                                                                                                this.selectedName = part.name +
                                                                                                                                                                                                                                                                                                                                                                                                                                                                    (part.code ? ' (' + part.code + ')' : '');

                                                                                                                                                                                                                                                                                                                                                                                                                                                                this.updateCost();

                                                                                                                                                                                                                                                                                                                                                                                                                                                                this.search = '';
                                                                                                                                                                                                                                                                                                                                                                                                                                                                this.open = false;
                                                                                                                                                                                                                                                                                                                                                                                                                                                            }
                                                                                                                                                                                                                                                                                                                                                                                                                                                        }"
                                                                                                                        x-init="initializeSelectedPart()"
                                                                                                                        @click.outside="open = false" class="mt-4">

                                                                                                                        {{-- Add Spare Part Button --}}
                                                                                                                        <button type="button" @click="showForm = !showForm"
                                                                                                                            class="inline-flex items-center gap-1.5 rounded-lg border border-indigo-200 bg-white px-3 py-2 text-xs font-semibold text-indigo-700 transition hover:bg-indigo-50">
                                                                                                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                                                                                                                stroke="currentColor" stroke-width="2">
                                                                                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                                                                                    d="M12 5v14m-7-7h14" />
                                                                                                                            </svg>

                                                                                                                            Add Spare Part
                                                                                                                        </button>

                                                                                                                        {{-- Add Spare Part Form --}}
                                                                                                                        <div x-cloak x-show="showForm" x-collapse
                                                                                                                            class="mt-3 rounded-lg border border-gray-200 bg-white p-4">

                                                                                                                            <form method="POST" action="{{ route('maintenance.parts.store', [
                                                                                    'maintenance' => $maintenance->id,
                                                                                    'visit' => $visit->id,
                                                                                    'recordDefect' => $recordDefect->id,
                                                                                    'repairWork' => $repairWork->id,
                                                                                ]) }}"
                                                                                                                                class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                                                                                                                @csrf

                                                                                                                                {{-- Spare Part Selection --}}
                                                                                                                                <div class="sm:col-span-2">

                                                                                                                                    <label
                                                                                                                                        class="mb-1.5 block text-xs font-semibold text-gray-700">
                                                                                                                                        Select Spare Part
                                                                                                                                    </label>

                                                                                                                                    {{-- Actual submitted value --}}
                                                                                                                                    <input type="hidden" name="part_id"
                                                                                                                                        :value="selectedPartId">

                                                                                                                                    {{-- Dropdown Trigger --}}
                                                                                                                                    <button type="button" @click="open = !open"
                                                                                                                                        class="flex w-full items-center justify-between rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-left text-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                                                                                                                                        <span class="truncate"
                                                                                                                                            :class="selectedName ? 'text-gray-900' : 'text-gray-400'"
                                                                                                                                            x-text="selectedName || 'Choose a spare part'"></span>

                                                                                                                                        <svg class="h-4 w-4 shrink-0 text-gray-400 transition-transform"
                                                                                                                                            :class="open ? 'rotate-180' : ''"
                                                                                                                                            fill="none" stroke="currentColor"
                                                                                                                                            viewBox="0 0 24 24">
                                                                                                                                            <path stroke-linecap="round"
                                                                                                                                                stroke-linejoin="round"
                                                                                                                                                stroke-width="2" d="m6 9 6 6 6-6" />
                                                                                                                                        </svg>
                                                                                                                                    </button>

                                                                                                                                    {{-- Dropdown Panel --}}
                                                                                                                                    <div x-cloak x-show="open" x-transition
                                                                                                                                        class="relative z-30 mt-1 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-lg">

                                                                                                                                        {{-- Search --}}
                                                                                                                                        <div class="border-b border-gray-100 p-2">
                                                                                                                                            <div class="relative">

                                                                                                                                                <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400"
                                                                                                                                                    fill="none"
                                                                                                                                                    stroke="currentColor"
                                                                                                                                                    viewBox="0 0 24 24">
                                                                                                                                                    <circle cx="11" cy="11" r="7"
                                                                                                                                                        stroke-width="2" />
                                                                                                                                                    <path d="m16 16 4 4"
                                                                                                                                                        stroke-width="2" />
                                                                                                                                                </svg>

                                                                                                                                                <input type="text" x-model="search"
                                                                                                                                                    @click.stop
                                                                                                                                                    placeholder="Search by part name or code..."
                                                                                                                                                    class="w-full rounded-md border-gray-200 py-2 pl-9 pr-3 text-sm focus:border-indigo-500 focus:ring-indigo-500">

                                                                                                                                            </div>
                                                                                                                                        </div>

                                                                                                                                        {{-- Search Results --}}
                                                                                                                                        <div class="max-h-48 overflow-y-auto py-1">

                                                                                                                                            <template x-for="part in filteredParts"
                                                                                                                                                :key="part.id">
                                                                                                                                                <button type="button"
                                                                                                                                                    @click="selectPart(part)"
                                                                                                                                                    class="block w-full px-3 py-2.5 text-left text-sm transition hover:bg-indigo-50"
                                                                                                                                                    :class="String(selectedPartId) === String(part.id) ? 'bg-indigo-50' : ''">
                                                                                                                                                    <span
                                                                                                                                                        class="block font-medium text-gray-800"
                                                                                                                                                        x-text="part.name"></span>

                                                                                                                                                    <span x-show="part.code"
                                                                                                                                                        class="mt-0.5 block text-xs text-gray-500"
                                                                                                                                                        x-text="part.code"></span>
                                                                                                                                                </button>
                                                                                                                                            </template>

                                                                                                                                            <div x-show="filteredParts.length === 0"
                                                                                                                                                class="px-3 py-5 text-center text-sm text-gray-500">
                                                                                                                                                No spare parts found.
                                                                                                                                            </div>

                                                                                                                                        </div>

                                                                                                                                        {{-- Permanent Create New Part Link --}}
                                                                                                                                        <div
                                                                                                                                            class="border-t border-gray-200 bg-gray-50 p-2">

                                                                                                                                            <a href="{{ route('parts.create', [
                                                                                    'return_to' => request()->getRequestUri()
                                                                                ]) }}"
                                                                                                                                                class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-semibold text-indigo-600 transition hover:bg-indigo-50 hover:text-indigo-700">
                                                                                                                                                <svg class="h-4 w-4" fill="none"
                                                                                                                                                    stroke="currentColor"
                                                                                                                                                    viewBox="0 0 24 24">
                                                                                                                                                    <path stroke-linecap="round"
                                                                                                                                                        stroke-linejoin="round"
                                                                                                                                                        stroke-width="2"
                                                                                                                                                        d="M12 5v14m-7-7h14" />
                                                                                                                                                </svg>

                                                                                                                                                Create New Spare Part
                                                                                                                                            </a>

                                                                                                                                        </div>

                                                                                                                                    </div>

                                                                                                                                    @error('part_id')
                                                                                                                                        <p class="mt-1 text-xs text-red-600">
                                                                                                                                            {{ $message }}
                                                                                                                                        </p>
                                                                                                                                    @enderror

                                                                                                                                </div>

                                                                                                                                {{-- Quantity --}}
                                                                                                                                <div>
                                                                                                                                    <label
                                                                                                                                        class="mb-1.5 block text-xs font-semibold text-gray-700">
                                                                                                                                        Quantity
                                                                                                                                    </label>

                                                                                                                                    <input type="number" name="quantity" min="0.001"
                                                                                                                                        step="0.001"
                                                                                                                                        value="{{ old('quantity', '') }}" required
                                                                                                                                        placeholder="Enter quantity"
                                                                                                                                        class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">

                                                                                                                                    @error('quantity')
                                                                                                                                        <p class="mt-1 text-xs text-red-600">
                                                                                                                                            {{ $message }}
                                                                                                                                        </p>
                                                                                                                                    @enderror
                                                                                                                                </div>

                                                                                                                                {{-- Unit Cost --}}
                                                                                                                                <div>
                                                                                                                                    <label
                                                                                                                                        class="mb-1.5 block text-xs font-semibold text-gray-700">
                                                                                                                                        Unit Cost
                                                                                                                                    </label>

                                                                                                                                    <input type="number" name="unit_cost" min="0"
                                                                                                                                        step="0.01" required x-model="unitCost"
                                                                                                                                        placeholder="0.00"
                                                                                                                                        class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">

                                                                                                                                    @error('unit_cost')
                                                                                                                                        <p class="mt-1 text-xs text-red-600">
                                                                                                                                            {{ $message }}
                                                                                                                                        </p>
                                                                                                                                    @enderror
                                                                                                                                </div>

                                                                                                                                {{-- Form Actions --}}
                                                                                                                                <div class="flex justify-end gap-2 sm:col-span-2">

                                                                                                                                    <button type="button"
                                                                                                                                        @click="showForm = false; open = false"
                                                                                                                                        class="rounded-lg border border-gray-200 px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-50">
                                                                                                                                        Cancel
                                                                                                                                    </button>

                                                                                                                                    <button type="submit"
                                                                                                                                        :disabled="!selectedPartId"
                                                                                                                                        class="rounded-lg bg-indigo-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-50">
                                                                                                                                        Add Part
                                                                                                                                    </button>

                                                                                                                                </div>

                                                                                                                            </form>

                                                                                                                        </div>

                                                                                                                    </div>

                                                                            @endif

                                                                        </div>
                                                                    </div>

                                                                @empty
                                                                    <div
                                                                        class="rounded-lg border border-dashed border-gray-300 bg-white px-4 py-5 text-center">
                                                                        <p class="text-sm font-medium text-gray-700">
                                                                            No repair activities yet
                                                                        </p>
                                                                        <p class="mt-1 text-xs text-gray-500">
                                                                            Record the work performed to address this defect.
                                                                        </p>
                                                                    </div>
                                                                @endforelse


                                                                {{-- ADD REPAIR ACTIVITY --}}
                                                                @if($canManageVisit)
                                                                                                    <div x-data="{ showForm: false }"
                                                                                                        class="mt-4 rounded-xl border border-dashed border-indigo-200 bg-white p-4">
                                                                                                        <div
                                                                                                            class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                                                                                            <div>
                                                                                                                <p class="text-sm font-semibold text-gray-800">
                                                                                                                    Record Repair Activity
                                                                                                                </p>
                                                                                                                <p class="mt-1 text-xs text-gray-500">
                                                                                                                    Describe the work performed to fix this defect.
                                                                                                                </p>
                                                                                                            </div>

                                                                                                            <button type="button" @click="showForm = !showForm"
                                                                                                                class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-indigo-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-indigo-700">
                                                                                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                                                                                                    stroke="currentColor" stroke-width="2">
                                                                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                                                                        d="M12 5v14m-7-7h14" />
                                                                                                                </svg>
                                                                                                                <span
                                                                                                                    x-text="showForm ? 'Close Form' : 'Add Activity'"></span>
                                                                                                            </button>
                                                                                                        </div>

                                                                                                        <div x-cloak x-show="showForm" x-collapse
                                                                                                            class="mt-4 border-t border-gray-100 pt-4">
                                                                                                            <form method="POST" action="{{ route('maintenance.repair-works.store', [
                                                                        $maintenance,
                                                                        $visit,
                                                                        $recordDefect
                                                                    ]) }}" class="space-y-3">
                                                                                                                @csrf

                                                                                                                <div>
                                                                                                                    <label
                                                                                                                        class="mb-1.5 block text-xs font-semibold text-gray-700">
                                                                                                                        Work Description <span class="text-red-500">*</span>
                                                                                                                    </label>
                                                                                                                    <textarea name="work_description" rows="3" required
                                                                                                                        placeholder="Describe the repair work performed..."
                                                                                                                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500">{{ old('work_description') }}</textarea>
                                                                                                                </div>

                                                                                                                <div>
                                                                                                                    <label
                                                                                                                        class="mb-1.5 block text-xs font-semibold text-gray-700">
                                                                                                                        Remarks <span
                                                                                                                            class="font-normal text-gray-400">(Optional)</span>
                                                                                                                    </label>
                                                                                                                    <textarea name="remarks" rows="2"
                                                                                                                        placeholder="Additional notes..."
                                                                                                                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500">{{ old('remarks') }}</textarea>
                                                                                                                </div>

                                                                                                                <div class="flex justify-end">
                                                                                                                    <button type="submit"
                                                                                                                        class="rounded-lg bg-indigo-600 px-5 py-2.5 text-xs font-semibold text-white transition hover:bg-indigo-700">
                                                                                                                        Save Repair Activity
                                                                                                                    </button>
                                                                                                                </div>
                                                                                                            </form>
                                                                                                        </div>
                                                                                                    </div>
                                                                @endif

                                                            </div>
                                                        </div>

                                                    @endforeach
                                                </div>
                                            @endif

                                        </div>
                                    </div>
                                </div>

                            </article>

                        @empty

                            {{-- NO VISITS --}}
                            <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 px-5 py-10 text-center">
                                <div
                                    class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-white text-gray-400 shadow-sm">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M8 7V3m8 4V3M4 11h16M5 5h14a1 1 0 0 1 1 1v14H4V6a1 1 0 0 1 1-1Z" />
                                    </svg>
                                </div>

                                <h4 class="mt-3 text-sm font-semibold text-gray-800">
                                    No maintenance visits yet
                                </h4>
                                <p class="mx-auto mt-1 max-w-sm text-sm leading-relaxed text-gray-500">
                                    Register the first visit to begin recording inspections, repair activities, and spare parts.
                                </p>
                            </div>

                        @endforelse

                    </div>
                </div>

            </div>
        </section>

        {{--COST SUMMARY --}}

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

            {{-- Cost breakdown --}}

            <div class="rounded-xl bg-white p-6 shadow-sm">

                <h2 class="text-lg font-semibold text-gray-900">
                    Cost Summary
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Total maintenance cost breakdown.
                </p>


                <div class="mt-6 space-y-4">

                    {{-- Parts --}}

                    <div class="flex items-center justify-between">

                        <span class="text-gray-500">
                            Parts
                        </span>

                        <span class="font-medium text-gray-900">
                            ${{ number_format($partsTotal, 2) }}
                        </span>

                    </div>

                    {{-- Labor --}}

                    <div class="flex items-center justify-between">

                        <span class="text-gray-500">
                            Labor
                        </span>

                        <span class="font-medium text-gray-900">
                            ${{ number_format($laborCost, 2) }}
                        </span>

                    </div>

                    {{-- Other --}}

                    <div class="flex items-center justify-between">

                        <span class="text-gray-500">
                            Other
                        </span>

                        <span class="font-medium text-gray-900">
                            ${{ number_format($otherCost, 2) }}
                        </span>

                    </div>

                    {{-- Total --}}

                    <div class="flex items-center justify-between border-t border-gray-200 pt-4">

                        <span class="font-semibold text-gray-900">
                            Total
                        </span>

                        <span class="text-2xl font-bold text-gray-900">
                            ${{ number_format($totalCost, 2) }}
                        </span>

                    </div>

                </div>

            </div>

            {{-- Service kilometers --}}

            <div class="rounded-xl bg-white p-6 shadow-sm">

                <h2 class="text-lg font-semibold text-gray-900">
                    Service Information
                </h2>

                <div class="mt-5 space-y-4">

                    <div>

                        <p class="text-sm text-gray-500">
                            Vehicle Current Kilometers
                        </p>

                        <p class="mt-1 text-xl font-semibold text-gray-900">

                            {{ $maintenance->vehicle?->current_kilometers !== null
        ? number_format($maintenance->vehicle->current_kilometers) . ' km'
        : '-'
                                                                                                                                                                                                                }}

                        </p>

                    </div>

                </div>

            </div>

        </div>

        {{-- GENERAL ATTACHMENTS --}}
        @php
            $generalAttachments = $maintenance->attachments
                ->whereNull('maintenance_visit_id');
        @endphp

        @if($generalAttachments->isNotEmpty())
            <div class="mt-6 rounded-xl border border-gray-200 bg-white p-5">

                <h3 class="text-sm font-semibold text-gray-900">
                    General Attachments
                </h3>

                <p class="mt-1 text-xs text-gray-500">
                    Previously uploaded files not assigned to a specific visit.
                </p>

                <div class="mt-4 space-y-3">
                    @foreach($generalAttachments as $attachment)
                        <div class="flex items-center justify-between gap-3 border-b border-gray-100 pb-3 last:border-0 last:pb-0">

                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-gray-800">
                                    {{ $attachment->file_name }}
                                </p>

                                <p class="mt-1 text-xs text-gray-500">
                                    {{ str($attachment->attachment_type ?? 'other')->title() }}
                                    ·
                                    {{ number_format(($attachment->file_size ?? 0) / 1024, 1) }} KB
                                </p>
                            </div>

                            <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank" rel="noopener noreferrer"
                                class="shrink-0 text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                                View
                            </a>

                        </div>
                    @endforeach
                </div>

            </div>
        @endif

    </div>


    {{--ACTIVITY HISTORY --}}

    <div class="rounded-xl bg-white p-6 shadow-sm mt-5">

        <h2 class="text-lg font-semibold text-gray-900">
            Activity History
        </h2>

        <p class="mt-1 text-sm text-gray-500">
            Record of changes made to this maintenance.
        </p>


        <div class="mt-6 space-y-5">

            @forelse($maintenance->audits as $audit)

                <div class="flex gap-4">

                    {{-- Icon / timeline --}}

                    <div class="flex flex-col items-center">

                        <div
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg
                                                                                                                                                                                                                                                                                                                                                                                                            @if($audit->action === 'created')
                                                                                                                                                                                                                                                                                                                                                                                                                bg-green-50 text-green-700
                                                                                                                                                                                                                                                                                                                                                                                                            @elseif($audit->action === 'status_changed')
                                                                                                                                                                                                                                                                                                                                                                                                                bg-blue-50 text-blue-700
                                                                                                                                                                                                                                                                                                                                                                                                            @else
                                                                                                                                                                                                                                                                                                                                                                                                                bg-gray-100 text-gray-600
                                                                                                                                                                                                                                                                                                                                                                                                            @endif">

                            @if($audit->action === 'created')

                                {{-- Created: Plus icon --}}
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m-7-7h14" />
                                </svg>

                            @elseif($audit->action === 'status_changed')

                                {{-- Status changed: Refresh icon --}}
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h5M20 20v-5h-5" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M5.64 15A7 7 0 0018.36 18M18.36 9A7 7 0 005.64 6" />
                                </svg>

                            @else

                                {{-- Updated: Pencil icon --}}
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16.862 4.487l2.651 2.651M4 20l4.5-1 11.2-11.2a1.875 1.875 0 00-2.65-2.65L5.85 16.35 4 20z" />
                                </svg>

                            @endif

                        </div>


                        @unless($loop->last)

                            <div class="mt-2 h-full w-px bg-gray-200"></div>

                        @endunless

                    </div>


                    {{-- Audit information --}}

                    <div class="pb-5">

                        <p class="font-medium text-gray-900">

                            {{ str($audit->action)
                ->replace('_', ' ')
                ->title()
                                                                                                                                                                                                                                                                                                                                                                                                                                                                    }}

                        </p>


                        {{-- Status transition --}}

                        @if($audit->old_status && $audit->new_status)

                                <p class="mt-1 text-sm text-gray-600">

                                    {{ str($audit->old_status)
                            ->replace('_', ' ')
                            ->title()
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        }}

                                    →

                                    {{ str($audit->new_status)
                            ->replace('_', ' ')
                            ->title()
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        }}

                                </p>

                        @endif


                        {{-- Description --}}

                        @if($audit->description)

                            <p class="mt-1 text-sm text-gray-500">
                                {{ $audit->description }}
                            </p>

                        @endif


                        {{-- User and time --}}

                        <p class="mt-2 text-xs text-gray-400">

                            {{ $audit->user?->name ?? 'System' }}

                            ·

                            {{ $audit->created_at
                ? $audit->created_at->format('d M Y, H:i')
                : '-'
                                                                                                                                                                                                                                                                                                                                                                                                                                                                    }}

                        </p>

                    </div>

                </div>

            @empty

                <p class="text-sm text-gray-500">
                    No activity history available.
                </p>

            @endforelse

        </div>

    </div>


    </div>

@endsection