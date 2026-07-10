<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 w-72 flex-shrink-0 bg-white border-r border-slate-200 h-screen flex flex-col z-50 transition-transform duration-300 ease-in-out md:sticky md:top-0 md:translate-x-0">
    <button @click="sidebarOpen = false" class="md:hidden absolute top-4 right-4 p-2 text-slate-400 hover:text-slate-600">
        <i data-lucide="x" class="w-5 h-5"></i>
    </button>
    <!-- Header -->
    <div class="p-6">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-[#04965e] flex items-center justify-center text-white shadow-sm">
                <i data-lucide="sprout" class="w-6 h-6"></i>
            </div>
            <div class="flex flex-col">
                <span class="font-bold text-slate-800 text-lg leading-none tracking-tight">Pricely Agri</span>
                <span class="text-xs font-bold text-emerald-600 tracking-wider mt-1">{{ strtoupper(auth()->user()?->role ?? 'GUEST') }} SPACE</span>
            </div>
        </a>
    </div>

    <!-- Navigation -->
    <div class="flex-1 overflow-y-auto px-4 py-2 flex flex-col gap-6">
        <!-- Main Dashboards -->
        <div>
            <h3 class="px-2 text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Main Dashboard</h3>
            <div class="space-y-1">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('dashboard') ? 'bg-emerald-600 text-white font-medium shadow-sm' : 'text-slate-600 hover:bg-slate-50 font-medium' }}">
                    <i data-lucide="layout-grid" class="w-5 h-5"></i>
                    Dashboard
                </a>
                <a href="{{ route('map.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('map.index') ? 'bg-emerald-600 text-white font-medium shadow-sm' : 'text-slate-600 hover:bg-slate-50 font-medium' }}">
                    <i data-lucide="compass" class="w-5 h-5"></i>
                    Price Map
                </a>
            </div>
        </div>

        <!-- Farmer Logbooks -->
        @if(auth()->user() && auth()->user()->isFarmer())
        <div>
            <h3 class="px-2 text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Farmer Logbooks</h3>
            <div class="space-y-1">
                <a href="{{ route('reports.index') }}" class="flex items-center justify-between px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('reports.*') ? 'bg-emerald-600 text-white font-medium shadow-sm' : 'text-slate-600 hover:bg-slate-50 font-medium' }}">
                    <div class="flex items-center gap-3">
                        <i data-lucide="file-text" class="w-5 h-5 {{ request()->routeIs('reports.*') ? 'text-white' : 'text-slate-500' }}"></i>
                        Reports
                    </div>
                    <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-bold">New</span>
                </a>
                <a href="{{ route('subscriptions.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('subscriptions.index') ? 'bg-emerald-600 text-white font-medium shadow-sm' : 'text-slate-600 hover:bg-slate-50 font-medium' }}">
                    <i data-lucide="smartphone" class="w-5 h-5 text-slate-500"></i>
                    SMS Alerts 
                </a>
                <a href="{{ route('forecast.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('forecast.index') ? 'bg-emerald-600 text-white font-medium shadow-sm' : 'text-slate-600 hover:bg-slate-50 font-medium' }}">
                    <i data-lucide="trending-up" class="w-5 h-5 text-slate-500"></i>
                    Price Forecasts 
                </a>
            </div>
        </div>
        @endif
        <!-- Buyer Tools -->
        @if(auth()->user() && auth()->user()->isBuyer())
        <div>
            <h3 class="px-2 text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Buyer Tools</h3>
            <div class="space-y-1">
                <a href="{{ route('shops.show') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('shops.*') ? 'bg-emerald-600 text-white font-medium shadow-sm' : 'text-slate-600 hover:bg-slate-50 font-medium' }}">
                    <i data-lucide="store" class="w-5 h-5 {{ request()->routeIs('shops.*') ? 'text-white' : 'text-slate-500' }}"></i>
                    My Shop Profile
                </a>
                <a href="{{ route('prices.create') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('prices.create') ? 'bg-emerald-600 text-white font-medium shadow-sm' : 'text-slate-600 hover:bg-slate-50 font-medium' }}">
                    <i data-lucide="tags" class="w-5 h-5 {{ request()->routeIs('prices.create') ? 'text-white' : 'text-slate-500' }}"></i>
                    Record Prices
                </a>
            </div>
        </div>
        @endif

        <!-- Admin Tools -->
        @if(auth()->user() && auth()->user()->isAdmin())
        <div>
            <h3 class="px-2 text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Admin Tools</h3>
            <div class="space-y-1">
                <a href="{{ route('reports.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('reports.*') ? 'bg-emerald-600 text-white font-medium shadow-sm' : 'text-slate-600 hover:bg-slate-50 font-medium' }}">
                    <i data-lucide="file-bar-chart-2" class="w-5 h-5 {{ request()->routeIs('reports.*') ? 'text-white' : 'text-slate-500' }}"></i>
                    Reports
                </a>
                <a href="{{ route('admin.ceiling-prices.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('admin.ceiling-prices.*') ? 'bg-emerald-600 text-white font-medium shadow-sm' : 'text-slate-600 hover:bg-slate-50 font-medium' }}">
                    <i data-lucide="badge-dollar-sign" class="w-5 h-5 {{ request()->routeIs('admin.ceiling-prices.*') ? 'text-white' : 'text-slate-500' }}"></i>
                    Set Ceiling Prices
                </a>
                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-emerald-600 text-white font-medium shadow-sm' : 'text-slate-600 hover:bg-slate-50 font-medium' }}">
                    <i data-lucide="user-round-cog" class="w-5 h-5 {{ request()->routeIs('admin.users.*') ? 'text-white' : 'text-slate-500' }}"></i>
                    Manage User Roles
                </a>
            </div>
        </div>
        @endif
    </div>

    <!-- Footer Profile -->
    <div class="p-4 border-t border-slate-100 bg-slate-50/50 mt-auto">
        @if(auth()->check())
            <div class="bg-white border border-slate-200 rounded-2xl p-3 mb-3 flex items-center gap-3 shadow-sm">
                @php
                    $initials = collect(explode(' ', auth()->user()->name))->map(fn($n) => substr($n, 0, 1))->take(2)->join('');
                @endphp
                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                    <span class="text-emerald-700 font-bold text-sm">{{ strtoupper($initials) }}</span>
                </div>
                <div class="flex flex-col min-w-0 overflow-hidden">
                    <span class="text-sm font-bold text-slate-800 truncate">{{ auth()->user()->name }}</span>
                    <span class="text-xs text-slate-400 truncate">{{ auth()->user()->email }}</span>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 py-2.5 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl transition-colors text-sm">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                    Exit {{ ucfirst(auth()->user()->role) }} Portal
                </button>
            </form>
        @else
            <a href="{{ route('login') }}" class="w-full flex items-center justify-center gap-2 py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl transition-colors text-sm">
                <i data-lucide="log-in" class="w-4 h-4"></i>
                Log in
            </a>
        @endif
    </div>
</aside>
