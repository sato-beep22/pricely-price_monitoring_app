<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="cupcake">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Pricely') }}</title>
    
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}?v=3">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        window.AppUrl = "{{ rtrim(url('/'), '/') }}";
    </script>
</head>
<body class="bg-base-100 relative">
    <!-- Global Notification Modal -->
    <x-notification-modal />
    <!-- Decorative Gradient Orbs Container (Prevents horizontal scroll) -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="bg-ambient absolute inset-0 opacity-40"></div>
        <div class="absolute w-[500px] h-[500px] rounded-full -top-28 -left-28 opacity-60 animate-blob blur-3xl" style="background: radial-gradient(circle, rgba(16,185,129,0.35) 0%, rgba(13,148,136,0.15) 50%, transparent 70%);"></div>
        <div class="absolute w-80 h-80 rounded-full top-[15%] -right-[5%] opacity-40 animate-blob blur-2xl" style="background: radial-gradient(circle, rgba(56,189,248,0.25) 0%, rgba(99,102,241,0.10) 50%, transparent 70%); animation-delay: 2s;"></div>
        <div class="absolute w-72 h-72 rounded-full top-[50%] -left-16 opacity-35 animate-blob blur-2xl" style="background: radial-gradient(circle, rgba(52,211,153,0.30) 0%, rgba(16,185,129,0.10) 50%, transparent 70%); animation-delay: 5s;"></div>
        <div class="absolute w-[450px] h-[450px] rounded-full -bottom-24 -right-24 opacity-50 animate-blob blur-3xl" style="background: radial-gradient(circle, rgba(13,148,136,0.30) 0%, rgba(16,185,129,0.12) 50%, transparent 70%); animation-delay: 3s;"></div>
        <div class="absolute w-64 h-64 rounded-full bottom-[30%] left-[40%] opacity-25 animate-blob blur-2xl" style="background: radial-gradient(circle, rgba(251,191,36,0.20) 0%, rgba(245,158,11,0.08) 50%, transparent 70%); animation-delay: 7s;"></div>
        <div class="absolute w-56 h-56 rounded-full top-[35%] right-[30%] opacity-30 animate-blob blur-xl" style="background: radial-gradient(circle, rgba(99,102,241,0.18) 0%, rgba(139,92,246,0.06) 50%, transparent 70%); animation-delay: 10s;"></div>
    </div>

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
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('san-mateo-logo.png') }}" alt="San Mateo Logo" class="w-12 h-12 rounded-full border-2 border-slate-200 bg-white p-0.5 object-contain">
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
</body>
</html>
