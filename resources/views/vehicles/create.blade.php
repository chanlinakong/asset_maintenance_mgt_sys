@extends('layouts.app')

@section('title', 'Add Vehicle')

@section('content')

<div class="mx-auto max-w-5xl">

    <div class="mb-6">
        <h1 class="text-2xl font-bold">
            Add Vehicle
        </h1>

        <p class="mt-1 text-sm text-gray-500">
            Register a new vehicle or equipment.
        </p>
    </div>


    <form
        method="POST"
        action="{{ route('vehicles.store') }}"
        class="rounded-xl bg-white p-6 shadow-sm"
    >

        @csrf

        @include('vehicles._form')

        <div class="mt-6 flex justify-end gap-3">

            <a
                href="{{ route('vehicles.index') }}"
                class="rounded-lg border px-4 py-2 transition-colors
                                                    hover:border-gray-400 hover:bg-gray-100 hover:text-gray-900
                                                    focus:outline-none focus:ring-2 focus:ring-gray-300"
            >
                Cancel
            </a>

            <button
                type="submit"
                class="rounded-lg bg-gray-900 px-4 py-2 text-white transition-colors
                                                        hover:border-gray-700 hover:bg-gray-700 hover:text-white-100
                                                        focus:outline-none focus:ring-2 focus:ring-gray-300"
            >
                Save
            </button>

        </div>

    </form>

</div>

@endsection