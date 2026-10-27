<x-guest-layout>
    {{-- Header --}}
    <div class="mb-7">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900">
            Reset your password
        </h1>

        <p class="mt-3 text-sm leading-6 text-gray-500">
            Create a new password to securely access your asset maintenance dashboard.
        </p>
    </div>

    {{-- Reset Password Form --}}
    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf

        {{-- Password Reset Token --}}
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        {{-- Email Address --}}
        <div>
            <x-input-label for="email" :value="__('Email address')" class="mb-2 font-semibold text-gray-700" />

            <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.7">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 7l9 6 9-6M5 5h14a2 2 0 0 1 2 2V17a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2Z" />
                    </svg>
                </div>

                <x-text-input id="email"
                    class="block w-full rounded-lg border-gray-300 py-3 pl-10 pr-3 text-sm shadow-sm transition focus:border-blue-500 focus:ring-blue-500"
                    type="email" name="email" :value="old('email', $request->email)" placeholder="you@example.com"
                    required autofocus autocomplete="username" />
            </div>

            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- New Password --}}
        <div>
            <x-input-label for="password" :value="__('New password')" class="mb-2 font-semibold text-gray-700" />

            <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.7">
                        <rect x="4" y="10" width="16" height="11" rx="2" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 10V7a4 4 0 1 1 8 0v3m-4 5v2" />
                    </svg>
                </div>

                <x-text-input id="password"
                    class="block w-full rounded-lg border-gray-300 py-3 pl-10 pr-3 text-sm shadow-sm transition focus:border-blue-500 focus:ring-blue-500"
                    type="password" name="password" placeholder="Enter your new password" required
                    autocomplete="new-password" />
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Confirm Password --}}
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm new password')"
                class="mb-2 font-semibold text-gray-700" />

            <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.7">
                        <rect x="4" y="10" width="16" height="11" rx="2" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 10V7a4 4 0 1 1 8 0v3m-4 5v2" />
                    </svg>
                </div>

                <x-text-input id="password_confirmation"
                    class="block w-full rounded-lg border-gray-300 py-3 pl-10 pr-3 text-sm shadow-sm transition focus:border-blue-500 focus:ring-blue-500"
                    type="password" name="password_confirmation" placeholder="Re-enter your new password" required
                    autocomplete="new-password" />
            </div>

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>


        {{-- Submit --}}
        <div class="pt-1">
            <x-primary-button
                class="flex w-full justify-center gap-2 rounded-lg bg-slate-900 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                {{ __('Reset Password') }}

                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6" />
                </svg>
            </x-primary-button>
        </div>
    </form>

    {{-- Footer --}}
    <div class="mt-7 border-t border-gray-100 pt-5 text-center">
        <p class="text-xs leading-5 text-gray-500">
            Remember your password?
            <a href="{{ route('login') }}"
                class="font-semibold text-blue-700 transition hover:text-blue-900 hover:underline">
                Back to login
            </a>
        </p>
    </div>
</x-guest-layout>