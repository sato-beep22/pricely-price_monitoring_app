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

        <!-- Buyer Classification (shown only when Buyer is selected) -->
        <div id="buyer-classification-section" class="mt-4 overflow-hidden transition-all duration-300 ease-in-out" style="max-height: 0; opacity: 0;">
            <input type="hidden" name="buyer_classification" id="buyer-classification-input" value="{{ old('buyer_classification', '') }}">
            <x-input-label :value="__('Buyer Classification')" class="mb-2" />
            <x-input-error :messages="$errors->get('buyer_classification')" class="mb-2" />
            <div class="grid grid-cols-2 gap-2">

                <button type="button" onclick="selectClassification('trader')"
                    data-classification="trader"
                    class="classification-option flex items-center gap-3 px-3 py-3 rounded-xl border-2 border-slate-200 transition-all duration-150 cursor-pointer text-left hover:border-violet-300 hover:bg-violet-50/50">
                    <span class="w-8 h-8 rounded-lg bg-violet-100 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="shopping-bag" class="w-4 h-4 text-violet-600"></i>
                    </span>
                    <div>
                        <p class="font-semibold text-xs text-slate-700 leading-tight">Trader / Dealer</p>
                        <p class="text-[10px] text-slate-400 leading-tight">Private commercial buyer</p>
                    </div>
                </button>

                <button type="button" onclick="selectClassification('miller')"
                    data-classification="miller"
                    class="classification-option flex items-center gap-3 px-3 py-3 rounded-xl border-2 border-slate-200 transition-all duration-150 cursor-pointer text-left hover:border-orange-300 hover:bg-orange-50/50">
                    <span class="w-8 h-8 rounded-lg bg-orange-100 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="cog" class="w-4 h-4 text-orange-600"></i>
                    </span>
                    <div>
                        <p class="font-semibold text-xs text-slate-700 leading-tight">Miller</p>
                        <p class="text-[10px] text-slate-400 leading-tight">Rice / corn mill operator</p>
                    </div>
                </button>

                <button type="button" onclick="selectClassification('wholesaler')"
                    data-classification="wholesaler"
                    class="classification-option flex items-center gap-3 px-3 py-3 rounded-xl border-2 border-slate-200 transition-all duration-150 cursor-pointer text-left hover:border-blue-300 hover:bg-blue-50/50">
                    <span class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="package" class="w-4 h-4 text-blue-600"></i>
                    </span>
                    <div>
                        <p class="font-semibold text-xs text-slate-700 leading-tight">Wholesaler</p>
                        <p class="text-[10px] text-slate-400 leading-tight">Bulk buyer / reseller</p>
                    </div>
                </button>

                <button type="button" onclick="selectClassification('retailer')"
                    data-classification="retailer"
                    class="classification-option flex items-center gap-3 px-3 py-3 rounded-xl border-2 border-slate-200 transition-all duration-150 cursor-pointer text-left hover:border-teal-300 hover:bg-teal-50/50">
                    <span class="w-8 h-8 rounded-lg bg-teal-100 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="shopping-cart" class="w-4 h-4 text-teal-600"></i>
                    </span>
                    <div>
                        <p class="font-semibold text-xs text-slate-700 leading-tight">Retailer</p>
                        <p class="text-[10px] text-slate-400 leading-tight">Direct-to-consumer seller</p>
                    </div>
                </button>

                <button type="button" onclick="selectClassification('government')"
                    data-classification="government"
                    class="classification-option flex items-center gap-3 px-3 py-3 rounded-xl border-2 border-slate-200 transition-all duration-150 cursor-pointer text-left hover:border-red-300 hover:bg-red-50/50">
                    <span class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="landmark" class="w-4 h-4 text-red-600"></i>
                    </span>
                    <div>
                        <p class="font-semibold text-xs text-slate-700 leading-tight">Gov't-Accredited</p>
                        <p class="text-[10px] text-slate-400 leading-tight">NFA / DA-accredited buyer</p>
                    </div>
                </button>

                <button type="button" onclick="selectClassification('cooperative')"
                    data-classification="cooperative"
                    class="classification-option flex items-center gap-3 px-3 py-3 rounded-xl border-2 border-slate-200 transition-all duration-150 cursor-pointer text-left hover:border-amber-300 hover:bg-amber-50/50">
                    <span class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="users" class="w-4 h-4 text-amber-600"></i>
                    </span>
                    <div>
                        <p class="font-semibold text-xs text-slate-700 leading-tight">Cooperative</p>
                        <p class="text-[10px] text-slate-400 leading-tight">Farmer coop / consolidator</p>
                    </div>
                </button>

                <button type="button" onclick="selectClassification('exporter')"
                    data-classification="exporter"
                    class="classification-option col-span-2 flex items-center gap-3 px-3 py-3 rounded-xl border-2 border-slate-200 transition-all duration-150 cursor-pointer text-left hover:border-cyan-300 hover:bg-cyan-50/50">
                    <span class="w-8 h-8 rounded-lg bg-cyan-100 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="ship" class="w-4 h-4 text-cyan-600"></i>
                    </span>
                    <div>
                        <p class="font-semibold text-xs text-slate-700 leading-tight">Exporter</p>
                        <p class="text-[10px] text-slate-400 leading-tight">International market buyer</p>
                    </div>
                </button>

            </div>
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

    <script>
        const classificationColors = {
            trader:      { border: '#7c3aed', bg: '#f5f3ff', icon: 'bg-violet-100' },
            miller:      { border: '#ea580c', bg: '#fff7ed', icon: 'bg-orange-100' },
            wholesaler:  { border: '#2563eb', bg: '#eff6ff', icon: 'bg-blue-100' },
            retailer:    { border: '#0d9488', bg: '#f0fdfa', icon: 'bg-teal-100' },
            government:  { border: '#dc2626', bg: '#fef2f2', icon: 'bg-red-100' },
            cooperative: { border: '#d97706', bg: '#fffbeb', icon: 'bg-amber-100' },
            exporter:    { border: '#0891b2', bg: '#ecfeff', icon: 'bg-cyan-100' },
        };

        function selectRole(role) {
            document.getElementById('role-input').value = role;

            document.querySelectorAll('.role-option').forEach(btn => {
                btn.classList.remove('border-emerald-500', 'bg-emerald-50', 'border-blue-500', 'bg-blue-50');
                btn.classList.add('border-slate-200');
            });

            const selectedBtn = document.getElementById('role-' + role + '-btn');
            if (role === 'farmer') {
                selectedBtn.classList.remove('border-slate-200');
                selectedBtn.classList.add('border-emerald-500', 'bg-emerald-50');
            } else {
                selectedBtn.classList.remove('border-slate-200');
                selectedBtn.classList.add('border-blue-500', 'bg-blue-50');
            }

            // Show/hide classification section
            const section = document.getElementById('buyer-classification-section');
            if (role === 'buyer') {
                section.style.maxHeight = '600px';
                section.style.opacity = '1';
                section.style.marginTop = '1rem';
            } else {
                section.style.maxHeight = '0';
                section.style.opacity = '0';
                section.style.marginTop = '0';
                // Clear classification when switching away from buyer
                document.getElementById('buyer-classification-input').value = '';
                document.querySelectorAll('.classification-option').forEach(btn => {
                    btn.style.borderColor = '';
                    btn.style.backgroundColor = '';
                });
            }
        }

        function selectClassification(type) {
            document.getElementById('buyer-classification-input').value = type;

            // Reset all
            document.querySelectorAll('.classification-option').forEach(btn => {
                btn.style.borderColor = '';
                btn.style.backgroundColor = '';
            });

            // Highlight selected
            const selected = document.querySelector(`[data-classification="${type}"]`);
            if (selected && classificationColors[type]) {
                selected.style.borderColor = classificationColors[type].border;
                selected.style.backgroundColor = classificationColors[type].bg;
            }
        }

        // Restore state on validation error
        document.addEventListener('DOMContentLoaded', function() {
            const oldRole = document.getElementById('role-input').value;
            if (oldRole) selectRole(oldRole);

            const oldClassification = document.getElementById('buyer-classification-input').value;
            if (oldClassification) selectClassification(oldClassification);
        });
    </script>
</x-guest-layout>
