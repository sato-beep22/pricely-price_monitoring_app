<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-base-content leading-tight">
            {{ __('Price Trend Reports') }}
        </h2>
    </x-slot>

    <div class="card bg-base-100 shadow-sm border border-base-300 animate-fade-in-up mb-6">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.index') }}" class="flex flex-wrap gap-4 items-end">
                <div class="form-control">
                    <label class="label"><span class="label-text">Select Crop</span></label>
                    <select name="crop_id" class="select select-bordered min-w-[200px]">
                        @foreach($crops as $crop)
                            <option value="{{ $crop->id }}" {{ $selectedCrop == $crop->id ? 'selected' : '' }}>
                                {{ $crop->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-control">
                    <label class="label"><span class="label-text">Shop</span></label>
                    <select name="shop_id" class="select select-bordered min-w-[200px]">
                        <option value="">All Shops</option>
                        @foreach($shops as $shop)
                            <option value="{{ $shop->id }}" {{ $selectedShop == $shop->id ? 'selected' : '' }}>
                                {{ $shop->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Variety</span></label>
                    <select name="variety" class="select select-bordered min-w-[200px]">
                        <option value="">All Varieties</option>
                        @foreach($varieties as $variety)
                            <option value="{{ $variety }}" {{ $selectedVariety == $variety ? 'selected' : '' }}>
                                {{ ucfirst($variety) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Time Period</span></label>
                    <select name="period" class="select select-bordered min-w-[200px]">
                        <option value="7" {{ $period == '7' ? 'selected' : '' }}>Last 7 Days</option>
                        <option value="30" {{ $period == '30' ? 'selected' : '' }}>Last 30 Days</option>
                        <option value="90" {{ $period == '90' ? 'selected' : '' }}>Last 90 Days</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary">Generate Report</button>
                <a href="{{ route('admin.reports.export', ['crop_id' => $selectedCrop, 'shop_id' => $selectedShop, 'variety' => $selectedVariety, 'period' => $period]) }}" class="btn btn-outline btn-success">
                    <i data-lucide="download" class="w-4 h-4 mr-2"></i> Export as CSV
                </a>
            </form>
        </div>
    </div>

    <div class="card bg-base-100 shadow-sm border border-base-300 animate-fade-in-up">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead class="bg-base-200">
                        <tr>
                            <th>Date</th>
                            <th>Shop / Buyer</th>
                            <th>Location</th>
                            <th>Variety</th>
                            <th>Price per kg</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($prices as $price)
                            <tr>
                                <td class="whitespace-nowrap">{{ $price->recorded_at->format('M d, Y h:i A') }}</td>
                                <td class="font-medium">{{ $price->shop->name ?? 'Unknown' }}</td>
                                <td>{{ Str::limit($price->shop->address ?? '-', 40) }}</td>
                                <td>{{ ucfirst($price->specification) }}</td>
                                <td class="font-bold text-primary">₱{{ number_format($price->price_per_kg, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-8 text-base-content/50">No price records found for this period.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
