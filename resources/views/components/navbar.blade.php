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

            <button x-show="canInstall" x-cloak @click="installApp" type="button"
                class="rounded-lg px-3 py-2 text-sm font-medium hover:bg-gray-100">
                📱 Install
            </button>

            @auth

                @php
                    $unreadNotifications =
                        auth()->user()
                            ->unreadNotifications()
                            ->latest()
                            ->take(5)
                            ->get();
                @endphp

                <div x-data="{ open: false }" class="relative">

                    <button @click="open = !open" type="button"
                        class="relative rounded-lg p-2 text-gray-600 hover:bg-gray-100">

                        🔔

                        @if($unreadNotifications->count())

                            <span
                                class="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-red-600 px-1 text-xs font-bold text-white">
                                {{ $unreadNotifications->count() }}
                            </span>

                        @endif

                    </button>


                    <div x-show="open" @click.outside="open = false" x-transition
                        class="absolute right-0 z-50 mt-2 w-80 rounded-xl bg-white shadow-lg ring-1 ring-black/5">

                        <div class="border-b border-gray-100 p-4">

                            <h3 class="font-semibold text-gray-900">
                                Notifications
                            </h3>

                        </div>


                        <div class="max-h-96 overflow-y-auto">

                            @forelse(
                                                    $unreadNotifications
                                                    as $notification
                                                )

                                                <a href="{{ route(
                                    'notifications.read',
                                    $notification->id
                                ) }}" class="block border-b border-gray-100 p-4 hover:bg-gray-50">

                                                    <p class="text-sm font-semibold text-gray-900">
                                                        {{ $notification->data['title'] }}
                                                    </p>

                                                    <p class="mt-1 text-xs text-gray-500">
                                                        {{ $notification->data['message'] }}
                                                    </p>

                                                </a>

                                                <div class="border-t border-gray-100 p-3 text-center">

                                                    <a href="{{ route('notifications.index') }}"
                                                        class="text-sm font-medium text-gray-700 hover:text-gray-900">
                                                        View all notifications
                                                    </a>

                                                </div>

                            @empty

                                <div class="p-6 text-center text-sm text-gray-500">
                                    No unread notifications.
                                </div>

                            @endforelse

                        </div>

                    </div>

                </div>

            @endauth

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