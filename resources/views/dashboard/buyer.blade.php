<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-base-content leading-tight">
                {{ __('Buyer Dashboard') }}
            </h2>
            <a href="{{ route('prices.create') }}" class="btn btn-primary btn-sm">Record New Prices</a>
        </div>
    </x-slot>

    @php
        $shop = Auth::user()->shop;
    @endphp

    @if(!$shop)
        <div class="alert alert-warning shadow-lg mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
            <span>You haven't set up your shop profile yet. Farmers won't be able to see you on the map.</span>
            <div>
                <a href="{{ route('shops.edit') }}" class="btn btn-sm btn-warning">Set up shop</a>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Stats -->
            <x-dashboard.stat-card 
                title="Subscribed Farmers" 
                value="{{ Auth::user()->subscribers()->active()->count() }}" 
                desc="Receiving your SMS alerts"
                color="primary" 
                stagger="1">
                <x-slot name="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </x-slot>
            </x-dashboard.stat-card>

            <x-dashboard.stat-card 
                title="Prices Recorded" 
                value="{{ $shop->prices()->count() }}" 
                desc="Total historical entries"
                color="primary" 
                stagger="2">
                <x-slot name="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </x-slot>
            </x-dashboard.stat-card>

            <x-dashboard.stat-card 
                title="Shop Status" 
                value="{{ $shop->is_active ? 'Active' : 'Inactive' }}" 
                color="primary" 
                stagger="3"
                href="{{ route('shops.show') }}"
                desc="Click to view shop details">
                <x-slot name="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </x-slot>
            </x-dashboard.stat-card>

            <!-- Recent Prices -->
            <div class="card bg-base-100 shadow-sm border border-base-200 md:col-span-3 mt-4 stagger-4">
                <div class="card-body">
                    <h2 class="card-title">Your Latest Prices</h2>
                    <div class="overflow-x-auto mt-4">
                        <table class="table table-zebra">
                            <thead class="bg-base-200 text-base-content/80">
                                <tr>
                                    <th>Crop</th>
                                    <th>Price/kg</th>
                                    <th>Last Updated</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(\App\Models\Crop::all() as $crop)
                                    @php $specs = array_map('trim', explode(',', $crop->specification)); @endphp
                                    @foreach($specs as $spec)
                                        @php
                                            $latestPrice = collect($shopLatestPrices)->where('crop_id', $crop->id)->where('specification', $spec)->first();
                                            $ceiling = collect($ceilingPrices)->where('crop_id', $crop->id)->where('specification', $spec)->first();
                                        @endphp
                                        <tr>
                                            <td class="font-semibold text-base-content">{{ $crop->name }} <span class="badge badge-sm badge-ghost ml-1">{{ ucfirst($spec) }}</span></td>
                                            <td>
                                                @if($latestPrice)
                                                    <div class="text-lg font-bold {{ $ceiling && $latestPrice->price_per_kg < $ceiling->max_price ? 'text-error' : 'text-primary' }}">₱{{ number_format($latestPrice->price_per_kg, 2) }}</div>
                                                @else
                                                    <div class="text-base-content/40 italic text-sm">Not set</div>
                                                @endif
                                            </td>
                                            <td class="text-sm">
                                                @if($latestPrice)
                                                    <span class="text-base-content/70">{{ $latestPrice->recorded_at->diffForHumans() }}</span>
                                                @else
                                                    <span class="text-base-content/40">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-8">
                                            <div class="flex flex-col items-center justify-center text-base-content/50">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-2 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                                </svg>
                                                <p class="italic">No crops available in the system.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- DA Minimum Prices -->
            <div class="md:col-span-3 mt-4 stagger-5">
                <x-dashboard.da-prices-table :prices="$ceilingPrices" />
            </div>
        </div>
    @endif
</x-app-layout>
