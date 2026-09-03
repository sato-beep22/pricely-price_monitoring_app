<header class="gsap-navbar w-full border-b border-slate-150/40 dark:border-slate-800 bg-white/60 dark:bg-slate-900/80 backdrop-blur-md fixed top-0 z-50 transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 md:px-8 h-20 flex items-center justify-between">
        
        <!-- Logo segment -->
        <a href="/" class="flex items-center gap-2 group shrink-0">
            <img src="{{ asset('san-mateo-logo.webp') }}" alt="San Mateo Logo" class="w-16 h-16 object-contain group-hover:scale-105 transition-all duration-300">
            <div class="flex flex-col">
                <span class="font-display font-bold text-base md:text-lg text-slate-900 dark:text-white tracking-tight leading-none">
                    Pricely
                </span>
                <span class="text-[10px] md:text-xs font-medium text-slate-500 dark:text-slate-400 leading-tight">San Mateo Isabela</span>
            </div>
        </a>

        <!-- Nav links -->
        <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-650 dark:text-slate-300">

            @auth
                <a href="{{ route('dashboard') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors cursor-pointer flex items-center gap-1">
                    Dashboard
                </a>
                @if(auth()->user()->isFarmer())
                    <a href="{{ route('subscriptions.index') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors cursor-pointer flex items-center gap-1">
                        Alerts
                    </a>
                @endif
            @endauth

        </nav>

        <!-- Right Action buttons -->
        <div class="flex items-center gap-1.5 md:gap-4 shrink-0 whitespace-nowrap">
            <!-- Theme Toggle -->
            <button onclick="toggleDarkMode()" class="p-2 mr-1 rounded-full text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors" aria-label="Toggle Dark Mode">
                <i data-lucide="moon" class="w-4 h-4 hidden dark:block"></i>
                <i data-lucide="sun" class="w-4 h-4 block dark:hidden"></i>
            </button>

            <!-- Language Toggle -->
            <div class="relative ml-2 mr-2" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center gap-1 text-xs md:text-sm font-semibold text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition-colors">
                    <i data-lucide="globe" class="w-4 h-4"></i>
                    {{ strtoupper(app()->getLocale()) }}
                    <i data-lucide="chevron-down" class="w-3 h-3"></i>
                </button>
                <div x-show="open" @click.away="open = false" style="display: none;" class="absolute right-0 mt-2 w-32 bg-white dark:bg-slate-800 rounded-xl shadow-lg py-1 border border-slate-100 dark:border-slate-700 z-50">
                    <a href="{{ route('language.switch', 'en') }}" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 {{ app()->getLocale() == 'en' ? 'font-bold text-emerald-600 dark:text-emerald-400' : '' }}">English</a>
                    <a href="{{ route('language.switch', 'tl') }}" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 {{ app()->getLocale() == 'tl' ? 'font-bold text-emerald-600 dark:text-emerald-400' : '' }}">Tagalog</a>
                </div>
            </div>

            @if (Route::has('login'))
                @auth
                    <a href="{{ route('dashboard') }}" class="md:hidden text-xs font-semibold text-emerald-600 bg-emerald-50 px-3 py-2 rounded-xl border border-emerald-100 mr-1 whitespace-nowrap">
                        Dashboard
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="m-0 p-0 flex items-center">
                        @csrf
                        <button type="submit" class="text-xs md:text-sm font-semibold text-slate-600 dark:text-slate-300 hover:text-slate-950 dark:hover:text-white transition-colors px-1.5 md:px-3 py-2 rounded-xl cursor-pointer whitespace-nowrap">
                            Log out
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="bg-slate-900 dark:bg-white text-white dark:text-slate-900 px-3 md:px-6 py-2 md:py-2.5 rounded-full text-[11px] md:text-sm font-semibold hover:bg-slate-950 dark:hover:bg-slate-100 transition-all cursor-pointer whitespace-nowrap">
                        Login / Signup
                    </a>
                @endauth
            @endif
        </div>

    </div>
</header>

<script>
function toggleDarkMode() {
    const html = document.documentElement;
    const isDark = html.classList.contains('dark');
    if (isDark) {
        html.classList.remove('dark');
        html.setAttribute('data-theme', 'emerald');
        localStorage.setItem('theme', 'light');
    } else {
        html.classList.add('dark');
        html.setAttribute('data-theme', 'forest');
        localStorage.setItem('theme', 'dark');
    }
}
</script>
