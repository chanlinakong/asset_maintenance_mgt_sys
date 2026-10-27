@extends('layouts.app')

@section('title', 'Reports')

@section('content')

    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    Reports
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    Maintenance reports and cost analysis.
                </p>
            </div>
        </div>

        {{-- Report Generator --}}
        <div class="rounded-xl bg-white p-4 shadow-sm sm:p-6">
            <div class="mb-5">
                <h2 class="text-lg font-semibold text-gray-900">
                    Generate Report
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Choose a report and filters to download a formatted Excel workbook or PDF.
                </p>
            </div>

            <form method="GET" action="{{ route('reports.excel') }}">
                <div class="grid grid-cols-1 items-end gap-4 sm:grid-cols-2 lg:grid-cols-4">

                    {{-- Report Type --}}
                    <div>
                        <label for="report_type" class="mb-1 block text-sm font-medium text-gray-700">
                            Report Type
                        </label>

                        <select id="report_type" name="report_type" required
                            class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            <option value="maintenance_history" @selected(old('report_type', 'maintenance_history') === 'maintenance_history')>
                                Maintenance History Report
                            </option>

                            <option value="vehicle_status" @selected(old('report_type') === 'vehicle_status')>
                                Vehicle Status Report
                            </option>

                            <option value="monthly_maintenance" @selected(old('report_type') === 'monthly_maintenance')>
                                Monthly Maintenance Report
                            </option>

                            <option value="maintenance_schedule" @selected(old('report_type') === 'maintenance_schedule')>
                                Maintenance Schedule Report
                            </option>

                            <option value="maintenance_cost" @selected(old('report_type') === 'maintenance_cost')>
                                Maintenance Cost Report
                            </option>
                        </select>
                    </div>

                    {{-- Date Period --}}
                    <div>
                        <label for="export_period" class="mb-1 block text-sm font-medium text-gray-700">
                            Date Range
                        </label>

                        <select id="export_period" name="period" required
                            class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            <option value="this_month" @selected(old('period', 'this_month') === 'this_month')>
                                This Month
                            </option>
                            <option value="today" @selected(old('period') === 'today')>
                                Today
                            </option>
                            <option value="yesterday" @selected(old('period') === 'yesterday')>
                                Yesterday
                            </option>
                            <option value="last_month" @selected(old('period') === 'last_month')>
                                Last Month
                            </option>
                            <option value="last_3_months" @selected(old('period') === 'last_3_months')>
                                Last 3 Months
                            </option>
                            <option value="this_year" @selected(old('period') === 'this_year')>
                                This Year
                            </option>
                            <option value="last_year" @selected(old('period') === 'last_year')>
                                Last Year
                            </option>
                            <option value="all" @selected(old('period') === 'all')>
                                All Dates
                            </option>
                            <option value="custom" @selected(old('period') === 'custom')>
                                Custom Date Range
                            </option>
                        </select>
                    </div>

                    {{-- Vehicle Status --}}
                    <div>
                        <label for="export_vehicle_status" class="mb-1 block text-sm font-medium text-gray-700">
                            Vehicle Status
                        </label>

                        <select id="export_vehicle_status" name="vehicle_status"
                            class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            <option value="">All Statuses</option>

                            @foreach ($vehicleStatuses as $vehicleStatus)
                                <option value="{{ $vehicleStatus->value }}"
                                    @selected(old('vehicle_status') === $vehicleStatus->value)>
                                    {{ str($vehicleStatus->value)->replace('_', ' ')->title() }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Export --}}
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        {{-- Export Excel --}}
                        <button type="submit" formaction="{{ route('reports.excel') }}" class="inline-flex w-full items-center justify-center gap-2 rounded-lg
                       bg-gray-900 px-4 py-2.5 text-sm font-medium text-white
                       transition hover:bg-gray-700 focus:outline-none
                       focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                            <icon name="file-spreadsheet" />
                            Export Excel
                        </button>

                        {{-- Export PDF --}}
                        <button type="submit" formaction="{{ route('reports.pdf') }}" class="inline-flex w-full items-center justify-center gap-2 rounded-lg
                       bg-red-600 px-4 py-2.5 text-sm font-medium text-white
                       transition hover:bg-red-700 focus:outline-none
                       focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                            <icon name="file-text" />
                            Export PDF
                        </button>
                    </div>
                </div>

                {{-- Custom Dates --}}
                <div id="export-custom-dates"
                    class="{{ old('period', 'this_month') === 'custom' ? '' : 'hidden' }} mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="export_from" class="mb-1 block text-sm font-medium text-gray-700">
                            From Date
                        </label>

                        <input id="export_from" name="from" type="date" value="{{ old('from') }}" max="{{ old('to') }}"
                            class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    </div>

                    <div>
                        <label for="export_to" class="mb-1 block text-sm font-medium text-gray-700">
                            To Date
                        </label>

                        <input id="export_to" name="to" type="date" value="{{ old('to') }}" min="{{ old('from') }}"
                            class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    </div>
                </div>

                @if ($errors->any())
                    <div class="mt-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                        <p class="font-medium">Please check the report options:</p>

                        <ul class="mt-1 list-inside list-disc">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </form>
        </div>

        {{-- Filters --}}
        <div class="rounded-xl bg-white p-4 shadow-sm sm:p-6">
            <form method="GET" action="{{ route('reports.index') }}">

                <div class="grid grid-cols-1 items-end gap-4 sm:grid-cols-2 lg:grid-cols-4">

                    {{-- Date Period --}}
                    <div>
                        <label for="period" class="mb-1 block text-sm font-medium text-gray-700">
                            Date Period
                        </label>

                        <select id="period" name="period"
                            class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm
                                                                   focus:border-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-200">
                            <option value="all" @selected(request('period', 'all') === 'all')>All Dates</option>
                            <option value="today" @selected(request('period') === 'today')>Today</option>
                            <option value="yesterday" @selected(request('period') === 'yesterday')>Yesterday</option>
                            <option value="this_month" @selected(request('period') === 'this_month')>This Month</option>
                            <option value="last_month" @selected(request('period') === 'last_month')>Last Month</option>
                            <option value="this_year" @selected(request('period') === 'this_year')>This Year</option>
                            <option value="last_year" @selected(request('period') === 'last_year')>Last Year</option>
                            <option value="custom" @selected(request('period') === 'custom')>Custom Date Range</option>
                        </select>
                    </div>

                    {{-- Custom Date Range --}}
                    <div id="custom-date-fields" class="{{ request('period') === 'custom' ? '' : 'hidden' }} sm:col-span-2">

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label for="from" class="mb-1 block text-sm font-medium text-gray-700">
                                    From Date
                                </label>

                                <input id="from" name="from" type="date" value="{{ request('from', $from ?? '') }}"
                                    max="{{ request('to', $to ?? '') }}"
                                    class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm
                                                                           focus:border-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-200">
                            </div>

                            <div>
                                <label for="to" class="mb-1 block text-sm font-medium text-gray-700">
                                    To Date
                                </label>

                                <input id="to" name="to" type="date" value="{{ request('to', $to ?? '') }}"
                                    min="{{ request('from', $from ?? '') }}"
                                    class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm
                                                                           focus:border-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-200">
                            </div>
                        </div>
                    </div>

                    {{-- Status --}}
                    <div>
                        <label for="status" class="mb-1 block text-sm font-medium text-gray-700">
                            Status
                        </label>

                        <select id="status" name="status"
                            class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm
                                                                   focus:border-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-200">
                            <option value="">All Statuses</option>

                            @foreach($statuses as $item)
                                <option value="{{ $item->value }}" @selected($status === $item->value)>
                                    {{ str($item->value)->replace('_', ' ')->title() }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Type --}}
                    <div>
                        <label for="type" class="mb-1 block text-sm font-medium text-gray-700">
                            Type
                        </label>

                        <select id="type" name="type"
                            class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm
                                                                   focus:border-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-200">
                            <option value="">All Types</option>

                            @foreach($types as $item)
                                <option value="{{ $item->value }}" @selected($type === $item->value)>
                                    {{ str($item->value)->replace('_', ' ')->title() }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Buttons --}}
                    <div class="flex items-end justify-end gap-2">
                        <button type="submit" class="w-24 rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white
                                           transition-colors hover:bg-gray-700
                                           focus:outline-none">
                            Search
                        </button>

                        <a id="schedule-clear-link" href="{{ route('reports.index') }}" class="{{ request()->hasAny(['search', 'status', 'due_status']) ? '' : 'hidden' }}
                                       shrink-0 rounded-lg border border-gray-300 px-4 py-2
                                       text-sm font-medium text-gray-700 transition-colors
                                       hover:border-gray-400 hover:bg-gray-100 hover:text-gray-900
                                       focus:outline-none">
                            Clear
                        </a>

                    </div>

                </div>

                {{-- Validation Errors --}}
                @if($errors->has('from') || $errors->has('to'))
                    <div class="mt-3 text-sm text-red-600">
                        @error('from')
                            <p>{{ $message }}</p>
                        @enderror

                        @error('to')
                            <p>{{ $message }}</p>
                        @enderror
                    </div>
                @endif

            </form>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">

            <x-stat-card title="Maintenance Records" :value="$totalRecords" icon="clipboard" />

            <x-stat-card title="Total Cost" :value="'$' . number_format($totalCost, 2)" icon="banknote" />

            <x-stat-card title="Completed" :value="$completedCount" icon="check-circle" />

            <x-stat-card title="In Progress" :value="$inProgressCount" icon="wrench" />

            <x-stat-card title="Vehicles in Maintenance" :value="$vehiclesUnderMaintenance" icon="truck" />

        </div>


        {{-- Cost by Vehicle --}}
        <div id="cost-by-vehicle">
            <div class="rounded-xl bg-white shadow-sm">

                <div class="border-b border-gray-200 p-4 sm:p-6">

                    <h2 class="text-lg font-semibold text-gray-900">
                        Maintenance Cost by Vehicle
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Total maintenance records and costs for each vehicle.
                    </p>

                </div>


                {{-- Desktop --}}
                <div class="hidden overflow-x-auto md:block">

                    <table class="min-w-full divide-y divide-gray-200">

                        <thead class="bg-gray-50">

                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                    Vehicle
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                    Type
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                    Records
                                </th>

                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase text-gray-500">
                                    Total Cost
                                </th>
                            </tr>

                        </thead>


                        <tbody class="divide-y divide-gray-200 bg-white">

                            @forelse($costByVehicle as $item)

                                <tr class="hover:bg-gray-50">

                                    <td class="px-6 py-4">

                                        <a href="{{ route('vehicles.show', $item['vehicle']) }}"
                                            class="font-medium text-gray-900 hover:underline">
                                            {{ $item['vehicle']->vehicle_code }}
                                        </a>

                                        <p class="text-sm text-gray-500">
                                            {{ $item['vehicle']->name }}
                                        </p>

                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $item['vehicle']->type }}
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $item['count'] }}
                                    </td>

                                    <td class="px-6 py-4 text-right font-medium text-gray-900">
                                        ${{ number_format($item['cost'], 2) }}
                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="4" class="px-6 py-10 text-center text-sm text-gray-500">
                                        No maintenance records found.
                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>


                {{-- Mobile --}}
                <div class="divide-y divide-gray-200 md:hidden">

                    @forelse($costByVehicle as $item)

                        <div class="space-y-3 p-4">

                            <div class="flex items-start justify-between gap-4">

                                <div>

                                    <a href="{{ route('vehicles.show', $item['vehicle']) }}"
                                        class="font-semibold text-gray-900">
                                        {{ $item['vehicle']->vehicle_code }}
                                    </a>

                                    <p class="text-sm text-gray-500">
                                        {{ $item['vehicle']->name }}
                                    </p>

                                </div>

                                <p class="font-semibold text-gray-900">
                                    ${{ number_format($item['cost'], 2) }}
                                </p>

                            </div>

                            <div class="flex justify-between text-sm text-gray-500">

                                <span>
                                    {{ $item['vehicle']->type }}
                                </span>

                                <span>
                                    {{ $item['count'] }} records
                                </span>

                            </div>

                        </div>

                    @empty

                        <div class="p-8 text-center text-sm text-gray-500">
                            No maintenance records found.
                        </div>

                    @endforelse

                </div>

                {{ $costByVehicle->links('vendors.pagination.custom') }}

            </div>

        </div>



        {{-- Maintenance by Type --}}
        <div class="rounded-xl bg-white shadow-sm">

            <div class="border-b border-gray-200 p-4 sm:p-6">

                <h2 class="text-lg font-semibold text-gray-900">
                    Maintenance by Type
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Maintenance activity grouped by type.
                </p>

            </div>


            <div class="overflow-x-auto">

                <table class="min-w-full divide-y divide-gray-200">

                    <thead class="bg-gray-50">

                        <tr>

                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                Type
                            </th>

                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                Records
                            </th>

                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase text-gray-500">
                                Total Cost
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-gray-200">

                        @forelse($costByType as $item)

                                        <tr class="hover:bg-gray-50">

                                            <td class="px-6 py-4">

                                                <span
                                                    class="inline-flex rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">
                                                    {{ str($item['type']->value)
                            ->replace('_', ' ')
                            ->title() }}
                                                </span>

                                            </td>

                                            <td class="px-6 py-4 text-sm text-gray-600">
                                                {{ $item['count'] }}
                                            </td>

                                            <td class="px-6 py-4 text-right font-medium text-gray-900">
                                                ${{ number_format($item['cost'], 2) }}
                                            </td>

                                        </tr>

                        @empty

                            <tr>

                                <td colspan="3" class="px-6 py-10 text-center text-sm text-gray-500">
                                    No maintenance records found.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        {{-- Report Details --}}
        <div id="maintenance-details">
            <div class="rounded-xl bg-white shadow-sm">

                <div class="border-b border-gray-200 p-4 sm:p-6">

                    <h2 class="text-lg font-semibold text-gray-900">
                        Maintenance Details
                    </h2>

                </div>


                <div class="overflow-x-auto">

                    <table class="min-w-full divide-y divide-gray-200">

                        <thead class="bg-gray-50">

                            <tr>

                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                    Date
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                    Vehicle
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                    Maintenance
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                    Status
                                </th>

                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase text-gray-500">
                                    Cost
                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y divide-gray-200">

                            @forelse($records as $record)

                                <tr class="hover:bg-gray-50">

                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                        {{ $record->reported_at->format('d M Y') }}
                                    </td>

                                    <td class="px-6 py-4">

                                        <a href="{{ route('vehicles.show', $record->vehicle) }}"
                                            class="font-medium text-gray-900 hover:underline">
                                            {{ $record->vehicle->vehicle_code }}
                                        </a>

                                    </td>

                                    <td class="px-6 py-4">

                                        <a href="{{ route('maintenance.show', $record) }}"
                                            class="font-medium text-gray-900 hover:underline">
                                            {{ $record->title }}
                                        </a>

                                    </td>

                                    <td class="px-6 py-4">

                                        @php
                                            $status = $record->status->value;

                                            $statusClasses = match ($status) {
                                                'pending' => 'bg-amber-100 text-amber-800 ring-1 ring-inset ring-amber-200',
                                                'in_progress' => 'bg-blue-100 text-blue-800 ring-1 ring-inset ring-blue-200',
                                                'completed' => 'bg-green-100 text-green-800 ring-1 ring-inset ring-green-200',
                                                'cancelled' => 'bg-red-100 text-red-800 ring-1 ring-inset ring-red-200',
                                                default => 'bg-gray-100 text-gray-700 ring-1 ring-inset ring-gray-200',
                                            };
                                        @endphp

                                        <span
                                            class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses }}">
                                            {{ str($status)->replace('_', ' ')->title() }}
                                        </span>
                                       
                                    </td>

                                    <td class="px-6 py-4 text-right font-medium text-gray-900">
                                        ${{ number_format((float) $record->cost, 2) }}
                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">
                                        No maintenance records found.
                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

                {{ $records->links('vendors.pagination.custom') }}

            </div>

        </div>

    </div>


    {{-- Print styling --}}
    <style>
        @media print {

            nav,
            aside,
            button,
            form {
                display: none !important;
            }

            body {
                background: white !important;
            }

            main {
                padding: 0 !important;
            }

            .shadow-sm {
                box-shadow: none !important;
            }
        }
    </style>

