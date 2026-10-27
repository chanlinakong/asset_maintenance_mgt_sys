<x-guest-layout>
    {{-- Header --}}
    <div class="mb-7">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900">
            Confirm your password
        </h1>

        <p class="mt-3 text-sm leading-6 text-gray-500">
            {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
        </p>
    </div>

    {{-- Session Status --}}
    <x-auth-session-status
        class="mb-4"
        :status="session('status')"
    />

    {{-- Confirm Password Form --}}
    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label
                for="password"
                :value="__('Password')"
                class="mb-2 font-semibold text-gray-700"
            />

            <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="h-5 w-5"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="1.7">
                        <rect x="4" y="10" width="16" height="11" rx="2" />
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M8 10V7a4 4 0 1 1 8 0v3m-4 5v2" />
                    </svg>
                </div>

                <x-text-input
                    id="password"
                    class="block w-full rounded-lg border-gray-300 py-3 pl-10 pr-3 text-sm shadow-sm transition focus:border-blue-500 focus:ring-blue-500"
                    type="password"
                    name="password"
                    placeholder="Enter your current password"
                    required
                    autofocus
                    autocomplete="current-password"
                />
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Submit --}}
        <div class="pt-1">
            <x-primary-button
                class="flex w-full justify-center gap-2 rounded-lg bg-slate-900 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
            >
                {{ __('Confirm') }}

                <svg xmlns="http://www.w3.org/2000/svg"
                     class="h-4 w-4"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor"
                     stroke-width="2">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M5 12h14m-6-6 6 6-6 6" />
                </svg>
            </x-primary-button>
        </div>
    </form>

    {{-- Footer --}}
    <div class="mt-7 border-t border-gray-100 pt-5 text-center">
        <p class="text-xs leading-5 text-gray-500">
            Your account security matters.
            <br>
            Asset Maintenance Management System
        </p>
    </div>
</x-guest-layout>
