<x-guest-layout>
    {{-- Header --}}
    <div class="mb-7">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900">
            Forgot your password?
        </h1>

        <p class="mt-3 text-sm leading-6 text-gray-500">
            No worries. Enter the email address associated with your account,
            and we'll send you a link to reset your password.
        </p>
    </div>

    {{-- Session Status --}}
    <x-auth-session-status class="mb-5" :status="session('status')" />

    {{-- Reset Form --}}
    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        {{-- Email Address --}}
        <div>
            <x-input-label for="email" :value="__('Email address')" class="mb-2 font-semibold text-gray-700" />

            <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.7">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 7l9 6 9-6M5 5h14a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2Z" />
                    </svg>
                </div>

                <x-text-input id="email"
                    class="block w-full rounded-lg border-gray-300 py-3 pl-10 pr-3 text-sm shadow-sm transition focus:border-blue-500 focus:ring-blue-500"
                    type="email" name="email" :value="old('email')" placeholder="you@example.com" required autofocus
                    autocomplete="username" />
            </div>

            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Submit --}}
        <div class="pt-1">
            <x-primary-button
                class="flex w-full justify-center gap-2 rounded-lg bg-slate-900 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                {{ __('Email Password Reset Link') }}

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
            Need access to your account?
            <a href="{{ route('login') }}"
                class="font-semibold text-blue-700 transition hover:text-blue-900 hover:underline">
                Back to login
            </a>
        </p>
    </div>
</x-guest-layout>