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

            <a
                href="{{ route('vehicles.edit', $vehicle) }}"
                class="rounded-lg border px-4 py-2"
            >
                Edit
            </a>

            <a
                href="{{ route('vehicles.index') }}"
                class="rounded-lg bg-gray-900 px-4 py-2 text-white"
            >
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

</div>

@endsection