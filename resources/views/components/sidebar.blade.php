<aside class="fixed inset-y-0 left-0 z-50 w-64
           transform bg-white shadow-lg
           transition-transform duration-300
           lg:translate-x-0" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
    <div class="flex h-full flex-col">

        {{-- Logo --}}
        <div class="flex h-16 items-center gap-3 border-b px-6">
            <div
                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-600 text-white shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h11v11H3z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 11h4l3 4v3h-7z" />
                    <circle cx="7" cy="18" r="2" />
                    <circle cx="18" cy="18" r="2" />
                </svg>
            </div>

            <span class="text-base font-bold tracking-tight text-gray-900">
                Vehicle Maintenance
            </span>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 space-y-1 p-4">

            {{-- Dashboard --}}
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5
        text-sm font-medium transition
        {{ request()->routeIs('dashboard')
    ? 'bg-blue-50 text-blue-700'
    : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 14l4-4 4 4 6-7" />
                </svg>
                <span>Dashboard</span>
            </a>

            {{-- Admin + Staff --}}
            <a href="{{ route('vehicles.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5
        text-sm font-medium transition
        {{ request()->routeIs('vehicles.*')
    ? 'bg-blue-50 text-blue-700'
    : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13l2-6h14l2 6M5 13h14v6H5z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 19v2m10-2v2M3 15v3h2m16-3v3h-2" />
                    <circle cx="7.5" cy="16" r="1" />
                    <circle cx="16.5" cy="16" r="1" />
                </svg>
                <span>Vehicles</span>
            </a>

            {{-- Admin + Staff --}}
            <a href="{{ route('maintenance.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5
        text-sm font-medium transition
        {{ request()->routeIs('maintenance.*')
    ? 'bg-blue-50 text-blue-700'
    : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M14.7 6.3a5 5 0 00-6.4 6.4L3 18l3 3 5.3-5.3a5 5 0 006.4-6.4l-3 3-3-3 3-3z" />
                </svg>
                <span>Maintenance</span>
            </a>

            {{-- Admin + Staff --}}
            <a href="{{ route('maintenance-schedules.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5
        text-sm font-medium transition
        {{ request()->routeIs('maintenance-schedules.*')
    ? 'bg-blue-50 text-blue-700'
    : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="1.8">
                    <rect x="3" y="5" width="18" height="16" rx="2" />
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16 3v4M8 3v4M3 10h18M8 14h2m4 0h2m-8 3h2" />
                </svg>
                <span>Maintenance Schedule</span>
            </a>

            {{-- Reports --}}
            <a href="{{ route('reports.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5
        text-sm font-medium transition
        {{ request()->routeIs('reports.*')
    ? 'bg-blue-50 text-blue-700'
    : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 19V5m0 14h17M7 15l4-4 3 2 6-7" />
                </svg>
                <span>Reports</span>
            </a>

            {{-- Admin only --}}
            @if (auth()->user()->role->value === 'admin')
                    <a href="{{ route('settings.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5
                                                                                                               text-sm font-medium transition
                                                                                                               {{ request()->routeIs('settings.*')
                ? 'bg-blue-50 text-blue-700'
                : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">

                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M10.325 4.317a1.724 1.724 0 013.35 0 1.724 1.724 0 002.573 1.066 1.724 1.724 0 012.372 2.372 1.724 1.724 0 001.066 2.573 1.724 1.724 0 010 3.35 1.724 1.724 0 00-1.066 2.573 1.724 1.724 0 01-2.372 2.372 1.724 1.724 0 00-2.573 1.066 1.724 1.724 0 01-3.35 0 1.724 1.724 0 00-2.573-1.066 1.724 1.724 0 01-2.372-2.372 1.724 1.724 0 00-1.066-2.573 1.724 1.724 0 010-3.35A1.724 1.724 0 005.38 7.755a1.724 1.724 0 012.372-2.372 1.724 1.724 0 002.573-1.066z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>

                        <span>Settings</span>
                    </a>
            @endif

        </nav>

        {{-- Footer --}}
        <div class="border-t p-4">
            <div class="text-sm text-gray-500">
                Vehicle Maintenance System
            </div>
        </div>

    </div>
</aside>