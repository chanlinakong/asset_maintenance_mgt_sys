{{-- Desktop table --}}
<div class="hidden overflow-x-auto md:block">

    <table class="min-w-full divide-y divide-gray-200">

        <thead class="bg-gray-50">
            <tr>
                <th
                    class="whitespace-nowrap px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                    Vehicle
                </th>

                <th
                    class="whitespace-nowrap px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                    Maintenance
                </th>

                <th
                    class="whitespace-nowrap px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                    Next Due
                </th>

                <th
                    class="whitespace-nowrap px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                    Status
                </th>

                <th
                    class="whitespace-nowrap px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                    Actions
                </th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-100">

            @forelse ($schedules as $schedule)

                @php
                    $currentKilometers = $schedule->vehicle?->current_kilometers;
                @endphp

                <tr class="transition-colors hover:bg-gray-50">

                    {{-- Vehicle --}}
                    <td class="whitespace-nowrap px-6 py-4">
                        <p class="font-semibold text-gray-900">
                            {{ $schedule->vehicle?->vehicle_code ?? '—' }}
                        </p>

                        <p class="mt-1 text-sm text-gray-500">
                            {{ $schedule->vehicle?->name ?? 'Unknown vehicle' }}
                        </p>
                    </td>

                    {{-- Maintenance --}}
                    <td class="px-6 py-4">
                        <p class="font-medium text-gray-900">
                            {{ $schedule->title }}
                        </p>

                        <p class="mt-1 text-sm text-gray-500">
                            @if ($schedule->interval_days)
                                Every {{ $schedule->interval_days }} days
                            @endif

                            @if ($schedule->interval_days && $schedule->interval_kilometers)
                                <span class="mx-1 text-gray-300">·</span>
                            @endif

                            @if ($schedule->interval_kilometers)
                                Every {{ number_format($schedule->interval_kilometers) }} km
                            @endif

                            @if (!$schedule->interval_days && !$schedule->interval_kilometers)
                                No interval specified
                            @endif
                        </p>
                    </td>

                    {{-- Next due --}}
                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">
                        {{ $schedule->next_due_date?->format('d M Y') ?? '—' }}
                    </td>

                    {{-- Status --}}
                    <td class="whitespace-nowrap px-6 py-4">
                        <span
                            class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $schedule->dueStatusClasses($currentKilometers) }}">
                            {{ $schedule->dueStatusLabel($currentKilometers) }}
                        </span>
                    </td>

                    {{-- Actions --}}
                    <td class="whitespace-nowrap px-6 py-4">
                        <div class="flex justify-end gap-2">

                            <a href="{{ route('maintenance-schedules.show', $schedule) }}"
                                class="inline-flex items-center rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 transition hover:border-gray-400 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-300">
                                View
                            </a>

                            @can('update', $schedule)
                                <a href="{{ route('maintenance-schedules.edit', $schedule) }}"
                                    class="inline-flex items-center rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 transition hover:border-gray-400 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-300">
                                    Edit
                                </a>
                            @endcan

                        </div>
                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="5" class="px-6 py-14 text-center">
                        <p class="font-medium text-gray-700">
                            No maintenance schedules found
                        </p>
                        <p class="mt-1 text-sm text-gray-500">
                            Try changing your filters or add a maintenance schedule.
                        </p>
                    </td>
                </tr>

            @endforelse

        </tbody>
    </table>
</div>

{{-- Mobile cards --}}
<div class="divide-y divide-gray-100 md:hidden">

    @forelse ($schedules as $schedule)

        @php
            $currentKilometers = $schedule->vehicle?->current_kilometers;
        @endphp

        <div class="p-4 transition-colors hover:bg-gray-50">

            <div class="flex items-start justify-between gap-3">

                <div class="min-w-0 flex-1">
                    <p class="break-words font-semibold text-gray-900">
                        {{ $schedule->title }}
                    </p>

                    <p class="mt-1 text-sm text-gray-500">
                        {{ $schedule->vehicle?->vehicle_code ?? '—' }}
                        <span class="mx-1 text-gray-300">·</span>
                        {{ $schedule->vehicle?->name ?? 'Unknown vehicle' }}
                    </p>
                </div>

                <span
                    class="inline-flex shrink-0 items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $schedule->dueStatusClasses($currentKilometers) }}">
                    {{ $schedule->dueStatusLabel($currentKilometers) }}
                </span>

            </div>

            <div class="mt-3 flex flex-wrap items-center justify-between gap-3">

                <p class="text-sm text-gray-500">
                    Next due:
                    <span class="font-medium text-gray-800">
                        {{ $schedule->next_due_date?->format('d M Y') ?? '—' }}
                    </span>
                </p>

                <div class="flex items-center gap-3">

                    <a href="{{ route('maintenance-schedules.show', $schedule) }}"
                        class="text-sm font-medium text-gray-700 underline-offset-4 hover:text-black hover:underline">
                        View details
                    </a>

                    @can('update', $schedule)
                        <a href="{{ route('maintenance-schedules.edit', $schedule) }}"
                            class="text-sm font-medium text-gray-700 underline-offset-4 hover:text-black hover:underline">
                            Edit
                        </a>
                    @endcan

                </div>

            </div>

        </div>

    @empty

        <div class="px-4 py-12 text-center">
            <p class="font-medium text-gray-700">
                No maintenance schedules found
            </p>
            <p class="mt-1 text-sm text-gray-500">
                Try changing your filters or add a maintenance schedule.
            </p>
        </div>

    @endforelse

</div>

{{-- Pagination: visible on desktop and mobile --}}
@if ($schedules->hasPages())
    <div>
        {{ $schedules->links('vendors.pagination.custom') }}
    </div>
@endif