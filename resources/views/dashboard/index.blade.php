@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

    <div class="space-y-6">

        {{-- Page heading --}}
        <div>
            <h1 class="text-2xl font-bold">
                Dashboard
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                Overview of vehicles and maintenance activities.
            </p>
        </div>


        {{-- Statistics --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

            <x-stat-card title="Total Vehicles" :value="$totalVehicles" icon="🚛" />

            <x-stat-card title="Active Vehicles" :value="$activeVehicles" icon="✅" />

            <x-stat-card title="Under Maintenance" :value="$vehiclesUnderMaintenance" icon="🔧" />

            <x-stat-card title="Out of Service" :value="$vehiclesOutOfService" icon="⚠️" />

        </div>


        {{-- Recent maintenance --}}
        <div class="rounded-xl bg-white shadow-sm">

            <div class="border-b px-6 py-4">

                <h2 class="font-semibold">
                    Recent Maintenance
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Latest maintenance activities.
                </p>

            </div>

            <div class="overflow-x-auto">

                <table class="min-w-full text-left text-sm">

                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 font-medium">
                                Vehicle
                            </th>

                            <th class="px-6 py-3 font-medium">
                                Maintenance
                            </th>

                            <th class="px-6 py-3 font-medium">
                                Status
                            </th>

                            <th class="px-6 py-3 font-medium">
                                Cost
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y">

                        @forelse($recentMaintenance as $maintenance)

                            <tr>
                                <td class="px-6 py-4">
                                    {{ $maintenance->vehicle->vehicle_code }}
                                </td>

                                <td class="px-6 py-4">
                                    {{ $maintenance->title }}
                                </td>

                                <td class="px-6 py-4">
                                    {{ str($maintenance->status->value)->replace('_', ' ')->title() }}
                                </td>

                                <td class="px-6 py-4">
                                    ${{ number_format((float) $maintenance->cost, 2) }}
                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                    No maintenance records found.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

@endsection