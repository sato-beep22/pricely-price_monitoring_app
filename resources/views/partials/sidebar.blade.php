<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 w-72 flex-shrink-0 bg-white border-r border-slate-200 h-screen flex flex-col z-[9999] transition-transform duration-300 ease-in-out md:sticky md:top-0 md:translate-x-0">
    <button @click="sidebarOpen = false" class="md:hidden absolute top-4 right-4 p-2 text-slate-400 hover:text-slate-600">
        <i data-lucide="x" class="w-5 h-5"></i>
    </button>
    <!-- Header -->
    <div class="p-6">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
            <img src="{{ asset('san-mateo-logo.webp') }}" alt="San Mateo Logo" class="w-12 h-12 object-contain">
            <div class="flex flex-col">
                <span class="font-bold text-slate-800 text-lg leading-none tracking-tight">Pricely</span>
                <span class="text-[10px] md:text-xs font-medium text-slate-500 leading-tight mt-0.5">San Mateo Isabela</span>
                <span class="text-[10px] font-bold text-emerald-600 tracking-wider mt-1">{{ strtoupper(auth()->user()?->role ?? 'GUEST') }} SPACE</span>
            </div>
        </a>
    </div>

    <!-- Navigation -->
    <div class="flex-1 overflow-y-auto px-4 py-2 flex flex-col gap-6">
        <!-- Main Dashboards -->
        <div>
            <h3 class="px-2 text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">{{ __('Main Dashboard') }}</h3>
            <div class="space-y-1">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('dashboard') ? 'bg-emerald-600 text-white font-medium shadow-sm' : 'text-slate-600 hover:bg-slate-50 font-medium' }}">
                    <i data-lucide="layout-grid" class="w-5 h-5"></i>
                    {{ __('Dashboard') }}
                </a>
                <a href="{{ route('map.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('map.index') ? 'bg-emerald-600 text-white font-medium shadow-sm' : 'text-slate-600 hover:bg-slate-50 font-medium' }}">
                    <i data-lucide="compass" class="w-5 h-5"></i>
                    {{ __('Price Map') }}
                </a>
            </div>
        </div>

        <!-- Farmer Logbooks -->
        @if(auth()->user() && auth()->user()->isFarmer())
        <div>
            <h3 class="px-2 text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">{{ __('Farmer Logbooks') }}</h3>
            <div class="space-y-1">
                <a href="{{ route('subscriptions.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('subscriptions.index') ? 'bg-emerald-600 text-white font-medium shadow-sm' : 'text-slate-600 hover:bg-slate-50 font-medium' }}">
                    <i data-lucide="smartphone" class="w-5 h-5 text-slate-500"></i>
                    {{ __('SMS Alerts') }} 
                </a>
                <a href="{{ route('forecast.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('forecast.index') ? 'bg-emerald-600 text-white font-medium shadow-sm' : 'text-slate-600 hover:bg-slate-50 font-medium' }}">
                    <i data-lucide="trending-up" class="w-5 h-5 text-slate-500"></i>
                    {{ __('Price Forecasts') }} 
                </a>
            </div>
        </div>
        @endif
        <!-- Buyer Tools -->
        @if(auth()->user() && auth()->user()->isBuyer())
        <div>
            <h3 class="px-2 text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">{{ __('Buyer Tools') }}</h3>
            <div class="space-y-1">
                <a href="{{ route('shops.show') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('shops.*') ? 'bg-emerald-600 text-white font-medium shadow-sm' : 'text-slate-600 hover:bg-slate-50 font-medium' }}">
                    <i data-lucide="store" class="w-5 h-5 {{ request()->routeIs('shops.*') ? 'text-white' : 'text-slate-500' }}"></i>
                    {{ __('My Shop Profile') }}
                </a>
                <a href="{{ route('prices.create') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('prices.create') ? 'bg-emerald-600 text-white font-medium shadow-sm' : 'text-slate-600 hover:bg-slate-50 font-medium' }}">
                    <i data-lucide="tags" class="w-5 h-5 {{ request()->routeIs('prices.create') ? 'text-white' : 'text-slate-500' }}"></i>
                    {{ __('Record Prices') }}
                </a>
            </div>
        </div>
        @endif

        <!-- Admin Tools -->
        @if(auth()->user() && auth()->user()->isAdmin())
        <div>
            <h3 class="px-2 text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">{{ __('Admin Tools') }}</h3>
            <div class="space-y-1">
                <a href="{{ route('admin.reports.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('admin.reports.*') ? 'bg-emerald-600 text-white font-medium shadow-sm' : 'text-slate-600 hover:bg-slate-50 font-medium' }}">
                    <i data-lucide="file-bar-chart-2" class="w-5 h-5 {{ request()->routeIs('admin.reports.*') ? 'text-white' : 'text-slate-500' }}"></i>
                    {{ __('Reports') }}
                </a>
                <a href="{{ route('admin.ceiling-prices.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('admin.ceiling-prices.*') ? 'bg-emerald-600 text-white font-medium shadow-sm' : 'text-slate-600 hover:bg-slate-50 font-medium' }}">
                    <i data-lucide="badge-dollar-sign" class="w-5 h-5 {{ request()->routeIs('admin.ceiling-prices.*') ? 'text-white' : 'text-slate-500' }}"></i>
                    {{ __('Set Ceiling Prices') }}
                </a>
                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-emerald-600 text-white font-medium shadow-sm' : 'text-slate-600 hover:bg-slate-50 font-medium' }}">
                    <i data-lucide="user-round-cog" class="w-5 h-5 {{ request()->routeIs('admin.users.*') ? 'text-white' : 'text-slate-500' }}"></i>
                    {{ __('Manage User Roles') }}
                </a>
                <a href="{{ route('admin.price-import.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('admin.price-import.*') ? 'bg-emerald-600 text-white font-medium shadow-sm' : 'text-slate-600 hover:bg-slate-50 font-medium' }}">
                    <i data-lucide="upload-cloud" class="w-5 h-5 {{ request()->routeIs('admin.price-import.*') ? 'text-white' : 'text-slate-500' }}"></i>
                    {{ __('Manage DA Links') }}
                </a>
                <a href="{{ route('admin.sms-logs.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('admin.sms-logs.*') ? 'bg-emerald-600 text-white font-medium shadow-sm' : 'text-slate-600 hover:bg-slate-50 font-medium' }}">
                    <i data-lucide="message-square" class="w-5 h-5 {{ request()->routeIs('admin.sms-logs.*') ? 'text-white' : 'text-slate-500' }}"></i>
                    {{ __('SMS Logs') }}
                </a>
            </div>
        </div>
        @endif
    </div>

    <!-- Footer Profile -->
    <div class="p-4 border-t border-slate-100 bg-slate-50/50 mt-auto">
        @if(auth()->check())
            <a href="{{ route('profile.edit') }}" class="bg-white border border-slate-200 rounded-2xl p-3 mb-3 flex items-center gap-3 shadow-sm hover:border-emerald-300 hover:shadow-md transition-all group">
                @php
                    $initials = collect(explode(' ', auth()->user()->name))->map(fn($n) => substr($n, 0, 1))->take(2)->join('');
                @endphp
                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                    <span class="text-emerald-700 font-bold text-sm">{{ strtoupper($initials) }}</span>
                </div>
                <div class="flex flex-col min-w-0 overflow-hidden flex-1">
                    <span class="text-sm font-bold text-slate-800 truncate">{{ auth()->user()->name }}</span>
                    <span class="text-xs text-slate-400 truncate">{{ auth()->user()->email }}</span>
                </div>
                <i data-lucide="pencil" class="w-4 h-4 text-slate-300 group-hover:text-emerald-500 transition-colors flex-shrink-0"></i>
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 py-2.5 px-4 bg-red-50 hover:bg-red-100 text-red-600 font-semibold rounded-xl transition-colors text-sm mb-2">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                    {{ __('Logout') }}
                </button>
            </form>

            <div class="w-full flex justify-center mt-2" x-data="{ open: false }">
                <div class="relative w-full">
                    <button @click="open = !open" class="w-full flex items-center justify-center gap-2 py-2 px-4 text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors bg-white border border-slate-200 rounded-lg">
                        <i data-lucide="globe" class="w-3.5 h-3.5"></i>
                        Language: {{ strtoupper(app()->getLocale()) }}
                        <i data-lucide="chevron-down" class="w-3 h-3 ml-1"></i>
                    </button>
                    <div x-show="open" @click.away="open = false" style="display: none;" class="absolute bottom-full left-0 mb-2 w-full bg-white rounded-xl shadow-lg py-1 border border-slate-100 z-50 overflow-hidden">
                        <a href="{{ route('language.switch', 'en') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 text-center {{ app()->getLocale() == 'en' ? 'font-bold text-emerald-600' : '' }}">English</a>
                        <a href="{{ route('language.switch', 'tl') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 text-center {{ app()->getLocale() == 'tl' ? 'font-bold text-emerald-600' : '' }}">Tagalog</a>
                    </div>
                </div>
            </div>
        @else
            <a href="{{ route('login') }}" class="w-full flex items-center justify-center gap-2 py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl transition-colors text-sm">
                <i data-lucide="log-in" class="w-4 h-4"></i>
                Log in
            </a>
        @endif
    </div>
</aside>
