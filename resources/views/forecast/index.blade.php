@extends('layouts.app')

@section('header')
    <h2 class="font-display font-bold text-2xl text-slate-900 leading-tight tracking-tight page-header">
        {{ __('Price Forecasting') }}
    </h2>
@endsection

@section('content')

    <div class="pricely-card reveal-stagger-item max-w-7xl mx-auto px-6 md:px-8">
        <div class="p-6">

            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                <div>
                    <h2 class="text-2xl text-slate-800">Market Trends &amp; Projections</h2>
                    <p class="text-slate-500 text-sm">Based on 30-day historical data and simple moving average (7-day forecast)</p>
                </div>
            </div>

            {{-- ========================================================
                 MARKET SUMMARY (ALL CROPS)
                 ======================================================== --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                {{-- SELL NOW --}}
                <div class="rounded-xl border border-rose-200 bg-rose-50/50 p-4">
                    <h3 class="font-bold text-rose-700 flex items-center gap-2 mb-3">
                        <span class="text-xl">🚨</span> Prices Dropping (Sell Now)
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        @forelse($marketSummary['SELL_NOW'] as $item)
                            <span class="badge bg-white border-rose-200 text-rose-700 shadow-sm font-semibold">
                                {{ $item['crop_name'] }} ({{ ucfirst($item['spec']) }})
                                <span class="ml-1 opacity-70 text-xs">{{ $item['trend'] }}%</span>
                            </span>
                        @empty
                            <span class="text-sm text-rose-400 italic">None at the moment</span>
                        @endforelse
                    </div>
                </div>

                {{-- HOLD --}}
                <div class="rounded-xl border border-amber-200 bg-amber-50/50 p-4">
                    <h3 class="font-bold text-amber-700 flex items-center gap-2 mb-3">
                        <span class="text-xl">⏳</span> Prices Rising (Hold)
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        @forelse($marketSummary['HOLD'] as $item)
                            <span class="badge bg-white border-amber-200 text-amber-700 shadow-sm font-semibold">
                                {{ $item['crop_name'] }} ({{ ucfirst($item['spec']) }})
                                <span class="ml-1 opacity-70 text-xs">+{{ $item['trend'] }}%</span>
                            </span>
                        @empty
                            <span class="text-sm text-amber-400 italic">None at the moment</span>
                        @endforelse
                    </div>
                </div>

                {{-- STABLE --}}
                <div class="rounded-xl border border-emerald-200 bg-emerald-50/50 p-4">
                    <h3 class="font-bold text-emerald-700 flex items-center gap-2 mb-3">
                        <span class="text-xl">✅</span> Stable (Safe to Sell)
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        @forelse($marketSummary['STABLE'] as $item)
                            <span class="badge bg-white border-emerald-200 text-emerald-700 shadow-sm font-semibold">
                                {{ $item['crop_name'] }} ({{ ucfirst($item['spec']) }})
                            </span>
                        @empty
                            <span class="text-sm text-emerald-400 italic">None at the moment</span>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="divider mb-6">Detailed Forecast by Crop</div>

            <div class="mb-4">
                <div class="tabs tabs-boxed bg-base-200 inline-flex" id="crop-tabs">
                    @php $firstTab = true; @endphp
                    @foreach($crops as $crop)
                        @php $specs = array_map('trim', explode(',', $crop->specification)); @endphp
                        @foreach($specs as $spec)
                            <a class="tab {{ $firstTab ? 'tab-active font-bold' : '' }}"
                               data-crop-id="{{ $crop->id }}"
                               data-spec="{{ $spec }}"
                               data-crop-name="{{ $crop->name }} ({{ ucfirst($spec) }})">
                               {{ $crop->name }} ({{ ucfirst($spec) }})
                            </a>
                            @php $firstTab = false; @endphp
                        @endforeach
                    @endforeach
                </div>
            </div>



            {{-- ========================================================
                 BREAK-EVEN INPUT
                 ======================================================== --}}
            <div class="mb-4 flex flex-col sm:flex-row items-start sm:items-center gap-3">
                <label for="break-even-input" class="text-sm font-semibold text-slate-600 whitespace-nowrap">
                    💰 Your Production Cost (₱/kg):
                </label>
                <div class="flex items-center gap-2">
                    <div class="join">
                        <span class="join-item btn btn-sm btn-disabled bg-base-200 border border-base-300 font-bold text-slate-500">₱</span>
                        <input
                            type="number"
                            id="break-even-input"
                            placeholder="e.g. 35.00"
                            min="0"
                            step="0.01"
                            class="join-item input input-sm input-bordered w-36 focus:outline-none focus:border-primary"
                        />
                    </div>
                    <span class="text-xs text-slate-400">Enter your cost to see profit/loss overlay on the chart</span>
                </div>
            </div>

            {{-- Chart Container --}}
            <div class="relative">
                <div id="forecast-chart" class="w-full h-[420px]"></div>
                <div id="chart-loader" class="hidden absolute inset-0 bg-base-100/80 flex items-center justify-center z-10 rounded-box">
                    <span class="loading loading-spinner loading-lg text-primary"></span>
                </div>
            </div>

            {{-- Legend --}}
            <div class="flex flex-wrap gap-4 mt-4 text-xs text-slate-500">
                <span class="flex items-center gap-1.5"><span class="inline-block w-6 h-0.5 bg-emerald-500 rounded"></span>Actual Price</span>
                <span class="flex items-center gap-1.5"><span class="inline-block w-6 border-t-2 border-dashed border-indigo-400"></span>Forecast Trend</span>
                <span id="legend-break-even" class="hidden items-center gap-1.5"><span class="inline-block w-6 border-t-2 border-dashed border-rose-500"></span>Your Break-Even Cost</span>
            </div>

        </div>
    </div>

    @push('scripts')
        @vite(['resources/js/forecast.js'])
    @endpush
@endsection
