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
            <div class="stat bg-base-100 rounded-box shadow-sm border border-base-300 stat-card stagger-1">
                <div class="stat-figure text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <div class="stat-title">Subscribed Farmers</div>
                <div class="stat-value text-primary">{{ Auth::user()->subscribers()->active()->count() }}</div>
                <div class="stat-desc">Receiving your SMS alerts</div>
            </div>

            <div class="stat bg-base-100 rounded-box shadow-sm border border-base-300 stat-card stagger-2">
                <div class="stat-figure text-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </div>
                <div class="stat-title">Prices Recorded</div>
                <div class="stat-value text-secondary">{{ $shop->prices()->count() }}</div>
                <div class="stat-desc">Total historical entries</div>
            </div>

            <div class="stat bg-base-100 rounded-box shadow-sm border border-base-300 stat-card stagger-3">
                <div class="stat-figure text-accent">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <div class="stat-title">Shop Status</div>
                <div class="stat-value text-xl">{{ $shop->is_active ? 'Active' : 'Inactive' }}</div>
                <div class="stat-desc"><a href="{{ route('shops.show') }}" class="link">View shop details</a></div>
            </div>

            <!-- Recent Prices -->
            <div class="card bg-base-100 shadow-sm border border-base-300 md:col-span-3 mt-4 stagger-4">
                <div class="card-body">
                    <h2 class="card-title">Your Latest Prices</h2>
                    <div class="overflow-x-auto mt-4">
                        <table class="table">
                            <thead>
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
                                            $latestPrice = $shop->prices()->where('crop_id', $crop->id)->where('specification', $spec)->latest('recorded_at')->first();
                                            $key = $crop->id . '_' . $spec;
                                            $ceiling = $latestCeilings[$key] ?? null;
                                        @endphp
                                        <tr>
                                            <td class="font-semibold">{{ $crop->name }} ({{ ucfirst($spec) }})</td>
                                            <td>
                                                @if($latestPrice)
                                                    <div class="text-lg font-bold {{ $ceiling && $latestPrice->price_per_kg < $ceiling->max_price ? 'text-error' : '' }}">₱{{ number_format($latestPrice->price_per_kg, 2) }}</div>
                                                @else
                                                    <div class="text-base-content/50 italic">Not set</div>
                                                @endif
                                            </td>
                                            <td>
                                                @if($latestPrice)
                                                    {{ $latestPrice->recorded_at->diffForHumans() }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @empty
                                    <tr><td colspan="3" class="text-center">No crops available in the system.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- DA Minimum Prices -->
            <div class="card bg-base-100 shadow-sm border border-base-300 md:col-span-3 mt-4 stat-card stagger-5">
                <div class="card-body">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-3">
                            <img src="https://img.bomboradyo.com/cauayan/2019/05/DA-LOGO.png" alt="Department of Agriculture Logo" class="w-10 h-10 object-contain drop-shadow-sm rounded-full bg-white">
                            <div>
                                <div class="flex items-center gap-2">
                                    <h2 class="card-title text-lg">{{ __('Department of Agriculture Minimum Prices') }}</h2>
                                    <span class="badge badge-warning badge-sm">{{ __('Guidelines') }}</span>
                                </div>
                                <p class="text-base-content/60 text-sm mt-0.5">{{ __('Minimum recommended buying prices set by the Department of Agriculture.') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="table" id="ceiling-prices-table">
                            <thead class="bg-base-200">
                                <tr>
                                    <th>{{ __('Crop') }}</th>
                                    <th>{{ __('Specification') }}</th>
                                    <th>{{ __('Minimum Price') }}</th>
                                    <th>{{ __('Effective Date') }}</th>
                                    <th>{{ __('Notes') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($latestCeilings as $cp)
                                    <tr class="hover">
                                        <td class="font-semibold">{{ $cp->crop->name }}</td>
                                        <td><span class="badge badge-primary badge-outline">{{ ucfirst($cp->specification) }}</span></td>
                                        <td>
                                            <span class="badge badge-error badge-lg font-bold gap-1">
                                                ₱{{ number_format($cp->max_price, 2) }}/{{ $cp->crop->unit }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ $cp->effective_date->format('M d, Y') }}
                                        </td>
                                        <td class="text-sm text-base-content/70 max-w-xs">{{ $cp->notes ?? '—' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-6 text-base-content/50 italic">{{ __('No DA minimum prices have been set yet.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-app-layout>
