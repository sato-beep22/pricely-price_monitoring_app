@extends('layouts.app')

@section('content')
    <!-- Hero Section -->
    <section class="max-w-5xl mx-auto px-6 md:px-8 pt-16 md:pt-24 pb-20 text-center flex flex-col items-center">
        
        <!-- Dynamic Badge -->
        <div class="gsap-hero-badge reveal-stagger-item inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-700 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest mb-6 border border-emerald-100 shadow-xs">
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-600"></span>
            </span>
            <span>Connecting Farmers & Buyers</span>
        </div>

        <!-- Heading Statement -->
        <h1 class="gsap-hero-title reveal-stagger-item text-5xl sm:text-6xl md:text-7xl leading-[0.95] max-w-4xl z-10 relative">
            Fair Prices,<br />
            <span class="text-gradient">
                Transparent Markets.
            </span>
        </h1>

        <!-- Subhead Subtitle -->
        <p class="gsap-hero-subtitle reveal-stagger-item text-lg mt-8 max-w-2xl leading-relaxed z-10 relative">
            Empowering agricultural communities with real-time price monitoring, predictive insights, and direct market access for sustainable growth.
        </p>

        <!-- Action Buttons row -->
        <div class="gsap-hero-cta reveal-stagger-item flex flex-wrap items-center justify-center gap-4 mt-10 z-10 relative">
            <a href="{{ route('map.index') }}" class="btn-pricely-primary flex items-center gap-2">
                Launch Portal
                <i data-lucide="arrow-right" class="w-5 h-5"></i>
            </a>
            
            @guest
            <a href="{{ route('register') }}" class="bg-white hover:bg-slate-50 active:scale-[0.98] text-slate-900 px-6 py-3 rounded-2xl font-bold border border-slate-200 shadow-sm transition-all cursor-pointer flex items-center h-full">
                Join as Farmer
            </a>
            @endguest
        </div>

    </section>

    <!-- Bottom feature cards layout -->
    <section class="max-w-7xl mx-auto px-6 md:px-8 pb-20 z-10 relative">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <!-- Feature 1 Card: Interactive Map -->
            <a href="{{ route('map.index') }}" class="pricely-card reveal-card text-left p-6 flex flex-col gap-6 group cursor-pointer focus:outline-none focus:ring-0 min-h-[220px]">
                <div class="icon-bg w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center mb-1">
                    <i data-lucide="compass" class="w-5 h-5 text-emerald-600"></i>
                </div>
                <div class="space-y-2">
                    <h3 class="group-hover:text-emerald-600 transition-colors text-lg leading-snug">
                        Interactive Map
                    </h3>
                    <p class="text-sm text-slate-500 leading-snug">
                        Locate buyers near you and instantly compare their buying prices for rice, corn, and mung beans.
                    </p>
                </div>
                <span class="text-[10px] font-mono font-bold text-slate-400 group-hover:text-emerald-600 group-hover:translate-x-1.5 transition-all flex items-center gap-1 mt-auto">
                    Simulate price map <i data-lucide="arrow-right" class="w-3 h-3"></i>
                </span>
            </a>

            <!-- Feature 2 Card: Instant SMS Alerts -->
            <div class="pricely-card reveal-card text-left p-6 flex flex-col gap-6 group cursor-pointer focus:outline-none focus:ring-0 min-h-[220px]">
                <div class="icon-bg w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center mb-1">
                    <i data-lucide="smartphone" class="w-5 h-5 text-blue-600"></i>
                </div>
                <div class="space-y-2">
                    <h3 class="group-hover:text-indigo-600 transition-colors text-lg leading-snug">
                        Instant SMS Alerts
                    </h3>
                    <p class="text-sm text-slate-500 leading-snug">
                        Subscribe to your preferred buyers and receive automatic SMS notifications the moment they update their prices.
                    </p>
                </div>
                <span class="text-[10px] font-mono font-bold text-slate-400 group-hover:text-indigo-600 group-hover:translate-x-1.5 transition-all flex items-center gap-1 mt-auto">
                    Trigger test message <i data-lucide="arrow-right" class="w-3 h-3"></i>
                </span>
            </div>

            <!-- Feature 3 Card: Price Forecasting -->
            <div class="pricely-card reveal-card text-left p-6 flex flex-col gap-6 group cursor-pointer focus:outline-none focus:ring-0 min-h-[220px]">
                <div class="icon-bg w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center mb-1">
                    <i data-lucide="trending-up" class="w-5 h-5 text-amber-600"></i>
                </div>
                <div class="space-y-2">
                    <h3 class="group-hover:text-amber-600 transition-colors text-lg leading-snug">
                        Price Forecasting
                    </h3>
                    <p class="text-sm text-slate-500 leading-snug">
                        Analyze historical trends and view simple predictive models to decide the best time to sell your harvest.
                    </p>
                </div>
                <span class="text-[10px] font-mono font-bold text-slate-400 group-hover:text-amber-600 group-hover:translate-x-1.5 transition-all flex items-center gap-1 mt-auto">
                    Open projection charts <i data-lucide="arrow-right" class="w-3 h-3"></i>
                </span>
            </div>

        </div>
    </section>
@endsection
