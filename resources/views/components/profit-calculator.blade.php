@props(['ceilingPrices'])

<div x-data="profitCalculator()" class="card bg-base-100 shadow-sm border border-base-300 w-full mb-6">
    <div class="card-body p-5 md:p-6">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                <i data-lucide="calculator" class="w-5 h-5"></i>
            </div>
            <div>
                <h3 class="font-bold text-lg leading-tight">{{ __('Profit Calculator') }}</h3>
                <p class="text-xs text-base-content/60">{{ __('Estimate earnings based on current ceiling prices.') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-control">
                <label class="label pt-0 pb-1">
                    <span class="label-text font-medium text-xs">{{ __('Select Crop') }}</span>
                </label>
                <select x-model="selectedCropId" @change="calculate()" class="select select-bordered select-sm w-full">
                    <option value="">{{ __('-- Choose a crop --') }}</option>
                    @foreach($ceilingPrices as $cp)
                        <option value="{{ $cp->crop_id }}" data-price="{{ $cp->max_price }}" data-unit="{{ $cp->crop->unit }}">
                            {{ $cp->crop->name }} (₱{{ number_format($cp->max_price, 2) }}/{{ $cp->crop->unit }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-control">
                <label class="label pt-0 pb-1">
                    <span class="label-text font-medium text-xs">{{ __('Estimated Volume') }} (<span x-text="unit">kg</span>)</span>
                </label>
                <input type="number" x-model.number="volume" @input="calculate()" class="input input-bordered input-sm w-full" placeholder="e.g. 50" min="0">
            </div>
        </div>

        <div class="mt-5 bg-emerald-50 rounded-xl p-4 border border-emerald-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold text-emerald-700 uppercase tracking-wider mb-1">{{ __('Estimated Revenue') }}</p>
                <div class="flex items-baseline gap-1">
                    <span class="text-2xl font-bold text-emerald-900">₱</span>
                    <span class="text-3xl font-black text-emerald-700 tracking-tight" x-text="formattedTotal">0.00</span>
                </div>
            </div>
            <div class="text-right flex flex-col gap-1 items-end shrink-0">
                <span class="text-[10px] text-emerald-600/80 font-medium">{{ __('Based on DA Ceiling Price') }}</span>
                <button type="button" @click="reset()" x-show="total > 0" class="btn btn-xs btn-ghost text-emerald-600 hover:bg-emerald-200">{{ __('Reset') }}</button>
            </div>
        </div>
    </div>
</div>

<script>
    function profitCalculator() {
        return {
            selectedCropId: '',
            volume: null,
            total: 0,
            unit: 'kg',

            calculate() {
                if (!this.selectedCropId || !this.volume) {
                    this.total = 0;
                    if (!this.selectedCropId) this.unit = 'kg';
                    return;
                }
                
                const select = document.querySelector(`select[x-model="selectedCropId"]`);
                const option = select.options[select.selectedIndex];
                const price = parseFloat(option.dataset.price);
                this.unit = option.dataset.unit;
                
                this.total = price * this.volume;
            },

            get formattedTotal() {
                return this.total.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            },

            reset() {
                this.selectedCropId = '';
                this.volume = null;
                this.total = 0;
                this.unit = 'kg';
            }
        }
    }
</script>
