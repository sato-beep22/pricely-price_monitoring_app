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
                <h2 class="card-title text-2xl">Welcome back, {{ Auth::user()->name }}!</h2>
                <p class="text-base-content/80">Stay updated with the latest crop prices in your area.</p>
                <div class="card-actions justify-end mt-4">
                    <a href="{{ route('map.index') }}" class="btn btn-primary">Open Price Map</a>
                    <a href="{{ route('forecast.index') }}" class="btn btn-secondary">View Forecast</a>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="stat bg-base-100 rounded-box shadow-sm border border-base-300 stat-card stagger-2">
            <div class="stat-figure text-primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
            </div>
            <div class="stat-title">Active Alerts</div>
            <div class="stat-value text-primary">{{ Auth::user()->subscriptions()->active()->count() }}</div>
            <div class="stat-desc">Shops you're tracking</div>
        </div>

        <div class="stat bg-base-100 rounded-box shadow-sm border border-base-300 stat-card stagger-3">
            <div class="stat-figure text-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
            </div>
            <div class="stat-title">Market Trend</div>
            <div class="stat-value text-secondary">Stable</div>
            <div class="stat-desc">Based on last 7 days</div>
        </div>

        <div class="stat bg-base-100 rounded-box shadow-sm border border-base-300 stat-card stagger-4">
            <div class="stat-figure text-accent">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
            </div>
            <div class="stat-title">SMS Settings</div>
            <div class="stat-value text-md whitespace-normal break-all">{{ Auth::user()->phone ?? 'Not set' }}</div>
            <div class="stat-desc"><a href="{{ route('profile.edit') }}" class="link">Update phone</a></div>
        </div>

        <!-- DA Ceiling Prices -->
        <div class="card bg-base-100 shadow-sm border border-base-300 md:col-span-3 mt-2 stat-card stagger-5">
            <div class="card-body">
                <div class="flex items-center gap-2 mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-warning">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                    <h2 class="card-title text-lg">Department of Agriculture Ceiling Prices</h2>
                    <span class="badge badge-warning badge-sm">Guidelines</span>
                </div>
                <p class="text-base-content/60 text-sm mb-4">Maximum recommended selling prices set by the Department of Agriculture.</p>

                <div class="overflow-x-auto">
                    <table class="table" id="ceiling-prices-table">
                        <thead class="bg-base-200">
                            <tr>
                                <th>Crop</th>
                                <th>Specification</th>
                                <th>Ceiling Price</th>
                                <th>Effective Date</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ceilingPrices as $cp)
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
                                        @if($cp->effective_date->isToday() || $cp->effective_date->isPast())
                                            <span class="badge badge-success badge-sm ml-1">Active</span>
                                        @else
                                            <span class="badge badge-warning badge-sm ml-1">Upcoming</span>
                                        @endif
                                    </td>
                                    <td class="text-sm text-base-content/70 max-w-xs">{{ $cp->notes ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-6 text-base-content/50 italic">No ceiling prices have been set yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
