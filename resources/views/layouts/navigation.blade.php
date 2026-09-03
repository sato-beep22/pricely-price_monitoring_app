<div class="navbar bg-base-100 border-b border-base-300 shadow-sm sticky top-0 z-50">
    <div class="navbar-start">
        <div class="dropdown">
            <label tabindex="0" class="btn btn-ghost lg:hidden">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16" /></svg>
            </label>
            <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
                @auth
                <li><a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a></li>
                @endauth
                <li><a href="{{ route('map.index') }}" class="{{ request()->routeIs('map.index') ? 'active' : '' }}">Price Map</a></li>
                @auth
                <li><a href="{{ route('forecast.index') }}" class="{{ request()->routeIs('forecast.index') ? 'active' : '' }}">Forecast</a></li>
                
                @if(Auth::user()->isFarmer())
                    <li><a href="{{ route('subscriptions.index') }}" class="{{ request()->routeIs('subscriptions.index') ? 'active' : '' }}">Alert Subscriptions</a></li>
                @elseif(Auth::user()->isBuyer())
                    <li><a href="{{ route('shops.edit') }}" class="{{ request()->routeIs('shops.edit') ? 'active' : '' }}">My Shop</a></li>
                    <li><a href="{{ route('prices.create') }}" class="{{ request()->routeIs('prices.create') ? 'active' : '' }}">Update Prices</a></li>
                @elseif(Auth::user()->isAdmin())
                    <li><a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.index') ? 'active' : '' }}">Users</a></li>
                    <li><a href="{{ route('admin.ceiling-prices.index') }}" class="{{ request()->routeIs('admin.ceiling-prices.index') ? 'active' : '' }}">Ceiling Prices</a></li>
                    <li><a href="{{ route('admin.price-import.index') }}" class="{{ request()->routeIs('admin.price-import.index') ? 'active' : '' }}">Manage DA Links</a></li>
                    <li><a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">Reports</a></li>
                    <li><a href="{{ route('admin.sms-logs.index') }}" class="{{ request()->routeIs('admin.sms-logs.*') ? 'active' : '' }}">SMS Logs</a></li>
                @endif
                @endauth
            </ul>
        </div>
        <a href="/" class="btn btn-ghost normal-case flex items-center gap-2 h-auto py-2">
            <img src="{{ asset('san-mateo-logo.webp') }}" alt="San Mateo Logo" class="w-12 h-12 object-contain">
            <div class="flex flex-col items-start">
                <span class="text-xl text-primary font-bold leading-none">Pricely</span>
                <span class="text-[10px] font-medium text-slate-500 leading-tight">San Mateo Isabela</span>
            </div>
        </a>
    </div>
    
    <div class="navbar-center hidden lg:flex">
        <ul class="menu menu-horizontal px-1 gap-1">
            @auth
            <li><a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a></li>
            @endauth
            <li><a href="{{ route('map.index') }}" class="{{ request()->routeIs('map.index') ? 'active' : '' }}">Price Map</a></li>
            @auth
            <li><a href="{{ route('forecast.index') }}" class="{{ request()->routeIs('forecast.index') ? 'active' : '' }}">Forecast</a></li>
            
            @if(Auth::user()->isFarmer())
                <li><a href="{{ route('subscriptions.index') }}" class="{{ request()->routeIs('subscriptions.index') ? 'active' : '' }}">Alert Subscriptions</a></li>
            @elseif(Auth::user()->isBuyer())
                <li><a href="{{ route('shops.edit') }}" class="{{ request()->routeIs('shops.edit') ? 'active' : '' }}">My Shop</a></li>
                <li><a href="{{ route('prices.create') }}" class="{{ request()->routeIs('prices.create') ? 'active' : '' }}">Update Prices</a></li>
            @elseif(Auth::user()->isAdmin())
                <li><a href="{{ route('reports.index') }}" class="{{ request()->routeIs('reports.index') ? 'active' : '' }}">Reports</a></li>
                <li tabindex="0">
                    <details>
                        <summary>Admin</summary>
                        <ul class="p-2 z-[1] shadow bg-base-100 rounded-box w-48 border border-base-300">
                            <li><a href="{{ route('admin.users.index') }}">Users</a></li>
                            <li><a href="{{ route('admin.ceiling-prices.index') }}">Ceiling Prices</a></li>
                            <li><a href="{{ route('admin.price-import.index') }}">Manage DA Links</a></li>
                        </ul>
                    </details>
                </li>
            @endif
            @endauth
        </ul>
    </div>
    
    <div class="navbar-end">
        @auth
        <div class="dropdown dropdown-end">
            <label tabindex="0" class="btn btn-ghost btn-circle avatar placeholder">
                <div class="bg-primary text-primary-content rounded-full w-10">
                    <span class="text-sm font-semibold">{{ substr(Auth::user()->name, 0, 1) }}</span>
                </div>
            </label>
            <ul tabindex="0" class="mt-3 z-[1] p-2 shadow menu menu-sm dropdown-content bg-base-100 rounded-box w-52 border border-base-300">
                <li class="menu-title px-4 py-2 opacity-100">
                    <div class="font-bold text-base-content">{{ Auth::user()->name }}</div>
                    <div class="text-xs text-base-content/70 capitalize">{{ Auth::user()->role }}</div>
                </li>
                <div class="divider mt-0 mb-1"></div>
                <li><a href="{{ route('profile.edit') }}">Profile</a></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}" class="w-full h-full p-0">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 hover:text-error">Logout</button>
                    </form>
                </li>
            </ul>
        </div>
        @else
        <div class="flex gap-2">
            <a href="{{ route('login') }}" class="btn btn-ghost">Log in</a>
            <a href="{{ route('register') }}" class="btn btn-primary">Register</a>
        </div>
        @endauth
    </div>
</div>
