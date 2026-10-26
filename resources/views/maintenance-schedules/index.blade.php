@extends('layouts.app')

@section('title', 'Maintenance Schedules')

@section('content')

    <div class="space-y-6">

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    Maintenance Schedules
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    Manage preventive maintenance schedules.
                </p>
            </div>

            @can('create', App\Models\MaintenanceSchedule::class)
                <a href="{{ route('maintenance-schedules.create') }}"
                    class="rounded-lg bg-gray-900 transition-colors hover:bg-gray-700 px-4 py-2 text-sm font-medium text-white">
                    + New Schedule
                </a>
            @endcan

        </div>

        <form method="GET" class="rounded-xl bg-white p-4 shadow-sm" id="maintenance-schedule-form">
            <div class="flex flex-col gap-3 sm:flex-row">

                <input type="text" name="search" value="{{ $search }}" placeholder="Search schedule or vehicle..."
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 sm:flex-1">

                <select name="status" class="rounded-lg border border-gray-300 px-3 py-2">
                    <option value="">
                        All
                    </option>

                    <option value="active" @selected($status === 'active')>
                        Active
                    </option>

                    <option value="inactive" @selected($status === 'inactive')>
                        Inactive
                    </option>
                </select>

                {{-- New Due Status Search --}}
                <select name="due_status" class="rounded-lg border border-gray-300 px-3 py-2">
                    <option value="">All Due Status</option>

                    <option value="up_to_date" @selected(request('due_status') === 'up_to_date')>
                        Up to Date
                    </option>

                    <option value="due_soon" @selected(request('due_status') === 'due_soon')>
                        Due Soon
                    </option>

                    <option value="overdue" @selected(request('due_status') === 'overdue')>
                        Overdue
                    </option>
                </select>

                <button type="submit"
                    class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-gray-700">
                    Search
                </button>

                <a id="schedule-clear-link" href="{{ route('maintenance-schedules.index') }}" class="{{ request()->hasAny(['search', 'status', 'due_status']) ? '' : 'hidden' }}
                   rounded-lg border border-gray-300 px-5 py-2 text-sm
                   font-medium text-gray-700 transition hover:bg-gray-50">
                    Clear
                </a>

            </div>
        </form>

        <div id="maintenance-schedule-data-table"
            class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">

            @include('maintenance-schedules.partials.table')

        </div>

    </div>

@endsection

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('maintenance-schedule-form');
        const table = document.getElementById('maintenance-schedule-data-table');

        if (!form || !table) return;

        const filterNames = ['search', 'status', 'due_status'];

        async function refreshSchedules(url, updateHistory = true) {
            const targetUrl = new URL(url, window.location.origin);

            table.classList.add('opacity-50', 'pointer-events-none');

            try {
                const response = await fetch(targetUrl, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html'
                    }
                });

                if (!response.ok) {
                    throw new Error('Failed to load maintenance schedules.');
                }

                // Replace table contents while keeping its wrapper and event listener.
                table.innerHTML = await response.text();

                const params = targetUrl.searchParams;

                // Show Clear only when at least one filter has a value.
                const hasFilters = filterNames.some(name =>
                    (params.get(name) || '').trim() !== ''
                );

                const clearLink = document.getElementById('schedule-clear-link');

                clearLink?.classList.toggle('hidden', !hasFilters);

                // Synchronize filter inputs with the URL.
                filterNames.forEach(name => {
                    const field = form.elements.namedItem(name);

                    if (field) {
                        field.value = params.get(name) || '';
                    }
                });

                if (updateHistory) {
                    history.pushState({}, '', targetUrl);
                }
            } catch (error) {
                window.location.href = targetUrl.href;
            } finally {
                table.classList.remove('opacity-50', 'pointer-events-none');
            }
        }

        // Search filters using AJAX.
        form.addEventListener('submit', function (event) {
            event.preventDefault();

            const url = new URL(form.action || window.location.pathname, window.location.origin);
            url.search = new URLSearchParams(new FormData(form)).toString();

            refreshSchedules(url);
        });

        // Clear all filters using AJAX.
        document.addEventListener('click', function (event) {
            const clearLink = event.target.closest('#schedule-clear-link');

            if (!clearLink) return;

            event.preventDefault();

            form.reset();

            filterNames.forEach(name => {
                const field = form.elements.namedItem(name);
                if (field) field.value = '';
            });

            refreshSchedules(clearLink.href);
        });

        // AJAX pagination only.
        table.addEventListener('click', function (event) {
            const link = event.target.closest(
                'nav[role="navigation"] a'
            );

            if (!link || !table.contains(link)) return;

            if (
                link.target === '_blank' ||
                event.ctrlKey ||
                event.metaKey ||
                event.shiftKey ||
                event.altKey
            ) {
                return;
            }

            const url = new URL(link.href, window.location.origin);

            if (url.origin !== window.location.origin) return;

            event.preventDefault();
            refreshSchedules(url);
        });

        // Support browser Back and Forward.
        window.addEventListener('popstate', function () {
            refreshSchedules(window.location.href, false);
        });
    });

</script>