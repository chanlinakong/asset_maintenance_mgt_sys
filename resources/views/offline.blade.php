@extends('layouts.app')

@section('title', 'Offline')

@section('content')

<div class="flex min-h-[60vh] items-center justify-center">

    <div class="max-w-md text-center">

        <div class="text-6xl">
            📡
        </div>

        <h1 class="mt-6 text-2xl font-bold">
            You're Offline
        </h1>

        <p class="mt-3 text-gray-500">
            Your device is not currently connected to the internet.
            Please reconnect and try again.
        </p>

        <button
            type="button"
            onclick="window.location.reload()"
            class="mt-6 rounded-lg bg-gray-900 px-5 py-3 text-sm font-medium text-white hover:bg-gray-800"
        >
            Try Again
        </button>

    </div>

</div>

@endsection