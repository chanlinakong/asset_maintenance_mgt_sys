
@extends('layouts.app')

@section('title', 'Defect Types')

@section('content')
    <div class="space-y-6">

        {{-- Page heading --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    Defect Types
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    Manage reusable categories for vehicle and equipment defects.
                </p>
            </div>

            <a href="{{ route('defect-types.create') }}"
                class="inline-flex items-center justify-center rounded-lg
                       bg-gray-900 px-4 py-2.5 text-sm font-medium text-white
                       transition hover:bg-gray-800">
                <span class="mr-2 text-lg">+</span>
                Add Defect Type
            </a>
        </div>

        {{-- Flash message --}}
        @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        {{-- Validation errors --}}
        @if($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="list-inside list-disc">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Search and filter --}}
        <div class="overflow-hidden rounded-xl bg-white shadow-sm">

            <div class="border-b border-gray-100 p-4 sm:p-6">
                <form method="GET"
                    action="{{ route('defect-types.index') }}"
                    id="defect-type-filter-form">

                    <div class="flex flex-col justify-end gap-3 sm:flex-row sm:items-center">

                        <input
                            type="search"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Search defect type..."
                            class="w-full rounded-lg border border-gray-300 px-4 py-2
                                   focus:border-gray-500 focus:outline-none
                                   focus:ring-2 focus:ring-gray-200 sm:max-w-md">

                        <select name="status"
                            class="rounded-lg border border-gray-300 px-4 py-2">
                            <option value="">All Statuses</option>
                            <option value="active" @selected($status === 'active')>
                                Active
                            </option>
                            <option value="inactive" @selected($status === 'inactive')>
                                Inactive
                            </option>
                        </select>

                        <button type="submit"
                            class="rounded-lg bg-gray-900 px-5 py-2 text-sm
                                   font-medium text-white hover:bg-gray-800">
                            Search
                        </button>

                        <a id="defect-type-clear-link"
                            href="{{ route('defect-types.index') }}"
                            class="{{ $search || $status ? '' : 'hidden' }}
                                   rounded-lg border border-gray-300 px-5 py-2
                                   text-center text-sm hover:bg-gray-50">
                            Clear
                        </a>
                    </div>
                </form>
            </div>

            {{-- Results --}}
            <div id="defect-types-results">
                @include('defect-types.partials.table')
            </div>

        </div>
    </div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('defect-type-filter-form');
    const results = document.getElementById('defect-types-results');
    const clearLink = document.getElementById('defect-type-clear-link');

    if (!form || !results) return;

    async function loadDefectTypes(url, updateHistory = true) {
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
                throw new Error('Unable to load defect types.');
            }

            results.innerHTML = await response.text();

            const params = targetUrl.searchParams;
            const search = params.get('search') || '';
            const status = params.get('status') || '';

            form.querySelector('[name="search"]').value = search;
            form.querySelector('[name="status"]').value = status;

            clearLink?.classList.toggle(
                'hidden',
                search.trim() === '' && status === ''
            );

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

    form.addEventListener('submit', event => {
        event.preventDefault();

        const url = new URL(form.action, window.location.origin);
        url.search = new URLSearchParams(new FormData(form)).toString();

        loadDefectTypes(url);
    });

    clearLink?.addEventListener('click', event => {
        event.preventDefault();

        form.querySelector('[name="search"]').value = '';
        form.querySelector('[name="status"]').value = '';

        loadDefectTypes(clearLink.href);
    });

    results.addEventListener('click', event => {
        const link = event.target.closest('nav[role="navigation"] a');

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
        loadDefectTypes(url);
    });

    window.addEventListener('popstate', () => {
        loadDefectTypes(window.location.href, false);
    });
});
</script>
