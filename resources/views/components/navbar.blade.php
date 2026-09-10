<header class="sticky top-0 z-30 border-b bg-white">

    <div class="flex h-16 items-center justify-between px-4 sm:px-6">

        {{-- Mobile menu button --}}
        <button type="button" @click="sidebarOpen = !sidebarOpen" class="rounded-lg p-2 hover:bg-gray-100 lg:hidden">
            ☰
        </button>

        {{-- Page title --}}
        <div class="hidden sm:block">
            <h1 class="text-lg font-semibold">
                Asset Maintenance System
            </h1>
        </div>

        {{-- User area --}}
        <div class="flex items-center gap-3">

            <button type="button" class="rounded-lg p-2 hover:bg-gray-100">
                🔔
            </button>

            @auth
                <div class="flex items-center gap-3">

                    <div class="hidden text-right sm:block">
                        <p class="text-sm font-medium text-gray-900">
                            {{ auth()->user()->name }}
                        </p>

                        <p class="text-xs text-gray-500">
                            {{ str(auth()->user()->role->value)->title() }}
                        </p>
                    </div>

                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-gray-200">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <button type="submit" class="rounded-lg px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50">
                            Logout
                        </button>
                    </form>

                </div>
            @endauth

        </div>

    </div>

</header>