<aside class="fixed inset-y-0 left-0 z-50 w-64
           transform bg-white shadow-lg
           transition-transform duration-300
           lg:translate-x-0" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
    <div class="flex h-full flex-col">

        {{-- Logo --}}
        <div class="flex h-16 items-center border-b px-6">
            <span class="text-xl font-bold">
                🚛 Asset Maintenance
            </span>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 space-y-1 p-4">

            <a href="{{ route('dashboard') }}" class="block rounded-lg px-4 py-3 font-medium hover:bg-gray-100">
                📊 Dashboard
            </a>

            <a href="{{ route('vehicles.index') }}" class="block rounded-lg px-4 py-3 font-medium hover:bg-gray-100">
                🚛 Vehicles
            </a>

            <a href="{{ route('maintenance.index') }}" class="block rounded-lg px-4 py-3 font-medium hover:bg-gray-100">
                🔧 Maintenance
            </a>

            <a href="#" class="block rounded-lg px-4 py-3
                       font-medium hover:bg-gray-100">
                📈 Reports
            </a>

            <a href="#" class="block rounded-lg px-4 py-3
                       font-medium hover:bg-gray-100">
                ⚙️ Settings
            </a>

        </nav>

        {{-- Bottom --}}
        <div class="border-t p-4">
            <div class="text-sm text-gray-500">
                Asset Maintenance System
            </div>
        </div>

    </div>
</aside>