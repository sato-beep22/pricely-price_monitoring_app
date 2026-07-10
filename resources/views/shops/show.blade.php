<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-base-content leading-tight">
                {{ __('My Shop Details') }}
            </h2>
            <a href="{{ route('shops.edit') }}" class="btn btn-primary btn-sm">
                <i data-lucide="edit" class="w-4 h-4 mr-1"></i> Customize Shop
            </a>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto space-y-6">
        <!-- Shop Header Card -->
        <div class="card bg-base-100 shadow-sm border border-base-300 animate-fade-in-up">
            <div class="card-body">
                <div class="flex items-start justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-base-content flex items-center gap-2">
                            <i data-lucide="store" class="w-8 h-8 text-primary"></i>
                            {{ $shop->name }}
                        </h1>
                        <p class="text-base-content/70 mt-2 flex items-center gap-1">
                            <i data-lucide="map-pin" class="w-4 h-4"></i> {{ $shop->address }}
                        </p>
                    </div>
                    <div class="badge {{ $shop->is_active ? 'badge-success' : 'badge-error' }} badge-lg font-bold">
                        {{ $shop->is_active ? 'Active' : 'Inactive' }}
                    </div>
                </div>

                @if($shop->description)
                <div class="mt-6">
                    <h3 class="font-semibold text-lg border-b border-base-200 pb-2 mb-3">About the Shop</h3>
                    <p class="text-base-content/80 whitespace-pre-line">{{ $shop->description }}</p>
                </div>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 animate-fade-in-up" style="animation-delay: 100ms;">
            <!-- Map Card -->
            <div class="card bg-base-100 shadow-sm border border-base-300">
                <div class="card-body p-0">
                    <div class="p-6 border-b border-base-200">
                        <h3 class="card-title text-lg flex items-center gap-2">
                            <i data-lucide="map" class="w-5 h-5 text-secondary"></i> Location Map
                        </h3>
                    </div>
                    <div id="shop-map" class="h-80 w-full rounded-b-box z-0"></div>
                </div>
            </div>

            <!-- Current Prices Card -->
            <div class="card bg-base-100 shadow-sm border border-base-300">
                <div class="card-body">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="card-title text-lg flex items-center gap-2">
                            <i data-lucide="tags" class="w-5 h-5 text-accent"></i> Current Buying Prices
                        </h3>
                        <a href="{{ route('prices.create') }}" class="btn btn-xs btn-outline">Update Prices</a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="table w-full">
                            <thead class="bg-base-200">
                                <tr>
                                    <th>Crop</th>
                                    <th>Price/kg</th>
                                    <th>Last Updated</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    // Get the latest price per crop specification
                                    $latestPrices = $shop->prices()
                                        ->orderBy('recorded_at', 'desc')
                                        ->get()
                                        ->unique(function ($item) {
                                            return $item['crop_id'].$item['specification'];
                                        });
                                @endphp
                                @forelse($latestPrices as $price)
                                    <tr class="hover">
                                        <td class="font-semibold">{{ $price->crop->name }} <span class="badge badge-primary badge-sm ml-1">{{ ucfirst($price->specification) }}</span></td>
                                        <td class="font-bold text-lg">₱{{ number_format($price->price_per_kg, 2) }}</td>
                                        <td class="text-sm text-base-content/70">{{ $price->recorded_at->diffForHumans() }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-6 text-base-content/50 italic">No prices recorded yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                if(typeof L !== 'undefined') {
                    const lat = {{ $shop->latitude ?? 14.5995 }};
                    const lng = {{ $shop->longitude ?? 120.9842 }};
                    
                    const map = L.map('shop-map').setView([lat, lng], 15);
                    
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; OpenStreetMap contributors'
                    }).addTo(map);

                    L.marker([lat, lng])
                        .addTo(map)
                        .bindPopup('<b>{{ addslashes($shop->name) }}</b><br>Here is your shop.')
                        .openPopup();
                }
            }, 500);
        });
    </script>
    @endpush
</x-app-layout>
