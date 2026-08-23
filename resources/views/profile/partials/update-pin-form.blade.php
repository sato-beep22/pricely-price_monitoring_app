<form method="post" action="{{ route('profile.pin.update') }}" class="space-y-4">
    @csrf
    @method('put')

    <div class="form-control w-full">
        <label class="label pb-1"><span class="label-text font-semibold text-slate-700">4-Digit PIN</span></label>
        <input
            id="pin_code"
            name="pin_code"
            type="password"
            class="input input-bordered w-full @error('pin_code', 'updatePin') input-error @enderror"
            maxlength="4"
            inputmode="numeric"
            pattern="[0-9]{4}"
            placeholder="••••"
            autocomplete="off"
        />
        @error('pin_code', 'updatePin')
            <span class="text-error text-xs mt-1">{{ $message }}</span>
        @enderror
    </div>

    <div class="flex items-center justify-between pt-2">
        <button type="submit" class="btn btn-primary btn-sm px-6">
            Save PIN
        </button>

        @if (session('status') === 'pin-updated')
            <p
                x-data="{ show: true }"
                x-show="show"
                x-transition
                x-init="setTimeout(() => show = false, 3000)"
                class="text-sm text-emerald-600 font-semibold flex items-center gap-1"
            >
                <i data-lucide="check-circle-2" class="w-4 h-4"></i> Saved!
            </p>
        @endif
    </div>
</form>
