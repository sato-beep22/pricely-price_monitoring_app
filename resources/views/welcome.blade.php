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

    <!-- Bottom feature cards layout -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8 pb-12 sm:pb-16 md:pb-20 z-10 relative" x-data="{ modal: null }">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <!-- Feature 1 Card: Interactive Map -->
            <button @click="modal = 'map'" class="pricely-card reveal-card text-left p-4 sm:p-6 flex flex-col gap-4 sm:gap-6 group cursor-pointer focus:outline-none focus:ring-0 min-h-[180px] sm:min-h-[220px] w-full">
                <div class="icon-bg w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center mb-1">
                    <i data-lucide="compass" class="w-5 h-5 text-emerald-600"></i>
                </div>
                <div class="space-y-2">
                    <h3 class="group-hover:text-emerald-600 transition-colors text-lg leading-snug">
                        Shop Map
                    </h3>
                    <p class="text-sm text-slate-500 leading-snug">
                        Tignan ang lokasyon ng mga mamimili malapit sa iyong lugar at tignan ang kanilang mga presyo
                    </p>
                </div>
                <span class="text-[10px] font-mono font-bold text-slate-400 group-hover:text-emerald-600 group-hover:translate-x-1.5 transition-all flex items-center gap-1 mt-auto">
                    Paano ito gumagana? <i data-lucide="arrow-right" class="w-3 h-3"></i>
                </span>
            </button>

            <!-- Feature 2 Card: Instant SMS Alerts -->
            <button @click="modal = 'sms'" class="pricely-card reveal-card text-left p-4 sm:p-6 flex flex-col gap-4 sm:gap-6 group cursor-pointer focus:outline-none focus:ring-0 min-h-[180px] sm:min-h-[220px] w-full">
                <div class="icon-bg w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center mb-1">
                    <i data-lucide="smartphone" class="w-5 h-5 text-blue-600"></i>
                </div>
                <div class="space-y-2">
                    <h3 class="group-hover:text-indigo-600 transition-colors text-lg leading-snug">
                        Instant SMS Alerts
                    </h3>
                    <p class="text-sm text-slate-500 leading-snug">
                        I Monitor ang presyo ng napiling shop upang makapag text sila sayo
                    </p>
                </div>
                <span class="text-[10px] font-mono font-bold text-slate-400 group-hover:text-indigo-600 group-hover:translate-x-1.5 transition-all flex items-center gap-1 mt-auto">
                    Paano ito gumagana? <i data-lucide="arrow-right" class="w-3 h-3"></i>
                </span>
            </button>

            <!-- Feature 3 Card: Price Forecasting -->
            <button @click="modal = 'forecast'" class="pricely-card reveal-card text-left p-4 sm:p-6 flex flex-col gap-4 sm:gap-6 group cursor-pointer focus:outline-none focus:ring-0 min-h-[180px] sm:min-h-[220px] w-full">
                <div class="icon-bg w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center mb-1">
                    <i data-lucide="trending-up" class="w-5 h-5 text-amber-600"></i>
                </div>
                <div class="space-y-2">
                    <h3 class="group-hover:text-amber-600 transition-colors text-lg leading-snug">
                        Price Forecasting
                    </h3>
                    <p class="text-sm text-slate-500 leading-snug">
                        Subaybayan ang pagtaas at pagbaba ng presyo
                    </p>
                </div>
                <span class="text-[10px] font-mono font-bold text-slate-400 group-hover:text-amber-600 group-hover:translate-x-1.5 transition-all flex items-center gap-1 mt-auto">
                    Paano ito gumagana? <i data-lucide="arrow-right" class="w-3 h-3"></i>
                </span>
            </button>

        </div>

        <!-- ===== FEATURE DEMO MODALS ===== -->
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
                    <div class="bg-emerald-50 border-b border-emerald-100 px-6 py-4 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 bg-emerald-100 rounded-xl flex items-center justify-center">
                                <i data-lucide="compass" class="w-4 h-4 text-emerald-600"></i>
                            </div>
                            <div>
                                <p class="text-base font-bold text-slate-800">Shop Map</p>
                                <p class="text-xs text-emerald-600 font-semibold">Paano ito Gumagana</p>
                            </div>
                        </div>
                        <button @click="modal = null" class="w-8 h-8 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:text-slate-700 transition-colors">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>
                    <div class="overflow-y-auto flex-1">
                        <img src="{{ asset('images/feature_interactive_map.png') }}" alt="Interactive Map Demo" class="w-full" style="height:420px; object-fit:cover; object-position:top;">
                        <div class="px-6 py-4 space-y-2">
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Tutorial</p>
                            <div class="space-y-2">
                                <div class="flex items-start gap-3">
                                    <span class="flex-shrink-0 w-5 h-5 bg-emerald-100 text-emerald-700 rounded-full text-[10px] font-bold flex items-center justify-center">1</span>
                                    <p class="text-sm text-slate-600">I open ang mapa para makita ang mga registered buyer sa inyong lugar.</p>
                                </div>
                                <div class="flex items-start gap-3">
                                    <span class="flex-shrink-0 w-5 h-5 bg-emerald-100 text-emerald-700 rounded-full text-[10px] font-bold flex items-center justify-center">2</span>
                                    <p class="text-sm text-slate-600">I Click ang marker ng mapa para makita ang presyo na inooffer ng shop na napili.</p>
                                </div>
                                <div class="flex items-start gap-3">
                                    <span class="flex-shrink-0 w-5 h-5 bg-emerald-100 text-emerald-700 rounded-full text-[10px] font-bold flex items-center justify-center">3</span>
                                    <p class="text-sm text-slate-600">I kumpara ang presyo ng ani at piliin ang pinakamagandang pag bentahan.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="px-6 pb-5">
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
                    <div class="bg-blue-50 border-b border-blue-100 px-6 py-4 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 bg-blue-100 rounded-xl flex items-center justify-center">
                                <i data-lucide="smartphone" class="w-4 h-4 text-blue-600"></i>
                            </div>
                            <div>
                                <p class="text-base font-bold text-slate-800">Instant SMS Alerts</p>
                                <p class="text-xs text-blue-600 font-semibold">Paano ito gumagana?</p>
                            </div>
                        </div>
                        <button @click="modal = null" class="w-8 h-8 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:text-slate-700 transition-colors">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>
                    <div class="overflow-y-auto flex-1">
                        <!-- SMS screenshots stacked for full visibility -->
                        <div class="flex flex-col">
                            <img src="{{ asset('images/feature_sms_page.png') }}" alt="My Price Alerts Page" class="w-full" style="height:340px; object-fit:cover; object-position:top;">
                            <div class="border-t border-slate-100"></div>
                            <img src="{{ asset('images/feature_sms_modal.png') }}" alt="Subscribe Modal" class="w-full bg-gray-100" style="height:340px; object-fit:contain; object-position:center;">
                        </div>
                        <div class="px-6 py-4">
                            <div class="space-y-2">
                                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Tutorial</p>
                                <div class="space-y-2">
                                    <div class="flex items-start gap-3">
                                        <span class="flex-shrink-0 w-5 h-5 bg-blue-100 text-blue-700 rounded-full text-[10px] font-bold flex items-center justify-center">1</span>
                                        <p class="text-sm text-slate-600">I register ang iyong phone number sa iyong profile setings.</p>
                                    </div>
                                    <div class="flex items-start gap-3">
                                        <span class="flex-shrink-0 w-5 h-5 bg-blue-100 text-blue-700 rounded-full text-[10px] font-bold flex items-center justify-center">2</span>
                                        <p class="text-sm text-slate-600">When a subscribed buyer updates their price, you instantly receive an SMS alert.</p>
                                    </div>
                                    <div class="flex items-start gap-3">
                                        <span class="flex-shrink-0 w-5 h-5 bg-blue-100 text-blue-700 rounded-full text-[10px] font-bold flex items-center justify-center">3</span>
                                        <p class="text-sm text-slate-600">No internet required — a basic mobile signal is enough to receive your price alerts.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="px-6 pb-5">
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
                    class="relative bg-white rounded-3xl shadow-2xl w-full max-w-xl overflow-hidden"
                    @click.stop
                >
                    <div class="bg-amber-50 border-b border-amber-100 px-6 py-4 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 bg-amber-100 rounded-xl flex items-center justify-center">
                                <i data-lucide="trending-up" class="w-4 h-4 text-amber-600"></i>
                            </div>
                            <div>
                                <p class="text-base font-bold text-slate-800">Price Forecasting</p>
                                <p class="text-xs text-amber-600 font-semibold">How It Works</p>
                            </div>
                        </div>
                        <button @click="modal = null" class="w-8 h-8 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:text-slate-700 transition-colors">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>
                    <div class="p-6 space-y-4">
                        <img src="{{ asset('images/feature_price_forecasting.png') }}" alt="Price Forecasting Demo" class="w-full rounded-2xl border border-slate-100 shadow-sm">
                        <div class="space-y-2">
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">How it works</p>
                            <div class="space-y-2">
                                <div class="flex items-start gap-3">
                                    <span class="flex-shrink-0 w-5 h-5 bg-amber-100 text-amber-700 rounded-full text-[10px] font-bold flex items-center justify-center">1</span>
                                    <p class="text-sm text-slate-600">The system collects historical price data from all registered buyers over time.</p>
                                </div>
                                <div class="flex items-start gap-3">
                                    <span class="flex-shrink-0 w-5 h-5 bg-amber-100 text-amber-700 rounded-full text-[10px] font-bold flex items-center justify-center">2</span>
                                    <p class="text-sm text-slate-600">A predictive trend line shows where prices are likely heading over the next few weeks.</p>
                                </div>
                                <div class="flex items-start gap-3">
                                    <span class="flex-shrink-0 w-5 h-5 bg-amber-100 text-amber-700 rounded-full text-[10px] font-bold flex items-center justify-center">3</span>
                                    <p class="text-sm text-slate-600">Use the insights to pick the best time to sell your harvest for maximum profit.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="px-6 pb-5">
                        <a href="{{ route('forecast.index') }}" class="w-full flex items-center justify-center gap-2 text-sm bg-amber-500 hover:bg-amber-600 text-white font-bold px-5 py-3 rounded-2xl transition-all">
                            View Forecast Charts <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                    </div>
                </div>

            </div>
        </template>

    </section>

    <script>
        // Close modals on Escape key
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
