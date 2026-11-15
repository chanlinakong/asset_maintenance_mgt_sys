
@extends('layouts.app')

@section('title', 'Spare Parts')

@section('content')
    <div class="space-y-6">

        {{-- Page Header --}}
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">
                    Spare Parts
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Manage the spare parts available for maintenance work.
                </p>
            </div>

            <a href="{{ route('parts.create') }}"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-500/20">

                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 4v16m8-8H4" />
                </svg>

                Add Spare Part
            </a>
        </div>

        {{-- Success Message --}}
        @if(session('success'))
            <div class="flex items-start gap-3 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                <svg class="mt-0.5 h-5 w-5 shrink-0" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M5 13l4 4L19 7" />
                </svg>

                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- Search and Filters --}}
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <form method="GET" action="{{ route('parts.index') }}"
                class="flex flex-col gap-3 sm:flex-row sm:items-center">

                {{-- Search Input --}}
                <div class="relative min-w-0 flex-1">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                        <svg class="h-4 w-4 text-gray-400" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m21 21-4.35-4.35" />
                        </svg>
                    </div>

                    <input
                        type="search"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search name, code, or category..."
                        class="block w-full rounded-lg border border-gray-300 bg-white py-2.5 pl-10 pr-3.5 text-sm text-gray-900 shadow-sm placeholder:text-gray-400 transition focus:border-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-500/10"
                    >
                </div>

                {{-- Status Filter --}}
                <div class="w-full sm:w-44">
                    <select name="status"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-700 shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-500/10">

                        <option value="">All statuses</option>

                        <option value="active" @selected(request('status') === 'active')>
                            Active
                        </option>

                        <option value="inactive" @selected(request('status') === 'inactive')>
                            Inactive
                        </option>
                    </select>
                </div>

                {{-- Search Button --}}
                <button type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-500/20">

                    Search
                </button>

                {{-- Reset Button --}}
                <a href="{{ route('parts.index') }}"
                    class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-600 shadow-sm transition hover:bg-gray-50 hover:text-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-200">

                    Reset
                </a>

            </form>
        </div>

        {{-- Parts Table --}}
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">

                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                        <tr>
                            <th class="whitespace-nowrap px-5 py-3.5">Part</th>
                            <th class="whitespace-nowrap px-5 py-3.5">Category</th>
                            <th class="whitespace-nowrap px-5 py-3.5">Unit</th>
                            <th class="whitespace-nowrap px-5 py-3.5 text-right">Default Cost</th>
                            <th class="whitespace-nowrap px-5 py-3.5 text-right">Stock</th>
                            <th class="whitespace-nowrap px-5 py-3.5">Status</th>
                            <th class="whitespace-nowrap px-5 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">

                        @forelse($parts as $part)
                            <tr class="transition hover:bg-gray-50/80">

                                {{-- Part Name --}}
                                <td class="whitespace-nowrap px-5 py-4">
                                    <div class="font-semibold text-gray-900">
                                        {{ $part->name }}
                                    </div>

                                    <div class="mt-1 text-xs text-gray-500">
                                        {{ $part->part_code ?: 'No code' }}
                                    </div>
                                </td>

                                {{-- Category --}}
                                <td class="whitespace-nowrap px-5 py-4 text-gray-600">
                                    {{ $part->category ?: '—' }}
                                </td>

                                {{-- Unit --}}
                                <td class="whitespace-nowrap px-5 py-4 text-gray-600">
                                    {{ $part->unit }}
                                </td>

                                {{-- Default Cost --}}
                                <td class="whitespace-nowrap px-5 py-4 text-right font-medium tabular-nums text-gray-800">
                                    ${{ number_format((float) $part->default_unit_cost, 2) }}
                                </td>

                                {{-- Stock --}}
                                <td class="whitespace-nowrap px-5 py-4 text-right font-medium tabular-nums text-gray-800">
                                    {{ number_format($part->stock_quantity) }}
                                </td>

                                {{-- Status --}}
                                <td class="whitespace-nowrap px-5 py-4">
                                    @if($part->is_active)
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-green-50 px-2.5 py-1 text-xs font-semibold text-green-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-600">
                                            <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span>
                                            Inactive
                                        </span>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td class="whitespace-nowrap px-5 py-4">
                                    <div class="flex items-center justify-end gap-2">

                                        {{-- Edit --}}
                                        <a href="{{ route('parts.edit', $part) }}"
                                            class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-semibold text-indigo-600 shadow-sm transition hover:border-indigo-200 hover:bg-indigo-50 focus:outline-none focus:ring-4 focus:ring-indigo-500/10">
                                            Edit
                                        </a>

                                        {{-- Activate / Deactivate --}}
                                        <form action="{{ route('parts.toggle-active', $part) }}"
                                            method="POST"
                                            onsubmit="return confirm('Are you sure you want to {{ $part->is_active ? 'deactivate' : 'activate' }} this part?')">

                                            @csrf
                                            @method('PATCH')

                                            <button type="submit"
                                                class="inline-flex items-center justify-center rounded-lg border px-3 py-1.5 text-xs font-semibold shadow-sm transition focus:outline-none focus:ring-4
                                                {{ $part->is_active
                                                    ? 'border-red-200 bg-white text-red-600 hover:bg-red-50 focus:ring-red-500/10'
                                                    : 'border-green-200 bg-white text-green-600 hover:bg-green-50 focus:ring-green-500/10' }}">

                                                {{ $part->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>

                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-16 text-center">

                                    <div class="flex flex-col items-center">
                                        <svg class="h-10 w-10 text-gray-300" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M20 7.5v9a2 2 0 0 1-1 1.73l-6 3.46a2 2 0 0 1-2 0l-6-3.46a2 2 0 0 1-1-1.73v-9a2 2 0 0 1 1-1.73l6-3.46a2 2 0 0 1 2 0l6 3.46a2 2 0 0 1 1 1.73Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m3.3 7 8.7 5 8.7-5M12 22V12" />
                                        </svg>

                                        <p class="mt-3 text-sm font-semibold text-gray-700">
                                            No spare parts found
                                        </p>

                                        <p class="mt-1 text-sm text-gray-500">
                                            Try changing your search or filter criteria.
                                        </p>

                                    </div>
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($parts->hasPages())
                <div class="border-t border-gray-100 px-5 py-4">
                    {{ $parts->appends(request()->query())->links() }}
                </div>
            @endif

        </div>

    </div>
@endsection
