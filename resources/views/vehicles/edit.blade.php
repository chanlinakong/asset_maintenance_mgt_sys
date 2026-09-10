@extends('layouts.app')

@section('title', 'Edit Vehicle')

@section('content')

<div class="mx-auto max-w-5xl">

    <div class="mb-6">

        <h1 class="text-2xl font-bold">
            Edit Vehicle
        </h1>

        <p class="mt-1 text-sm text-gray-500">
            Update vehicle information.
        </p>

    </div>


    <form
        method="POST"
        action="{{ route('vehicles.update', $vehicle) }}"
        class="rounded-xl bg-white p-6 shadow-sm"
    >

        @csrf
        @method('PUT')

        @include('vehicles._form')

        <div class="mt-6 flex justify-end gap-3">

            <a
                href="{{ route('vehicles.show', $vehicle) }}"
                class="rounded-lg border px-4 py-2"
            >
                Cancel
            </a>

            <button
                type="submit"
                class="rounded-lg bg-gray-900 px-4 py-2 text-white"
            >
                Update Vehicle
            </button>

        </div>

    </form>

</div>

@endsection