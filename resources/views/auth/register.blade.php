<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Juan Dela Cruz" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-5">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="you@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Role Selection -->
        <div class="mt-5">
            <x-input-label :value="__('I am a...')" />
            <input type="hidden" name="role" id="role-input" value="{{ old('role', '') }}">
            <div class="grid grid-cols-2 gap-3 mt-1">
                <button type="button" id="role-farmer-btn" onclick="selectRole('farmer')"
                    class="role-option flex flex-col items-center gap-2 px-4 py-4 rounded-xl border-2 border-slate-200 transition-all duration-200 cursor-pointer hover:border-emerald-300 hover:bg-emerald-50/50"
                >
                    <span class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center">
                        <i data-lucide="sprout" class="w-5 h-5 text-emerald-600"></i>
                    </span>
                    <span class="font-semibold text-sm text-slate-700">Farmer</span>
                    <span class="text-[11px] text-slate-400 leading-tight text-center">Sell your harvest at fair prices</span>
                </button>
                <button type="button" id="role-buyer-btn" onclick="selectRole('buyer')"
                    class="role-option flex flex-col items-center gap-2 px-4 py-4 rounded-xl border-2 border-slate-200 transition-all duration-200 cursor-pointer hover:border-blue-300 hover:bg-blue-50/50"
                >
                    <span class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                        <i data-lucide="store" class="w-5 h-5 text-blue-600"></i>
                    </span>
                    <span class="font-semibold text-sm text-slate-700">Buyer</span>
                    <span class="text-[11px] text-slate-400 leading-tight text-center">Advertise your product</span>
                </button>
            </div>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-5">
            <x-input-label for="password" :value="__('Password')" />

            <div class="relative mt-1">
                <x-text-input id="password" class="block w-full pr-12"
                                type="password"
                                name="password"
                                required autocomplete="new-password"
                                placeholder="••••••••" />
                <button type="button" onclick="togglePassword('password', this)" class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 hover:text-slate-600 transition-colors cursor-pointer">
                    <i data-lucide="eye" class="w-5 h-5 eye-open"></i>
                    <i data-lucide="eye-off" class="w-5 h-5 eye-closed hidden"></i>
                </button>
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-5">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <div class="relative mt-1">
                <x-text-input id="password_confirmation" class="block w-full pr-12"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password"
                                placeholder="••••••••" />
                <button type="button" onclick="togglePassword('password_confirmation', this)" class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 hover:text-slate-600 transition-colors cursor-pointer">
                    <i data-lucide="eye" class="w-5 h-5 eye-open"></i>
                    <i data-lucide="eye-off" class="w-5 h-5 eye-closed hidden"></i>
                </button>
            </div>

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-6">
            <x-primary-button class="w-full justify-center">
                {{ __('Create Account') }}
            </x-primary-button>
        </div>

        <!-- Login link -->
        <div class="mt-6 pt-6 border-t border-slate-100 text-center">
            <p class="text-sm text-slate-500">
                Already have an account?
                <a href="{{ route('login') }}" class="text-emerald-600 hover:text-emerald-700 font-semibold transition-colors">
                    Log in
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>
