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
            fn ($part) => (float) $part->total_cost
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
            && ! $isInProgress
            && ! $isCompleted
            && ! $isCancelled;
    @endphp



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

                    <a
                        href="{{ route('maintenance.edit', $maintenance) }}"
                        class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
                    >
                        Edit
                    </a>

                @endif

                {{-- Delete --}}

                @if($canDelete)

                    <form
                        method="POST"
                        action="{{ route('maintenance.destroy', $maintenance) }}"
                        onsubmit="return confirm('Are you sure you want to delete this maintenance record?')"
                    >

                        @csrf

                        @method('DELETE')

                        <button
                            type="submit"
                            class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700"
                        >
                            Delete
                        </button>

                    </form>

                @endif

                {{-- Back --}}

                <a
                    href="{{ route('maintenance.index') }}"
                    class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800"
                >
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

                    <div
                        @class([
                            'rounded-full px-4 py-2 text-sm font-medium',

                            'bg-gray-900 text-white'
                                => $isCurrent,

                            'bg-green-100 text-green-700'
                                => $isPastStep,

                            'bg-gray-100 text-gray-500'
                                => ! $isCurrent && ! $isPastStep,
                        ])
                    >

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

                    <p class="mt-1 font-medium text-gray-900">

                        {{ $maintenanceTypeValue
                            ? str($maintenanceTypeValue)
                                ->replace('_', ' ')
                                ->title()
                            : '-'
                        }}

                    </p>

                </div>

                {{-- Status --}}

                <div>

                    <p class="text-sm text-gray-500">
                        Status
                    </p>

                    <p class="mt-1 font-medium text-gray-900">

                        {{ $maintenanceStatusValue
                            ? str($maintenanceStatusValue)
                                ->replace('_', ' ')
                                ->title()
                            : '-'
                        }}

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

                    <div>

                        <p class="text-sm text-gray-500">
                            Kilometers at Service
                        </p>

                        <p class="mt-1 text-xl font-semibold text-gray-900">

                            {{ $maintenance->service_kilometers !== null
                                ? number_format($maintenance->service_kilometers) . ' km'
                                : '-'
                            }}

                        </p>

                    </div>

                </div>

            </div>

        </div>

        {{--PARTS USED --}}

        <div class="rounded-xl bg-white p-6 shadow-sm">

            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">

                <div>

                    <h2 class="text-lg font-semibold text-gray-900">
                        Parts Used
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Parts consumed during this maintenance.
                    </p>

                </div>

                <div class="text-sm text-gray-500">

                    Parts Total:

                    <span class="font-semibold text-gray-900">
                        ${{ number_format($partsTotal, 2) }}
                    </span>

                </div>

            </div>

            {{-- Parts table --}}

            <div class="mt-6 overflow-x-auto">

                <table class="min-w-full text-sm">

                    <thead>

                        <tr class="border-b border-gray-200 text-left text-gray-500">

                            <th class="px-3 py-3">
                                Part
                            </th>

                            <th class="px-3 py-3">
                                Quantity
                            </th>

                            <th class="px-3 py-3">
                                Unit Cost
                            </th>

                            <th class="px-3 py-3 text-right">
                                Total
                            </th>

                            @if($canUpdate)
                                <th class="px-3 py-3 text-right">
                                    Action
                                </th>
                            @endif

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($maintenance->maintenanceParts as $maintenancePart)

                            <tr class="border-b border-gray-100">

                                {{-- Part --}}

                                <td class="px-3 py-4">

                                    <div class="font-medium text-gray-900">

                                        {{ $maintenancePart->part?->name ?? '-' }}

                                    </div>

                                    @if($maintenancePart->part?->part_code)

                                        <div class="mt-1 text-xs text-gray-500">

                                            {{ $maintenancePart->part->part_code }}

                                        </div>

                                    @endif

                                </td>

                                {{-- Quantity --}}

                                <td class="px-3 py-4">

                                    {{ $maintenancePart->quantity }}

                                    @if($maintenancePart->part?->unit)
                                        {{ $maintenancePart->part->unit }}
                                    @endif

                                </td>

                                {{-- Unit Cost --}}

                                <td class="px-3 py-4">

                                    ${{ number_format(
                                        (float) $maintenancePart->unit_cost,
                                        2
                                    ) }}

                                </td>

                                {{-- Total --}}

                                <td class="px-3 py-4 text-right font-medium">

                                    ${{ number_format(
                                        (float) $maintenancePart->total_cost,
                                        2
                                    ) }}

                                </td>

                                {{-- Delete --}}

                                @if($canUpdate)

                                    <td class="px-3 py-4 text-right">

                                        <form
                                            method="POST"
                                            action="{{ route(
                                                'maintenance.parts.destroy',
                                                [
                                                    'maintenance' => $maintenance,
                                                    'maintenancePart' => $maintenancePart,
                                                ]
                                            ) }}"
                                        >

                                            @csrf

                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="text-sm text-red-600 hover:text-red-800"
                                                onclick="return confirm('Remove this part?')"
                                            >
                                                Remove
                                            </button>

                                        </form>

                                    </td>

                                @endif

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="{{ $canUpdate ? 5 : 4 }}"
                                    class="px-3 py-8 text-center text-sm text-gray-500"
                                >
                                    No parts have been recorded.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- ADD PART --}}

            @if($canUpdate && !$isCompleted && !$isCancelled)

                <div class="mt-6 border-t border-gray-200 pt-6">

                    <h3 class="font-medium text-gray-900">
                        Add Part
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        Add a part used during this maintenance.
                    </p>


                    <form
                        method="POST"
                        action="{{ route('maintenance.parts.store', $maintenance) }}"
                        x-data="{
                            unitCost: '',

                            parts: {
                                @foreach($parts as $part)
                                    {{ $part->id }}:
                                        {{ (float) $part->default_unit_cost }},
                                @endforeach
                            },

                            updateCost() {
                                const partId = this.$refs.part.value;

                                this.unitCost =
                                    this.parts[partId] ?? '';
                            }
                        }"
                        class="mt-5 grid grid-cols-1 gap-4 rounded-xl bg-gray-50 p-4 md:grid-cols-4"
                    >

                        @csrf


                        {{-- Part --}}

                        <div>

                            <label
                                for="part_id"
                                class="block text-sm font-medium text-gray-700"
                            >
                                Part
                            </label>

                            <select
                                id="part_id"
                                name="part_id"
                                x-ref="part"
                                @change="updateCost()"
                                class="mt-1 block w-full rounded-lg border-gray-300"
                                required
                            >

                                <option value="">
                                    Select part
                                </option>

                                @foreach($parts as $part)

                                    <option value="{{ $part->id }}">

                                        {{ $part->part_code }}
                                        —
                                        {{ $part->name }}

                                    </option>

                                @endforeach

                            </select>

                            @error('part_id')

                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>

                            @enderror

                        </div>


                        {{-- Quantity --}}

                        <div>

                            <label
                                for="quantity"
                                class="block text-sm font-medium text-gray-700"
                            >
                                Quantity
                            </label>

                            <input
                                type="number"
                                name="quantity"
                                id="quantity"
                                min="1"
                                step="1"
                                value="{{ old('quantity', 1) }}"
                                class="mt-1 block w-full rounded-lg border-gray-300"
                                required
                            >

                            @error('quantity')

                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>

                            @enderror

                        </div>


                        {{-- Unit Cost --}}

                        <div>

                            <label
                                for="unit_cost"
                                class="block text-sm font-medium text-gray-700"
                            >
                                Unit Cost
                            </label>

                            <input
                                type="number"
                                name="unit_cost"
                                id="unit_cost"
                                x-model="unitCost"
                                min="0"
                                step="0.01"
                                class="mt-1 block w-full rounded-lg border-gray-300"
                                required
                            >

                            @error('unit_cost')

                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>

                            @enderror

                        </div>


                        {{-- Submit --}}

                        <div class="flex items-end">

                            <button
                                type="submit"
                                class="w-full rounded-lg bg-gray-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-gray-800"
                            >
                                Add Part
                            </button>

                        </div>

                    </form>

                </div>

            @endif

        </div>


        {{--ATTACHMENTS --}}

        <div class="rounded-xl bg-white p-6 shadow-sm">

            <div>

                <h2 class="text-lg font-semibold text-gray-900">
                    Attachments
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Photos, invoices, and maintenance documents.
                </p>

            </div>


            {{-- Upload --}}

            @if($canUpdate && !$isCompleted && !$isCancelled)

                <form
                    method="POST"
                    action="{{ route(
                        'maintenance.attachments.store',
                        $maintenance
                    ) }}"
                    enctype="multipart/form-data"
                    class="mt-6 rounded-xl bg-gray-50 p-4"
                >

                    @csrf


                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">


                        {{-- File --}}

                        <div class="md:col-span-2">

                            <label
                                for="file"
                                class="block text-sm font-medium text-gray-700"
                            >
                                File
                            </label>

                            <input
                                type="file"
                                name="file"
                                id="file"
                                accept=".jpg,.jpeg,.png,.webp,.pdf"
                                class="mt-1 block w-full rounded-lg border border-gray-300 bg-white text-sm"
                                required
                            >

                            <p class="mt-1 text-xs text-gray-500">
                                JPG, JPEG, PNG, WebP, or PDF. Maximum 10 MB.
                            </p>

                            @error('file')

                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>

                            @enderror

                        </div>


                        {{-- Attachment Type --}}

                        <div>

                            <label
                                for="attachment_type"
                                class="block text-sm font-medium text-gray-700"
                            >
                                Attachment Type
                            </label>

                            <select
                                name="attachment_type"
                                id="attachment_type"
                                class="mt-1 block w-full rounded-lg border-gray-300"
                            >

                                <option value="before">
                                    Before Repair
                                </option>

                                <option value="after">
                                    After Repair
                                </option>

                                <option value="invoice">
                                    Invoice / Receipt
                                </option>

                                <option value="report">
                                    Maintenance Report
                                </option>

                                <option value="other" selected>
                                    Other
                                </option>

                            </select>

                            @error('attachment_type')

                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>

                            @enderror

                        </div>

                    </div>


                    <div class="mt-4">

                        <button
                            type="submit"
                            class="rounded-lg bg-gray-900 px-5 py-2.5 text-sm font-medium text-white hover:bg-gray-800"
                        >
                            Upload Attachment
                        </button>

                    </div>

                </form>

            @endif


            {{-- Attachment list --}}

            <div class="mt-6">

                @forelse($maintenance->attachments as $attachment)

                    <div class="flex flex-col gap-4 border-b border-gray-100 py-4 sm:flex-row sm:items-center sm:justify-between">


                        <div class="flex items-center gap-3">


                            {{-- Preview --}}

                            @if(
                                $attachment->file_type
                                && str_starts_with(
                                    $attachment->file_type,
                                    'image/'
                                )
                            )

                                <img
                                    src="{{ asset(
                                        'storage/' . $attachment->file_path
                                    ) }}"
                                    alt="{{ $attachment->file_name }}"
                                    class="h-16 w-16 rounded-lg object-cover"
                                >

                            @else

                                <div class="flex h-16 w-16 items-center justify-center rounded-lg bg-gray-100 text-2xl">
                                    📄
                                </div>

                            @endif


                            {{-- File information --}}

                            <div>

                                <a
                                    href="{{ asset(
                                        'storage/' . $attachment->file_path
                                    ) }}"
                                    target="_blank"
                                    class="font-medium text-gray-900 hover:underline"
                                >
                                    {{ $attachment->file_name }}
                                </a>


                                <div class="mt-1 flex flex-wrap gap-2 text-xs text-gray-500">

                                    <span>
                                        {{ str($attachment->attachment_type ?? 'other')
                                            ->replace('_', ' ')
                                            ->title()
                                        }}
                                    </span>

                                    <span>
                                        •
                                    </span>

                                    <span>

                                        {{ number_format(
                                            ((int) ($attachment->file_size ?? 0)) / 1024,
                                            1
                                        ) }}
                                        KB

                                    </span>

                                </div>

                            </div>

                        </div>


                        {{-- Delete --}}

                        @if($canUpdate && !$isCompleted && !$isCancelled)

                            <form
                                method="POST"
                                action="{{ route(
                                    'maintenance.attachments.destroy',
                                    [
                                        'maintenance' => $maintenance,
                                        'attachment' => $attachment,
                                    ]
                                ) }}"
                            >

                                @csrf

                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="text-sm text-red-600 hover:text-red-800"
                                    onclick="return confirm('Delete this attachment?')"
                                >
                                    Delete
                                </button>

                            </form>

                        @endif

                    </div>

                @empty

                    <p class="py-6 text-center text-sm text-gray-500">
                        No attachments yet.
                    </p>

                @endforelse

            </div>

        </div>


        {{--ACTIVITY HISTORY --}}

        <div class="rounded-xl bg-white p-6 shadow-sm">

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

                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-gray-100">

                                @if($audit->action === 'created')

                                    ➕

                                @elseif($audit->action === 'status_changed')

                                    🔄

                                @else

                                    📝

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