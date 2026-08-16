<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-base-content leading-tight">
            {{ __('Ceiling Prices (DA Guidelines)') }}
        </h2>
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 animate-fade-in-up">
        
        <!-- Left column: Manual form + AI Sync panel -->
        <div class="md:col-span-1 flex flex-col gap-6">

            <!-- Manual Add Form (unchanged) -->
            <div class="card bg-base-100 shadow-sm border border-base-300 sticky top-24">
                <div class="card-body">
                    <h2 class="card-title text-lg mb-4">Set New Ceiling Price</h2>
                    
                    <form method="POST" action="{{ route('admin.ceiling-prices.store') }}" class="space-y-4">
                        @csrf
                        
                        <div x-data="cropSpecForm({{ $crops->map->only(['id', 'name', 'specification'])->toJson() }})">
                            <div class="form-control w-full">
                                <label class="label"><span class="label-text">Crop</span></label>
                                <select name="crop_id" x-model="selectedCropId" @change="updateSpecs" class="select select-bordered w-full" required>
                                    <option value="" disabled selected>Choose...</option>
                                    <template x-for="crop in crops" :key="crop.id">
                                        <option :value="crop.id" x-text="crop.name"></option>
                                    </template>
                                </select>
                                @error('crop_id') <span class="text-error text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-control w-full mt-4" x-show="selectedCropId">
                                <label class="label"><span class="label-text">Specification</span></label>
                                <select name="specification" x-model="selectedSpec" class="select select-bordered w-full" :required="selectedCropId !== ''">
                                    <option value="" disabled selected>Choose specification...</option>
                                    <template x-for="spec in currentSpecs" :key="spec">
                                        <option :value="spec" x-text="spec.charAt(0).toUpperCase() + spec.slice(1)"></option>
                                    </template>
                                    <option value="manual">-- Add Manually --</option>
                                </select>
                                @error('specification') <span class="text-error text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-control w-full mt-4" x-show="selectedSpec === 'manual'">
                                <label class="label"><span class="label-text">Enter Manual Specification</span></label>
                                <input type="text" name="manual_specification" class="input input-bordered w-full" :required="selectedSpec === 'manual'" placeholder="e.g. Premium" />
                                @error('manual_specification') <span class="text-error text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <script>
                            document.addEventListener('alpine:init', () => {
                                Alpine.data('cropSpecForm', (initialCrops) => ({
                                    crops: initialCrops,
                                    selectedCropId: '',
                                    selectedSpec: '',
                                    currentSpecs: [],

                                    updateSpecs() {
                                        this.selectedSpec = '';
                                        const crop = this.crops.find(c => c.id == this.selectedCropId);
                                        if (crop && crop.specification) {
                                            this.currentSpecs = crop.specification.split(',').map(s => s.trim()).filter(s => s.length > 0);
                                        } else {
                                            this.currentSpecs = [];
                                        }
                                    }
                                }));
                            });
                        </script>

                        <div class="form-control w-full">
                            <label class="label"><span class="label-text">Max Price (₱)</span></label>
                            <input type="number" step="0.01" name="max_price" class="input input-bordered w-full" required />
                            @error('max_price') <span class="text-error text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-control w-full">
                            <label class="label"><span class="label-text">Effective Date</span></label>
                            <input type="date" name="effective_date" value="{{ date('Y-m-d') }}" class="input input-bordered w-full" required />
                            @error('effective_date') <span class="text-error text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-control w-full">
                            <label class="label"><span class="label-text">Notes / Memo Ref</span></label>
                            <textarea name="notes" class="textarea textarea-bordered h-20"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-full mt-2">Save Guideline</button>
                    </form>
                </div>
            </div>

            <!-- AI Price Sync Panel -->
            <div x-data="daPriceSync()" class="card bg-base-100 shadow-sm border border-primary/30">
                <div class="card-body">
                    <!-- Header -->
                    <div class="flex items-center gap-2 mb-1">
                        <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-lg">🤖</div>
                        <div>
                            <h2 class="card-title text-base leading-tight">AI Price Sync</h2>
                            <p class="text-xs text-base-content/50">Powered by Gemini AI</p>
                        </div>
                        <div x-show="loading" class="ml-auto">
                            <span class="loading loading-spinner loading-sm text-primary"></span>
                        </div>
                    </div>

                    <p class="text-xs text-base-content/60 mb-3">
                        Paste a DA price monitoring page URL. AI will read and extract all ceiling prices for your review.
                    </p>

                    <!-- URL Input -->
                    <div class="form-control w-full">
                        <label class="label"><span class="label-text text-xs font-semibold">DA Price Page URL</span></label>
                        <input
                            type="url"
                            x-model="url"
                            id="da-sync-url"
                            placeholder="https://www.da.gov.ph/price-monitoring/..."
                            class="input input-bordered input-sm w-full text-xs"
                            :disabled="loading"
                        />
                    </div>

                    <!-- Effective Date for AI sync -->
                    <div class="form-control w-full mt-2">
                        <label class="label"><span class="label-text text-xs font-semibold">Effective Date</span></label>
                        <input
                            type="date"
                            x-model="effectiveDate"
                            id="da-sync-date"
                            class="input input-bordered input-sm w-full"
                            :disabled="loading"
                        />
                    </div>

                    <!-- Sync Button -->
                    <button
                        id="da-sync-btn"
                        @click="syncPrices"
                        :disabled="loading || !url"
                        class="btn btn-primary btn-sm w-full mt-3 gap-2"
                    >
                        <span x-show="!loading">🔄 Sync Prices</span>
                        <span x-show="loading">Fetching &amp; Analyzing...</span>
                    </button>

                    <!-- Error Message -->
                    <div x-show="errorMessage" x-transition class="alert alert-error mt-3 py-2 text-xs">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-4 w-4" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span x-text="errorMessage"></span>
                    </div>

                    <!-- Preview Results -->
                    <div x-show="prices.length > 0" x-transition class="mt-4">

                        <!-- Summary badge -->
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-semibold text-base-content/70">Preview — AI Extracted Data</span>
                            <div class="flex gap-1">
                                <span class="badge badge-success badge-xs" x-text="matchedCount + ' matched'"></span>
                                <template x-if="unmatchedCount > 0">
                                    <span class="badge badge-warning badge-xs" x-text="unmatchedCount + ' unmatched'"></span>
                                </template>
                            </div>
                        </div>

                        <!-- Source link -->
                        <div class="mb-2">
                            <a :href="sourceUrl" target="_blank" class="text-xs text-primary underline underline-offset-2 break-all" x-text="sourceUrl"></a>
                        </div>

                        <!-- Price Preview Table -->
                        <div class="overflow-x-auto rounded-lg border border-base-200 max-h-64 overflow-y-auto">
                            <table class="table table-xs w-full">
                                <thead class="bg-base-200 sticky top-0">
                                    <tr>
                                        <th class="text-xs">Crop</th>
                                        <th class="text-xs">Spec</th>
                                        <th class="text-xs">Max Price</th>
                                        <th class="text-xs">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(row, i) in prices" :key="i">
                                        <tr :class="row.status === 'matched' ? 'hover' : 'opacity-50'">
                                            <td class="font-semibold text-xs" x-text="row.matched_crop_name || row.crop"></td>
                                            <td class="text-xs">
                                                <span class="badge badge-ghost badge-xs" x-text="row.specification"></span>
                                            </td>
                                            <td class="text-xs font-bold text-error" x-text="'₱' + parseFloat(row.max_price).toFixed(2)"></td>
                                            <td>
                                                <span
                                                    class="badge badge-xs"
                                                    :class="row.status === 'matched' ? 'badge-success' : 'badge-warning'"
                                                    x-text="row.status === 'matched' ? '✓ Ready' : '✗ No match'"
                                                ></span>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        <p x-show="unmatchedCount > 0" class="text-xs text-warning mt-1">
                            ⚠ Unmatched crops are not in your system and will be skipped.
                        </p>

                        <!-- Apply Button -->
                        <button
                            id="da-sync-apply-btn"
                            @click="applyPrices"
                            :disabled="applying || matchedCount === 0"
                            class="btn btn-success btn-sm w-full mt-3 gap-2"
                        >
                            <span x-show="!applying">✅ Replace <span x-text="matchedCount"></span> Ceiling Price(s)</span>
                            <span x-show="applying">Saving...</span>
                        </button>
                    </div>

                    <!-- Success toast -->
                    <div x-show="successMessage" x-transition class="alert alert-success mt-3 py-2 text-xs">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-4 w-4" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span x-text="successMessage"></span>
                    </div>
                </div>
            </div>

        </div>

        <!-- History Table (unchanged) -->
        <div class="md:col-span-2">
            <div class="card bg-base-100 shadow-sm border border-base-300">
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
                                        <td>
                                            {{ $cp->effective_date->format('M d, Y') }}
                                        </td>
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

    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('daPriceSync', () => ({
                url: '',
                effectiveDate: new Date().toISOString().split('T')[0],
                loading: false,
                applying: false,
                prices: [],
                sourceUrl: '',
                errorMessage: '',
                successMessage: '',

                get matchedCount() {
                    return this.prices.filter(p => p.status === 'matched').length;
                },
                get unmatchedCount() {
                    return this.prices.filter(p => p.status === 'unmatched').length;
                },

                async syncPrices() {
                    this.loading = true;
                    this.errorMessage = '';
                    this.successMessage = '';
                    this.prices = [];

                    try {
                        const res = await fetch('{{ route('admin.da-price-sync.preview') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ url: this.url }),
                        });

                        const data = await res.json();

                        if (!res.ok || !data.success) {
                            this.errorMessage = data.message || 'Failed to extract prices from the URL.';
                        } else {
                            this.prices = data.prices;
                            this.sourceUrl = data.source_url;
                        }
                    } catch (e) {
                        this.errorMessage = 'Network error. Please try again.';
                    } finally {
                        this.loading = false;
                    }
                },

                async applyPrices() {
                    const matched = this.prices.filter(p => p.status === 'matched');
                    if (matched.length === 0) { return; }

                    this.applying = true;
                    this.errorMessage = '';
                    this.successMessage = '';

                    try {
                        const res = await fetch('{{ route('admin.da-price-sync.apply') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                prices: matched,
                                source_url: this.sourceUrl,
                                effective_date: this.effectiveDate,
                            }),
                        });

                        const data = await res.json();

                        if (!res.ok || !data.success) {
                            this.errorMessage = data.message || 'Failed to save prices.';
                        } else {
                            this.successMessage = data.message;
                            this.prices = [];
                            this.url = '';
                            // Reload the page after 1.5s to refresh the table
                            setTimeout(() => window.location.reload(), 1500);
                        }
                    } catch (e) {
                        this.errorMessage = 'Network error. Please try again.';
                    } finally {
                        this.applying = false;
                    }
                },
            }));
        });
    </script>

</x-app-layout>
