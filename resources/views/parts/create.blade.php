@extends('layouts.app')

@section('title', 'Add Spare Part')

@section('content')
    <div class="mx-auto max-w-4xl space-y-6">

        <div>
            <!-- <a href="{{ route('parts.index') }}" class="text-sm text-gray-500 hover:text-indigo-600">
                &larr; Back to Parts
            </a> -->

            <h1 class="mt-2 text-2xl font-bold text-gray-900">
                Add Spare Part
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                Add a spare part to the maintenance parts catalog.
            </p>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">

            @php
                $cancelUrl = $returnTo;
            @endphp

            <form action="{{ route('parts.store') }}" method="POST">
                @csrf

                <input type="hidden" name="return_to" value="{{ old('return_to', $returnTo) }}">

                @include('parts._form')
            </form>

        </div>

    </div>
@endsection