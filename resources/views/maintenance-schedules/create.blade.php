@extends('layouts.app')

@section('title', 'Create Maintenance Schedule')

@section('content')

<div class="mx-auto max-w-3xl space-y-6">

    <div>
        <h1 class="text-2xl font-bold text-gray-900">
            Create Maintenance Schedule
        </h1>

        <p class="mt-1 text-sm text-gray-500">
            Create a preventive maintenance schedule for a vehicle.
        </p>
    </div>

    <form
        method="POST"
        action="{{ route('maintenance-schedules.store') }}"
        class="rounded-xl bg-white p-6 shadow-sm"
    >
        @csrf

        @include('maintenance-schedules._form')

        <div class="mt-8 flex justify-end gap-3">

            <a
                href="{{ route('maintenance-schedules.index') }}"
                class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition-colors
                                                    hover:border-gray-400 hover:bg-gray-100 hover:text-gray-900
                                                    focus:outline-none focus:ring-2 focus:ring-gray-300"
            >
                Cancel
            </a>

            <button
                type="submit"
                class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white text-white transition-colors
                                                        hover:border-gray-700 hover:bg-gray-700 hover:text-white-100
                                                        focus:outline-none focus:ring-2 focus:ring-gray-300"
            >
                Create
            </button>

        </div>

    </form>

</div>

@endsection