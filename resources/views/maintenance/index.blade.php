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

            <a href="{{ route('maintenance.create') }}"
                class="rounded-lg bg-gray-900 transition-colors hover:bg-gray-700 px-4 py-2 text-center text-white">
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
                <form method="GET" action="{{ route('maintenance.index') }}" class="p-4 sm:p-6" id="maintenance-form">

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

                        <a id="maintenance-clear-link" href="{{ route('maintenance.index') }}" class="{{ $search || $status || $type || $vehicleId || request('period') || request('from') || request('to') ? '' : 'hidden' }}
                       rounded-lg border border-gray-300 px-5 py-2 text-sm
                       font-medium text-gray-700 transition-colors
                       hover:border-gray-400 hover:bg-gray-50">
                            Clear
                        </a>
                    </div>

                </form>
            </div>

            <div class="overflow-x-auto" id="maintenance-table-data">

                @include('maintenance.partials.table')

            </div>

        </div>

    </div>

@endsection

<script>

    document.addEventListener('DOMContentLoaded', function () {
        const period = document.getElementById('period');
        const dateFields = document.getElementById('custom-date-fields');
        const from = document.getElementById('from');
        const to = document.getElementById('to');

        const clearLink = document.getElementById('maintenance-clear-link');

        const maintenanceForm = document.getElementById('maintenance-form');
        const maintenanceTableData = document.getElementById('maintenance-table-data');

        // Date filter behavior
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
                if (to) to.min = from.value;
            });

            to?.addEventListener('change', function () {
                if (from) from.max = to.value;
            });

            updateDateFields();
        }

        // AJAX refresh
        if (!maintenanceForm || !maintenanceTableData) return;

        async function refreshMaintenance(url, updateHistory = true) {
            const targetUrl = new URL(url, window.location.origin);

            maintenanceTableData.classList.add('opacity-50', 'pointer-events-none');

            try {
                const response = await fetch(targetUrl, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html'
                    }
                });

                if (!response.ok) {
                    throw new Error('Unable to load maintenance records.');
                }

                maintenanceTableData.innerHTML = await response.text();

                const params = targetUrl.searchParams;

                const hasFilters = [
                    'search', 'status', 'type', 'vehicle_id',
                    'period', 'from', 'to'
                ].some(key => (params.get(key) || '').trim() !== '');

                if (clearLink) {
                    clearLink.classList.toggle('hidden', !hasFilters);
                }

                if (updateHistory) {
                    history.pushState({}, '', targetUrl);
                }
            } catch (error) {
                window.location.href = targetUrl.href;
            } finally {
                maintenanceTableData.classList.remove('opacity-50', 'pointer-events-none');
            }
        }

        // Submit filters without reloading the whole page
        maintenanceForm.addEventListener('submit', function (event) {
            event.preventDefault();

            const url = new URL(maintenanceForm.action, window.location.origin);
            url.search = new URLSearchParams(
                new FormData(maintenanceForm)
            ).toString();

            refreshMaintenance(url);
        });

        clearLink?.addEventListener('click', function (event) {
            event.preventDefault();

            // Clear filter inputs
            maintenanceForm.reset();

            // Reset custom date fields
            const period = maintenanceForm.querySelector('[name="period"]');
            const from = maintenanceForm.querySelector('[name="from"]');
            const to = maintenanceForm.querySelector('[name="to"]');

            if (period) period.value = '';
            if (from) from.value = '';
            if (to) to.value = '';

            if (period) period.dispatchEvent(new Event('change'));

            // Load the maintenance page without any query parameters
            refreshMaintenance(clearLink.href);
        });

        // Handle pagination links only, including AJAX-inserted links
        maintenanceTableData.addEventListener('click', function (event) {
            const link = event.target.closest(
                'nav[role="navigation"] a'
            );

            if (!link || !maintenanceTableData.contains(link)) return;

            if (
                link.target === '_blank' ||
                event.ctrlKey ||
                event.metaKey ||
                event.shiftKey ||
                event.altKey
            ) return;

            const url = new URL(link.href, window.location.origin);

            if (url.origin !== window.location.origin) return;

            event.preventDefault();
            refreshMaintenance(url);
        });

        // Support browser Back and Forward
        window.addEventListener('popstate', function () {
            refreshMaintenance(window.location.href, false);
        });
    });

</script>