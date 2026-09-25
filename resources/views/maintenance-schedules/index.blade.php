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
                    class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">
                    + New Schedule
                </a>
            @endcan

        </div>

        <form method="GET" class="rounded-xl bg-white p-4 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row">

                <input type="text" name="search" value="{{ $search }}" placeholder="Search schedule or vehicle..."
                    class="w-full rounded-lg border-gray-300 sm:flex-1">

                <select name="status" class="rounded-lg border-gray-300">
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

                <button type="submit" class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white">
                    Search
                </button>

            </div>
        </form>

        <div class="overflow-hidden rounded-xl bg-white shadow-sm">

            <div class="hidden overflow-x-auto md:block">

                <table class="min-w-full divide-y divide-gray-200">

                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                Vehicle
                            </th>

                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                Maintenance
                            </th>

                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                Next Due
                            </th>

                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                Status
                            </th>

                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase text-gray-500">
                                Action
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">

                        @forelse($schedules as $schedule)

                                            <tr>

                                                <td class="px-6 py-4">
                                                    <p class="font-medium text-gray-900">
                                                        {{ $schedule->vehicle->vehicle_code }}
                                                    </p>

                                                    <p class="text-sm text-gray-500">
                                                        {{ $schedule->vehicle->name }}
                                                    </p>
                                                </td>

                                                <td class="px-6 py-4">
                                                    <p class="font-medium text-gray-900">
                                                        {{ $schedule->title }}
                                                    </p>

                                                    <p class="text-sm text-gray-500">
                                                        @if($schedule->interval_days)
                                                            Every {{ $schedule->interval_days }} days
                                                        @endif

                                                        @if(
                                                                $schedule->interval_days
                                                                && $schedule->interval_kilometers
                                                            )
                                                            ·
                                                        @endif

                                                        @if($schedule->interval_kilometers)
                                                            Every {{ number_format($schedule->interval_kilometers) }} km
                                                        @endif
                                                    </p>
                                                </td>

                                                <td class="px-6 py-4 text-sm text-gray-700">
                                                    {{ $schedule->next_due_date?->format('d M Y') ?? '—' }}
                                                </td>

                                                <td class="px-6 py-4">

                                                    @php
                                                        $currentKilometers = $schedule->vehicle?->current_kilometers;
                                                    @endphp

                                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ 
                                $schedule->dueStatusClasses($currentKilometers)
                            }}">
                                                        {{ $schedule->dueStatusLabel($currentKilometers) }}
                                                    </span>

                                                </td>

                                                <td class="px-6 py-4 text-right">

                                                    <a href="{{ route('maintenance-schedules.show', $schedule) }}"
                                                        class="text-sm font-medium text-gray-700 hover:text-gray-900">
                                                        View
                                                    </a>

                                                </td>

                                            </tr>

                        @empty

                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">
                                    No maintenance schedules found.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            {{-- Mobile --}}
            <div class="divide-y divide-gray-100 md:hidden">

                @forelse($schedules as $schedule)

                    <a href="{{ route('maintenance-schedules.show', $schedule) }}" class="block p-4 hover:bg-gray-50">

                        <div class="flex items-start justify-between gap-3">

                            <div>
                                <p class="font-semibold text-gray-900">
                                    {{ $schedule->title }}
                                </p>

                                <p class="mt-1 text-sm text-gray-500">
                                    {{ $schedule->vehicle->vehicle_code }}
                                    ·
                                    {{ $schedule->vehicle->name }}
                                </p>
                            </div>

                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $schedule->dueStatusClasses() }}">
                                {{ $schedule->dueStatusLabel($schedule->vehicle->current_kilometers) }}
                            </span>

                        </div>

                        <p class="mt-3 text-sm text-gray-500">
                            Next due:
                            <span class="font-medium text-gray-700">
                                {{ $schedule->next_due_date?->format('d M Y') ?? '—' }}
                            </span>
                        </p>

                    </a>

                @empty

                    <div class="p-8 text-center text-sm text-gray-500">
                        No maintenance schedules found.
                    </div>

                @endforelse

            </div>

        </div>

        <div>
            {{ $schedules->links() }}
        </div>

    </div>

@endsection