<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-base-content leading-tight">
            {{ __('Ceiling Prices (DA Guidelines)') }}
        </h2>
    </x-slot>

    <div class="animate-fade-in-up">

        {{-- Flash status --}}
        @if (session('status'))
            <div class="alert alert-success shadow-sm mb-6 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        {{-- Validation errors --}}
        @if ($errors->any())
            <div class="alert alert-error shadow-sm mb-6">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="alert alert-info shadow-sm mb-8 items-start">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6 mt-0.5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div>
                <p>Select one or more crop/spec combinations below, fill in the new ceiling prices, and submit.</p>
                <p class="font-semibold mt-1">A single SMS alert will be sent to all subscribed buyers with all updated prices.</p>
            </div>
        </div>

        @php
            $allKeys = collect($crops)->flatMap(function ($crop) {
                return collect(array_filter(array_map('trim', explode(',', $crop->specification))))
                    ->map(fn ($spec) => $crop->id . '_' . $spec);
            })->values();

            $ceilingsJson = collect($latestCeilings)->mapWithKeys(function ($ceiling, $key) {
                return [$key => ['max_price' => $ceiling->max_price, 'effective_date' => $ceiling->effective_date->format('Y-m-d')]];
            });
        @endphp

        <form
            method="POST"
            action="{{ route('admin.ceiling-prices.store') }}"
            x-data="multiCeilingForm()"
            @submit.prevent="submitForm($el)"
        >
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

            {{-- Crop/Spec Cards Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                @foreach($crops as $crop)
                    @php $specs = array_filter(array_map('trim', explode(',', $crop->specification))); @endphp
                    @foreach($specs as $spec)
                        @php $key = $crop->id . '_' . $spec; @endphp
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
                                    @if(isset($latestCeilings[$key]))
                                        <div class="text-right flex-shrink-0">
                                            <div class="text-xs text-error/70 font-semibold">Current Ceiling</div>
                                            <div class="font-bold text-error">₱{{ number_format($latestCeilings[$key]->max_price, 2) }}</div>
                                            <div class="text-xs text-base-content/50">{{ $latestCeilings[$key]->effective_date->format('M d, Y') }}</div>
                                        </div>
                                    @else
                                        <div class="text-right flex-shrink-0">
                                            <span class="badge badge-ghost badge-sm">No ceiling yet</span>
                                        </div>
                                    @endif
                                </div>

                                {{-- Inline fields (shown when card is selected) --}}
                                <div x-show="selected.includes('{{ $key }}')" x-transition x-cloak class="mt-3 pt-3 border-t border-base-200 space-y-3">

                                    {{-- Max Price --}}
                                    <div>
                                        <label class="label py-1"><span class="label-text text-xs font-semibold">New Max Price (₱/kg)</span></label>
                                        <label class="input input-bordered input-sm flex items-center gap-2" @click.stop>
                                            ₱
                                            <input
                                                type="number"
                                                step="0.01"
                                                min="0"
                                                :name="'entries[{{ $key }}][max_price]'"
                                                x-model="prices['{{ $key }}']"
                                                placeholder="0.00"
                                                class="grow"
                                                :disabled="!selected.includes('{{ $key }}')"
                                                @click.stop
                                                @focus.stop
                                            />
                                        </label>
                                    </div>

                                    {{-- Effective Date --}}
                                    <div>
                                        <label class="label py-1"><span class="label-text text-xs font-semibold">Effective Date</span></label>
                                        <input
                                            type="date"
                                            :name="'entries[{{ $key }}][effective_date]'"
                                            x-model="dates['{{ $key }}']"
                                            class="input input-bordered input-sm w-full"
                                            :disabled="!selected.includes('{{ $key }}')"
                                            @click.stop
                                        />
                                    </div>

                                    {{-- Notes --}}
                                    <div>
                                        <label class="label py-1"><span class="label-text text-xs font-semibold">Notes / Memo Ref <span class="opacity-50">(optional)</span></span></label>
                                        <textarea
                                            :name="'entries[{{ $key }}][notes]'"
                                            x-model="notes['{{ $key }}']"
                                            class="textarea textarea-bordered textarea-sm w-full h-16"
                                            :disabled="!selected.includes('{{ $key }}')"
                                            @click.stop
                                        ></textarea>
                                    </div>

                                    {{-- Hidden fields --}}
                                    <input type="hidden" :name="'entries[{{ $key }}][crop_id]'" value="{{ $crop->id }}" :disabled="!selected.includes('{{ $key }}')" />
                                    <input type="hidden" :name="'entries[{{ $key }}][specification]'" value="{{ $spec }}" :disabled="!selected.includes('{{ $key }}')" />
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endforeach
            </div>

            {{-- Sticky Bottom Action Bar --}}
            <div class="sticky bottom-4 z-20" x-show="selected.length > 0" x-transition x-cloak>
                <div class="bg-base-100 border border-base-300 rounded-box shadow-lg p-4 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <div class="flex items-center gap-2 text-base-content">
                        <i data-lucide="tag" class="w-5 h-5 text-primary"></i>
                        <span class="font-semibold"><span x-text="selected.length"></span> crop(s) selected</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <button type="button" @click="clearAll()" class="btn btn-ghost btn-sm">Clear All</button>
                        <button type="submit" class="btn btn-primary" :disabled="!canSubmit()">
                            <i data-lucide="save" class="w-4 h-4"></i>
                            Save Guidelines &amp; Notify Buyers
                        </button>
                    </div>
                </div>
            </div>
        </form>

        {{-- Current Ceiling Prices Table --}}
        <div class="card bg-base-100 shadow-sm border border-base-300 mt-10">
            <div class="card-body p-0">
                <div class="p-6 border-b border-base-200">
                    <h2 class="card-title text-lg">Current Ceiling Prices</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="table w-full">
                        <thead class="bg-base-200">
                            <tr>
                                <th>Crop</th>
                                <th>Specification</th>
                                <th>Max Price</th>
                                <th>Effective Date</th>
                                <th>Set By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ceilingPrices as $cp)
                                <tr class="hover">
                                    <td class="font-bold">{{ $cp->crop->name }}</td>
                                    <td><span class="badge badge-primary badge-outline">{{ ucfirst($cp->specification) }}</span></td>
                                    <td class="text-error font-bold">₱{{ number_format($cp->max_price, 2) }}</td>
                                    <td>{{ $cp->effective_date->format('M d, Y') }}</td>
                                    <td class="text-sm opacity-70">{{ $cp->admin->name }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-6">No ceiling prices recorded.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('multiCeilingForm', () => ({
                search: '',
                selected: [],
                prices: {},
                dates: {},
                notes: {},
                allKeys: @json($allKeys),
                today: '{{ date('Y-m-d') }}',

                get allSelected() {
                    return this.allKeys.length > 0 && this.allKeys.every(k => this.selected.includes(k));
                },

                toggle(key) {
                    const idx = this.selected.indexOf(key);
                    if (idx > -1) {
                        this.selected.splice(idx, 1);
                        delete this.prices[key];
                        delete this.dates[key];
                        delete this.notes[key];
                    } else {
                        this.selected.push(key);
                        if (!this.prices[key]) this.prices[key] = '';
                        if (!this.dates[key])  this.dates[key]  = this.today;
                        if (!this.notes[key])  this.notes[key]  = '';
                    }
                },

                toggleAll(checked) {
                    if (checked) {
                        this.selected = [...this.allKeys];
                        this.allKeys.forEach(k => {
                            if (!this.prices[k]) this.prices[k] = '';
                            if (!this.dates[k])  this.dates[k]  = this.today;
                            if (!this.notes[k])  this.notes[k]  = '';
                        });
                    } else {
                        this.clearAll();
                    }
                },

                clearAll() {
                    this.selected = [];
                    this.prices = {};
                    this.dates = {};
                    this.notes = {};
                },

                matchesSearch(cropName, spec) {
                    if (!this.search) return true;
                    const q = this.search.toLowerCase();
                    return cropName.toLowerCase().includes(q) || spec.toLowerCase().includes(q);
                },

                canSubmit() {
                    return this.selected.length > 0 && this.selected.every(k => {
                        const val = parseFloat(this.prices[k]);
                        return val > 0 && this.dates[k];
                    });
                },

                submitForm(el) {
                    if (!this.canSubmit()) return;
                    // Re-enable all inputs so they are included in the POST payload
                    el.querySelectorAll('input[disabled], textarea[disabled]').forEach(i => i.removeAttribute('disabled'));
                    // Disable inputs belonging to un-selected cards
                    el.querySelectorAll('input[name^="entries["], textarea[name^="entries["]').forEach(input => {
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
