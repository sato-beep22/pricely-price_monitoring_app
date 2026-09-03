<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Pricely connects farmers and buyers with live price monitoring, interactive maps, and market forecasts in San Mateo, Isabela.">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Pricely') }}</title>
    
    <link rel="icon" type="image/webp" href="{{ asset('favicon.webp') }}?v=4">
    <!-- PWA Manifest & Theme Color -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#047857">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&family=Inter:wght@100..900&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&family=Inter:wght@100..900&family=JetBrains+Mono:wght@400;500&display=swap"></noscript>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts" defer></script>
    <script>
        window.AppUrl = "{{ rtrim(url('/'), '/') }}";
    </script>
</head>
<body class="bg-base-100 relative">
    <!-- Global Notification Modal -->
    <x-notification-modal />
    <!-- Main Application Wrapper -->

    @php
        $showSidebar = auth()->check() && !request()->is('/');
        $showTopNavbar = !$showSidebar;
    @endphp

    @if($showTopNavbar)
        <!-- Top Navbar -->
        <div class="w-full fixed top-0 z-50">
            @include('partials.navbar')
        </div>
    @endif

    <!-- Main Application Wrapper -->
    <div x-data="{ sidebarOpen: false }" class="min-h-screen bg-transparent font-sans antialiased selection:bg-success selection:text-success-content flex {{ $showSidebar ? 'flex-row' : 'flex-col' }} relative z-10">
        @if($showSidebar)
            <!-- Mobile Overlay -->
            <div x-show="sidebarOpen" x-transition.opacity style="display: none;" class="fixed inset-0 z-40 bg-slate-900/50 md:hidden" @click="sidebarOpen = false"></div>
            @include('partials.sidebar')
        @endif
        
        <div class="flex-1 flex flex-col min-h-screen min-w-0 overflow-x-hidden">
            
            @if($showSidebar)
                <!-- Mobile Header for Hamburger -->
                <div class="md:hidden flex items-center justify-between bg-white border-b border-slate-200 px-4 py-3 sticky top-0 z-30">
                    <div class="flex items-center gap-2">
                        <img src="{{ asset('san-mateo-logo.webp') }}" alt="San Mateo Logo" class="w-12 h-12 object-contain">
                        <div class="flex flex-col">
                            <span class="font-bold text-slate-800 text-lg tracking-tight leading-none">Pricely</span>
                            <span class="text-[10px] font-medium text-slate-500 leading-tight">San Mateo Isabela</span>
                        </div>
                    </div>
                    <button @click="sidebarOpen = true" class="p-2 text-slate-600 hover:bg-slate-100 rounded-lg">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>
                </div>
            @endif

            <main class="flex-grow flex flex-col">
                @hasSection('header')
                    <header class="w-full px-6 md:px-8 py-6 reveal-header bg-white/50 border-b border-slate-100">
                        @yield('header')
                    </header>
                @endif

                @if(isset($header))
                    <header class="w-full px-6 md:px-8 py-6 reveal-header bg-white/50 border-b border-slate-100">
                        {{ $header }}
                    </header>
                @endif
                
                <div class="flex-grow p-6 md:p-8">
                    @yield('content')
                    
                    @if(isset($slot))
                        {{ $slot }}
                    @endif
                </div>
            </main>
            
            @include('partials.footer')
        </div>
    </div>

    <script>
        // Initialize Lucide Icons globally
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
    </script>
    @stack('scripts')

    {{-- Pricely AI Chatbot --}}
    <x-chatbot />
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js').then(function(registration) {
                    console.log('ServiceWorker registration successful');
                }, function(err) {
                    console.log('ServiceWorker registration failed: ', err);
                });
            });
        }
    </script>
</body>
</html>
