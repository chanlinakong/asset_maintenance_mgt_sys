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

            <a href="{{ route('vehicles.create') }}" class="rounded-lg bg-gray-900 px-4 py-2 text-center text-white">
                + Add Vehicle
            </a>

        </div>


        @if(session('success'))
            <div class="rounded-lg bg-green-50 px-4 py-3 text-green-700">
                {{ session('success') }}
            </div>
        @endif


        <div class="overflow-hidden rounded-xl bg-white shadow-sm">

            <div class="overflow-x-auto">

                <table class="min-w-full text-left text-sm">

                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3">
                                Code
                            </th>

                            <th class="px-6 py-3">
                                Name
                            </th>

                            <th class="px-6 py-3">
                                Type
                            </th>

                            <th class="px-6 py-3">
                                Registration
                            </th>

                            <th class="px-6 py-3">
                                Status
                            </th>

                            <th class="px-6 py-3 text-right">
                                Actions
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y">

                        @forelse($vehicles as $vehicle)

                                        <tr>

                                            <td class="px-6 py-4 font-medium">
                                                {{ $vehicle->vehicle_code }}
                                            </td>

                                            <td class="px-6 py-4">
                                                {{ $vehicle->name }}
                                            </td>

                                            <td class="px-6 py-4">
                                                {{ $vehicle->type }}
                                            </td>

                                            <td class="px-6 py-4">
                                                {{ $vehicle->registration_number ?? '-' }}
                                            </td>

                                            <td class="px-6 py-4">
                                                {{ str($vehicle->status->value)
                            ->replace('_', ' ')
                            ->title() }}
                                            </td>

                                            <td class="px-6 py-4">

                                                <div class="flex justify-end gap-2">

                                                    <a href="{{ route('vehicles.show', $vehicle) }}" class="rounded-lg border px-3 py-1.5">
                                                        View
                                                    </a>

                                                    <a href="{{ route('vehicles.edit', $vehicle) }}" class="rounded-lg border px-3 py-1.5">
                                                        Edit
                                                    </a>

                                                </div>

                                            </td>

                                        </tr>

                        @empty

                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                    No vehicles found.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        <div>
            {{ $vehicles->links() }}
        </div>

    </div>

    <div class="rounded-xl border border-red-200 bg-white p-6">

        <h2 class="font-semibold text-red-700">
            Remove Vehicle
        </h2>

        <p class="mt-1 text-sm text-gray-500">
            This vehicle will be removed from active records.
        </p>

        <form method="POST" action="{{ route('vehicles.destroy', $vehicle) }}" class="mt-4"
            onsubmit="return confirm('Are you sure you want to remove this vehicle?')">

            @csrf
            @method('DELETE')

            <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-white">
                Remove Vehicle
            </button>

        </form>

    </div>
@endsection