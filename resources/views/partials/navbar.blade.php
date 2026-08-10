<header class="gsap-navbar w-full border-b border-slate-150/40 bg-white/60 dark:bg-transparent backdrop-blur-md fixed top-0 z-50 transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 md:px-8 h-20 flex items-center justify-between">
        
        <!-- Logo segment -->
        <a href="/" class="flex items-center gap-2 group shrink-0">
            <span class="w-8 h-8 bg-[#04965e] rounded-lg flex items-center justify-center text-white shadow-md group-hover:scale-105 transition-all duration-300">
                <i data-lucide="sprout" class="w-5 h-5"></i>
            </span>
            <div class="flex flex-col">
                <span class="font-display font-bold text-base md:text-lg text-slate-900 tracking-tight leading-none">
                    Pricely
                </span>
            </div>
        </a>

        <!-- Nav links -->
        <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-650">

            @auth
                <a href="{{ route('dashboard') }}" class="hover:text-emerald-600 transition-colors cursor-pointer flex items-center gap-1">
                    Dashboard
                </a>
                @if(auth()->user()->isFarmer())
                    <a href="{{ route('subscriptions.index') }}" class="hover:text-emerald-600 transition-colors cursor-pointer flex items-center gap-1">
                        Alerts
                    </a>
                @endif
            @endauth

        </nav>

        <!-- Right Action buttons -->
        <div class="flex items-center gap-1.5 md:gap-4 shrink-0 whitespace-nowrap">
            @if (Route::has('login'))
                @auth
                    <a href="{{ route('dashboard') }}" class="md:hidden text-xs font-semibold text-emerald-600 bg-emerald-50 px-3 py-2 rounded-xl border border-emerald-100 mr-1 whitespace-nowrap">
                        Dashboard
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="m-0 p-0 flex items-center">
                        @csrf
                        <button type="submit" class="text-xs md:text-sm font-semibold text-slate-600 hover:text-slate-950 transition-colors px-1.5 md:px-3 py-2 rounded-xl cursor-pointer whitespace-nowrap">
                            Log out
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-xs md:text-sm font-semibold text-slate-600 hover:text-slate-950 transition-colors px-1.5 md:px-3 py-2 rounded-xl whitespace-nowrap">
                        Log in
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="bg-slate-900 text-white px-3 md:px-6 py-2 md:py-2.5 rounded-full text-[11px] md:text-sm font-semibold hover:bg-slate-950 transition-all cursor-pointer whitespace-nowrap">
                            Get Started
                        </a>
                    @endif
                @endauth
            @endif
        </div>

    </div>
</header>
