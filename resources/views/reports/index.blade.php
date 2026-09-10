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

            <div class="flex flex-wrap gap-2">

                <button type="button" onclick="window.print()"
                    class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">
                    🖨️ Print
                </button>

                <a href="{{ route('reports.pdf', request()->query()) }}"
                    class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">
                    📄 PDF
                </a>

            </div>
        </div>


        {{-- Filters --}}
        <div class="rounded-xl bg-white p-4 shadow-sm sm:p-6">

            <form method="GET" action="{{ route('reports.index') }}"
                class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">

                {{-- From --}}
                <div>
                    <label for="from" class="mb-1 block text-sm font-medium text-gray-700">
                        From
                    </label>

                    <input id="from" name="from" type="date" value="{{ $from }}"
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-gray-900 focus:ring-gray-900">
                </div>


                {{-- To --}}
                <div>
                    <label for="to" class="mb-1 block text-sm font-medium text-gray-700">
                        To
                    </label>

                    <input id="to" name="to" type="date" value="{{ $to }}"
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-gray-900 focus:ring-gray-900">
                </div>


                {{-- Status --}}
                <div>
                    <label for="status" class="mb-1 block text-sm font-medium text-gray-700">
                        Status
                    </label>

                    <select id="status" name="status"
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-gray-900 focus:ring-gray-900">
                        <option value="">
                            All statuses
                        </option>

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
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-gray-900 focus:ring-gray-900">
                        <option value="">
                            All types
                        </option>

                        @foreach($types as $item)
                            <option value="{{ $item->value }}" @selected($type === $item->value)>
                                {{ str($item->value)->replace('_', ' ')->title() }}
                            </option>
                        @endforeach
                    </select>
                </div>


                {{-- Buttons --}}
                <div class="flex items-end gap-2">

                    <button type="submit"
                        class="flex-1 rounded-lg bg-gray-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-gray-800">
                        Filter
                    </button>

                    <a href="{{ route('reports.index') }}"
                        class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Reset
                    </a>

                </div>

            </form>

        </div>


        {{-- Summary Cards --}}
        <div class="grid grid-cols-2 gap-4 lg:grid-cols-5">

            <x-stat-card title="Maintenance Records" :value="$totalRecords" icon="🔧" />

            <x-stat-card title="Total Cost" :value="'$' . number_format($totalCost, 2)" icon="💰" />

            <x-stat-card title="Completed" :value="$completedCount" icon="✅" />

            <x-stat-card title="In Progress" :value="$inProgressCount" icon="🔄" />

            <x-stat-card title="Vehicles in Maintenance" :value="$vehiclesUnderMaintenance" icon="🚛" />

        </div>


        {{-- Cost by Vehicle --}}
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

                                <a href="{{ route('vehicles.show', $item['vehicle']) }}" class="font-semibold text-gray-900">
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

                                                <span
                                                    class="inline-flex rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">
                                                    {{ str($record->status->value)
                            ->replace('_', ' ')
                            ->title() }}
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