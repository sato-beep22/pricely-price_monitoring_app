@extends('layouts.app')

@section('content')
    <style>
        body, html {
            background-color: #0b1120 !important;
            color: white !important;
        }
        .text-slate-900 {
            color: white !important;
        }
        .bg-white {
            background-color: #1e293b !important;
            border-color: #334155 !important;
        }
        .text-slate-500, .text-slate-600 {
            color: #94a3b8 !important;
        }
    </style>
    <!-- Animated Dots Background -->
    <canvas id="dotsCanvas" class="fixed top-0 left-0 w-full h-full -z-10 pointer-events-none"></canvas>

    <!-- Hero Section -->
    <section class="max-w-5xl mx-auto px-4 sm:px-6 md:px-8 pt-28 sm:pt-24 md:pt-28 pb-12 sm:pb-16 md:pb-20 text-center flex flex-col items-center">
        
        <!-- Dynamic Badge -->
        <div class="gsap-hero-badge reveal-stagger-item inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-700 px-3 sm:px-4 py-1.5 rounded-full text-[10px] sm:text-xs font-bold uppercase tracking-widest mb-4 sm:mb-6 border border-emerald-100 shadow-xs">
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-500 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-700"></span>
            </span>
            <span>Connecting Farmers & Buyers</span>
        </div>

        <!-- Heading Statement -->
        <h1 class="gsap-hero-title reveal-stagger-item text-4xl sm:text-5xl md:text-6xl lg:text-7xl leading-[1.05] sm:leading-[0.95] max-w-4xl z-10 relative text-slate-900 dark:text-white font-extrabold">
            Patas na Presyo,<br />
            <span class="text-gradient">
                Sapat na Kita.
            </span>
        </h1>

        <!-- Subhead Subtitle -->
        <p class="gsap-hero-subtitle reveal-stagger-item text-sm sm:text-base md:text-lg mt-5 sm:mt-8 max-w-2xl leading-relaxed z-10 relative px-2 sm:px-0 text-slate-600 dark:text-slate-300">
            I monitor ang pagtaas at pagbaba ng presyo ng ating mga ani mula sa mga kalapit na merkado sa pamamagitan ng app na ito.
        </p>

        <!-- Action Buttons row -->
        <div class="gsap-hero-cta reveal-stagger-item flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center justify-center gap-3 sm:gap-4 mt-8 sm:mt-10 z-10 relative w-full px-4 sm:px-0">
            <a href="{{ route('map.index') }}" class="btn-pricely-primary flex items-center justify-center gap-2 w-full sm:w-auto">
                Tignan ang Map
                <i data-lucide="arrow-right" class="w-5 h-5"></i>
            </a>

            @guest
            <a href="{{ route('register') }}" class="bg-white hover:bg-slate-50 dark:bg-slate-800 dark:hover:bg-slate-700 active:scale-[0.98] text-slate-900 dark:text-white px-6 py-3 rounded-2xl font-bold border border-slate-200 dark:border-slate-700 shadow-sm transition-all cursor-pointer flex items-center justify-center w-full sm:w-auto">
                Mag Register
            </a>
            @endguest

            <!-- Install PWA Button (Hidden by default until PWA is ready) -->
            <button id="install-pwa-btn" style="display: none;" class="bg-slate-800 hover:bg-slate-900 active:scale-[0.98] text-white px-6 py-3 rounded-2xl font-bold shadow-md transition-all cursor-pointer flex items-center justify-center gap-2 w-full sm:w-auto">
                <i data-lucide="download" class="w-5 h-5"></i>
                Install App
            </button>
        </div>

    </section>

    {{-- ===== PHONE MOCKUP SHOWCASE SECTION ===== --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8 pb-20 sm:pb-28 z-10 relative" x-data="{ modal: null }">

        {{-- Section Header --}}
        <div class="text-center mb-12 sm:mb-16">
            <p class="text-xs font-bold uppercase tracking-widest text-emerald-600 dark:text-emerald-400 mb-2">Mga Feature</p>
            <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-slate-900 dark:text-white leading-tight">
                Lahat ng kailangan mo,<br>
                <span class="text-gradient">nasa isang app na.</span>
            </h2>
        </div>

        {{-- ===== MOBILE: 3 vertical feature cards (phone left + info right) ===== --}}
        <div class="flex flex-col gap-5 md:hidden">

            {{-- Mobile Card 1: Shop Map --}}
            <button @click="modal = 'map'" id="feat-map-mobile"
                class="group focus:outline-none w-full bg-white/70 dark:bg-slate-800/70 backdrop-blur-sm border border-emerald-100 dark:border-emerald-900/50 rounded-3xl p-4 flex items-center gap-4 shadow-md hover:shadow-xl hover:border-emerald-300 dark:hover:border-emerald-500/50 transition-all duration-300 text-left active:scale-[0.98]">
                {{-- Phone --}}
                <div class="shrink-0 transition-transform duration-300 group-hover:-translate-y-1"
                     style="filter: drop-shadow(0 12px 28px rgba(16,185,129,0.30)) drop-shadow(0 4px 10px rgba(0,0,0,0.20));">
                    @include('partials.phone-mockup', [
                        'image'  => asset('images/price_map1.webp'),
                        'alt'    => 'Interactive Map',
                        'width'  => 88,
                        'height' => 180,
                        'color'  => '#0f172a',
                    ])
                </div>
                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <span class="inline-flex items-center gap-1.5 bg-emerald-100 dark:bg-emerald-900/50 text-emerald-800 dark:text-emerald-300 text-[9px] font-bold uppercase tracking-widest px-2.5 py-1 rounded-full mb-2 border border-transparent dark:border-emerald-800/50">
                        <i data-lucide="compass" class="w-2.5 h-2.5"></i> Shop Map
                    </span>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-white leading-tight mb-1">Interactive Price Map</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed line-clamp-3">Hanapin ang mga buyer malapit sa iyo at ikumpara ang kanilang presyo sa mapa.</p>
                    <span class="inline-flex items-center gap-1 mt-2 text-[10px] font-semibold text-emerald-600 dark:text-emerald-400">
                        Tignan <i data-lucide="arrow-right" class="w-3 h-3"></i>
                    </span>
                </div>
            </button>

            {{-- Mobile Card 2: SMS Alerts --}}
            <button @click="modal = 'sms'" id="feat-sms-mobile"
                class="group focus:outline-none w-full bg-white/70 dark:bg-slate-800/70 backdrop-blur-sm border border-indigo-100 dark:border-indigo-900/50 rounded-3xl p-4 flex items-center gap-4 shadow-md hover:shadow-xl hover:border-indigo-300 dark:hover:border-indigo-500/50 transition-all duration-300 text-left active:scale-[0.98]">
                {{-- Phone --}}
                <div class="shrink-0 transition-transform duration-300 group-hover:-translate-y-1"
                     style="filter: drop-shadow(0 12px 28px rgba(99,102,241,0.30)) drop-shadow(0 4px 10px rgba(0,0,0,0.20));">
                    @include('partials.phone-mockup', [
                        'image'  => asset('images/sms_feature.webp'),
                        'alt'    => 'SMS Alerts',
                        'width'  => 88,
                        'height' => 180,
                        'color'  => '#0f172a',
                    ])
                </div>
                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <span class="inline-flex items-center gap-1.5 bg-indigo-100 dark:bg-indigo-900/50 text-indigo-800 dark:text-indigo-300 text-[9px] font-bold uppercase tracking-widest px-2.5 py-1 rounded-full mb-2 border border-transparent dark:border-indigo-800/50">
                        <i data-lucide="smartphone" class="w-2.5 h-2.5"></i> SMS Alerts
                    </span>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-white leading-tight mb-1">Instant SMS Price Alerts</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed line-clamp-3">Makatanggap ng text kapag nagbago ang presyo ng shop na iyong pinili.</p>
                    <span class="inline-flex items-center gap-1 mt-2 text-[10px] font-semibold text-indigo-600 dark:text-indigo-400">
                        Tignan <i data-lucide="arrow-right" class="w-3 h-3"></i>
                    </span>
                </div>
            </button>

            {{-- Mobile Card 3: Price Forecast --}}
            <button @click="modal = 'forecast'" id="feat-forecast-mobile"
                class="group focus:outline-none w-full bg-white/70 dark:bg-slate-800/70 backdrop-blur-sm border border-amber-100 dark:border-amber-900/50 rounded-3xl p-4 flex items-center gap-4 shadow-md hover:shadow-xl hover:border-amber-300 dark:hover:border-amber-500/50 transition-all duration-300 text-left active:scale-[0.98]">
                {{-- Phone --}}
                <div class="shrink-0 transition-transform duration-300 group-hover:-translate-y-1"
                     style="filter: drop-shadow(0 12px 28px rgba(245,158,11,0.30)) drop-shadow(0 4px 10px rgba(0,0,0,0.20));">
                    @include('partials.phone-mockup', [
                        'image'  => asset('images/price_forecast1.webp'),
                        'alt'    => 'Market Trends',
                        'width'  => 88,
                        'height' => 180,
                        'color'  => '#0f172a',
                    ])
                </div>
                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <span class="inline-flex items-center gap-1.5 bg-amber-100 dark:bg-amber-900/50 text-amber-800 dark:text-amber-300 text-[9px] font-bold uppercase tracking-widest px-2.5 py-1 rounded-full mb-2 border border-transparent dark:border-amber-800/50">
                        <i data-lucide="trending-up" class="w-2.5 h-2.5"></i> Price Forecast
                    </span>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-white leading-tight mb-1">Market Trends & Projections</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed line-clamp-3">Alamin kung tataas o bababa ang presyo para makapag-desisyon ng maayos.</p>
                    <span class="inline-flex items-center gap-1 mt-2 text-[10px] font-semibold text-amber-600 dark:text-amber-400">
                        Tignan <i data-lucide="arrow-right" class="w-3 h-3"></i>
                    </span>
                </div>
            </button>

        </div>

        {{-- ===== DESKTOP: 2+1+2 dual-phone layout (larger + swappable) ===== --}}
        <div class="hidden md:flex items-end justify-center gap-6 lg:gap-12 xl:gap-20 pb-6">

            {{-- ── FEATURE 1: Shop Map (2 phones, click to swap) ── --}}
            <div x-data="{ swapped: false }" class="flex flex-col items-center gap-5" id="feat-map">

                {{-- Phone group — click anywhere to swap --}}
                <div class="relative cursor-pointer select-none"
                     style="width: 380px; height: 580px;"
                     @click="swapped = !swapped">

                    {{-- Phone A: Map View --}}
                    <div class="absolute left-0 top-0"
                         :style="`transition: all 0.55s cubic-bezier(0.34,1.56,0.64,1); ` + (swapped
                           ? 'z-index:1; transform: translateX(110px) translateY(45px) rotate(7deg) scale(0.86); filter: drop-shadow(0 16px 32px rgba(0,0,0,0.26));'
                           : 'z-index:2; transform: none; filter: drop-shadow(0 32px 64px rgba(16,185,129,0.28)) drop-shadow(0 12px 24px rgba(0,0,0,0.28));')">
                        @include('partials.phone-mockup', [
                            'image'  => asset('images/price_map1.webp'),
                            'alt'    => 'Interactive Map',
                            'width'  => 260,
                            'height' => 533,
                            'color'  => '#0f172a',
                        ])
                    </div>

                    {{-- Phone B: Shop Detail --}}
                    <div class="absolute left-0 top-0"
                         :style="`transition: all 0.55s cubic-bezier(0.34,1.56,0.64,1); ` + (swapped
                           ? 'z-index:2; transform: none; filter: drop-shadow(0 32px 64px rgba(16,185,129,0.28)) drop-shadow(0 12px 24px rgba(0,0,0,0.28));'
                           : 'z-index:1; transform: translateX(110px) translateY(45px) rotate(7deg) scale(0.86); filter: drop-shadow(0 16px 32px rgba(0,0,0,0.26));')">
                        @include('partials.phone-mockup', [
                            'image'  => asset('images/price_map2.webp'),
                            'alt'    => 'Shop Detail',
                            'width'  => 260,
                            'height' => 533,
                            'color'  => '#0f172a',
                        ])
                    </div>

                    {{-- Glow orb --}}
                    <div class="absolute left-1/2 -translate-x-1/2 w-[400px] h-32 rounded-full blur-[60px] opacity-40 pointer-events-none"
                         style="bottom: -100px; background: radial-gradient(ellipse, rgba(16,185,129,0.7) 0%, transparent 70%);"></div>
                </div>

                {{-- Label + modal CTA --}}
                <div class="flex flex-col items-center gap-2 mt-4">
                    <span class="inline-flex items-center gap-1.5 text-[10px] text-slate-600 dark:text-slate-400 font-semibold bg-slate-100/80 dark:bg-slate-800 px-3 py-1 rounded-full whitespace-nowrap mb-1">
                        <i data-lucide="refresh-cw" class="w-3 h-3"></i>
                        I-click ang phone para baguhin ang view
                    </span>
                    <span class="inline-flex items-center gap-1.5 bg-emerald-100 dark:bg-emerald-900/50 text-emerald-800 dark:text-emerald-300 border border-transparent dark:border-emerald-800/50 text-[11px] font-bold uppercase tracking-widest px-4 py-2 rounded-full shadow-sm">
                        <i data-lucide="compass" class="w-3.5 h-3.5"></i> Shop Map
                    </span>
                    <button @click.stop="modal = 'map'" class="text-xs text-slate-500 dark:text-slate-400 hover:text-emerald-700 dark:hover:text-emerald-400 font-semibold transition-colors flex items-center gap-1 mt-1">
                        I-tap para sa detalye <i data-lucide="arrow-right" class="w-3 h-3"></i>
                    </button>
                </div>
            </div>

            {{-- ── FEATURE 2: SMS Alerts (single center phone, larger) ── --}}
            <div class="flex flex-col items-center gap-5 z-10" id="feat-sms">

                <div class="relative" style="height: 600px;">
                    <div class="relative transition-transform duration-300 hover:-translate-y-4"
                         style="filter: drop-shadow(0 40px 80px rgba(99,102,241,0.35)) drop-shadow(0 14px 28px rgba(0,0,0,0.28));">
                        
                        {{-- Live badge --}}
                        <div class="absolute left-1/2 -translate-x-1/2 z-30 whitespace-nowrap" style="top: -20px;">
                            <span class="inline-flex items-center gap-1.5 bg-emerald-700 text-white text-[10px] font-bold uppercase tracking-widest px-4 py-1.5 rounded-full shadow-lg shadow-emerald-500/40">
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-white"></span>
                                </span>
                                Live Feature
                            </span>
                        </div>

                        @include('partials.phone-mockup', [
                            'image'  => asset('images/sms_feature.webp'),
                            'alt'    => 'SMS Alerts',
                            'width'  => 280,
                            'height' => 574,
                            'color'  => '#0f172a',
                        ])
                    </div>

                    <div class="absolute left-1/2 -translate-x-1/2 w-[450px] h-32 rounded-full blur-[60px] opacity-50 pointer-events-none"
                         style="bottom: -100px; background: radial-gradient(ellipse, rgba(99,102,241,0.7) 0%, transparent 70%);"></div>
                </div>

                <div class="flex flex-col items-center gap-2 mt-4">
                    <div class="h-[28px] mb-1"></div> {{-- Spacer to match the height of the swap hint on adjacent features --}}
                    <span class="inline-flex items-center gap-1.5 bg-indigo-100 dark:bg-indigo-900/50 text-indigo-800 dark:text-indigo-300 border border-transparent dark:border-indigo-800/50 text-[11px] font-bold uppercase tracking-widest px-4 py-2 rounded-full shadow-sm">
                        <i data-lucide="smartphone" class="w-3.5 h-3.5"></i> SMS Alerts
                    </span>
                    <button @click="modal = 'sms'" class="text-xs text-slate-500 dark:text-slate-400 hover:text-indigo-700 dark:hover:text-indigo-400 font-semibold transition-colors flex items-center gap-1 mt-1">
                        I-tap para sa detalye <i data-lucide="arrow-right" class="w-3 h-3"></i>
                    </button>
                </div>
            </div>

            {{-- ── FEATURE 3: Price Forecast (2 phones, mirrored, click to swap) ── --}}
            <div x-data="{ swapped: false }" class="flex flex-col items-center gap-5" id="feat-forecast">

                <div class="relative cursor-pointer select-none"
                     style="width: 380px; height: 580px;"
                     @click="swapped = !swapped">

                    {{-- Phone A: Market Trends (starts front-right) --}}
                    <div class="absolute left-0 top-0"
                         :style="`transition: all 0.55s cubic-bezier(0.34,1.56,0.64,1); ` + (swapped
                           ? 'z-index:1; transform: translateX(0px) translateY(45px) rotate(-7deg) scale(0.86); filter: drop-shadow(0 16px 32px rgba(0,0,0,0.26));'
                           : 'z-index:2; transform: translateX(110px); filter: drop-shadow(0 32px 64px rgba(245,158,11,0.28)) drop-shadow(0 12px 24px rgba(0,0,0,0.28));')">
                        @include('partials.phone-mockup', [
                            'image'  => asset('images/price_forecast1.webp'),
                            'alt'    => 'Market Trends',
                            'width'  => 260,
                            'height' => 533,
                            'color'  => '#0f172a',
                        ])
                    </div>

                    {{-- Phone B: Forecast Chart (starts back-left) --}}
                    <div class="absolute left-0 top-0"
                         :style="`transition: all 0.55s cubic-bezier(0.34,1.56,0.64,1); ` + (swapped
                           ? 'z-index:2; transform: translateX(110px); filter: drop-shadow(0 32px 64px rgba(245,158,11,0.28)) drop-shadow(0 12px 24px rgba(0,0,0,0.28));'
                           : 'z-index:1; transform: translateX(0px) translateY(45px) rotate(-7deg) scale(0.86); filter: drop-shadow(0 16px 32px rgba(0,0,0,0.26));')">
                        @include('partials.phone-mockup', [
                            'image'  => asset('images/price_forecast2.webp'),
                            'alt'    => 'Forecast Chart',
                            'width'  => 260,
                            'height' => 533,
                            'color'  => '#0f172a',
                        ])
                    </div>

                    {{-- Glow orb --}}
                    <div class="absolute left-1/2 -translate-x-1/2 w-[400px] h-32 rounded-full blur-[60px] opacity-40 pointer-events-none"
                         style="bottom: -100px; background: radial-gradient(ellipse, rgba(245,158,11,0.7) 0%, transparent 70%);"></div>
                </div>

                <div class="flex flex-col items-center gap-2 mt-4">
                    <span class="inline-flex items-center gap-1.5 text-[10px] text-slate-600 dark:text-slate-400 font-semibold bg-slate-100/80 dark:bg-slate-800 px-3 py-1 rounded-full whitespace-nowrap mb-1">
                        <i data-lucide="refresh-cw" class="w-3 h-3"></i>
                        I-click ang phone para baguhin ang view
                    </span>
                    <span class="inline-flex items-center gap-1.5 bg-amber-100 dark:bg-amber-900/50 text-amber-800 dark:text-amber-300 border border-transparent dark:border-amber-800/50 text-[11px] font-bold uppercase tracking-widest px-4 py-2 rounded-full shadow-sm">
                        <i data-lucide="trending-up" class="w-3.5 h-3.5"></i> Price Forecast
                    </span>
                    <button @click.stop="modal = 'forecast'" class="text-xs text-slate-500 dark:text-slate-400 hover:text-amber-700 dark:hover:text-amber-400 font-semibold transition-colors flex items-center gap-1 mt-1">
                        I-tap para sa detalye <i data-lucide="arrow-right" class="w-3 h-3"></i>
                    </button>
                </div>
            </div>

        </div>

        {{-- ===== FEATURE DEMO MODALS ===== --}}
        <template x-teleport="body">
            <div x-show="modal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 py-6" x-cloak>

                <!-- Backdrop -->
                <div
                    @click="modal = null"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="absolute inset-0 bg-black/50 backdrop-blur-sm"
                ></div>

                <!-- Modal: Interactive Map -->
                <div
                    x-show="modal === 'map'"
                    x-transition:enter="transition ease-out duration-250"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                    class="relative bg-white dark:bg-slate-800 rounded-3xl shadow-2xl w-full max-w-3xl overflow-hidden flex flex-col border border-slate-200 dark:border-slate-700" style="max-height:90vh;"
                    @click.stop
                >
                    <div class="bg-emerald-50 dark:bg-slate-900 border-b border-emerald-100 dark:border-slate-700 px-4 sm:px-6 py-4 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 bg-emerald-100 dark:bg-emerald-900/50 rounded-xl flex items-center justify-center shrink-0 border border-transparent dark:border-emerald-800/50">
                                <i data-lucide="compass" class="w-4 h-4 text-emerald-600 dark:text-emerald-400"></i>
                            </div>
                            <div>
                                <p class="text-base font-bold text-slate-800 dark:text-white">Shop Map</p>
                                <p class="text-xs text-emerald-600 dark:text-emerald-400 font-semibold">Paano ito Gumagana</p>
                            </div>
                        </div>
                        <button @click="modal = null" class="w-8 h-8 rounded-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 flex items-center justify-center text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors shrink-0">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>
                    <div class="overflow-y-auto flex-1">
                        <div class="flex flex-col md:flex-row bg-slate-100 dark:bg-slate-950">
                            <img src="{{ asset('images/price_map1.webp') }}?v=3" alt="Map View" class="w-full md:w-1/2 h-80 md:h-[500px]" style="object-fit:contain; padding: 1rem;">
                            <div class="border-t md:border-t-0 md:border-l border-slate-200 dark:border-slate-800"></div>
                            <img src="{{ asset('images/price_map2.webp') }}?v=3" alt="Shop Detail" class="w-full md:w-1/2 h-80 md:h-[500px]" style="object-fit:contain; padding: 1rem;">
                        </div>
                        <div class="px-4 sm:px-6 py-4 space-y-2">
                            <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tutorial</p>
                            <div class="space-y-2">
                                <div class="flex items-start gap-3">
                                    <span class="flex-shrink-0 w-5 h-5 bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-400 border border-transparent dark:border-emerald-800/50 rounded-full text-[10px] font-bold flex items-center justify-center">1</span>
                                    <p class="text-sm text-slate-600 dark:text-slate-300">I-open ang mapa para makita ang mga registered buyer sa inyong lugar.</p>
                                </div>
                                <div class="flex items-start gap-3">
                                    <span class="flex-shrink-0 w-5 h-5 bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-400 border border-transparent dark:border-emerald-800/50 rounded-full text-[10px] font-bold flex items-center justify-center">2</span>
                                    <p class="text-sm text-slate-600 dark:text-slate-300">I-click ang marker ng mapa para makita ang presyo na ino-offer ng shop na napili.</p>
                                </div>
                                <div class="flex items-start gap-3">
                                    <span class="flex-shrink-0 w-5 h-5 bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-400 border border-transparent dark:border-emerald-800/50 rounded-full text-[10px] font-bold flex items-center justify-center">3</span>
                                    <p class="text-sm text-slate-600 dark:text-slate-300">I-kumpara ang presyo ng ani at piliin ang pinakamagandang pagbentahan.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 sm:px-6 pb-5">
                        <a href="{{ route('map.index') }}" class="btn-pricely-primary w-full flex items-center justify-center gap-2 text-sm">
                            Open Live Map <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                    </div>
                </div>

                <!-- Modal: SMS Alerts -->
                <div
                    x-show="modal === 'sms'"
                    x-transition:enter="transition ease-out duration-250"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                    class="relative bg-white dark:bg-slate-800 rounded-3xl shadow-2xl w-full max-w-md overflow-hidden flex flex-col border border-slate-200 dark:border-slate-700" style="max-height:90vh;"
                    @click.stop
                >
                    <div class="bg-blue-50 dark:bg-slate-900 border-b border-blue-100 dark:border-slate-700 px-4 sm:px-6 py-4 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 bg-blue-100 dark:bg-indigo-900/50 rounded-xl flex items-center justify-center shrink-0 border border-transparent dark:border-indigo-800/50">
                                <i data-lucide="smartphone" class="w-4 h-4 text-blue-600 dark:text-indigo-400"></i>
                            </div>
                            <div>
                                <p class="text-base font-bold text-slate-800 dark:text-white">Instant SMS Alerts</p>
                                <p class="text-xs text-blue-600 dark:text-indigo-400 font-semibold">Paano ito Gumagana</p>
                            </div>
                        </div>
                        <button @click="modal = null" class="w-8 h-8 rounded-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 flex items-center justify-center text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors shrink-0">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>
                    <div class="overflow-y-auto flex-1">
                        <div class="bg-slate-100 dark:bg-slate-950 flex justify-center">
                            <img src="{{ asset('images/sms_feature.webp') }}?v=3" alt="SMS Alerts Feature" class="w-full max-w-sm h-80 md:h-[500px]" style="object-fit:contain; padding: 1rem;">
                        </div>
                        <div class="px-4 sm:px-6 py-4">
                            <div class="space-y-2">
                                <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tutorial</p>
                                <div class="space-y-2">
                                    <div class="flex items-start gap-3">
                                        <span class="flex-shrink-0 w-5 h-5 bg-blue-100 dark:bg-indigo-900/50 text-blue-700 dark:text-indigo-400 border border-transparent dark:border-indigo-800/50 rounded-full text-[10px] font-bold flex items-center justify-center">1</span>
                                        <p class="text-sm text-slate-600 dark:text-slate-300">I-register ang iyong phone number sa profile settings.</p>
                                    </div>
                                    <div class="flex items-start gap-3">
                                        <span class="flex-shrink-0 w-5 h-5 bg-blue-100 dark:bg-indigo-900/50 text-blue-700 dark:text-indigo-400 border border-transparent dark:border-indigo-800/50 rounded-full text-[10px] font-bold flex items-center justify-center">2</span>
                                        <p class="text-sm text-slate-600 dark:text-slate-300">Makakatanggap ka ng SMS alert kapag may bagong presyo ang shop na iyong na-subscribe.</p>
                                    </div>
                                    <div class="flex items-start gap-3">
                                        <span class="flex-shrink-0 w-5 h-5 bg-blue-100 dark:bg-indigo-900/50 text-blue-700 dark:text-indigo-400 border border-transparent dark:border-indigo-800/50 rounded-full text-[10px] font-bold flex items-center justify-center">3</span>
                                        <p class="text-sm text-slate-600 dark:text-slate-300">Hindi kailangan ng internet — sapat na ang basic na signal para makatanggap ng text.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 sm:px-6 pb-5">
                        <a href="{{ route('register') }}" class="w-full flex items-center justify-center gap-2 text-sm bg-blue-600 hover:bg-blue-700 text-white font-bold px-5 py-3 rounded-2xl transition-all">
                            Sign Up to Enable Alerts <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                    </div>
                </div>

                <!-- Modal: Price Forecasting -->
                <div
                    x-show="modal === 'forecast'"
                    x-transition:enter="transition ease-out duration-250"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                    class="relative bg-white dark:bg-slate-800 rounded-3xl shadow-2xl w-full max-w-3xl overflow-hidden flex flex-col border border-slate-200 dark:border-slate-700" style="max-height:90vh;"
                    @click.stop
                >
                    <div class="bg-amber-50 dark:bg-slate-900 border-b border-amber-100 dark:border-slate-700 px-4 sm:px-6 py-4 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 bg-amber-100 dark:bg-amber-900/50 rounded-xl flex items-center justify-center shrink-0 border border-transparent dark:border-amber-800/50">
                                <i data-lucide="trending-up" class="w-4 h-4 text-amber-600 dark:text-amber-400"></i>
                            </div>
                            <div>
                                <p class="text-base font-bold text-slate-800 dark:text-white">Price Forecasting</p>
                                <p class="text-xs text-amber-600 dark:text-amber-400 font-semibold">Paano ito Gumagana</p>
                            </div>
                        </div>
                        <button @click="modal = null" class="w-8 h-8 rounded-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 flex items-center justify-center text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors shrink-0">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>
                    <div class="overflow-y-auto flex-1">
                        <div class="flex flex-col md:flex-row bg-slate-100 dark:bg-slate-950">
                            <img src="{{ asset('images/price_forecast1.webp') }}?v=3" alt="Market Trends" class="w-full md:w-1/2 h-80 md:h-[500px]" style="object-fit:contain; padding: 1rem;">
                            <div class="border-t md:border-t-0 md:border-l border-slate-200 dark:border-slate-800"></div>
                            <img src="{{ asset('images/price_forecast2.webp') }}?v=3" alt="Forecast Chart" class="w-full md:w-1/2 h-80 md:h-[500px]" style="object-fit:contain; padding: 1rem;">
                        </div>
                        <div class="px-4 sm:px-6 py-4 space-y-2">
                            <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tutorial</p>
                            <div class="space-y-2">
                                <div class="flex items-start gap-3">
                                    <span class="flex-shrink-0 w-5 h-5 bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-400 border border-transparent dark:border-amber-800/50 rounded-full text-[10px] font-bold flex items-center justify-center">1</span>
                                    <p class="text-sm text-slate-600 dark:text-slate-300">Kinokolekta ng system ang mga nakaraang presyo mula sa mga registered buyers.</p>
                                </div>
                                <div class="flex items-start gap-3">
                                    <span class="flex-shrink-0 w-5 h-5 bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-400 border border-transparent dark:border-amber-800/50 rounded-full text-[10px] font-bold flex items-center justify-center">2</span>
                                    <p class="text-sm text-slate-600 dark:text-slate-300">Ipinapakita ng trend line kung saan maaaring pumunta ang presyo sa mga susunod na linggo.</p>
                                </div>
                                <div class="flex items-start gap-3">
                                    <span class="flex-shrink-0 w-5 h-5 bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-400 border border-transparent dark:border-amber-800/50 rounded-full text-[10px] font-bold flex items-center justify-center">3</span>
                                    <p class="text-sm text-slate-600 dark:text-slate-300">Gamitin ang impormasyong ito para piliin ang pinakamagandang oras ng pagbenta ng iyong ani.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 sm:px-6 pb-5">
                        <a href="{{ route('forecast.index') }}" class="w-full flex items-center justify-center gap-2 text-sm bg-amber-500 hover:bg-amber-600 text-white font-bold px-5 py-3 rounded-2xl transition-all">
                            View Forecast Charts <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                    </div>
                </div>

            </div>
        </template>

    </section>

    <script>
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const section = document.querySelector('[x-data]');
                if (section && section._x_dataStack) {
                    section._x_dataStack[0].modal = null;
                }
            }
        });

        // PWA Installation Logic
        let deferredPrompt;
        const installBtn = document.getElementById('install-pwa-btn');

        window.addEventListener('beforeinstallprompt', (e) => {
            // Prevent the mini-infobar from appearing on mobile
            e.preventDefault();
            // Stash the event so it can be triggered later.
            deferredPrompt = e;
            // Update UI notify the user they can install the PWA
            installBtn.style.display = 'flex';
        });

        installBtn.addEventListener('click', async () => {
            if (deferredPrompt !== null) {
                // Show the install prompt
                deferredPrompt.prompt();
                // Wait for the user to respond to the prompt
                const { outcome } = await deferredPrompt.userChoice;
                console.log(`User response to the install prompt: ${outcome}`);
                // We've used the prompt, and can't use it again, throw it away
                deferredPrompt = null;
                installBtn.style.display = 'none';
            }
        });

        window.addEventListener('appinstalled', () => {
            // Hide the app-provided install promotion
            installBtn.style.display = 'none';
            console.log('PWA was installed');
        });

        // Animated Dots Background
        document.addEventListener('DOMContentLoaded', () => {
            const canvas = document.getElementById('dotsCanvas');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');

            const DOT_COLOR_LIGHT = 'rgba(16, 185, 129, 0.6)'; // Emerald (Higher opacity)
            const DOT_COLOR_DARK = 'rgba(56, 189, 248, 0.8)';  // Light blue (Higher opacity)
            const SPACING = 30; // Distance between dots
            const DOT_RADIUS = 1.5;

            function getDotColor() {
                // Since we forced dark mode in the style block, we'll just use the dark color
                return DOT_COLOR_DARK;
            }

            function resize() {
                width = canvas.width = window.innerWidth;
                height = canvas.height = window.innerHeight;
            }
            window.addEventListener('resize', resize);
            resize();

            let mouse = { x: -1000, y: -1000 };
            window.addEventListener('mousemove', (e) => {
                mouse.x = e.clientX;
                mouse.y = e.clientY;
            });
            window.addEventListener('mouseleave', () => {
                mouse.x = -1000;
                mouse.y = -1000;
            });

            function animate() {
                ctx.clearRect(0, 0, width, height);
                ctx.fillStyle = getDotColor();
                
                const time = Date.now() * 0.0015; // Time for the consistent wave
                const cols = Math.floor(width / SPACING) + 2;
                const rows = Math.floor(height / SPACING) + 2;
                
                const offsetX = (width - (cols - 1) * SPACING) / 2;
                const offsetY = (height - (rows - 1) * SPACING) / 2;

                for (let i = 0; i < cols; i++) {
                    for (let j = 0; j < rows; j++) {
                        let baseX = offsetX + i * SPACING;
                        let baseY = offsetY + j * SPACING;

                        // Consistent wavy movement
                        let waveX = Math.sin(time + (i * 0.3) + (j * 0.2)) * 3;
                        let waveY = Math.cos(time + (j * 0.3) + (i * 0.2)) * 3;

                        let currentX = baseX + waveX;
                        let currentY = baseY + waveY;

                        // Mouse hover displacement
                        let dx = mouse.x - currentX;
                        let dy = mouse.y - currentY;
                        let distance = Math.sqrt(dx * dx + dy * dy);
                        
                        let hoverDispX = 0;
                        let hoverDispY = 0;

                        let maxDist = 150;
                        if (distance < maxDist) {
                            let force = (maxDist - distance) / maxDist;
                            hoverDispX = -dx * force * 0.2;
                            hoverDispY = -dy * force * 0.2;
                        }

                        ctx.beginPath();
                        ctx.arc(currentX + hoverDispX, currentY + hoverDispY, DOT_RADIUS, 0, Math.PI * 2);
                        ctx.fill();
                    }
                }

                requestAnimationFrame(animate);
            }
            animate();
        });
    </script>
@endsection
