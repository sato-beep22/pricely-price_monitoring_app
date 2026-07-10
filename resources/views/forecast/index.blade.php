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
                    <h2 class="text-2xl text-slate-800">Market Trends & Projections</h2>
                    <p class="text-slate-500 text-sm">Based on 30-day historical data and simple moving average (7-day forecast)</p>
                </div>
                
                <div class="tabs tabs-boxed bg-base-200" id="crop-tabs">
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

            <!-- Chart Container -->
            <div id="forecast-chart" class="w-full h-[400px]"></div>
            
            <!-- Loading indicator -->
            <div id="chart-loader" class="hidden absolute inset-0 bg-base-100/80 flex items-center justify-center z-10 rounded-box">
                <span class="loading loading-spinner loading-lg text-primary"></span>
            </div>

        </div>
    </div>

    @push('scripts')
        <!-- Vite will load ApexCharts JS globally via app.js -->
        @vite(['resources/js/forecast.js'])
    @endpush
@endsection
