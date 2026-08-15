<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-base-content leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-6 mb-8">
        <div class="stat bg-base-100 rounded-box shadow-sm border border-base-300 stagger-1">
            <div class="stat-figure text-primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
            <div class="stat-title">Total Users</div>
            <div class="stat-value text-primary">{{ \App\Models\User::count() }}</div>
        </div>

        <div class="stat bg-base-100 rounded-box shadow-sm border border-base-300 stagger-1">
            <div class="stat-figure text-success">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            </div>
            <div class="stat-title">Farmers</div>
            <div class="stat-value text-success">{{ \App\Models\User::where('role', 'farmer')->count() }}</div>
        </div>

        <div class="stat bg-base-100 rounded-box shadow-sm border border-base-300 stagger-1">
            <div class="stat-figure text-warning">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            </div>
            <div class="stat-title">Buyers</div>
            <div class="stat-value text-warning">{{ \App\Models\User::where('role', 'buyer')->count() }}</div>
        </div>
        
        <div class="stat bg-base-100 rounded-box shadow-sm border border-base-300 stagger-2">
            <div class="stat-figure text-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </div>
            <div class="stat-title">Active Shops</div>
            <div class="stat-value text-secondary">{{ \App\Models\Shop::active()->count() }}</div>
        </div>

        <div class="stat bg-base-100 rounded-box shadow-sm border border-base-300 stagger-3">
            <div class="stat-figure text-accent">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            </div>
            <div class="stat-title">Prices Logged</div>
            <div class="stat-value text-accent">{{ \App\Models\Price::count() }}</div>
        </div>

        <div class="stat bg-base-100 rounded-box shadow-sm border border-base-300 stagger-4">
            <div class="stat-figure text-info">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
            </div>
            <div class="stat-title">SMS Alerts Sent</div>
            <div class="stat-value text-info">--</div>
            <div class="stat-desc">Check Semaphore logs</div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6">
        <!-- Current DA Guidelines -->
        <div class="card bg-base-100 shadow-sm border border-base-300 stagger-6">
            <div class="card-body">
                <h2 class="card-title mb-4">Current Department of Agriculture Ceiling Prices</h2>
                <ul class="space-y-4">
                    @forelse(\App\Models\Crop::all() as $crop)
                        @php $specs = array_map('trim', explode(',', $crop->specification)); @endphp
                        @foreach($specs as $spec)
                            @php
                                $ceiling = \App\Models\CeilingPrice::where('crop_id', $crop->id)
                                    ->where('specification', $spec)
                                    ->where('effective_date', '<=', now())
                                    ->orderByDesc('effective_date')
                                    ->first();
                            @endphp
                            <li class="flex items-center justify-between p-3 bg-base-200 rounded-lg">
                                <span class="font-medium">{{ $crop->name }} ({{ ucfirst($spec) }})</span>
                                @if($ceiling)
                                    <div class="text-right">
                                        <span class="font-bold text-lg text-error">₱{{ number_format($ceiling->max_price, 2) }}</span>
                                        <div class="text-xs text-base-content/60">effective {{ $ceiling->effective_date->format('M d, Y') }}</div>
                                    </div>
                                @else
                                    <span class="text-base-content/50 italic text-sm">Not set</span>
                                @endif
                            </li>
                        @endforeach
                    @empty
                        <li class="text-center text-base-content/50">No crops found.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
