@extends('layouts.app')

@section('title', 'Maintenance Details')

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
                    {{ data_get($maintenance, 'title', '-') }}
                </h1>

                @php
                    $vehicleCode = data_get($maintenance, 'vehicle.vehicle_code');
                    $vehicleName = data_get($maintenance, 'vehicle.name');
                @endphp

                <p class="mt-1 text-sm text-gray-500">
                    {{ $vehicleCode ?: '-' }}
                    @if($vehicleCode && $vehicleName)
                        —
                        {{ $vehicleName }}
                    @elseif($vehicleName)
                        {{ $vehicleName }}
                    @endif
                </p>

            </div>


            <div class="flex gap-2">

                <a href="{{ route('maintenance.edit', $maintenance) }}"
                    class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                    Edit
                </a>
                @if(auth()->user()->role->value === 'admin')
                    <form method="POST" action="{{ route('maintenance.destroy', $maintenance) }}"
                        onsubmit="return confirm('Are you sure you want to delete this maintenance record?')">
                        @csrf
                        @method('DELETE')

                        <button type="submit"
                            class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">
                            Delete
                        </button>
                    </form>
                @endif
                <a href="{{ route('maintenance.index') }}" class="rounded-lg bg-gray-900 px-4 py-2 text-white">
                    Back
                </a>

            </div>

        </div>


        <div class="rounded-xl bg-white p-6 shadow-sm">

            <h2 class="mb-6 font-semibold">
                Maintenance Information
            </h2>


            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">

                <div>
                    <p class="text-sm text-gray-500">
                        Vehicle
                    </p>

                    <p class="font-medium">
                        {{ data_get($maintenance, 'vehicle.vehicle_code', '-') }}
                    </p>
                </div>


                @php
                    $maintenanceType = is_object($maintenance) || is_array($maintenance)
                        ? data_get($maintenance, 'type')
                        : null;
                    $maintenanceTypeValue = is_object($maintenanceType) && method_exists($maintenanceType, 'value')
                        ? $maintenanceType->value
                        : $maintenanceType;

                    $maintenanceStatus = is_object($maintenance) || is_array($maintenance)
                        ? data_get($maintenance, 'status')
                        : null;
                    $maintenanceStatusValue = is_object($maintenanceStatus) && method_exists($maintenanceStatus, 'value')
                        ? $maintenanceStatus->value
                        : $maintenanceStatus;
                @endphp

                <div>
                    <p class="text-sm text-gray-500">
                        Type
                    </p>

                    <p class="font-medium">
                        {{ $maintenanceTypeValue
        ? str($maintenanceTypeValue->value)
            ->replace('_', ' ')
            ->title()
        : '-' }}
                    </p>
                </div>


                <div>
                    <p class="text-sm text-gray-500">
                        Status
                    </p>

                    <p class="font-medium">
                        {{ $maintenanceStatusValue
        ? str($maintenanceStatusValue->value)
            ->replace('_', ' ')
            ->title()
        : '-' }}
                    </p>
                </div>


                <div>
                    <p class="text-sm text-gray-500">
                        Reported At
                    </p>

                    <p class="font-medium">
                        {{ $maintenance->reported_at
        ? $maintenance->reported_at->format('d M Y H:i')
        : '-' }}
                    </p>
                </div>


                <div>
                    <p class="text-sm text-gray-500">
                        Started At
                    </p>

                    <p class="font-medium">
                        {{ $maintenance->started_at
        ? $maintenance->started_at->format('d M Y H:i')
        : '-' }}
                    </p>
                </div>


                <div>
                    <p class="text-sm text-gray-500">
                        Completed At
                    </p>

                    <p class="font-medium">
                        {{ $maintenance->completed_at
        ? $maintenance->completed_at->format('d M Y H:i')
        : '-' }}
                    </p>
                </div>


                <div>
                    <p class="text-sm text-gray-500">
                        Cost
                    </p>

                    <p class="font-medium">
                        ${{ number_format((float) data_get($maintenance, 'cost', 0), 2) }}
                    </p>
                </div>


                <div>
                    <p class="text-sm text-gray-500">
                        Service Provider
                    </p>

                    <p class="font-medium">
                        {{ data_get($maintenance, 'service_provider', '-') }}
                    </p>
                </div>

            </div>


            <div class="mt-6 border-t pt-6">

                <h3 class="font-medium">
                    Description
                </h3>

                <p class="mt-2 text-gray-600">
                    {{ data_get($maintenance, 'description', '-') ?: '-' }}
                </p>

            </div>


            <div class="mt-6 border-t pt-6">

                <h3 class="font-medium">
                    Notes
                </h3>

                <p class="mt-2 text-gray-600">
                    {{ data_get($maintenance, 'notes', '-') ?: '-' }}
                </p>

            </div>

        </div>

    </div>

@endsection