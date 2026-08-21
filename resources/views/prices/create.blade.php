<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-base-content leading-tight">
            {{ __('Update Buying Prices') }}
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto animate-fade-in-up">

        <div class="alert alert-info shadow-sm mb-8 items-start">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6 mt-0.5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div>
                <p>Select one or more crops below, enter their new prices, and submit. SMS alerts will be sent to all subscribed farmers.</p>
                <p class="font-semibold mt-1">Note: Your price should exceed the minimum price of DA.</p>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-error shadow-sm mb-8">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('prices.store') }}" x-data="multiPriceForm()" @submit.prevent="submitForm($el)">
            @csrf

            {{-- Search & Select All --}}
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-6">
                <div class="form-control w-full sm:w-72">
                    <label class="input input-bordered flex items-center gap-2">
                        <i data-lucide="search" class="w-4 h-4 opacity-50"></i>
                        <input type="text" x-model="search" placeholder="Search crops..." class="grow" />
                    </label>
                </div>
                <label class="cursor-pointer label gap-2">
                    <span class="label-text font-semibold">Select All</span>
                    <input type="checkbox" class="toggle toggle-primary toggle-sm" @change="toggleAll($event.target.checked)" :checked="allSelected" />
                </label>
            </div>

            {{-- Crop Cards Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                @foreach($crops as $crop)
                    @php $specs = array_filter(array_map('trim', explode(',', $crop->specification))); @endphp
                    @foreach($specs as $spec)
                        @php
                            $key = $crop->id . '_' . $spec;
                            $latest = $latestPrices[$crop->id][$spec] ?? null;
                            $ceiling = $latestCeilings[$key] ?? null;
                        @endphp
                        <div
                            x-show="matchesSearch('{{ addslashes($crop->name) }}', '{{ addslashes($spec) }}')"
                            x-transition
                            :class="selected.includes('{{ $key }}') ? 'ring-2 ring-primary border-primary bg-primary/5' : 'border-base-300 bg-base-100 hover:border-primary/30'"
                            class="card border shadow-sm transition-all duration-200 cursor-pointer"
                            @click="toggle('{{ $key }}')"
                        >
                            <div class="card-body p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-3 flex-1 min-w-0">
                                        <input
                                            type="checkbox"
                                            class="checkbox checkbox-primary checkbox-sm flex-shrink-0"
                                            :checked="selected.includes('{{ $key }}')"
                                            @click.stop="toggle('{{ $key }}')"
                                        />
                                        <div class="min-w-0">
                                            <h3 class="font-bold text-base-content leading-tight">{{ $crop->name }}</h3>
                                            <span class="badge badge-outline badge-sm mt-1">{{ ucfirst($spec) }}</span>
                                        </div>
                                    </div>
                                    @if($latest)
                                        <div class="text-right flex-shrink-0">
                                            <div class="text-xs text-base-content/50">Current</div>
                                            <div class="font-bold text-primary">₱{{ number_format($latest->price_per_kg, 2) }}</div>
                                        </div>
                                    @else
                                        <div class="text-right flex-shrink-0">
                                            <span class="badge badge-ghost badge-sm">No price yet</span>
                                        </div>
                                    @endif
                                    @if($ceiling)
                                        <div class="text-right flex-shrink-0 border-l border-base-200 pl-3 ml-3">
                                            <div class="text-xs text-warning font-semibold">DA Minimum Price</div>
                                            <div class="font-bold text-warning">₱{{ number_format($ceiling->max_price, 2) }}</div>
                                        </div>
                                    @endif
                                </div>

                                {{-- Price Input (shown when selected) --}}
                                <div x-show="selected.includes('{{ $key }}')" x-transition x-cloak class="mt-3 pt-3 border-t border-base-200">
                                    <label class="label py-1"><span class="label-text text-xs font-semibold">New Price (₱/kg)</span></label>
                                    <label class="input input-bordered input-sm flex items-center gap-2" @click.stop>
                                        ₱
                                        <input
                                            type="number"
                                            step="0.01"
                                            min="{{ $ceiling ? $ceiling->max_price : '0' }}"
                                            :name="'entries[{{ $key }}][price_per_kg]'"
                                            x-model="prices['{{ $key }}']"
                                            placeholder="0.00"
                                            class="grow"
                                            :class="prices['{{ $key }}'] && parseFloat(prices['{{ $key }}']) < {{ $ceiling ? $ceiling->max_price : '0' }} ? 'text-error' : ''"
                                            :disabled="!selected.includes('{{ $key }}')"
                                            @click.stop
                                            @focus.stop
                                        />
                                    </label>
                                    <input type="hidden" :name="'entries[{{ $key }}][crop_id]'" value="{{ $crop->id }}" :disabled="!selected.includes('{{ $key }}')" />
                                    <input type="hidden" :name="'entries[{{ $key }}][specification]'" value="{{ $spec }}" :disabled="!selected.includes('{{ $key }}')" />
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endforeach
            </div>

            {{-- Summary & Submit --}}
            <div class="sticky bottom-4 z-20" x-show="selected.length > 0" x-transition x-cloak>
                <div class="bg-base-100 border border-base-300 rounded-box shadow-lg p-4 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <div class="flex items-center gap-2 text-base-content">
                        <i data-lucide="shopping-cart" class="w-5 h-5 text-primary"></i>
                        <span class="font-semibold"><span x-text="selected.length"></span> crop(s) selected</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <button type="button" @click="clearAll()" class="btn btn-ghost btn-sm">Clear All</button>
                        <button type="submit" class="btn btn-primary" :disabled="!canSubmit()">
                            <i data-lucide="send" class="w-4 h-4"></i>
                            Record & Send Alerts
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @php
        $allKeys = collect($crops)->flatMap(function($crop) {
            return collect(array_filter(array_map('trim', explode(',', $crop->specification))))->map(fn($spec) => $crop->id . '_' . $spec);
        })->values();
        
        $ceilingsJson = collect($latestCeilings)->mapWithKeys(function($ceiling, $key) {
            return [$key => $ceiling->max_price];
        });
    @endphp
    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('multiPriceForm', () => ({
                search: '',
                selected: [],
                prices: {},
                allKeys: @json($allKeys),
                ceilings: @json($ceilingsJson),

                get allSelected() {
                    return this.allKeys.length > 0 && this.allKeys.every(k => this.selected.includes(k));
                },

                toggle(key) {
                    const idx = this.selected.indexOf(key);
                    if (idx > -1) {
                        this.selected.splice(idx, 1);
                        delete this.prices[key];
                    } else {
                        this.selected.push(key);
                        if (!this.prices[key]) {
                            this.prices[key] = '';
                        }
                    }
                },

                toggleAll(checked) {
                    if (checked) {
                        this.selected = [...this.allKeys];
                        this.allKeys.forEach(k => {
                            if (!this.prices[k]) this.prices[k] = '';
                        });
                    } else {
                        this.clearAll();
                    }
                },

                clearAll() {
                    this.selected = [];
                    this.prices = {};
                },

                matchesSearch(cropName, spec) {
                    if (!this.search) return true;
                    const q = this.search.toLowerCase();
                    return cropName.toLowerCase().includes(q) || spec.toLowerCase().includes(q);
                },

                canSubmit() {
                    return this.selected.length > 0 && this.selected.every(k => {
                        const val = parseFloat(this.prices[k]);
                        if (!val || val <= 0) return false;
                        if (this.ceilings[k] && val < this.ceilings[k]) return false;
                        return true;
                    });
                },

                submitForm(el) {
                    if (!this.canSubmit()) return;
                    // Remove disabled attributes before submit
                    el.querySelectorAll('input[disabled]').forEach(i => i.removeAttribute('disabled'));
                    // Only include selected entries
                    el.querySelectorAll('input[name^="entries["]').forEach(input => {
                        const match = input.name.match(/entries\[([^\]]+)\]/);
                        if (match && !this.selected.includes(match[1])) {
                            input.disabled = true;
                        }
                    });
                    el.submit();
                }
            }));
        });
    </script>
    @endpush
</x-app-layout>
