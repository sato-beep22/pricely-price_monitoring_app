@extends('layouts.app')

@section('content')
    <!-- Hero Section -->
    <section class="max-w-5xl mx-auto px-4 sm:px-6 md:px-8 pt-28 sm:pt-24 md:pt-28 pb-12 sm:pb-16 md:pb-20 text-center flex flex-col items-center">
        
        <!-- Dynamic Badge -->
        <div class="gsap-hero-badge reveal-stagger-item inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-700 px-3 sm:px-4 py-1.5 rounded-full text-[10px] sm:text-xs font-bold uppercase tracking-widest mb-4 sm:mb-6 border border-emerald-100 shadow-xs">
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-600"></span>
            </span>
            <span>Connecting Farmers & Buyers</span>
        </div>

        <!-- Heading Statement -->
        <h1 class="gsap-hero-title reveal-stagger-item text-4xl sm:text-5xl md:text-6xl lg:text-7xl leading-[1.05] sm:leading-[0.95] max-w-4xl z-10 relative">
            Patas na Presyo,<br />
            <span class="text-gradient">
                Sapat na Kita.
            </span>
        </h1>

        <!-- Subhead Subtitle -->
        <p class="gsap-hero-subtitle reveal-stagger-item text-sm sm:text-base md:text-lg mt-5 sm:mt-8 max-w-2xl leading-relaxed z-10 relative px-2 sm:px-0">
            I monitor ang pagtaas at pagbaba ng presyo ng ating mga ani mula sa mga kalapit na merkado sa pamamagitan ng app na ito.
        </p>

        <!-- Action Buttons row -->
        <div class="gsap-hero-cta reveal-stagger-item flex flex-col sm:flex-row items-stretch sm:items-center justify-center gap-3 sm:gap-4 mt-8 sm:mt-10 z-10 relative w-full sm:w-auto px-4 sm:px-0">
            <a href="{{ route('map.index') }}" class="btn-pricely-primary flex items-center justify-center gap-2 w-full sm:w-auto">
                Tignan ang Map
                <i data-lucide="arrow-right" class="w-5 h-5"></i>
            </a>

            @guest
            <a href="{{ route('register') }}" class="bg-white hover:bg-slate-50 active:scale-[0.98] text-slate-900 px-6 py-3 rounded-2xl font-bold border border-slate-200 shadow-sm transition-all cursor-pointer flex items-center justify-center w-full sm:w-auto">
                Mag Register
            </a>
            @endguest
        </div>

    </section>

    {{-- ===== PHONE MOCKUP SHOWCASE SECTION ===== --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8 pb-20 sm:pb-28 z-10 relative overflow-hidden" x-data="{ modal: null }">

        {{-- Section Header --}}
        <div class="text-center mb-12 sm:mb-16">
            <p class="text-xs font-bold uppercase tracking-widest text-emerald-600 mb-2">Mga Feature</p>
            <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-slate-900 leading-tight">
                Lahat ng kailangan mo,<br>
                <span class="text-gradient">nasa isang app na.</span>
            </h2>
        </div>

        {{-- ===== 3-FEATURE PHONE ROW ===== --}}
        <div class="flex items-center justify-center gap-6 sm:gap-10 md:gap-14 lg:gap-20">

            {{-- ── FEATURE 1: Shop Map (2 stacked/offset phones) ── --}}
            <button @click="modal = 'map'" id="feat-map"
                class="group focus:outline-none flex flex-col items-center gap-4">
                <div class="relative" style="width:200px; height:320px;">

                    {{-- Back phone (price_map2 – shop detail) --}}
                    <div class="absolute" style="left:50px; top:30px; transform: rotate(6deg) scale(0.88); transform-origin: bottom center; z-index:1; filter: drop-shadow(0 20px 40px rgba(0,0,0,0.28));">
                        @include('partials.phone-mockup', [
                            'image'  => asset('images/price_map2.jpg'),
                            'alt'    => 'Shop Detail',
                            'width'  => 110,
                            'height' => 230,
                            'color'  => '#0f172a',
                        ])
                    </div>

                    {{-- Front phone (price_map1 – map view) --}}
                    <div class="absolute transition-transform duration-300 group-hover:-translate-y-3" style="left:0; top:0; z-index:2; filter: drop-shadow(0 28px 50px rgba(16,185,129,0.30)) drop-shadow(0 10px 20px rgba(0,0,0,0.30));">
                        @include('partials.phone-mockup', [
                            'image'  => asset('images/price_map1.jpg'),
                            'alt'    => 'Interactive Map',
                            'width'  => 120,
                            'height' => 248,
                            'color'  => '#0f172a',
                        ])
                    </div>

                    {{-- Glow orb --}}
                    <div class="absolute -bottom-6 left-1/2 -translate-x-1/2 w-40 h-10 rounded-full blur-2xl opacity-0 group-hover:opacity-70 transition-opacity duration-500"
                         style="background: radial-gradient(ellipse, rgba(16,185,129,0.7) 0%, transparent 70%);"></div>
                </div>
                {{-- Label --}}
                <div class="flex flex-col items-center gap-1">
                    <span class="inline-flex items-center gap-1.5 bg-emerald-100 text-emerald-800 text-[10px] font-bold uppercase tracking-widest px-3 py-1.5 rounded-full">
                        <i data-lucide="compass" class="w-3 h-3"></i> Shop Map
                    </span>
                    <span class="text-[10px] text-slate-400 font-medium group-hover:text-emerald-600 transition-colors">I-tap para sa detalye →</span>
                </div>
            </button>

            {{-- ── FEATURE 2: SMS Alerts (1 center prominent phone) ── --}}
            <button @click="modal = 'sms'" id="feat-sms"
                class="group focus:outline-none flex flex-col items-center gap-4 z-10">
                <div class="relative" style="width:155px; height:340px;">

                    {{-- Live badge --}}
                    <div class="absolute -top-4 left-1/2 -translate-x-1/2 z-30 whitespace-nowrap">
                        <span class="inline-flex items-center gap-1.5 bg-emerald-600 text-white text-[9px] font-bold uppercase tracking-widest px-3 py-1 rounded-full shadow-lg shadow-emerald-500/40">
                            <span class="relative flex h-1.5 w-1.5">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-white"></span>
                            </span>
                            Live Feature
                        </span>
                    </div>

                    <div class="absolute inset-0 transition-transform duration-300 group-hover:-translate-y-4" style="filter: drop-shadow(0 32px 60px rgba(99,102,241,0.35)) drop-shadow(0 12px 24px rgba(0,0,0,0.30));">
                        @include('partials.phone-mockup', [
                            'image'  => asset('images/sms_feature.jpg'),
                            'alt'    => 'SMS Alerts',
                            'width'  => 140,
                            'height' => 290,
                            'color'  => '#0f172a',
                        ])
                    </div>

                    {{-- Glow orb --}}
                    <div class="absolute -bottom-6 left-1/2 -translate-x-1/2 w-40 h-10 rounded-full blur-2xl opacity-40 group-hover:opacity-80 transition-opacity duration-500"
                         style="background: radial-gradient(ellipse, rgba(99,102,241,0.7) 0%, transparent 70%);"></div>
                </div>
                {{-- Label --}}
                <div class="flex flex-col items-center gap-1">
                    <span class="inline-flex items-center gap-1.5 bg-indigo-100 text-indigo-800 text-[10px] font-bold uppercase tracking-widest px-3 py-1.5 rounded-full">
                        <i data-lucide="smartphone" class="w-3 h-3"></i> SMS Alerts
                    </span>
                    <span class="text-[10px] text-slate-400 font-medium group-hover:text-indigo-600 transition-colors">I-tap para sa detalye →</span>
                </div>
            </button>

            {{-- ── FEATURE 3: Price Forecasting (2 stacked/offset phones) ── --}}
            <button @click="modal = 'forecast'" id="feat-forecast"
                class="group focus:outline-none flex flex-col items-center gap-4">
                <div class="relative" style="width:200px; height:320px;">

                    {{-- Back phone (price_forecast2 – chart) --}}
                    <div class="absolute" style="right:50px; top:30px; transform: rotate(-6deg) scale(0.88); transform-origin: bottom center; z-index:1; filter: drop-shadow(0 20px 40px rgba(0,0,0,0.28));">
                        @include('partials.phone-mockup', [
                            'image'  => asset('images/price_forecast2.jpg'),
                            'alt'    => 'Forecast Chart',
                            'width'  => 110,
                            'height' => 230,
                            'color'  => '#0f172a',
                        ])
                    </div>

                    {{-- Front phone (price_forecast1 – market summary) --}}
                    <div class="absolute transition-transform duration-300 group-hover:-translate-y-3" style="right:0; top:0; z-index:2; filter: drop-shadow(0 28px 50px rgba(245,158,11,0.30)) drop-shadow(0 10px 20px rgba(0,0,0,0.30));">
                        @include('partials.phone-mockup', [
                            'image'  => asset('images/price_forecast1.jpg'),
                            'alt'    => 'Market Trends',
                            'width'  => 120,
                            'height' => 248,
                            'color'  => '#0f172a',
                        ])
                    </div>

                    {{-- Glow orb --}}
                    <div class="absolute -bottom-6 left-1/2 -translate-x-1/2 w-40 h-10 rounded-full blur-2xl opacity-0 group-hover:opacity-70 transition-opacity duration-500"
                         style="background: radial-gradient(ellipse, rgba(245,158,11,0.7) 0%, transparent 70%);"></div>
                </div>
                {{-- Label --}}
                <div class="flex flex-col items-center gap-1">
                    <span class="inline-flex items-center gap-1.5 bg-amber-100 text-amber-800 text-[10px] font-bold uppercase tracking-widest px-3 py-1.5 rounded-full">
                        <i data-lucide="trending-up" class="w-3 h-3"></i> Price Forecast
                    </span>
                    <span class="text-[10px] text-slate-400 font-medium group-hover:text-amber-600 transition-colors">I-tap para sa detalye →</span>
                </div>
            </button>

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
                    class="relative bg-white rounded-3xl shadow-2xl w-full max-w-5xl overflow-hidden flex flex-col" style="max-height:90vh;"
                    @click.stop
                >
                    <div class="bg-emerald-50 border-b border-emerald-100 px-4 sm:px-6 py-4 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 bg-emerald-100 rounded-xl flex items-center justify-center shrink-0">
                                <i data-lucide="compass" class="w-4 h-4 text-emerald-600"></i>
                            </div>
                            <div>
                                <p class="text-base font-bold text-slate-800">Shop Map</p>
                                <p class="text-xs text-emerald-600 font-semibold">Paano ito Gumagana</p>
                            </div>
                        </div>
                        <button @click="modal = null" class="w-8 h-8 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:text-slate-700 transition-colors shrink-0">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>
                    <div class="overflow-y-auto flex-1">
                        <div class="flex flex-col md:flex-row">
                            <img src="{{ asset('images/price_map1.jpg') }}?v=3" alt="Map View" class="w-full md:w-1/2 h-64 sm:h-80 md:h-96" style="object-fit:cover; object-position:top;">
                            <div class="border-t md:border-t-0 md:border-l border-slate-100"></div>
                            <img src="{{ asset('images/price_map2.jpg') }}?v=3" alt="Shop Detail" class="w-full md:w-1/2 h-64 sm:h-80 md:h-96" style="object-fit:cover; object-position:top;">
                        </div>
                        <div class="px-4 sm:px-6 py-4 space-y-2">
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Tutorial</p>
                            <div class="space-y-2">
                                <div class="flex items-start gap-3">
                                    <span class="flex-shrink-0 w-5 h-5 bg-emerald-100 text-emerald-700 rounded-full text-[10px] font-bold flex items-center justify-center">1</span>
                                    <p class="text-sm text-slate-600">I-open ang mapa para makita ang mga registered buyer sa inyong lugar.</p>
                                </div>
                                <div class="flex items-start gap-3">
                                    <span class="flex-shrink-0 w-5 h-5 bg-emerald-100 text-emerald-700 rounded-full text-[10px] font-bold flex items-center justify-center">2</span>
                                    <p class="text-sm text-slate-600">I-click ang marker ng mapa para makita ang presyo na ino-offer ng shop na napili.</p>
                                </div>
                                <div class="flex items-start gap-3">
                                    <span class="flex-shrink-0 w-5 h-5 bg-emerald-100 text-emerald-700 rounded-full text-[10px] font-bold flex items-center justify-center">3</span>
                                    <p class="text-sm text-slate-600">I-kumpara ang presyo ng ani at piliin ang pinakamagandang pagbentahan.</p>
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
                    class="relative bg-white rounded-3xl shadow-2xl w-full max-w-5xl overflow-hidden flex flex-col" style="max-height:90vh;"
                    @click.stop
                >
                    <div class="bg-blue-50 border-b border-blue-100 px-4 sm:px-6 py-4 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 bg-blue-100 rounded-xl flex items-center justify-center shrink-0">
                                <i data-lucide="smartphone" class="w-4 h-4 text-blue-600"></i>
                            </div>
                            <div>
                                <p class="text-base font-bold text-slate-800">Instant SMS Alerts</p>
                                <p class="text-xs text-blue-600 font-semibold">Paano ito Gumagana</p>
                            </div>
                        </div>
                        <button @click="modal = null" class="w-8 h-8 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:text-slate-700 transition-colors shrink-0">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>
                    <div class="overflow-y-auto flex-1">
                        <img src="{{ asset('images/sms_feature.jpg') }}?v=3" alt="SMS Alerts Feature" class="w-full h-64 sm:h-80 md:h-[420px]" style="object-fit:cover; object-position:top;">
                        <div class="px-4 sm:px-6 py-4">
                            <div class="space-y-2">
                                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Tutorial</p>
                                <div class="space-y-2">
                                    <div class="flex items-start gap-3">
                                        <span class="flex-shrink-0 w-5 h-5 bg-blue-100 text-blue-700 rounded-full text-[10px] font-bold flex items-center justify-center">1</span>
                                        <p class="text-sm text-slate-600">I-register ang iyong phone number sa profile settings.</p>
                                    </div>
                                    <div class="flex items-start gap-3">
                                        <span class="flex-shrink-0 w-5 h-5 bg-blue-100 text-blue-700 rounded-full text-[10px] font-bold flex items-center justify-center">2</span>
                                        <p class="text-sm text-slate-600">Makakatanggap ka ng SMS alert kapag may bagong presyo ang shop na iyong na-subscribe.</p>
                                    </div>
                                    <div class="flex items-start gap-3">
                                        <span class="flex-shrink-0 w-5 h-5 bg-blue-100 text-blue-700 rounded-full text-[10px] font-bold flex items-center justify-center">3</span>
                                        <p class="text-sm text-slate-600">Hindi kailangan ng internet — sapat na ang basic na signal para makatanggap ng text.</p>
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
                    class="relative bg-white rounded-3xl shadow-2xl w-full max-w-5xl overflow-hidden flex flex-col" style="max-height:90vh;"
                    @click.stop
                >
                    <div class="bg-amber-50 border-b border-amber-100 px-4 sm:px-6 py-4 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 bg-amber-100 rounded-xl flex items-center justify-center shrink-0">
                                <i data-lucide="trending-up" class="w-4 h-4 text-amber-600"></i>
                            </div>
                            <div>
                                <p class="text-base font-bold text-slate-800">Price Forecasting</p>
                                <p class="text-xs text-amber-600 font-semibold">Paano ito Gumagana</p>
                            </div>
                        </div>
                        <button @click="modal = null" class="w-8 h-8 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:text-slate-700 transition-colors shrink-0">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>
                    <div class="overflow-y-auto flex-1">
                        <div class="flex flex-col md:flex-row">
                            <img src="{{ asset('images/price_forecast1.jpg') }}?v=3" alt="Market Trends" class="w-full md:w-1/2 h-64 sm:h-80 md:h-96" style="object-fit:cover; object-position:top;">
                            <div class="border-t md:border-t-0 md:border-l border-slate-100"></div>
                            <img src="{{ asset('images/price_forecast2.jpg') }}?v=3" alt="Forecast Chart" class="w-full md:w-1/2 h-64 sm:h-80 md:h-96" style="object-fit:cover; object-position:top;">
                        </div>
                        <div class="px-4 sm:px-6 py-4 space-y-2">
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Tutorial</p>
                            <div class="space-y-2">
                                <div class="flex items-start gap-3">
                                    <span class="flex-shrink-0 w-5 h-5 bg-amber-100 text-amber-700 rounded-full text-[10px] font-bold flex items-center justify-center">1</span>
                                    <p class="text-sm text-slate-600">Kinokolekta ng system ang mga nakaraang presyo mula sa mga registered buyers.</p>
                                </div>
                                <div class="flex items-start gap-3">
                                    <span class="flex-shrink-0 w-5 h-5 bg-amber-100 text-amber-700 rounded-full text-[10px] font-bold flex items-center justify-center">2</span>
                                    <p class="text-sm text-slate-600">Ipinapakita ng trend line kung saan maaaring pumunta ang presyo sa mga susunod na linggo.</p>
                                </div>
                                <div class="flex items-start gap-3">
                                    <span class="flex-shrink-0 w-5 h-5 bg-amber-100 text-amber-700 rounded-full text-[10px] font-bold flex items-center justify-center">3</span>
                                    <p class="text-sm text-slate-600">Gamitin ang impormasyong ito para piliin ang pinakamagandang oras ng pagbenta ng iyong ani.</p>
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
    </script>
@endsection
