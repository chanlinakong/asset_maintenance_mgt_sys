
@extends('layouts.app')

@section('title', 'Settings Administration')

@section('content')
    <div class="mx-auto max-w-7xl space-y-6">

        {{-- Page Header --}}
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                Settings Administration
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                Manage system access and maintenance configuration.
            </p>
        </div>

        {{-- Administration Modules --}}
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">

            {{-- Users & Access --}}
            <a href="{{ url('/settings/users') }}"
                class="group rounded-xl border border-gray-200 bg-white p-6
                       shadow-sm transition hover:border-blue-300
                       hover:shadow-md">

                <div class="flex h-11 w-11 items-center justify-center
                            rounded-lg bg-blue-50 text-blue-700">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8">
                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M16 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2m16 0v-2a4 4 0 00-3-3.87M14 3.13a4 4 0 010 7.75M14 7a4 4 0 01-8 0 4 4 0 018 0z" />
                    </svg>
                </div>

                <h2 class="mt-4 font-semibold text-gray-900
                           group-hover:text-blue-700">
                    Users & Access
                </h2>

                <p class="mt-2 text-sm leading-6 text-gray-500">
                    Create accounts, manage roles, and control account access.
                </p>

                <div class="mt-5 flex items-center text-sm font-medium text-blue-700">
                    Manage users
                    <span class="ml-2 transition group-hover:translate-x-1">
                        &rarr;
                    </span>
                </div>
            </a>

            {{-- Maintenance Configuration --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex h-11 w-11 items-center justify-center
                            rounded-lg bg-amber-50 text-amber-700">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8">
                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M11.42 15.17l-5.66 5.66a2 2 0 01-2.83-2.83l5.66-5.66m2.83 2.83l3.54-3.54a4 4 0 005.66-5.66l-3.54 3.54-2.83-2.83 3.54-3.54a4 4 0 00-5.66 5.66l-3.54 3.54" />
                    </svg>
                </div>

                <h2 class="mt-4 font-semibold text-gray-900">
                    Maintenance Configuration
                </h2>

                <p class="mt-2 text-sm leading-6 text-gray-500">
                    Configure maintenance rules only where administrative
                    flexibility is genuinely needed.
                </p>

                <span class="mt-5 inline-block text-sm text-gray-400">
                    Planned
                </span>
            </div>

            {{-- System Preferences & History --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex h-11 w-11 items-center justify-center
                            rounded-lg bg-purple-50 text-purple-700">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8">
                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>

                <h2 class="mt-4 font-semibold text-gray-900">
                    System Preferences & History
                </h2>

                <p class="mt-2 text-sm leading-6 text-gray-500">
                    Manage defined system preferences and review recorded
                    security or administrative activity.
                </p>

                <span class="mt-5 inline-block text-sm text-gray-400">
                    Planned
                </span>
            </div>

        </div>
    </div>
@endsection
