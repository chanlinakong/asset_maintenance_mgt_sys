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

            <a href="{{ route('maintenance.create') }}" class="rounded-lg bg-gray-900 transition-colors hover:bg-gray-700 px-4 py-2 text-center text-white">
                + Report Maintenance
            </a>

        </div>


        @if(session('success'))

            <div class="rounded-lg bg-green-50 px-4 py-3 text-green-700">
                {{ session('success') }}
            </div>

        @endif

        @error('from')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror

        @error('to')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror


        <div class="overflow-hidden rounded-xl bg-white shadow-sm">
            {{-- Filter form --}}
            <div class="border-b border-gray-200">
                <form method="GET" action="{{ route('maintenance.index') }}" class="p-4 sm:p-6">

                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">

                        {{-- Search --}}
                        <div class="lg:col-span-2">
                            <input type="search" name="search" value="{{ $search }}" placeholder="Search maintenance..."
                                class="min-w-0 w-full rounded-lg border border-gray-300 px-3 py-2
                                               focus:border-gray-500 focus:outline-none">
                        </div>

                        {{-- Vehicle --}}
                        <div class="lg:col-span-2">
                            <select name="vehicle_id" class="w-full rounded-lg border border-gray-300 px-3 py-2
                                               focus:border-gray-500 focus:outline-none">
                                <option value="">All Vehicles</option>

                                @foreach ($vehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}" @selected($vehicleId == $vehicle->id)>
                                        {{ $vehicle->vehicle_code }} — {{ $vehicle->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Date Period --}}
                        <select name="period" id="period" class="w-full rounded-lg border border-gray-300 px-3 py-2
                                               focus:border-gray-500 focus:outline-none">
                            <option value="">All Dates</option>
                            <option value="today" @selected(request('period') === 'today')>Today</option>
                            <option value="yesterday" @selected(request('period') === 'yesterday')>Yesterday</option>
                            <option value="this_month" @selected(request('period') === 'this_month')>This Month</option>
                            <option value="last_month" @selected(request('period') === 'last_month')>Last Month</option>
                            <option value="this_year" @selected(request('period') === 'this_year')>This Year</option>
                            <option value="last_year" @selected(request('period') === 'last_year')>Last Year</option>
                            <option value="custom" @selected(request('period') === 'custom')>Custom Date Range</option>
                        </select>



                        {{-- Maintenance Type --}}
                        <select name="type" class="w-full rounded-lg border border-gray-300 px-3 py-2
                                               focus:border-gray-500 focus:outline-none">
                            <option value="">All Types</option>

                            @foreach ($types as $maintenanceType)
                                <option value="{{ $maintenanceType->value }}" @selected($type === $maintenanceType->value)>
                                    {{ str($maintenanceType->value)->replace('_', ' ')->title() }}
                                </option>
                            @endforeach
                        </select>

                        {{-- Status --}}
                        <select name="status" class="w-full rounded-lg border border-gray-300 px-3 py-2
                                               focus:border-gray-500 focus:outline-none">
                            <option value="">All Statuses</option>

                            @foreach ($statuses as $maintenanceStatus)
                                <option value="{{ $maintenanceStatus->value }}" @selected($status === $maintenanceStatus->value)>
                                    {{ str($maintenanceStatus->value)->replace('_', ' ')->title() }}
                                </option>
                            @endforeach
                        </select>

                    </div>

                    {{-- Custom Date Range --}}
                    <div id="custom-date-fields" class="{{ request('period') === 'custom' ? '' : 'hidden' }} mt-3">
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:max-w-2xl">

                            <div>
                                <label for="from" class="mb-1 block text-sm font-medium text-gray-700">
                                    From Date
                                </label>

                                <input type="date" name="from" id="from" value="{{ request('from') }}"
                                    max="{{ request('to') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2
                                                       focus:border-gray-500 focus:outline-none">
                            </div>

                            <div>
                                <label for="to" class="mb-1 block text-sm font-medium text-gray-700">
                                    To Date
                                </label>

                                <input type="date" name="to" id="to" value="{{ request('to') }}" min="{{ request('from') }}"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2
                                                       focus:border-gray-500 focus:outline-none">
                            </div>

                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="mt-4 flex flex-wrap items-center justify-end gap-2">
                        <button type="submit" class="rounded-lg bg-gray-900 px-5 py-2 text-sm font-medium
                                               text-white transition-colors hover:bg-gray-700">
                            Search
                        </button>

                        @if ($search || $status || $type || $vehicleId || request('period') || request('from') || request('to'))
                            <a href="{{ route('maintenance.index') }}" class="rounded-lg border border-gray-300 px-5 py-2 text-sm
                                                                               font-medium text-gray-700 transition-colors
                                                                               hover:border-gray-400 hover:bg-gray-50">
                                Clear
                            </a>
                        @endif
                    </div>

                </form>
            </div>

            <div class="overflow-x-auto">

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
                                                    <span
                                                        class="inline-flex rounded-full bg-gray-100 px-3 py-1
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

                                                <td class="px-6 py-4">
                                                    <div class="flex justify-end gap-2">

                                                        <a href="{{ route('maintenance.show', $maintenance) }}"
                                                            class="rounded-lg border px-3 py-1.5 transition-colors
                                                                hover:border-gray-400 hover:bg-gray-100 hover:text-gray-900
                                                                focus:outline-none focus:ring-2 focus:ring-gray-300">View
                                                        </a>

                                                        <a href="{{ route('maintenance.edit', $maintenance) }}"
                                                            class="rounded-lg border px-3 py-1.5 transition-colors
                                                                hover:border-gray-400 hover:bg-gray-100 hover:text-gray-900
                                                                focus:outline-none focus:ring-2 focus:ring-gray-300">
                                                            Edit
                                                        </a>

                                                        @if(auth()->user()->role->value === 'admin')
                                                            <form method="POST" action="{{ route('maintenance.destroy', $maintenance) }}"
                                                                onsubmit="return confirm('Are you sure you want to delete this maintenance record?')">
                                                                @csrf
                                                                @method('DELETE')

                                                                <button type="submit"
                                                                    class="rounded-lg border px-3 py-1.5 transition-colors text-red-600 hover:bg-red-50 hover:border-red-300">
                                                                    Delete
                                                                </button>

                                                            </form>
                                                        @endif
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const period = document.getElementById('period');
        const dateFields = document.getElementById('custom-date-fields');
        const from = document.getElementById('from');
        const to = document.getElementById('to');

        if (!period || !dateFields) return;

        function updateDateFields() {
            const isCustom = period.value === 'custom';

            dateFields.classList.toggle('hidden', !isCustom);

            if (from && to) {
                from.required = isCustom;
                to.required = isCustom;
            }
        }

        period.addEventListener('change', updateDateFields);

        from?.addEventListener('change', function () {
            to.min = from.value;
        });

        to?.addEventListener('change', function () {
            from.max = to.value;
        });

        updateDateFields();
    });
</script>