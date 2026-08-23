<header class="gsap-navbar w-full border-b border-slate-150/40 bg-white/60 dark:bg-transparent backdrop-blur-md fixed top-0 z-50 transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 md:px-8 h-20 flex items-center justify-between">
        
        <!-- Logo segment -->
        <a href="/" class="flex items-center gap-2 md:gap-3 group shrink-0">
            <div class="w-12 h-12 md:w-14 md:h-14 rounded-full border border-slate-200 bg-white overflow-hidden flex items-center justify-center shrink-0">
                <img src="{{ asset('san-mateo-logo.png') }}" alt="San Mateo Logo" class="w-full h-full object-cover scale-[1.15] group-hover:scale-[1.25] transition-all duration-300">
            </div>
            <div class="flex flex-col">
                <span class="font-display font-bold text-base md:text-xl text-slate-900 tracking-tight leading-none">
                    Pricely
                </span>
                <span class="text-[10px] md:text-xs font-medium text-slate-500 leading-tight">San Mateo Isabela</span>
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
            <!-- Language Toggle -->
            <div class="relative ml-2 mr-2" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center gap-1 text-xs md:text-sm font-semibold text-slate-600 hover:text-slate-900 transition-colors">
                    <i data-lucide="globe" class="w-4 h-4"></i>
                    {{ strtoupper(app()->getLocale()) }}
                    <i data-lucide="chevron-down" class="w-3 h-3"></i>
                </button>
                <div x-show="open" @click.away="open = false" style="display: none;" class="absolute right-0 mt-2 w-32 bg-white rounded-xl shadow-lg py-1 border border-slate-100 z-50">
                    <a href="{{ route('language.switch', 'en') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 {{ app()->getLocale() == 'en' ? 'font-bold text-emerald-600' : '' }}">English</a>
                    <a href="{{ route('language.switch', 'tl') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 {{ app()->getLocale() == 'tl' ? 'font-bold text-emerald-600' : '' }}">Tagalog</a>
                </div>
            </div>

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
