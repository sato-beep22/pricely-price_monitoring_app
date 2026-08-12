<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="flex border-b border-slate-200 mb-6">
        <button type="button" id="tab-standard" class="w-1/2 py-2 text-center border-b-2 font-medium text-sm transition-colors focus:outline-none border-emerald-500 text-emerald-600">
            Standard Login
        </button>
        <button type="button" id="tab-pin" class="w-1/2 py-2 text-center border-b-2 font-medium text-sm transition-colors focus:outline-none border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300">
            PIN Login
        </button>
    </div>

    <form method="POST" action="{{ route('login') }}" id="login-form">
        @csrf

        <!-- Standard Login Fields -->
        <div id="standard-fields">
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
        </div>

        <!-- PIN Login Fields -->
        <div id="pin-fields" class="hidden">
            <!-- Phone Number -->
            <div>
                <x-input-label for="phone" :value="__('Phone Number')" />
                <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" placeholder="e.g. 09123456789" />
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            </div>

            <!-- PIN Code -->
            <div class="mt-5">
                <x-input-label for="pin_code" :value="__('4-Digit PIN')" />
                <x-text-input id="pin_code" class="block mt-1 w-full" type="password" name="pin_code" placeholder="••••" maxlength="4" inputmode="numeric" pattern="[0-9]{4}" />
                <x-input-error :messages="$errors->get('pin_code')" class="mt-2" />
            </div>
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

            <x-primary-button id="submit-btn" disabled class="opacity-50 cursor-not-allowed">
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
            const phoneInput = document.getElementById('phone');
            const pinCodeInput = document.getElementById('pin_code');
            const submitBtn = document.getElementById('submit-btn');

            const tabStandard = document.getElementById('tab-standard');
            const tabPin = document.getElementById('tab-pin');
            const standardFields = document.getElementById('standard-fields');
            const pinFields = document.getElementById('pin-fields');

            let currentMode = '{{ old('phone') ? 'pin' : 'standard' }}';

            function setMode(mode) {
                currentMode = mode;
                if (mode === 'standard') {
                    tabStandard.classList.add('border-emerald-500', 'text-emerald-600');
                    tabStandard.classList.remove('border-transparent', 'text-slate-500');
                    tabPin.classList.remove('border-emerald-500', 'text-emerald-600');
                    tabPin.classList.add('border-transparent', 'text-slate-500');

                    standardFields.classList.remove('hidden');
                    pinFields.classList.add('hidden');

                    // Enable standard fields, disable PIN fields so they are not submitted
                    loginInput.disabled = false;
                    passwordInput.disabled = false;
                    phoneInput.disabled = true;
                    pinCodeInput.disabled = true;
                } else {
                    tabPin.classList.add('border-emerald-500', 'text-emerald-600');
                    tabPin.classList.remove('border-transparent', 'text-slate-500');
                    tabStandard.classList.remove('border-emerald-500', 'text-emerald-600');
                    tabStandard.classList.add('border-transparent', 'text-slate-500');

                    pinFields.classList.remove('hidden');
                    standardFields.classList.add('hidden');

                    // Enable PIN fields, disable standard fields so they are not submitted
                    phoneInput.disabled = false;
                    pinCodeInput.disabled = false;
                    loginInput.disabled = true;
                    passwordInput.disabled = true;
                }
                checkForm();
            }

            tabStandard.addEventListener('click', () => setMode('standard'));
            tabPin.addEventListener('click', () => setMode('pin'));

            function checkForm() {
                let isValid = false;
                if (currentMode === 'standard') {
                    if (loginInput.value.trim() !== '' && passwordInput.value.trim() !== '') {
                        isValid = true;
                    }
                } else {
                    if (phoneInput.value.trim() !== '' && pinCodeInput.value.trim().length === 4) {
                        isValid = true;
                    }
                }

                if (isValid) {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                } else {
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                }
            }

            loginInput.addEventListener('input', checkForm);
            passwordInput.addEventListener('input', checkForm);
            phoneInput.addEventListener('input', checkForm);
            pinCodeInput.addEventListener('input', checkForm);

            // Initialize
            setMode(currentMode);
        });
    </script>
</x-guest-layout>
