@extends('layouts.app')

@section('title', 'Vehicle Details')

@section('content')

    <div class="space-y-6">

        @if(session('success'))
            <div class="rounded-lg bg-green-50 px-4 py-3 text-green-700">
                {{ session('success') }}
            </div>
        @endif


        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>

                <h1 class="text-2xl font-bold">
                    {{ $vehicle->name }}
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    {{ $vehicle->vehicle_code }}
                </p>

            </div>


            <div class="flex gap-2">

                <a href="{{ route('vehicles.edit', $vehicle) }}" class="rounded-lg border px-4 py-2">
                    Edit
                </a>

                <a href="{{ route('vehicles.index') }}" class="rounded-lg bg-gray-900 px-4 py-2 text-white">
                    Back
                </a>

            </div>

        </div>


        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

            <div class="rounded-xl bg-white p-6 shadow-sm lg:col-span-2">

                <h2 class="mb-5 font-semibold">
                    Vehicle Information
                </h2>


                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                    <div>
                        <p class="text-sm text-gray-500">
                            Vehicle Code
                        </p>

                        <p class="font-medium">
                            {{ $vehicle->vehicle_code }}
                        </p>
                    </div>


                    <div>
                        <p class="text-sm text-gray-500">
                            Type
                        </p>

                        <p class="font-medium">
                            {{ $vehicle->type }}
                        </p>
                    </div>


                    <div>
                        <p class="text-sm text-gray-500">
                            Brand
                        </p>

                        <p class="font-medium">
                            {{ $vehicle->brand ?? '-' }}
                        </p>
                    </div>


                    <div>
                        <p class="text-sm text-gray-500">
                            Model
                        </p>

                        <p class="font-medium">
                            {{ $vehicle->model ?? '-' }}
                        </p>
                    </div>


                    <div>
                        <p class="text-sm text-gray-500">
                            Registration Number
                        </p>

                        <p class="font-medium">
                            {{ $vehicle->registration_number ?? '-' }}
                        </p>
                    </div>


                    <div>
                        <p class="text-sm text-gray-500">
                            Status
                        </p>

                        <p class="font-medium">
                            {{ str($vehicle->status->value)
        ->replace('_', ' ')
        ->title() }}
                        </p>
                    </div>


                    <div>
                        <p class="text-sm text-gray-500">
                            Purchase Date
                        </p>

                        <p class="font-medium">
                            {{ $vehicle->purchase_date
        ? $vehicle->purchase_date->format('d M Y')
        : '-' }}
                        </p>
                    </div>

                </div>

            </div>


            <div class="rounded-xl bg-white p-6 shadow-sm">

                <h2 class="mb-4 font-semibold">
                    Notes
                </h2>

                <p class="text-sm text-gray-600">
                    {{ $vehicle->notes ?: 'No notes available.' }}
                </p>

            </div>

        </div>

        <div class="rounded-xl bg-white shadow-sm">

            <div class="flex flex-col gap-3 border-b px-6 py-5 sm:flex-row sm:items-center sm:justify-between">

                <div>
                    <h2 class="font-semibold">
                        Maintenance History
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Maintenance records for this vehicle.
                    </p>
                </div>

                <a href="{{ route('maintenance.create') }}"
                    class="rounded-lg bg-gray-900 px-4 py-2 text-center text-sm font-medium text-white hover:bg-gray-800">
                    + Report Maintenance
                </a>

            </div>


            @if($vehicle->maintenanceRecords->isNotEmpty())

                <div class="overflow-x-auto">

                    <table class="w-full min-w-[700px] text-left text-sm">

                        <thead class="border-b bg-gray-50 text-xs uppercase text-gray-500">

                            <tr>
                                <th class="px-6 py-3">
                                    Reported
                                </th>

                                <th class="px-6 py-3">
                                    Maintenance
                                </th>

                                <th class="px-6 py-3">
                                    Type
                                </th>

                                <th class="px-6 py-3">
                                    Status
                                </th>

                                <th class="px-6 py-3">
                                    Cost
                                </th>

                                <th class="px-6 py-3">
                                    Action
                                </th>
                            </tr>

                        </thead>


                        <tbody class="divide-y">

                            @foreach($vehicle->maintenanceRecords as $maintenance)

                                        <tr class="hover:bg-gray-50">

                                            <td class="whitespace-nowrap px-6 py-4 text-gray-600">
                                                {{ $maintenance->reported_at->format('d M Y H:i') }}
                                            </td>


                                            <td class="px-6 py-4">

                                                <a href="{{ route('maintenance.show', $maintenance) }}"
                                                    class="font-medium text-blue-600 hover:underline">
                                                    {{ $maintenance->title }}
                                                </a>

                                            </td>


                                            <td class="px-6 py-4">

                                                <span
                                                    class="inline-flex rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">
                                                    {{ str($maintenance->type->value)
                                ->replace('_', ' ')
                                ->title() }}
                                                </span>

                                            </td>


                                            <td class="px-6 py-4">

                                                @php
                                                    $maintenanceStatus = $maintenance->status;
                                                @endphp

                                                <span @class([
                                                    'inline-flex rounded-full px-3 py-1 text-xs font-semibold',

                                                    'bg-yellow-100 text-yellow-700' =>
                                                        $maintenanceStatus->value === 'pending',

                                                    'bg-blue-100 text-blue-700' =>
                                                        $maintenanceStatus->value === 'in_progress',

                                                    'bg-green-100 text-green-700' =>
                                                        $maintenanceStatus->value === 'completed',

                                                    'bg-red-100 text-red-700' =>
                                                        $maintenanceStatus->value === 'cancelled',
                                                ])>
                                                    {{ str($maintenanceStatus->value)
                                ->replace('_', ' ')
                                ->title() }}
                                                </span>

                                            </td>


                                            <td class="whitespace-nowrap px-6 py-4 font-medium">
                                                ${{ number_format((float) $maintenance->cost, 2) }}
                                            </td>


                                            <td class="px-6 py-4">

                                                <a href="{{ route('maintenance.show', $maintenance) }}"
                                                    class="font-medium text-blue-600 hover:underline">
                                                    View
                                                </a>

                                            </td>

                                        </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @else

                <div class="px-6 py-10 text-center">

                    <div class="text-4xl">
                        🔧
                    </div>

                    <h3 class="mt-3 font-semibold text-gray-900">
                        No Maintenance Records
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        This vehicle does not have any maintenance records yet.
                    </p>

                    <a href="{{ route('maintenance.create', ['vehicle_id' => $vehicle->id]) }}"
                        class="mt-4 inline-block rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">
                        Report Maintenance
                    </a>

                </div>

            @endif

        </div>

    </div>

@endsection