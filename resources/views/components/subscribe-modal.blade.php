@props([
    'shop',
    'crops'
])

@php
// Get crop IDs available at this shop
$availableCropIds = $shop->prices()->distinct('crop_id')->pluck('crop_id')->toArray();
// Filter crops to only show available ones
$availableCrops = $crops->filter(function ($crop) use ($availableCropIds) {
    return in_array($crop->id, $availableCropIds);
});
@endphp

<x-modal name="subscribe-{{ $shop->id }}" focusable maxWidth="md">
    <form method="POST" action="{{ route('subscriptions.store') }}" class="p-6">
        @csrf

        <h2 class="text-lg font-semibold text-slate-900 mb-4">
            Subscribe to {{ $shop->name }}
        </h2>

        <input type="hidden" name="buyer_id" value="{{ $shop->user_id }}">

        <!-- Phone Number -->
        <div class="mb-4">
            <x-input-label for="phone_number_{{ $shop->id }}" :value="__('Phone Number')" />
            <div class="mt-1">
                <x-text-input
                    id="phone_number_{{ $shop->id }}"
                    class="block w-full bg-slate-50 text-slate-500 cursor-not-allowed"
                    type="text"
                    value="{{ auth()->user()->phone }}"
                    readonly
                />
                <p class="text-xs text-slate-500 mt-1">Your phone number must be verified before proceeding.</p>
                <p class="text-xs text-slate-500 mt-1">Note: You will receive SMS notifications when the shop updates their prices.</p>
            </div>
        </div>

        <!-- Crops Selection -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-slate-700 mb-3">
                {{ __('Select Crops to Monitor') }}
            </label>
            @if($availableCrops->isNotEmpty())
                <div class="space-y-2 max-h-64 overflow-y-auto">
                    @foreach($availableCrops as $crop)
                        <div class="flex items-center">
                            <input
                                type="checkbox"
                                id="crop_{{ $shop->id }}_{{ $crop->id }}"
                                name="crop_ids[]"
                                value="{{ $crop->id }}"
                                class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary"
                            >
                            <label
                                for="crop_{{ $shop->id }}_{{ $crop->id }}"
                                class="ml-3 text-sm text-slate-700 cursor-pointer"
                            >
                                {{ $crop->name }}
                            </label>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-slate-600 italic">No crops available at this shop yet.</p>
            @endif
            <x-input-error :messages="$errors->get('crop_ids')" class="mt-2" />
        </div>

        <!-- Actions -->
        <div class="flex gap-3 justify-end">
            <x-secondary-button x-on:click="show = false">
                {{ __('Cancel') }}
            </x-secondary-button>
            <x-primary-button>
                {{ __('Subscribe') }}
            </x-primary-button>
        </div>
    </form>
</x-modal>
