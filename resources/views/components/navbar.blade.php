<header class="sticky top-0 z-30 border-b bg-white">

    <div class="flex h-16 items-center justify-between px-4 sm:px-6">

        {{-- Mobile menu button --}}
        <button type="button" @click="sidebarOpen = !sidebarOpen" class="rounded-lg p-2 hover:bg-gray-100 lg:hidden">
            ☰
        </button>

        {{-- Page title --}}
        <div class="hidden sm:block">
            <h1 class="text-lg font-semibold">
                
            </h1>
        </div>

        {{-- User area --}}
        <div class="flex items-center gap-3">

            <button x-show="canInstall" x-cloak @click="installApp" type="button"
                class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-gray-600 transition hover:bg-gray-100 hover:text-gray-900">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="1.8">
                    <rect x="6" y="2.5" width="12" height="19" rx="2" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 18.5h4" />
                </svg>
                <span>Install</span>
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
                        class="relative rounded-lg p-2 text-gray-600 transition hover:bg-gray-100 hover:text-gray-900">

                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M18 8a6 6 0 00-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 21h4" />
                        </svg>

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
                @php
                    $user = auth()->user();

                    $initials = collect(explode(' ', trim($user->name)))
                        ->filter()
                        ->take(2)
                        ->map(fn($part) => mb_strtoupper(mb_substr($part, 0, 1)))
                        ->implode('');

                    $avatarUrl = $user->avatar_path
                        ? asset('storage/' . $user->avatar_path)
                        : null;
                @endphp

                <div x-data="{ open: false }" class="relative flex items-center gap-3">

                    {{-- User name and role --}}
                    <div class="hidden text-right sm:block">
                        <p class="text-sm font-medium text-gray-900">
                            {{ $user->name }}
                        </p>

                        <p class="text-xs text-gray-500">
                            {{ str($user->role->value)->replace('_', ' ')->title() }}
                        </p>
                    </div>

                    {{-- Avatar and dropdown trigger --}}
                    <button type="button" @click="open = !open" @keydown.escape.window="open = false"
                        :aria-expanded="open.toString()" aria-label="Open user menu" class="flex h-10 w-10 shrink-0 items-center justify-center
                           overflow-hidden rounded-full border border-gray-200
                           bg-gray-100 transition hover:ring-2 hover:ring-blue-200">

                        @if ($avatarUrl)
                            <img src="{{ $avatarUrl }}" alt="" class="h-full w-full object-cover">
                        @else
                            <span class="text-sm font-semibold text-gray-600">
                                {{ $initials ?: '?' }}
                            </span>
                        @endif
                    </button>

                    {{-- Dropdown menu --}}
                    <div x-cloak x-show="open" @click.outside="open = false" x-transition class="absolute right-0 top-full z-50 mt-3 w-60
                           overflow-hidden rounded-xl border border-gray-200
                           bg-white py-2 shadow-lg">

                        {{-- Account summary --}}
                        <div class="border-b border-gray-100 px-4 py-3">
                            <p class="truncate text-sm font-semibold text-gray-900">
                                {{ $user->name }}
                            </p>

                            <p class="truncate text-xs text-gray-500">
                                {{ $user->email }}
                            </p>
                        </div>

                        {{-- My Account --}}
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-3 text-sm
                               text-gray-700 hover:bg-gray-50">

                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <circle cx="12" cy="8" r="4" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 21v-2a7 7 0 0114 0v2" />
                            </svg>

                            <span>My Account</span>
                        </a>

                        {{-- Logout --}}
                        <div class="border-t border-gray-100 pt-2">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <button type="submit" class="flex w-full items-center gap-3 px-4 py-3
                                       text-left text-sm text-red-600
                                       hover:bg-red-50">

                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M18 15l3-3m0 0l-3-3m3 3H9" />
                                    </svg>

                                    <span>Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endauth


        </div>

    </div>

</header>