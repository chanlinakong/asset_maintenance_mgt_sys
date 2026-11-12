
@extends('layouts.app')

@section('title', 'Edit Defect Type')

@section('content')
    <div class="mx-auto max-w-3xl space-y-6">

        <div>
            <!-- <a href="{{ route('defect-types.index') }}"
                class="text-sm text-gray-500 hover:text-gray-900">
                ← Back to Defect Types
            </a> -->

            <h1 class="mt-3 text-2xl font-bold text-gray-900">
                Edit Defect Type
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                Update the category information.
            </p>
        </div>

        <div class="rounded-xl bg-white p-5 shadow-sm sm:p-7">

            <form method="POST"
                action="{{ route('defect-types.update', $defectType) }}">

                @include('defect-types._form')

            </form>

        </div>
    </div>
@endsection
