<aside class="fixed inset-y-0 left-0 z-50 w-64
           transform bg-white shadow-lg
           transition-transform duration-300
           lg:translate-x-0" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
    <div class="flex h-full flex-col">

        {{-- Logo --}}
        <div class="flex h-16 items-center border-b px-6">
            <span class="text-xl font-bold">
                🚛 Vehicle Maintenance
            </span>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 space-y-1 p-4">

            {{-- Dashboard --}}
            <a href="{{ route('dashboard') }}" class="block rounded-lg px-4 py-3 font-medium hover:bg-gray-100">
                📊 Dashboard
            </a>

            {{-- Admin + Staff --}}

            <a href="{{ route('vehicles.index') }}" class="block rounded-lg px-4 py-3 font-medium hover:bg-gray-100">
                🚛 Vehicles
            </a>

            {{-- Admin + Staff --}}
            <a href="{{ route('maintenance.index') }}" class="block rounded-lg px-4 py-3 font-medium hover:bg-gray-100">
                🔧 Maintenance
            </a>

            {{-- Admin + Staff --}}
            <a href="{{ route('maintenance-schedules.index') }}"
                class="block rounded-lg px-4 py-3 font-medium hover:bg-gray-100">
                📅 Maintenance Schedule
            </a>

            {{-- Reports --}}
            <a href="{{ route('reports.index') }}" class="block rounded-lg px-4 py-3 font-medium hover:bg-gray-100">
                📈 Reports
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