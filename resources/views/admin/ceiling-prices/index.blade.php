<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-base-content leading-tight">
            {{ __('Ceiling Prices (DA Guidelines)') }}
        </h2>
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 animate-fade-in-up">
        
        <!-- Add New Form -->
        <div class="md:col-span-1">
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
        </div>

        <!-- History Table -->
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
</x-app-layout>
