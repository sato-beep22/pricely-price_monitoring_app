<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-base-content leading-tight">
            {{ __('My Shop Profile') }}
        </h2>
    </x-slot>

    <div class="max-w-3xl mx-auto bg-base-100 p-8 rounded-box shadow-sm border border-base-300 animate-fade-in-up">
        <form method="POST" action="{{ route('shops.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="form-control w-full">
                <label class="label"><span class="label-text font-semibold">Shop Name</span></label>
                <input type="text" name="name" value="{{ old('name', $shop->name) }}" class="input input-bordered w-full" required />
                @error('name') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="form-control w-full">
                <label class="label"><span class="label-text font-semibold">Complete Address</span></label>
                <textarea name="address" class="textarea textarea-bordered h-24" required>{{ old('address', $shop->address) }}</textarea>
                @error('address') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-control w-full">
                    <label class="label"><span class="label-text font-semibold">Latitude</span></label>
                    <input type="number" step="any" name="latitude" id="lat-input" value="{{ old('latitude', $shop->latitude) }}" class="input input-bordered w-full" required />
                    @error('latitude') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                </div>
                
                <div class="form-control w-full">
                    <label class="label"><span class="label-text font-semibold">Longitude</span></label>
                    <input type="number" step="any" name="longitude" id="lng-input" value="{{ old('longitude', $shop->longitude) }}" class="input input-bordered w-full" required />
                    @error('longitude') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-control w-full mt-4">
                <div class="flex items-center justify-between mb-2">
                    <label class="label p-0">
                        <span class="label-text font-semibold">Location Picker</span>
                        <span class="label-text-alt text-base-content/60 ml-2">Click on the map to set coordinates</span>
                    </label>
                    <button type="button" id="detect-location-btn" class="btn btn-sm btn-secondary">
                        <i data-lucide="crosshair" class="w-4 h-4 mr-1"></i> Detect My Location
                    </button>
                </div>
                <div id="location-picker-map" class="h-64 rounded-box border border-base-300 z-0"></div>
            </div>

            <div class="form-control w-full">
                <label class="label"><span class="label-text font-semibold">Description (Optional)</span></label>
                <textarea name="description" class="textarea textarea-bordered h-24">{{ old('description', $shop->description) }}</textarea>
            </div>

            <div class="flex justify-end pt-4 border-t border-base-200">
                <button type="submit" class="btn btn-primary">Save Shop Profile</button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Simple map initialization for coordinate picking
            setTimeout(() => {
                if(typeof L !== 'undefined') {
                    const latInput = document.getElementById('lat-input');
                    const lngInput = document.getElementById('lng-input');
                    
                    // Default to Metro Manila if empty
                    const startLat = latInput.value ? parseFloat(latInput.value) : 14.5995;
                    const startLng = lngInput.value ? parseFloat(lngInput.value) : 120.9842;
                    
                    const map = L.map('location-picker-map').setView([startLat, startLng], 10);
                    
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; OpenStreetMap contributors'
                    }).addTo(map);

                    let marker;
                    if(latInput.value && lngInput.value) {
                        marker = L.marker([startLat, startLng]).addTo(map);
                    }

                    map.on('click', function(e) {
                        const lat = e.latlng.lat.toFixed(7);
                        const lng = e.latlng.lng.toFixed(7);
                        
                        latInput.value = lat;
                        lngInput.value = lng;
                        
                        if (marker) {
                            marker.setLatLng(e.latlng);
                        } else {
                            marker = L.marker(e.latlng).addTo(map);
                        }
                    });

                    // Detect Location Feature
                    const detectBtn = document.getElementById('detect-location-btn');
                    if (detectBtn) {
                        detectBtn.addEventListener('click', function() {
                            if ("geolocation" in navigator) {
                                const originalHtml = detectBtn.innerHTML;
                                detectBtn.innerHTML = '<span class="loading loading-spinner loading-xs"></span> Detecting...';
                                detectBtn.disabled = true;
                                
                                navigator.geolocation.getCurrentPosition(function(position) {
                                    const lat = position.coords.latitude.toFixed(7);
                                    const lng = position.coords.longitude.toFixed(7);
                                    
                                    latInput.value = lat;
                                    lngInput.value = lng;
                                    
                                    const latlng = [lat, lng];
                                    map.setView(latlng, 15);
                                    
                                    if (marker) {
                                        marker.setLatLng(latlng);
                                    } else {
                                        marker = L.marker(latlng).addTo(map);
                                    }
                                    
                                    detectBtn.innerHTML = originalHtml;
                                    detectBtn.disabled = false;
                                    if(typeof lucide !== 'undefined') lucide.createIcons();
                                }, function(error) {
                                    alert("Error detecting location: " + error.message);
                                    detectBtn.innerHTML = originalHtml;
                                    detectBtn.disabled = false;
                                    if(typeof lucide !== 'undefined') lucide.createIcons();
                                }, {
                                    enableHighAccuracy: true,
                                    timeout: 5000,
                                    maximumAge: 0
                                });
                            } else {
                                alert("Geolocation is not supported by your browser.");
                            }
                        });
                    }
                }
            }, 500); // slight delay to ensure L is loaded
        });
    </script>
    @endpush
</x-app-layout>
