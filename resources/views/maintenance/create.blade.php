@extends('layouts.app')

@section('title', 'Report Maintenance')

@section('content')

<div class="mx-auto max-w-5xl">

    <div class="mb-6">

        <h1 class="text-2xl font-bold">
            Report Maintenance
        </h1>

        <p class="mt-1 text-sm text-gray-500">
            Create a maintenance record for a vehicle.
        </p>

    </div>


    <form
        method="POST"
        action="{{ route('maintenance.store') }}"
        class="rounded-xl bg-white p-6 shadow-sm"
    >

        @csrf

        @include('maintenance._form')

        <div class="mt-6 flex justify-end gap-3">

            <a
                href="{{ route('maintenance.index') }}"
                class="rounded-lg border px-4 py-2"
            >
                Cancel
            </a>

            <button
                type="submit"
                class="rounded-lg bg-gray-900 px-4 py-2 text-white"
            >
                Save Maintenance
            </button>

        </div>

    </form>

</div>

@endsection