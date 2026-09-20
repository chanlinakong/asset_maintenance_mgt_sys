@extends('layouts.app')

@section('title', 'Edit Maintenance Schedule')

@section('content')

<div class="mx-auto max-w-4xl space-y-6">

    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold">
            Edit Maintenance Schedule
        </h1>

        <p class="mt-1 text-sm text-gray-500">
            Update the maintenance schedule information.
        </p>
    </div>

    {{-- Validation errors --}}
    @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 p-4">
            <ul class="list-disc space-y-1 pl-5 text-sm text-red-700">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form --}}
    <div class="rounded-xl bg-white p-6 shadow-sm">

        <form
            method="POST"
            action="{{ route('maintenance-schedules.update', $maintenanceSchedule) }}"
            class="space-y-6"
        >

            @csrf
            @method('PUT')

            @include(
                'maintenance-schedules._form',
                [
                    'maintenanceSchedule' => $maintenanceSchedule,
                    'vehicles' => $vehicles,
                ]
            )

            <div class="flex items-center justify-end gap-3">

                <a
                    href="{{ route('maintenance-schedules.show', $maintenanceSchedule) }}"
                    class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
                >
                    Update Schedule
                </button>

            </div>

        </form>

    </div>

</div>

@endsection