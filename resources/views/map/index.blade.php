@extends('layouts.app')

@section('header')
    <div class="flex justify-between items-center page-header gap-4">
        <h2 class="font-display font-bold text-2xl text-slate-900 leading-tight tracking-tight">
            {{ __('Interactive Price Map') }}
        </h2>
        <div class="flex gap-2 items-center">
            <input
                type="text"
                id="shop-search"
                placeholder="Search shops..."
                class="input input-bordered input-sm w-64"
            />
            <select class="select select-bordered select-sm w-full max-w-xs" id="crop-filter">
                <option value="all">All Crops</option>
                @foreach($crops as $crop)
                    <option value="{{ $crop->name }}">{{ $crop->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
@endsection

@section('content')
    <!-- Map Container -->
    <div class="pricely-card h-[600px] overflow-hidden reveal-stagger-item max-w-7xl mx-auto px-6 md:px-8 relative">
        <div id="price-map" class="w-full h-full z-0"></div>

        <!-- Shop Info Side Panel -->
        <div
            id="shop-info-panel"
            class="absolute top-0 right-0 h-full w-80 max-w-full bg-white shadow-2xl z-[500] flex flex-col transform translate-x-full opacity-0 transition-all duration-300 ease-in-out rounded-r-2xl"
        >
            <!-- Panel Header -->
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
            <!-- Panel Body -->
            <div id="panel-shop-body" class="flex-1 overflow-y-auto px-5 py-4 space-y-5"></div>
            <!-- Panel Footer -->
            <div class="px-5 py-4 border-t border-slate-100">
                <button id="shop-info-close" class="w-full py-2.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-600 font-semibold text-sm transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- Search Results Dropdown -->
    <div class="max-w-7xl mx-auto px-6 md:px-8 mt-4">
        <div id="shop-search-results" class="hidden bg-white border border-base-300 rounded-lg shadow-lg p-4 max-h-64 overflow-y-auto">
            <div class="space-y-2">
                <!-- Results will be populated here -->
            </div>
        </div>
    </div>

    <!-- Legend -->
    <div class="mt-4 flex flex-wrap gap-4 text-sm text-base-content/80 justify-center">
        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-blue-500 block"></span> Default Buyer
        </div>
        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-green-500 block"></span> High Buying Price
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
    </style>

    @push('scripts')
        <!-- Vite will load Leaflet CSS and JS globally via app.js -->
        @vite(['resources/js/map.js'])
    @endpush
@endsection
