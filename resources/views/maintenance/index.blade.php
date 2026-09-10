@extends('layouts.app')

@section('title', 'Maintenance')

@section('content')

    <div class="space-y-6">

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>

                <h1 class="text-2xl font-bold">
                    Maintenance
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    Track vehicle maintenance activities.
                </p>

            </div>

            <a href="{{ route('maintenance.create') }}" class="rounded-lg bg-gray-900 px-4 py-2 text-center text-white">
                + Report Maintenance
            </a>

        </div>


        @if(session('success'))

            <div class="rounded-lg bg-green-50 px-4 py-3 text-green-700">
                {{ session('success') }}
            </div>

        @endif


        <div class="overflow-hidden rounded-xl bg-white shadow-sm">

            <div class="overflow-x-auto">

                <form method="GET" action="{{ route('maintenance.index') }}" class="mb-6 rounded-xl bg-white p-4 shadow-sm">
                    <div class="grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-4">

                        {{-- Search --}}
                        <input type="search" name="search" value="{{ $search }}" placeholder="Search maintenance..."
                            class="rounded-lg border border-gray-300 px-4 py-2">

                        {{-- Vehicle --}}
                        <select name="vehicle_id" class="rounded-lg border border-gray-300 px-4 py-2">
                            <option value="">All Vehicles</option>

                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" @selected($vehicleId == $vehicle->id)>
                                    {{ $vehicle->vehicle_code }}
                                    — {{ $vehicle->name }}
                                </option>
                            @endforeach
                        </select>

                        {{-- Type --}}
                        <select name="type" class="rounded-lg border border-gray-300 px-4 py-2">
                            <option value="">All Types</option>

                            @foreach($types as $maintenanceType)
                                                <option value="{{ $maintenanceType->value }}" @selected($type === $maintenanceType->value)>
                                                    {{ str($maintenanceType->value)
                                ->replace('_', ' ')
                                ->title() }}
                                                </option>
                            @endforeach
                        </select>

                        {{-- Status --}}
                        <select name="status" class="rounded-lg border border-gray-300 px-4 py-2">
                            <option value="">All Statuses</option>

                            @foreach($statuses as $maintenanceStatus)
                                                <option value="{{ $maintenanceStatus->value }}" @selected($status === $maintenanceStatus->value)>
                                                    {{ str($maintenanceStatus->value)
                                ->replace('_', ' ')
                                ->title() }}
                                                </option>
                            @endforeach
                        </select>

                    </div>

                    <div class="mt-3 flex flex-wrap gap-2">

                        <button type="submit" class="rounded-lg bg-gray-900 px-5 py-2 text-sm
                                               font-medium text-white hover:bg-gray-800">
                            Filter
                        </button>

                        @if($search || $status || $type || $vehicleId)
                            <a href="{{ route('maintenance.index') }}" class="rounded-lg border border-gray-300 px-5 py-2
                                                                               text-sm font-medium hover:bg-gray-50">
                                Clear
                            </a>
                        @endif

                    </div>
                </form>

                <table class="min-w-full text-left text-sm">

                    <thead class="bg-gray-50">

                        <tr>

                            <th class="px-6 py-3">
                                Vehicle
                            </th>

                            <th class="px-6 py-3">
                                Maintenance
                            </th>

                            <th class="px-6 py-3">
                                Type
                            </th>

                            <th class="px-6 py-3">
                                Status
                            </th>

                            <th class="px-6 py-3">
                                Reported
                            </th>

                            <th class="px-6 py-3">
                                Cost
                            </th>

                            <th class="px-6 py-3 text-right">
                                Actions
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y">

                        @forelse($maintenanceRecords as $maintenance)

                                            <tr>

                                                <td class="px-6 py-4 font-medium">
                                                    {{ $maintenance->vehicle->vehicle_code }}
                                                </td>

                                                <td class="px-6 py-4">
                                                    {{ $maintenance->title }}
                                                </td>
                                                @php
                                                    $maintenanceType = $maintenance->type;
                                                @endphp

                                                <td class="px-6 py-4">
                                                    <span class="inline-flex rounded-full bg-gray-100 px-3 py-1
                                   text-xs font-semibold text-gray-700">
                                                        {{ str($maintenanceType->value)
                                ->replace('_', ' ')
                                ->title() }}
                                                    </span>
                                                </td>

                                                @php
                                                    $maintenanceStatus = $maintenance->status;
                                                @endphp

                                                <td class="px-6 py-4">
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

                                                <td class="px-6 py-4">
                                                    {{ $maintenance->reported_at
                                ->format('d M Y H:i') }}
                                                </td>

                                                <td class="px-6 py-4">
                                                    ${{ number_format(
                                (float) $maintenance->cost,
                                2
                            ) }}
                                                </td>

                                                <td class="px-4 py-3">
                                                    <div class="flex flex-wrap gap-2">

                                                        <a href="{{ route('maintenance.show', $maintenance) }}"
                                                            class="rounded-lg bg-gray-100 px-3 py-2 text-sm font-medium hover:bg-gray-200">
                                                            View
                                                        </a>

                                                        <a href="{{ route('maintenance.edit', $maintenance) }}"
                                                            class="rounded-lg bg-blue-600 px-3 py-2 text-sm font-medium text-white hover:bg-blue-700">
                                                            Edit
                                                        </a>

                                                        <form method="POST" action="{{ route('maintenance.destroy', $maintenance) }}"
                                                            onsubmit="return confirm('Are you sure you want to delete this maintenance record?')">
                                                            @csrf
                                                            @method('DELETE')

                                                            <button type="submit"
                                                                class="rounded-lg bg-red-600 px-3 py-2 text-sm font-medium text-white hover:bg-red-700">
                                                                Delete
                                                            </button>
                                                        </form>

                                                    </div>
                                                </td>

                                            </tr>

                        @empty

                            <tr>

                                <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                                    No maintenance records found.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        <div>
            {{ $maintenanceRecords->links() }}
        </div>

    </div>

@endsection