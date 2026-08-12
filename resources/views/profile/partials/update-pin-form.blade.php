<section>
    <header>
        <h2 class="text-lg font-medium text-slate-900">
            {{ __('Set Login PIN') }}
        </h2>

        <p class="mt-1 text-sm text-slate-600">
            {{ __('Set a 4-digit PIN code to log in quickly using your phone number.') }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.pin.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <x-input-label for="pin_code" :value="__('4-Digit PIN')" />
            <x-text-input id="pin_code" name="pin_code" type="password" class="mt-1 block w-full max-w-xl" maxlength="4" inputmode="numeric" pattern="[0-9]{4}" placeholder="••••" />
            <x-input-error :messages="$errors->updatePin->get('pin_code')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save PIN') }}</x-primary-button>

            @if (session('status') === 'pin-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-slate-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
