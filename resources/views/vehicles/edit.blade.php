@extends('layouts.app')

@section('title', 'Edit Vehicle')

@section('content')

    <div class="mx-auto max-w-5xl">

        {{-- Header --}}
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">
                Edit Vehicle
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                Update vehicle information.
            </p>
        </div>

        {{-- Form --}}
        <form
            method="POST"
            action="{{ route('vehicles.update', $vehicle) }}"
            class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">

            @csrf
            @method('PUT')

            {{-- Form Content --}}
            <div class="p-5 sm:p-6">
                @include('vehicles._form')
            </div>

            {{-- Actions --}}
            <div class="flex flex-col-reverse gap-3 border-t border-gray-100 bg-gray-50 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">

                <p class="text-xs text-gray-500">
                    Update the information and save your changes.
                </p>

                <div class="flex items-center justify-end gap-3">

                    <a href="{{ route('vehicles.show', $vehicle) }}"
                        class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:border-gray-400 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="rounded-lg bg-gray-900 px-5 py-2 text-sm font-medium text-white transition-colors hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Update Vehicle
                    </button>

                </div>
            </div>

        </form>

        {{-- Attachment Modal --}}
        @include('vehicles.partials.attachment-modal')

    </div>

@endsection
