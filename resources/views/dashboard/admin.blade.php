<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-base-content leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-6 mb-8">
        <x-dashboard.stat-card 
            title="Total Users" 
            value="{{ \App\Models\User::count() }}" 
            color="primary" 
            stagger="1">
            <x-slot name="icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </x-slot>
        </x-dashboard.stat-card>

        <x-dashboard.stat-card 
            title="Farmers" 
            value="{{ \App\Models\User::where('role', 'farmer')->count() }}" 
            color="primary" 
            stagger="1">
            <x-slot name="icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            </x-slot>
        </x-dashboard.stat-card>

        <x-dashboard.stat-card 
            title="Buyers" 
            value="{{ \App\Models\User::where('role', 'buyer')->count() }}" 
            color="primary" 
            stagger="1">
            <x-slot name="icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            </x-slot>
        </x-dashboard.stat-card>
        
        <x-dashboard.stat-card 
            title="Active Shops" 
            value="{{ \App\Models\Shop::active()->count() }}" 
            color="primary" 
            stagger="2">
            <x-slot name="icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </x-slot>
        </x-dashboard.stat-card>

        <x-dashboard.stat-card 
            title="Prices Logged" 
            value="{{ \App\Models\Price::count() }}" 
            color="primary" 
            stagger="3">
            <x-slot name="icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            </x-slot>
        </x-dashboard.stat-card>

        <x-dashboard.stat-card 
            title="SMS Alerts Sent" 
            value="{{ \App\Models\SmsLog::count() }}" 
            desc="View Semaphore SMS Logs"
            color="info" 
            stagger="4"
            href="{{ route('admin.sms-logs.index') }}">
            <x-slot name="icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
            </x-slot>
        </x-dashboard.stat-card>
    </div>

    <div class="grid grid-cols-1 gap-6">
        <x-dashboard.da-prices-table :prices="$ceilingPrices" />
    </div>
</x-app-layout>
