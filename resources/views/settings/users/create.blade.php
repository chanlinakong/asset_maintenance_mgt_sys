@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-3xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">

    {{-- Header --}}
    <div>
        <a href="{{ route('settings.users.index') }}"
           class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 transition hover:text-indigo-600">
            <span aria-hidden="true">&larr;</span>
            Back to Users & Access
        </a>

        <h1 class="mt-3 text-2xl font-bold tracking-tight text-gray-900">
            Create User
        </h1>
        <p class="mt-1 text-sm text-gray-500">
            Create an account and assign its access role.
        </p>
    </div>

    {{-- Errors --}}
    @if ($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <p class="font-semibold">Please correct the following errors:</p>
            <ul class="mt-1 list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Create form --}}
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 px-5 py-4 sm:px-6">
            <h2 class="font-semibold text-gray-900">Account information</h2>
            <p class="mt-1 text-sm text-gray-500">
                All fields are required.
            </p>
        </div>

        <form method="POST" action="{{ route('settings.users.store') }}">
            @csrf

            <div class="space-y-5 p-5 sm:p-6">

                {{-- Name --}}
                <div>
                    <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700">
                        Full name
                    </label>
                    <input
                        id="name"
                        name="name"
                        type="text"
                        value="{{ old('name') }}"
                        autocomplete="name"
                        required
                        autofocus
                        maxlength="255"
                        placeholder="Enter full name"
                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="mb-1.5 block text-sm font-medium text-gray-700">
                        Email address
                    </label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        autocomplete="email"
                        required
                        maxlength="255"
                        placeholder="name@example.com"
                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Role --}}
                <div>
                    <label for="role" class="mb-1.5 block text-sm font-medium text-gray-700">
                        User role
                    </label>
                    <select
                        id="role"
                        name="role"
                        required
                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="staff" @selected(old('role', 'staff') === 'staff')>
                            Staff
                        </option>
                        <option value="admin" @selected(old('role') === 'admin')>
                            Admin
                        </option>
                    </select>
                    <p class="mt-1.5 text-xs text-gray-500">
                        Admins can manage settings and user access. Staff permissions are controlled by your application's route and authorization rules.
                    </p>
                    @error('role')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="border-t border-gray-100 pt-5">
                    <h3 class="text-sm font-semibold text-gray-900">Login credentials</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Use a password of at least 8 characters.
                    </p>
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="mb-1.5 block text-sm font-medium text-gray-700">
                        Password
                    </label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        autocomplete="new-password"
                        required
                        minlength="8"
                        placeholder="Enter password"
                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirm password --}}
                <div>
                    <label for="password_confirmation" class="mb-1.5 block text-sm font-medium text-gray-700">
                        Confirm password
                    </label>
                    <input
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        autocomplete="new-password"
                        required
                        minlength="8"
                        placeholder="Enter the same password again"
                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex flex-col-reverse gap-3 border-t border-gray-200 bg-gray-50 px-5 py-4 sm:flex-row sm:justify-end sm:px-6">
                <a
                    href="{{ route('settings.users.index') }}"
                    class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-100"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700"
                >
                    Create Account
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
