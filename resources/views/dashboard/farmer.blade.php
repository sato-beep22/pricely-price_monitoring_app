<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-base-content leading-tight">
            {{ __('Farmer Dashboard') }}
        </h2>
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Welcome Card -->
        <div class="card bg-base-100 shadow-sm border border-base-300 md:col-span-3 stat-card stagger-1">
            <div class="card-body">
                <h2 class="card-title text-2xl">{{ __('Welcome back, :name!', ['name' => Auth::user()->name]) }}</h2>
                <p class="text-base-content/80">{{ __('Stay updated with the latest crop prices in your area.') }}</p>
                <div class="card-actions justify-end mt-4">
                    <a href="{{ route('map.index') }}" class="btn btn-primary">{{ __('Open Price Map') }}</a>
                    <a href="{{ route('forecast.index') }}" class="btn btn-secondary">{{ __('View Forecast') }}</a>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <x-dashboard.stat-card 
            title="Active Alerts" 
            value="{{ Auth::user()->subscriptions()->active()->count() }}" 
            desc="Shops you're tracking"
            color="primary" 
            stagger="2">
            <x-slot name="icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
            </x-slot>
        </x-dashboard.stat-card>

        <x-dashboard.stat-card 
            title="Market Trend" 
            value="Stable" 
            desc="Based on last 7 days"
            color="primary" 
            stagger="3">
            <x-slot name="icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
            </x-slot>
        </x-dashboard.stat-card>

        <div class="stat bg-base-100 rounded-box shadow-sm border border-base-200 stat-card stagger-4">
            <div class="stat-figure text-primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
            </div>
            <div class="stat-title">{{ __('SMS Settings') }}</div>
            <div class="stat-value text-primary text-md whitespace-normal break-all">{{ Auth::user()->phone ?? __('Not set') }}</div>
            <div class="stat-desc flex flex-col gap-1 mt-1">
                @if(Auth::user()->phone)
                    @if(Auth::user()->phoneVerified())
                        <span class="inline-flex items-center gap-1 text-success font-semibold text-xs">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ __('Verified') }}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 text-error font-semibold text-xs">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                            </svg>
                            {{ __('Not Verified') }}
                        </span>
                        <span class="text-warning text-xs">{{ __('Please verify your number to receive SMS alerts.') }}</span>
                    @endif
                @else
                    <span class="text-base-content/50 text-xs">{{ __('No phone number set.') }}</span>
                @endif
                <a href="{{ route('profile.edit') }}" class="link text-xs mt-0.5">{{ __('Update phone') }}</a>
            </div>
        </div>

        <!-- Profit Calculator -->
        <div class="md:col-span-3 stat-card stagger-5 mt-2">
            <x-profit-calculator :ceiling-prices="$ceilingPrices" />
        </div>

        <!-- DA Ceiling Prices -->
        <div class="md:col-span-3 mt-2 stagger-6">
            <x-dashboard.da-prices-table :prices="$ceilingPrices" />
        </div>

        <!-- Interactive Price Trend -->
        <div class="card bg-base-100 shadow-sm border border-base-200 md:col-span-3 mt-2 stat-card stagger-7" x-data="{ loading: true }">
            <div class="card-body">
                <h2 class="card-title text-lg mb-4">{{ __('Market Price Trend (Last 30 Days)') }}</h2>
                
                <!-- Skeleton Loader -->
                <div x-show="loading" class="w-full h-[350px] animate-pulse flex flex-col justify-end gap-2 border-b border-base-200 pb-2">
                    <div class="flex items-end justify-between w-full h-full gap-4 px-4">
                        <div class="w-1/6 bg-base-200 h-[40%] rounded-t-md"></div>
                        <div class="w-1/6 bg-base-200 h-[60%] rounded-t-md"></div>
                        <div class="w-1/6 bg-base-200 h-[30%] rounded-t-md"></div>
                        <div class="w-1/6 bg-base-200 h-[80%] rounded-t-md"></div>
                        <div class="w-1/6 bg-base-200 h-[50%] rounded-t-md"></div>
                    </div>
                </div>

                <div id="price-trend-chart" style="height: 350px;" x-show="!loading" style="display: none;" @chart-loaded.window="loading = false"></div>
            </div>
        </div>

        <!-- DA Price Direct Sources -->
        <div class="card bg-base-100 shadow-sm border border-base-300 md:col-span-3 mt-2 stat-card stagger-8">
            <div class="card-body">
                <div class="flex items-center gap-3 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-info">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                    </svg>
                    <div>
                        <h2 class="card-title text-lg">{{ __('DA Price Direct Sources') }}</h2>
                        <p class="text-base-content/60 text-sm mt-0.5">{{ __('Click on a crop below to view the official reference link.') }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach($crops as $crop)
                        <div class="flex items-center justify-between p-4 border border-base-200 rounded-xl hover:bg-base-200 transition-colors">
                            <span class="font-medium text-base-content">{{ $crop->name }}</span>
                            @if(isset($sourceLinks['da_price_source_link_' . $crop->id]))
                                <a href="{{ $sourceLinks['da_price_source_link_' . $crop->id] }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-info btn-outline gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                                    {{ __('View') }}
                                </a>
                            @else
                                <span class="text-xs text-base-content/40 italic">{{ __('No link available') }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize Price Trend Chart
                if (document.getElementById('price-trend-chart')) {
                    fetch('{{ route('api.price-trend') }}')
                        .then(response => response.json())
                        .then(data => {
                            var options = {
                                series: data.series,
                                chart: {
                                    height: 350,
                                    type: 'line',
                                    zoom: { enabled: false },
                                    toolbar: { show: false },
                                    fontFamily: 'inherit'
                                },
                                dataLabels: { enabled: false },
                                stroke: { curve: 'smooth', width: 3 },
                                colors: ['#059669', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6'],
                                grid: {
                                    borderColor: '#e2e8f0',
                                    strokeDashArray: 4,
                                },
                                xaxis: { categories: data.categories }
                            };
                            var chart = new ApexCharts(document.querySelector("#price-trend-chart"), options);
                            chart.render().then(() => {
                                window.dispatchEvent(new CustomEvent('chart-loaded'));
                            });
                        });
                }
            });
        </script>
    @endpush
</x-app-layout>
