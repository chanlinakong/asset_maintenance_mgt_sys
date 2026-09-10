<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="theme-color" content="#111827">

    <link rel="manifest" href="{{ asset('manifest.json') }}">

    <meta name="apple-mobile-web-app-capable" content="yes">

    <meta name="apple-mobile-web-app-status-bar-style" content="default">

    <meta name="apple-mobile-web-app-title" content="Vehicle Maintenance">

    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192.png') }}">

    <title>
        @yield('title', 'Asset Maintenance')
    </title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 text-gray-900">

    <div x-data="{
        sidebarOpen: false,
        canInstall: false,
        deferredPrompt: null,

        installApp() {
            if (!this.deferredPrompt) {
                return;
            }

            this.deferredPrompt.prompt();

            this.deferredPrompt.userChoice.then(() => {
                this.deferredPrompt = null;
                this.canInstall = false;
            });
        }
    }" x-init="
        window.addEventListener('beforeinstallprompt', (event) => {
            event.preventDefault();

            deferredPrompt = event;
            canInstall = true;
        });

        window.addEventListener('appinstalled', () => {
            canInstall = false;
            deferredPrompt = null;
        });
    " class="min-h-screen">

        {{-- Mobile overlay --}}
        <div x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false"
            class="fixed inset-0 z-40 bg-black/50 lg:hidden"></div>

        {{-- Sidebar --}}
        <x-sidebar />

        <div class="lg:pl-64">

            {{-- Navbar --}}
            <x-navbar />

            {{-- Main content --}}
            <main class="p-4 sm:p-6 lg:p-8">
                @yield('content')
            </main>

        </div>

    </div>

</body>

</html>