<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address or Username -->
        <div>
            <x-input-label for="login" :value="__('Email or Username')" />
            <x-text-input id="login" class="block mt-1 w-full" type="text" name="login" :value="old('login')" required autofocus autocomplete="username" placeholder="you@example.com or your_username" />
            <x-input-error :messages="$errors->get('login')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-5">
            <x-input-label for="password" :value="__('Password')" />

            <div class="relative mt-1">
                <x-text-input id="password" class="block w-full pr-12"
                                type="password"
                                name="password"
                                required autocomplete="current-password"
                                placeholder="••••••••" />
                <button type="button" onclick="togglePassword('password', this)" class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 hover:text-slate-600 transition-colors cursor-pointer">
                    <i data-lucide="eye" class="w-5 h-5 eye-open"></i>
                    <i data-lucide="eye-off" class="w-5 h-5 eye-closed hidden"></i>
                </button>
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-5">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="w-4 h-4 rounded border-slate-300 text-emerald-600 shadow-sm focus:ring-emerald-500 focus:ring-offset-0 transition-colors cursor-pointer" name="remember">
                <span class="ms-2 text-sm text-slate-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-between mt-6">
            @if (Route::has('password.request'))
                <a class="text-sm text-emerald-600 hover:text-emerald-700 font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors" href="{{ route('password.request') }}">
                    {{ __('Forgot password?') }}
                </a>
            @endif

            <x-primary-button>
                {{ __('Log in') }}
            </x-primary-button>
        </div>

        <!-- Register link -->
        @if (Route::has('register'))
            <div class="mt-6 pt-6 border-t border-slate-100 text-center">
                <p class="text-sm text-slate-500">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="text-emerald-600 hover:text-emerald-700 font-semibold transition-colors">
                        Sign up free
                    </a>
                </p>
            </div>
        @endif
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loginInput = document.getElementById('login');
            const passwordInput = document.getElementById('password');
            const submitBtn = document.querySelector('button[type="submit"]');

            function checkForm() {
                if (loginInput.value.trim() !== '' && passwordInput.value.trim() !== '') {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                } else {
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                }
            }

            loginInput.addEventListener('input', checkForm);
            passwordInput.addEventListener('input', checkForm);

            // Run check on page load to handle auto-filled credentials or old input
            checkForm();
        });
    </script>
</x-guest-layout>
