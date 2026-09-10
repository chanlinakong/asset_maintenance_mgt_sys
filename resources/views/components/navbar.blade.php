<header class="sticky top-0 z-30 border-b bg-white">

    <div class="flex h-16 items-center justify-between px-4 sm:px-6">

        {{-- Mobile menu button --}}
        <button
            type="button"
            @click="sidebarOpen = !sidebarOpen"
            class="rounded-lg p-2 hover:bg-gray-100 lg:hidden"
        >
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

            <button
                type="button"
                class="rounded-lg p-2 hover:bg-gray-100"
            >
                🔔
            </button>

            <div class="flex items-center gap-2">

                <div
                    class="flex h-9 w-9 items-center
                           justify-center rounded-full
                           bg-gray-200"
                >
                    A
                </div>

                <span class="hidden text-sm font-medium sm:block">
                    Admin
                </span>

            </div>

        </div>

    </div>

</header>