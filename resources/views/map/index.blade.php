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
    <div class="pricely-card h-[600px] overflow-hidden reveal-stagger-item max-w-7xl mx-auto px-6 md:px-8">
        <div id="price-map" class="w-full h-full z-0"></div>
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

    <!-- Modals rendered outside containers for proper fixed positioning -->
    @auth
        @if($availableShops->isNotEmpty())
            @foreach($availableShops as $shop)
                <x-subscribe-modal :shop="$shop" :crops="$crops" />
            @endforeach
        @endif
    @endauth

    @push('scripts')
        <!-- Vite will load Leaflet CSS and JS globally via app.js -->
        @vite(['resources/js/map.js'])
    @endpush
@endsection
