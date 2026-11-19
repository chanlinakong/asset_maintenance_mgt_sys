@use('Illuminate\Support\Facades\Storage')

@extends('layouts.app')

@section('title', 'Vehicle Details')

@section('content')

    <div class="space-y-6">

        @if(session('success'))
            <div class="rounded-lg bg-green-50 px-4 py-3 text-green-700">
                {{ session('success') }}
            </div>
        @endif


        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>

                <h1 class="text-2xl font-bold">
                    {{ $vehicle->name }}
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    {{ $vehicle->vehicle_code }}
                </p>

            </div>


            <div class="flex gap-2">

                @if(auth()->user()->role->value === 'admin')
                    <a href="{{ route('vehicles.edit', $vehicle) }}"
                        class="rounded-lg border border-gray-300 px-4 py-2 text-gray-700 transition-colors hover:border-gray-400 hover:bg-gray-200 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Edit
                    </a>
                @endif

                <a href="{{ route('vehicles.index') }}"
                    class="rounded-lg bg-gray-900 px-4 py-2 text-white transition-colors hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2">
                    Back
                </a>

            </div>

        </div>


        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

            <div class="rounded-xl bg-white p-6 shadow-sm lg:col-span-2">

                <h2 class="mb-5 font-semibold">
                    Vehicle Information
                </h2>


                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                    <div>
                        <p class="text-sm text-gray-500">
                            Vehicle Code
                        </p>

                        <p class="font-medium">
                            {{ $vehicle->vehicle_code }}
                        </p>
                    </div>


                    <div>
                        <p class="text-sm text-gray-500">
                            Type
                        </p>

                        <p class="font-medium">
                            {{ $vehicle->type }}
                        </p>
                    </div>


                    <div>
                        <p class="text-sm text-gray-500">
                            Brand
                        </p>

                        <p class="font-medium">
                            {{ $vehicle->brand ?? '-' }}
                        </p>
                    </div>


                    <div>
                        <p class="text-sm text-gray-500">
                            Model
                        </p>

                        <p class="font-medium">
                            {{ $vehicle->model ?? '-' }}
                        </p>
                    </div>


                    <div>
                        <p class="text-sm text-gray-500">
                            Registration Number
                        </p>

                        <p class="font-medium">
                            {{ $vehicle->registration_number ?? '-' }}
                        </p>
                    </div>


                    <div>
                        <p class="text-sm text-gray-500">
                            Status
                        </p>

                        <span @class([
                            'inline-flex rounded-full px-3 py-1 text-xs font-semibold',

                            'bg-green-100 text-green-700' =>
                                $vehicle->status->value === 'active',

                            'bg-yellow-100 text-yellow-700' =>
                                $vehicle->status->value === 'maintenance',

                            'bg-red-100 text-red-700' =>
                                $vehicle->status->value === 'out_of_service',
                        ])>
                            {{ str($vehicle->status->value)
        ->replace('_', ' ')
        ->title() }}
                        </span>
                    </div>


                    <div>
                        <p class="text-sm text-gray-500">
                            Current Kilometers
                        </p>

                        <p class="font-medium">
                            {{ $vehicle->current_kilometers ?? '-' }}
                        </p>
                    </div>


                    <div>
                        <p class="text-sm text-gray-500">
                            Purchase Date
                        </p>

                        <p class="font-medium">
                            {{ $vehicle->purchase_date
        ? $vehicle->purchase_date->format('d M Y')
        : '-' }}
                        </p>
                    </div>

                </div>

            </div>


            <div class="rounded-xl bg-white p-6 shadow-sm">

                <h2 class="mb-4 font-semibold">
                    Notes
                </h2>

                <p class="text-sm text-gray-600">
                    {{ $vehicle->notes ?: 'No notes available.' }}
                </p>

            </div>

        </div>

        {{-- Vehicle Attachments --}}

        {{-- Vehicle Attachments --}}
        <div x-data="vehicleAttachmentPreviews(
                    @js($vehicle->attachments->map(fn($attachment) => [
                        'id' => $attachment->id,
                        'name' => $attachment->original_name,
                        'size' => $attachment->file_size,
                        'extension' => strtolower(
                            pathinfo($attachment->original_name, PATHINFO_EXTENSION)
                        ),
                        'url' => Storage::disk('public')->url($attachment->file_path),
                        'preview' => null,
                    ])->values())
                )" x-init="initialize()" class="mt-6 rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            {{-- Header --}}
            <div class="mb-4 flex items-center justify-between gap-3">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">
                        Vehicle Attachments
                    </h3>

                    <p class="text-sm text-gray-500">
                        Documents and pictures associated with this vehicle.
                    </p>
                </div>

                <span class="shrink-0 rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700"
                    x-text="files.length + ' Files'"></span>
            </div>

            {{-- Attachment List --}}
            <div>
                <template x-for="attachment in visibleFiles" :key="attachment.id">
                    <div class="flex items-center gap-3 border-t border-gray-100 py-3">

                        {{-- Thumbnail --}}
                        <a :href="attachment.url" target="_blank" rel="noopener noreferrer"
                            class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-lg border border-gray-200 bg-gray-50"
                            :title="'View ' + attachment.name">
                            {{-- Image or PDF preview --}}
                            <template x-if="attachment.preview">
                                <img :src="attachment.preview" :alt="attachment.name" class="h-full w-full object-cover">
                            </template>

                            {{-- Fallback document icon --}}
                            <template x-if="!attachment.preview">
                                <div class="flex flex-col items-center justify-center gap-1">
                                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                                            d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8zM14 2v6h6M8 13h8M8 17h8" />
                                    </svg>

                                    <span class="text-[9px] font-semibold uppercase text-gray-500"
                                        x-text="attachment.extension"></span>
                                </div>
                            </template>
                        </a>

                        {{-- File Information --}}
                        <div class="min-w-0 flex-1">
                            <a :href="attachment.url" target="_blank" rel="noopener noreferrer"
                                class="block truncate text-sm font-medium text-gray-800 hover:text-blue-600"
                                x-text="attachment.name" :title="attachment.name"></a>

                            <p class="mt-1 text-xs text-gray-500">
                                <span x-text="(attachment.size / 1024).toFixed(1) + ' KB'"></span>
                                <span class="mx-1">·</span>
                                <span class="uppercase" x-text="attachment.extension"></span>
                            </p>
                        </div>

                        {{-- View Button --}}
                        <a :href="attachment.url" target="_blank" rel="noopener noreferrer"
                            :aria-label="'View ' + attachment.name"
                            class="shrink-0 rounded-lg p-2 text-gray-400 transition hover:bg-gray-100 hover:text-blue-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.5 12s3.5-7 9.5-7 9.5 7 9.5 7-3.5 7-9.5 7-9.5-7-9.5-7z" />
                            </svg>
                        </a>

                    </div>
                </template>
            </div>

            {{-- Empty State --}}
            <template x-if="files.length === 0">
                <div class="border-t border-gray-100 py-8 text-center">
                    <svg class="mx-auto mb-2 h-8 w-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8zM14 2v6h6" />
                    </svg>

                    <p class="text-sm text-gray-500">
                        No attachments uploaded for this vehicle.
                    </p>
                </div>
            </template>

            {{-- Show More / Show Less --}}
            <div x-show="files.length > 5"
                class="mt-3 flex items-center justify-center gap-3 border-t border-gray-100 pt-3">
                {{-- Show More --}}
                <button type="button" x-show="hasMore" @click="loadMore()" class="inline-flex items-center gap-2 rounded-lg px-4 py-2
                   text-sm font-medium text-blue-600
                   transition hover:bg-blue-50">
                    Show more

                    <span class="text-xs text-gray-400" x-text="'(' + (files.length - visibleCount) + ' remaining)'"></span>

                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                {{-- Show Less --}}
                <button type="button" x-show="visibleCount > 5" @click="visibleCount = 5" class="inline-flex items-center gap-2 rounded-lg px-4 py-2
                   text-sm font-medium text-gray-600
                   transition hover:bg-gray-100">
                    Show less

                    <svg class="h-4 w-4 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
            </div>
        </div>


        <div class="rounded-xl bg-white shadow-sm">

            <div class="flex flex-col gap-3 border-b px-6 py-5 sm:flex-row sm:items-center sm:justify-between">

                <div>
                    <h2 class="font-semibold">
                        Maintenance History
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Maintenance records for this vehicle.
                    </p>
                </div>

                @if ($vehicle->maintenanceRecords->isNotEmpty() && $vehicle->status->value !== 'out_of_service')
                    <a href="{{ route('maintenance.create', ['vehicle_id' => $vehicle->id]) }}"
                        class="rounded-lg bg-gray-900 px-4 py-2 text-center text-sm font-medium text-white transition-colors hover:bg-gray-700">
                        + Report Maintenance
                    </a>
                @endif

            </div>


            @if($vehicle->maintenanceRecords->isNotEmpty())

                {{-- Desktop Table --}}
                <div class="hidden overflow-x-auto md:block">

                    <table class="w-full min-w-[700px] text-left text-sm">

                        <thead class="border-b bg-gray-50 text-xs uppercase text-gray-500">
                            <tr>
                                <th class="px-6 py-3">Reported</th>
                                <th class="px-6 py-3">Maintenance</th>
                                <th class="px-6 py-3">Type</th>
                                <th class="px-6 py-3">Status</th>
                                <th class="px-6 py-3">Cost</th>
                                <th class="px-6 py-3">Action</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y">

                            @foreach($vehicle->maintenanceRecords as $maintenance)

                                        <tr class="hover:bg-gray-50">

                                            <td class="whitespace-nowrap px-6 py-4 text-gray-600">
                                                {{ $maintenance->reported_at->format('d M Y H:i') }}
                                            </td>

                                            <td class="px-6 py-4">
                                                <a href="{{ route('maintenance.show', $maintenance) }}"
                                                    class="font-medium text-blue-600 hover:underline">
                                                    {{ $maintenance->title }}
                                                </a>
                                            </td>

                                            <td class="px-6 py-4">
                                                <span
                                                    class="inline-flex rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">
                                                    {{ str($maintenance->type->value)
                                ->replace('_', ' ')
                                ->title() }}
                                                </span>
                                            </td>

                                            <td class="px-6 py-4">

                                                @php
                                                    $maintenanceStatus = $maintenance->status;
                                                @endphp

                                                <span @class([
                                                    'inline-flex rounded-full px-3 py-1 text-xs font-semibold',

                                                    'bg-yellow-100 text-yellow-700' =>
                                                        $maintenanceStatus->value === 'pending',

                                                    'bg-blue-100 text-blue-700' =>
                                                        $maintenanceStatus->value === 'in_progress',

                                                    'bg-green-100 text-green-700' =>
                                                        $maintenanceStatus->value === 'completed',

                                                    'bg-red-100 text-red-700' =>
                                                        $maintenanceStatus->value === 'cancelled',
                                                ])>
                                                    {{ str($maintenanceStatus->value)
                                ->replace('_', ' ')
                                ->title() }}
                                                </span>

                                            </td>

                                            <td class="whitespace-nowrap px-6 py-4 font-medium">
                                                ${{ number_format((float) $maintenance->cost, 2) }}
                                            </td>

                                            <td class="px-6 py-4">
                                                <a href="{{ route('maintenance.show', $maintenance) }}"
                                                    class="font-medium text-blue-600 hover:underline">
                                                    View
                                                </a>
                                            </td>

                                        </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>


                {{-- Mobile Cards --}}
                <div class="space-y-4 p-4 md:hidden">

                    @foreach($vehicle->maintenanceRecords as $maintenance)

                        <div class="rounded-xl border border-gray-200 p-4">

                            <div class="flex items-start justify-between gap-3">

                                <div>

                                    <a href="{{ route('maintenance.show', $maintenance) }}"
                                        class="font-semibold text-blue-600 hover:underline">
                                        {{ $maintenance->title }}
                                    </a>

                                    <p class="mt-1 text-xs text-gray-500">
                                        {{ $maintenance->reported_at->format('d M Y H:i') }}
                                    </p>

                                </div>


                                @php
                                    $maintenanceStatus = $maintenance->status;
                                @endphp

                                <span @class([
                                    'shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold',

                                    'bg-yellow-100 text-yellow-700' =>
                                        $maintenanceStatus->value === 'pending',

                                    'bg-blue-100 text-blue-700' =>
                                        $maintenanceStatus->value === 'in_progress',

                                    'bg-green-100 text-green-700' =>
                                        $maintenanceStatus->value === 'completed',

                                    'bg-red-100 text-red-700' =>
                                        $maintenanceStatus->value === 'cancelled',
                                ])>
                                    {{ str($maintenanceStatus->value)
                        ->replace('_', ' ')
                        ->title() }}
                                </span>

                            </div>


                            <div class="mt-4 grid grid-cols-2 gap-4 text-sm">

                                <div>
                                    <p class="text-xs text-gray-500">
                                        Type
                                    </p>

                                    <p class="mt-1 font-medium">
                                        {{ str($maintenance->type->value)
                        ->replace('_', ' ')
                        ->title() }}
                                    </p>
                                </div>


                                <div>
                                    <p class="text-xs text-gray-500">
                                        Cost
                                    </p>

                                    <p class="mt-1 font-medium">
                                        ${{ number_format((float) $maintenance->cost, 2) }}
                                    </p>
                                </div>

                            </div>


                            <div class="mt-4 border-t pt-3">

                                <a href="{{ route('maintenance.show', $maintenance) }}"
                                    class="text-sm font-medium text-blue-600 hover:underline">
                                    View Maintenance →
                                </a>

                            </div>

                        </div>

                    @endforeach

                </div>

            @else

                    <div class="px-6 py-10 text-center">

                        <div class="text-4xl">
                            🔧
                        </div>

                        <h3 class="mt-3 font-semibold text-gray-900">
                            No Maintenance Records
                        </h3>

                        <p class="mx-auto mt-1 max-w-md text-sm text-gray-500">
                            No maintenance has been reported for
                            {{ $vehicle->vehicle_code }} yet.
                        </p>

                        <a href="{{ route('maintenance.create', [
                    'vehicle_id' => $vehicle->id
                ]) }}"
                            class="mt-4 inline-block rounded-lg bg-gray-900 px-4 py-2
                                                                                                                                    text-sm font-medium text-white hover:bg-gray-800">
                            + Report Maintenance
                        </a>

                    </div>

            @endif

        </div>

    </div>

    @if(auth()->user()->role->value === 'admin')
        <div class="rounded-xl border border-red-200 bg-white p-6 mt-5">

            <h2 class="font-semibold text-red-700">
                Delete Vehicle
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                This vehicle will be deleted and removed from active records.
            </p>

            <form method="POST" action="{{ route('vehicles.destroy', $vehicle) }}" class="mt-4 flex justify-end"
                onsubmit="return confirm('Are you sure you want to delete this vehicle?')">

                @csrf
                @method('DELETE')

                <button type="submit"
                    class="rounded-lg bg-red-600 px-4 py-2 text-white transition-colors hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                    Delete Vehicle
                </button>

            </form>

        </div>
    @endif

@endsection