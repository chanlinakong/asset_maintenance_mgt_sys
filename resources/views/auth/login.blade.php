<x-guest-layout>
    {{-- Login Header --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900">
            Welcome back
        </h1>

        <p class="mt-2 text-sm leading-6 text-gray-500">
            Sign in to manage your assets, maintenance schedules, and service records.
        </p>
    </div>

    {{-- Session Status --}}
    <x-auth-session-status
        class="mb-4"
        :status="session('status')"
    />

    {{-- Login Form --}}
    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        {{-- Email Address --}}
        <div>
            <x-input-label
                for="email"
                :value="__('Email address')"
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
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M3 7l9 6 9-6M5 5h14a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2Z" />
                    </svg>
                </div>

                <x-text-input
                    id="email"
                    class="block w-full rounded-lg border-gray-300 py-3 pl-10 pr-3 text-sm shadow-sm transition focus:border-blue-500 focus:ring-blue-500"
                    type="email"
                    name="email"
                    :value="old('email')"
                    placeholder="you@example.com"
                    required
                    autofocus
                    autocomplete="username"
                />
            </div>

            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Password --}}
        <div>
            <div class="mb-2 flex items-center justify-between">
                <x-input-label
                    for="password"
                    :value="__('Password')"
                    class="font-semibold text-gray-700"
                />

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                       class="text-xs font-medium text-blue-700 transition hover:text-blue-900 hover:underline">
                        {{ __('Forgot password?') }}
                    </a>
                @endif
            </div>

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
                    placeholder="Enter your password"
                    required
                    autocomplete="current-password"
                />
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Remember Me --}}
        <div class="flex items-center">
            <label for="remember_me" class="inline-flex cursor-pointer items-center gap-2">
                <input
                    id="remember_me"
                    type="checkbox"
                    name="remember"
                    class="h-4 w-4 rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                >

                <span class="text-sm text-gray-600">
                    {{ __('Remember me') }}
                </span>
            </label>
        </div>

        {{-- Submit --}}
        <button
            type="submit"
            class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-slate-900 px-5 py-3 text-sm font-semibold text-white shadow-sm transition duration-200 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
        >
            {{ __('Log in') }}

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
        </button>
    </form>

    {{-- Footer --}}
    <div class="mt-7 border-t border-gray-100 pt-5 text-center">
        <p class="text-xs leading-5 text-gray-500">
            Authorized personnel only.
            <br>
            Asset Maintenance Management System
        </p>
    </div>
</x-guest-layout>
