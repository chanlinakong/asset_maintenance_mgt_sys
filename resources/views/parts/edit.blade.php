
@extends('layouts.app')

@section('title', 'Edit Spare Part')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">

    <div>
        <a href="{{ route('parts.index') }}"
           class="text-sm text-gray-500 hover:text-indigo-600">
            &larr; Back to Parts
        </a>

        <h1 class="mt-2 text-2xl font-bold text-gray-900">
            Edit Spare Part
        </h1>
        <p class="mt-1 text-sm text-gray-500">
            Update the catalog information for {{ $part->name }}.
        </p>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <form action="{{ route('parts.update', $part) }}" method="POST">
            @csrf
            @method('PUT')
            @include('parts._form')
        </form>
    </div>

</div>
@endsection
