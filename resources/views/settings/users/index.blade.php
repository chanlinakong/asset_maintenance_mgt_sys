@extends('layouts.app')

@section('title', 'Users & Access')

@section('content')
    <div class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">

        {{-- Page header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">
                    Users & Access
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Manage user accounts, roles, and account access.
                </p>
            </div>

            <a href="{{ route('settings.users.create') }}"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">
                <span class="text-lg leading-none">+</span>
                Create User
            </a>
        </div>

        {{-- Success message --}}
        @if (session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                {{ session('success') }}
            </div>
        @endif

        {{-- Validation errors --}}
        @if ($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <p class="font-semibold">Please check the following:</p>
                <ul class="mt-1 list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Search and filters --}}
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm sm:p-5">
            <form id="users-filter-form" method="GET" action="{{ route('settings.users.index') }}"
                class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">

                <div class="lg:col-span-2">
                    <label for="search" class="mb-1.5 block text-sm font-medium text-gray-700">
                        Search users
                    </label>

                    <input id="search" name="search" type="search" value="{{ request('search') }}"
                        placeholder="Name or email..."
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="role" class="mb-1.5 block text-sm font-medium text-gray-700">
                        Role
                    </label>

                    <select id="role" name="role"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All roles</option>
                        <option value="admin" @selected(request('role') === 'admin')>Admin</option>
                        <option value="staff" @selected(request('role') === 'staff')>Staff</option>
                    </select>
                </div>

                <div>
                    <label for="status" class="mb-1.5 block text-sm font-medium text-gray-700">
                        Account status
                    </label>

                    <select id="status" name="status"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All statuses</option>
                        <option value="active" @selected(request('status') === 'active')>Active</option>
                        <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                    </select>
                </div>

                <div class="flex items-end justify-end gap-2 sm:col-span-2 lg:col-span-4">
                    <button type="submit"
                        class="rounded-lg bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-gray-700 focus:outline-none">
                        Search
                    </button>

                    <a id="users-clear-link" href="{{ route('settings.users.index') }}"
                        class="{{ request('search') || request('role') || request('status') ? '' : 'hidden' }} shrink-0 rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition-colors hover:border-gray-400 hover:bg-gray-100 hover:text-gray-900">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        {{-- AJAX results container --}}
        <div id="users-results">
            @include('settings.users.partials.table')
        </div>

    </div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('users-filter-form');
        const results = document.getElementById('users-results');

        if (!form || !results) return;

        const filterNames = ['search', 'role', 'status'];

        function updateClearButton(params) {
            const clearLink = document.getElementById('users-clear-link');

            const hasFilters = filterNames.some(name =>
                (params.get(name) || '').trim() !== ''
            );

            clearLink?.classList.toggle('hidden', !hasFilters);
        }

        function syncForm(params) {
            filterNames.forEach(name => {
                const field = form.elements.namedItem(name);

                if (field) {
                    field.value = params.get(name) || '';
                }
            });

            updateClearButton(params);
        }

        async function refreshUsers(url, updateHistory = true) {
            const targetUrl = new URL(url, window.location.origin);

            if (targetUrl.origin !== window.location.origin) return;

            results.classList.add('opacity-50', 'pointer-events-none');

            try {
                const response = await fetch(targetUrl, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html'
                    }
                });

                if (!response.ok) {
                    throw new Error('Unable to load users.');
                }

                results.innerHTML = await response.text();

                syncForm(targetUrl.searchParams);

                if (updateHistory) {
                    history.pushState({}, '', targetUrl.href);
                }
            } catch (error) {
                window.location.href = targetUrl.href;
            } finally {
                results.classList.remove('opacity-50', 'pointer-events-none');
            }
        }

        // AJAX search
        form.addEventListener('submit', function (event) {
            event.preventDefault();

            const url = new URL(form.action, window.location.origin);
            url.search = new URLSearchParams(new FormData(form)).toString();

            refreshUsers(url);
        });

        // AJAX clear
        document.addEventListener('click', function (event) {
            const clearLink = event.target.closest('#users-clear-link');

            if (!clearLink) return;

            event.preventDefault();

            form.reset();

            filterNames.forEach(name => {
                const field = form.elements.namedItem(name);
                if (field) field.value = '';
            });

            refreshUsers(clearLink.href);
        });

        // AJAX pagination
        results.addEventListener('click', function (event) {
            const link = event.target.closest('a');

            if (!link || !results.contains(link)) return;

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

            // Only intercept pagination links.
            if (!link.closest('nav')) return;

            event.preventDefault();
            refreshUsers(url);
        });

        // Browser Back / Forward
        window.addEventListener('popstate', function () {
            refreshUsers(window.location.href, false);
        });
    });
</script>
