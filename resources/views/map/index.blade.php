@extends('layouts.app')

@section('header')
    <div class="flex justify-between items-center page-header gap-4 flex-wrap">
        <h2 class="font-display font-bold text-2xl text-slate-900 leading-tight tracking-tight">
            {{ __('Interactive Price Map') }}
        </h2>
        <div class="flex gap-2 items-center flex-wrap">
            <input
                type="text"
                id="shop-search"
                placeholder="Search shops..."
                class="input input-bordered input-sm w-52"
            />
            <select class="select select-bordered select-sm" id="crop-filter">
                <option value="all">All Crops</option>
                @foreach($crops as $crop)
                    <option value="{{ $crop->name }}">{{ $crop->name }}</option>
                @endforeach
            </select>
            <select class="select select-bordered select-sm" id="classification-filter">
                <option value="all">All Classifications</option>
                <option value="trader">Trader / Dealer</option>
                <option value="miller">Miller</option>
                <option value="wholesaler">Wholesaler</option>
                <option value="retailer">Retailer</option>
                <option value="government">Gov't-Accredited</option>
                <option value="cooperative">Cooperative</option>
            </select>
        </div>
    </div>
@endsection

@section('content')
    {{-- Map Container --}}
    <div class="pricely-card h-[600px] overflow-hidden reveal-stagger-item max-w-7xl mx-auto px-6 md:px-8 relative">
        <div id="price-map" class="w-full h-full z-0"></div>

        {{-- Shop Info Side Panel --}}
        <div
            id="shop-info-panel"
            class="absolute top-0 right-0 h-full w-80 max-w-full bg-white shadow-2xl z-[500] flex flex-col transform translate-x-full opacity-0 transition-all duration-300 ease-in-out rounded-r-2xl"
        >
            <div class="flex items-center px-5 py-4 bg-emerald-50 border-b border-emerald-100">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-emerald-100 rounded-xl flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    </div>
                    <div>
                        <p id="panel-shop-name" class="text-sm font-bold text-slate-800 leading-tight">Shop Info</p>
                        <p class="text-xs text-emerald-600 font-semibold">Shop Information</p>
                    </div>
                </div>
            </div>
            <div id="panel-shop-body" class="flex-1 overflow-y-auto px-5 py-4 space-y-5"></div>
            <div class="px-5 py-4 border-t border-slate-100 flex flex-col gap-2">
                <button id="get-directions-btn" class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm transition-colors flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="3 11 22 2 13 21 11 13 3 11"/></svg>
                    Get Directions
                </button>
                <button id="shop-info-close" class="w-full py-2.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-600 font-semibold text-sm transition-colors">
                    Close
                </button>
            </div>
        </div>

        {{-- Active Route Banner (shown inside map when route is active) --}}
        <div
            id="route-active-bar"
            class="hidden absolute bottom-4 left-1/2 -translate-x-1/2 z-[510] bg-white rounded-2xl shadow-xl border border-emerald-100 px-5 py-3 flex items-center gap-4 min-w-[280px] max-w-[90%]"
        >
            <div class="w-8 h-8 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="3 11 22 2 13 21 11 13 3 11"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-bold text-emerald-700 leading-tight" id="route-label">Calculating route…</p>
                <p class="text-[11px] text-slate-400" id="route-meta"></p>
            </div>
            <button id="clear-route-btn" class="flex-shrink-0 text-xs font-semibold text-slate-500 hover:text-red-600 transition-colors">✕ Clear</button>
        </div>
    </div>

    {{-- Search Results Dropdown --}}
    <div class="max-w-7xl mx-auto px-6 md:px-8 mt-4">
        <div id="shop-search-results" class="hidden bg-white border border-base-300 rounded-lg shadow-lg p-4 max-h-64 overflow-y-auto">
            <div class="space-y-2"></div>
        </div>
    </div>

    {{-- Classification Legend --}}
    <div class="mt-5 max-w-7xl mx-auto px-6 md:px-8">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Map Legend</p>
        <div class="flex flex-wrap gap-2">
            <span class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-violet-50 border border-violet-200 text-xs font-semibold text-violet-700">
                <span class="w-2.5 h-2.5 rounded-full bg-violet-600"></span> Trader / Dealer
            </span>
            <span class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-orange-50 border border-orange-200 text-xs font-semibold text-orange-700">
                <span class="w-2.5 h-2.5 rounded-full bg-orange-600"></span> Miller
            </span>
            <span class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-blue-50 border border-blue-200 text-xs font-semibold text-blue-700">
                <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span> Wholesaler
            </span>
            <span class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-teal-50 border border-teal-200 text-xs font-semibold text-teal-700">
                <span class="w-2.5 h-2.5 rounded-full bg-teal-600"></span> Retailer
            </span>
            <span class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-red-50 border border-red-200 text-xs font-semibold text-red-700">
                <span class="w-2.5 h-2.5 rounded-full bg-red-600"></span> Gov't-Accredited
            </span>
            <span class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-amber-50 border border-amber-200 text-xs font-semibold text-amber-700">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Cooperative
            </span>
        </div>
    </div>

    {{-- ─── Discovery Cards ─────────────────────────────────────────────────────── --}}
    <div class="mt-6 max-w-7xl mx-auto px-6 md:px-8 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

        {{-- Shops Nearby --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden flex flex-col">
            <div class="px-4 py-3 border-b border-slate-100 flex items-center gap-2.5 bg-gradient-to-r from-emerald-50 to-white">
                <div class="w-8 h-8 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-slate-800 leading-tight">Shops Nearby</p>
                    <p class="text-[11px] text-slate-400">Closest to your location</p>
                </div>
            </div>
            <div id="nearby-shops-list" class="flex-1 divide-y divide-slate-50/80 min-h-[120px]">
                <div class="px-4 py-6 text-center">
                    <p class="text-xs text-slate-400 mb-2">Allow location access to see nearby shops</p>
                    <button id="retry-geolocation" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 transition-colors">
                        Enable Location ↗
                    </button>
                </div>
            </div>
        </div>

        {{-- Most Popular --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden flex flex-col">
            <div class="px-4 py-3 border-b border-slate-100 flex items-center gap-2.5 bg-gradient-to-r from-rose-50 to-white">
                <div class="w-8 h-8 rounded-xl bg-rose-100 flex items-center justify-center flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#e11d48" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 0 1-7 7 7 7 0 0 1-7-7c0-1.53.4-2.973 1-4.5"/><path d="M12 22v0"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-slate-800 leading-tight">Most Popular</p>
                    <p class="text-[11px] text-slate-400">By subscribers &amp; visits</p>
                </div>
            </div>
            <div id="popular-shops-list" class="flex-1 divide-y divide-slate-50/80 min-h-[120px]">
                <div class="px-4 py-6 text-center text-xs text-slate-400">Loading...</div>
            </div>
        </div>

        {{-- Recently Updated --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden flex flex-col">
            <div class="px-4 py-3 border-b border-slate-100 flex items-center gap-2.5 bg-gradient-to-r from-blue-50 to-white">
                <div class="w-8 h-8 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-slate-800 leading-tight">Recently Updated</p>
                    <p class="text-[11px] text-slate-400">Latest price updates</p>
                </div>
            </div>
            <div id="recent-shops-list" class="flex-1 divide-y divide-slate-50/80 min-h-[120px]">
                <div class="px-4 py-6 text-center text-xs text-slate-400">Loading...</div>
            </div>
        </div>

        {{-- Best Prices --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden flex flex-col">
            <div class="px-4 py-3 border-b border-slate-100 flex items-center gap-2.5 bg-gradient-to-r from-amber-50 to-white">
                <div class="w-8 h-8 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-slate-800 leading-tight">Best Prices</p>
                    <p class="text-[11px] text-slate-400">Highest buying offers</p>
                </div>
            </div>
            <div id="best-price-shops-list" class="flex-1 divide-y divide-slate-50/80 min-h-[120px]">
                <div class="px-4 py-6 text-center text-xs text-slate-400">Loading...</div>
            </div>
        </div>

    </div>

    <style>
        .shop-info-section { margin-bottom: 0.25rem; }
        .shop-section-label { font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #94a3b8; margin-bottom: 0.5rem; display: block; }
        .shop-info-rows { display: flex; flex-direction: column; gap: 0.5rem; }
        .shop-info-row { display: flex; align-items: flex-start; gap: 0.5rem; font-size: 0.8rem; }
        .shop-info-label { display: flex; align-items: center; gap: 0.3rem; color: #64748b; font-weight: 600; min-width: 80px; flex-shrink: 0; }
        .shop-info-value { color: #1e293b; font-weight: 500; }
        .shop-info-muted { color: #94a3b8; font-style: italic; }
        .shop-crops-list { display: flex; flex-direction: column; gap: 0.4rem; }
        .shop-crop-row { display: flex; align-items: center; justify-content: space-between; background: #f8fafc; border-radius: 0.5rem; padding: 0.45rem 0.6rem; }
        .shop-crop-name { font-size: 0.8rem; font-weight: 600; color: #334155; }
        .shop-crop-price { font-size: 0.85rem; font-weight: 700; color: #059669; }
        .shop-crop-unit { font-size: 0.7rem; font-weight: 500; color: #94a3b8; margin-left: 1px; }
        .shop-no-crops { font-size: 0.8rem; color: #94a3b8; font-style: italic; text-align: center; padding: 0.5rem 0; }
        .discovery-shop-item:hover { background: #f8fafc; }
    </style>

    @push('scripts')
        @vite(['resources/js/map.js'])
        <script
            src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_key') }}&libraries=marker&callback=initPriceMap&loading=async"
            async defer
        ></script>
    @endpush
@endsection
