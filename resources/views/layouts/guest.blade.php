<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="emerald">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Pricely') }}</title>

        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}?v=3">
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://unpkg.com/lucide@latest"></script>
    </head>
    <body class="font-sans antialiased bg-[#F8FAFC] min-h-screen text-base-content relative overflow-x-hidden">
        <!-- Global Notification Modal -->
        <x-notification-modal />
        <!-- Background Wrapper to prevent overflow -->
        <div class="fixed inset-0 z-0 overflow-hidden pointer-events-none">
            <!-- Ambient Background Layer -->
            <div class="bg-ambient absolute inset-0 opacity-40"></div>

            <!-- Decorative Gradient Orbs -->
            <div class="absolute w-[500px] h-[500px] rounded-full -top-28 -left-28 opacity-60 animate-blob blur-3xl" style="background: radial-gradient(circle, rgba(16,185,129,0.35) 0%, rgba(13,148,136,0.15) 50%, transparent 70%);"></div>
            <div class="absolute w-80 h-80 rounded-full top-[10%] right-[5%] opacity-40 animate-blob blur-2xl" style="background: radial-gradient(circle, rgba(56,189,248,0.25) 0%, rgba(99,102,241,0.10) 50%, transparent 70%); animation-delay: 2s;"></div>
            <div class="absolute w-72 h-72 rounded-full bottom-[10%] -left-16 opacity-35 animate-blob blur-2xl" style="background: radial-gradient(circle, rgba(52,211,153,0.30) 0%, rgba(16,185,129,0.10) 50%, transparent 70%); animation-delay: 5s;"></div>
            <div class="absolute w-[450px] h-[450px] rounded-full -bottom-24 -right-24 opacity-50 animate-blob blur-3xl" style="background: radial-gradient(circle, rgba(13,148,136,0.30) 0%, rgba(16,185,129,0.12) 50%, transparent 70%); animation-delay: 3s;"></div>
            <div class="absolute w-56 h-56 rounded-full top-[40%] right-[25%] opacity-30 animate-blob blur-xl" style="background: radial-gradient(circle, rgba(99,102,241,0.18) 0%, rgba(139,92,246,0.06) 50%, transparent 70%); animation-delay: 7s;"></div>
        </div>

        <!-- Content -->
        <div class="relative z-10 min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 px-4">
            <!-- Logo -->
            <div class="mb-2">
                <a href="/" class="flex items-center gap-3 group">
                    <img src="{{ asset('san-mateo-logo.png') }}" alt="San Mateo Logo" class="w-16 h-16 object-contain group-hover:scale-105 transition-all duration-300">
                    <div class="flex flex-col">
                        <span class="font-display font-bold text-2xl text-gradient leading-none">
                            Pricely
                        </span>
                        <span class="text-xs font-medium text-slate-500 leading-tight">San Mateo Isabela</span>
                    </div>
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
