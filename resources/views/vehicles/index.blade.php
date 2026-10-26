@extends('layouts.app')

@section('title', 'Vehicles')

@section('content')

    <div class="space-y-6">

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h1 class="text-2xl font-bold">
                    Vehicles
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    Manage all registered vehicles and equipment.
                </p>
            </div>

            @if(auth()->user()->role->value === 'admin')
                <a href="{{ route('vehicles.create') }}"
                    class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">
                    + Add Vehicle
                </a>
            @endif

        </div>


        @if(session('success'))
            <div class="rounded-lg bg-green-50 px-4 py-3 text-green-700">
                {{ session('success') }}
            </div>
        @elseif(session('error'))
            <div class="rounded-lg bg-red-50 px-4 py-3 text-red-700">
                {{ session('error') }}
            </div>
        @endif


        <div class="overflow-hidden rounded-xl bg-white shadow-sm">

            <div class="p-4 sm:p-6">
                <form method="GET" action="{{ route('vehicles.index') }}" id="vehicle-filter-form">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center justify-end">

                        <input type="search" name="search" value="{{ $search }}"
                            placeholder="Search by code, name, type, brand..." class="w-full rounded-lg border border-gray-300 px-4 py-2
                                                            focus:border-gray-500 focus:outline-none
                                                            sm:max-w-md">

                        <select name="status" class="rounded-lg border border-gray-300 px-4 py-2">
                            <option value="">All Statuses</option>

                            @foreach ($statuses as $vehicleStatus)
                                                <option value="{{ $vehicleStatus->value }}" @selected($status === $vehicleStatus->value)>
                                                    {{ str($vehicleStatus->value)
                                ->replace('_', ' ')
                                ->title() }}
                                                </option>
                            @endforeach
                        </select>

                        <button type="submit" class="rounded-lg bg-gray-900 px-5 py-2 text-white
                                                               hover:bg-gray-800">
                            Search
                        </button>

                        <a id="vehicle-clear-link" href="{{ route('vehicles.index') }}"
                            class="{{ $search || $status ? '' : 'hidden' }} rounded-lg border border-gray-300 px-5 py-2 text-center hover:bg-gray-50">
                            Clear
                        </a>

                    </div>
                </form>
            </div>

            <div class="overflow-x-auto" id="vehicles-results">

                @include('vehicles.partials.table')

            </div>

        </div>

    </div>

@endsection


<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('vehicle-filter-form');
        const results = document.getElementById('vehicles-results');
        const clearLink = document.getElementById('vehicle-clear-link');

        if (!form || !results) return;

        async function loadVehicles(url, updateHistory = true) {
            const targetUrl = new URL(url, window.location.origin);

            results.style.opacity = '0.5';
            results.style.pointerEvents = 'none';

            try {
                const response = await fetch(targetUrl, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html'
                    }
                });

                if (!response.ok) {
                    throw new Error('Unable to load vehicles.');
                }

                results.innerHTML = await response.text();

                const params = targetUrl.searchParams;

                const hasFilters =
                    (params.get('search') || '').trim() !== '' ||
                    (params.get('status') || '').trim() !== '';

                if (clearLink) {
                    clearLink.classList.toggle('hidden', !hasFilters);
                }

                // Keep the filter controls synchronized with the URL.
                form.querySelector('[name="search"]').value =
                    params.get('search') || '';

                form.querySelector('[name="status"]').value =
                    params.get('status') || '';

                if (updateHistory) {
                    history.pushState({}, '', targetUrl);
                }
            } catch (error) {
                window.location.href = targetUrl.href;
            } finally {
                results.style.opacity = '1';
                results.style.pointerEvents = '';
            }
        }

        // Search and status filter submission.
        form.addEventListener('submit', event => {
            event.preventDefault();

            const url = new URL(form.action, window.location.origin);
            url.search = new URLSearchParams(
                new FormData(form)
            ).toString();

            loadVehicles(url);
        });

        // Clear search and status filters.
        clearLink?.addEventListener('click', event => {
            event.preventDefault();

            form.querySelector('[name="search"]').value = '';
            form.querySelector('[name="status"]').value = '';

            loadVehicles(clearLink.href);
        });

        // Intercept pagination links only.
        // View, Edit, and other action links should navigate normally.
        results.addEventListener('click', event => {
            const link = event.target.closest(
                'nav[role="navigation"] a'
            );

            if (!link || !results.contains(link)) return;

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
            loadVehicles(url);
        });

        const link = event.target.closest('a');

        if (!link || !results.contains(link)) return;

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
        loadVehicles(url);
    });

    // Support browser Back and Forward buttons.
    window.addEventListener('popstate', () => {
        loadVehicles(window.location.href, false);
    });
    });
</script>