@endsection

<script>
    document.addEventListener('DOMContentLoaded', function () {

        // 1. AJAX Pagination: Cost by Vehicle & Maintenance Details
        document.addEventListener('click', async function (event) {
            const target = event.target;

            if (!(target instanceof Element)) return;

            const link = target.closest(
                '#cost-by-vehicle nav a, #maintenance-details nav a'
            );

            if (!link) return;

            // Preserve browser behavior for opening links in a new tab.
            if (
                event.ctrlKey ||
                event.metaKey ||
                event.shiftKey ||
                event.altKey ||
                link.target === '_blank'
            ) {
                return;
            }

            event.preventDefault();

            const container = link.closest(
                '#cost-by-vehicle, #maintenance-details'
            );

            if (!container) return;

            container.classList.add('opacity-50', 'pointer-events-none');

            try {
                const response = await fetch(link.href, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    window.location.href = link.href;
                    return;
                }

                const html = await response.text();

                const documentPage = new DOMParser().parseFromString(
                    html,
                    'text/html'
                );

                const updatedContainer = documentPage.getElementById(
                    container.id
                );

                if (updatedContainer) {
                    container.innerHTML = updatedContainer.innerHTML;

                    const url = new URL(link.href, window.location.origin);

                    history.pushState({}, '', url.href);
                } else {
                    window.location.href = link.href;
                }
            } catch (error) {
                window.location.href = link.href;
            } finally {
                container.classList.remove(
                    'opacity-50',
                    'pointer-events-none'
                );
            }
        });

        // 2. Report Filters: Custom Date Range
        const period = document.getElementById('period');
        const dateFields = document.getElementById('custom-date-fields');
        const from = document.getElementById('from');
        const to = document.getElementById('to');

        if (period && dateFields) {

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
                if (to) {
                    to.min = from.value;
                }
            });

            to?.addEventListener('change', function () {
                if (from) {
                    from.max = to.value;
                }
            });

            updateDateFields();
        }

        // 3. Export Form: Custom Date Range
        const exportPeriod = document.getElementById('export_period');
        const exportDates = document.getElementById('export-custom-dates');
        const exportFrom = document.getElementById('export_from');
        const exportTo = document.getElementById('export_to');

        if (exportPeriod && exportDates && exportFrom && exportTo) {

            function updateExportDateFields() {
                const isCustom = exportPeriod.value === 'custom';

                exportDates.classList.toggle('hidden', !isCustom);

                exportFrom.required = isCustom;
                exportTo.required = isCustom;
            }

            exportPeriod.addEventListener(
                'change',
                updateExportDateFields
            );

            exportFrom.addEventListener('change', function () {
                exportTo.min = exportFrom.value;
            });

            exportTo.addEventListener('change', function () {
                exportFrom.max = exportTo.value;
            });

            updateExportDateFields();
        }

    });
</script>