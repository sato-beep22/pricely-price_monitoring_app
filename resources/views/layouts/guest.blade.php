<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="emerald">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Pricely') }}</title>

        <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Crect width='24' height='24' rx='6' fill='%2304965e' /%3E%3Cpath d='M7 18h10' fill='none' stroke='%23ffffff' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' /%3E%3Cpath d='M10 18c5.5-1.25 6-9.5 6-9.5s-2 1-4 2c-2.5-3.5-6-3-6-3s1 4.5 3 5.5c0 0-2.5 1-1 5' fill='none' stroke='%23ffffff' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' /%3E%3C/svg%3E">
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://unpkg.com/lucide@latest"></script>
    </head>
    <body class="font-sans antialiased bg-[#F8FAFC] min-h-screen text-base-content relative overflow-x-hidden">
        <!-- Global Notification Modal -->
        <x-notification-modal />
        <!-- Ambient Background Layer -->
        <div class="bg-ambient absolute inset-0 z-0 opacity-40 pointer-events-none"></div>

        <!-- Decorative Gradient Orbs -->
        <div class="absolute w-[500px] h-[500px] rounded-full -top-28 -left-28 opacity-60 pointer-events-none animate-blob blur-3xl" style="background: radial-gradient(circle, rgba(16,185,129,0.35) 0%, rgba(13,148,136,0.15) 50%, transparent 70%);"></div>
        <div class="absolute w-80 h-80 rounded-full top-[10%] right-[5%] opacity-40 pointer-events-none animate-blob blur-2xl" style="background: radial-gradient(circle, rgba(56,189,248,0.25) 0%, rgba(99,102,241,0.10) 50%, transparent 70%); animation-delay: 2s;"></div>
        <div class="absolute w-72 h-72 rounded-full bottom-[10%] -left-16 opacity-35 pointer-events-none animate-blob blur-2xl" style="background: radial-gradient(circle, rgba(52,211,153,0.30) 0%, rgba(16,185,129,0.10) 50%, transparent 70%); animation-delay: 5s;"></div>
        <div class="absolute w-[450px] h-[450px] rounded-full -bottom-24 -right-24 opacity-50 pointer-events-none animate-blob blur-3xl" style="background: radial-gradient(circle, rgba(13,148,136,0.30) 0%, rgba(16,185,129,0.12) 50%, transparent 70%); animation-delay: 3s;"></div>
        <div class="absolute w-56 h-56 rounded-full top-[40%] right-[25%] opacity-30 pointer-events-none animate-blob blur-xl" style="background: radial-gradient(circle, rgba(99,102,241,0.18) 0%, rgba(139,92,246,0.06) 50%, transparent 70%); animation-delay: 7s;"></div>

        <!-- Content -->
        <div class="relative z-10 min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 px-4">
            <!-- Logo -->
            <div class="mb-2">
                <a href="/" class="flex items-center gap-3 group">
                    <span class="w-10 h-10 bg-[#04965e] rounded-xl flex items-center justify-center text-white shadow-lg shadow-emerald-200 group-hover:scale-105 transition-all duration-300">
                        <i data-lucide="sprout" class="w-6 h-6"></i>
                    </span>
                    <span class="font-display font-bold text-2xl text-gradient">
                        Pricely
                    </span>
                </a>
            </div>

            <!-- Auth Card -->
            <div class="w-full sm:max-w-md mt-6 px-8 py-8 bg-white/70 shadow-xl shadow-slate-200/50 overflow-hidden rounded-2xl border border-slate-200/60 backdrop-blur-xl">
                {{ $slot }}
            </div>

            <!-- Footer link -->
            <p class="mt-8 text-sm text-slate-400">
                &copy; {{ date('Y') }} Pricely. All rights reserved.
            </p>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                lucide.createIcons();
            });

            function togglePassword(inputId, btn) {
                const input = document.getElementById(inputId);
                const eyeOpen = btn.querySelector('.eye-open');
                const eyeClosed = btn.querySelector('.eye-closed');

                if (input.type === 'password') {
                    input.type = 'text';
                    eyeOpen.classList.add('hidden');
                    eyeClosed.classList.remove('hidden');
                } else {
                    input.type = 'password';
                    eyeOpen.classList.remove('hidden');
                    eyeClosed.classList.add('hidden');
                }
            }
        </script>
    </body>
</html>
