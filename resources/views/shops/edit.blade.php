<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-base-content leading-tight">
            {{ __('My Shop Profile') }}
        </h2>
    </x-slot>

    <div class="max-w-3xl mx-auto bg-base-100 p-8 rounded-box shadow-sm border border-base-300 animate-fade-in-up">
        <form method="POST" action="{{ route('shops.update') }}" enctype="multipart/form-data" class="space-y-6">
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
                <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-2 gap-2">
                    <label class="label p-0 flex flex-col items-start sm:flex-row sm:items-center">
                        <span class="label-text font-semibold">Location Picker</span>
                        <span class="label-text-alt text-base-content/60 sm:ml-2">Click on the map to set coordinates</span>
                    </label>
                    <button type="button" id="detect-location-btn" class="btn btn-sm btn-secondary w-full sm:w-auto">
                        <i data-lucide="crosshair" class="w-4 h-4 mr-1"></i> Detect My Location
                    </button>
                </div>
                <div id="location-picker-map" class="h-64 rounded-box border border-base-300 z-0"></div>
            </div>

            <div class="form-control w-full">
                <label class="label"><span class="label-text font-semibold">Description (Optional)</span></label>
                <textarea name="description" class="textarea textarea-bordered h-24">{{ old('description', $shop->description) }}</textarea>
            </div>

            {{-- Shop Photo --}}
            <div class="form-control w-full">
                <label class="label"><span class="label-text font-semibold">Shop Photo <span class="text-slate-400 font-normal">(Optional)</span></span></label>

                {{-- Preview area --}}
                <div
                    id="photo-drop-zone"
                    class="relative w-full rounded-2xl overflow-hidden border-2 border-dashed border-base-300 bg-slate-50 transition-all duration-200 cursor-pointer hover:border-primary hover:bg-primary/5 group"
                    onclick="document.getElementById('photo-input').click()"
                >
                    {{-- Existing / preview image --}}
                    @if($shop->photo_url)
                        <img
                            id="photo-preview"
                            src="{{ $shop->photo_url }}"
                            alt="Shop photo"
                            class="w-full h-52 object-cover"
                        >
                    @else
                        <img
                            id="photo-preview"
                            src=""
                            alt="Photo preview"
                            class="hidden w-full h-52 object-cover"
                        >
                        <div id="photo-placeholder" class="flex flex-col items-center justify-center py-10 gap-2">
                            <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center group-hover:bg-primary/10 transition-colors">
                                <i data-lucide="image-plus" class="w-7 h-7 text-slate-400 group-hover:text-primary transition-colors"></i>
                            </div>
                            <p class="text-sm font-semibold text-slate-500 group-hover:text-primary transition-colors">Click to upload a photo</p>
                            <p class="text-xs text-slate-400">JPG, PNG, WEBP — max 2MB</p>
                        </div>
                    @endif

                    {{-- Overlay hint on existing photo --}}
                    @if($shop->photo_url)
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 flex items-center justify-center transition-all duration-200 opacity-0 group-hover:opacity-100">
                        <span class="flex items-center gap-2 bg-white/90 text-slate-800 font-semibold text-sm px-4 py-2 rounded-full shadow">
                            <i data-lucide="pencil" class="w-4 h-4"></i> Change Photo
                        </span>
                    </div>
                    @endif
                </div>

                {{-- Hidden real file input --}}
                <input
                    type="file"
                    id="photo-input"
                    name="photo"
                    accept="image/jpeg,image/png,image/jpg,image/webp"
                    class="hidden"
                    onchange="previewPhoto(this)"
                />
                {{-- Hidden remove flag --}}
                <input type="hidden" id="remove-photo-flag" name="remove_photo" value="0">

                @error('photo')
                    <span class="text-error text-sm mt-1 flex items-center gap-1">
                        <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>{{ $message }}
                    </span>
                @enderror

                {{-- Action row --}}
                <div id="photo-actions" class="flex items-center gap-2 mt-2 {{ $shop->photo_url ? '' : 'hidden' }}">
                    <button
                        type="button"
                        id="change-photo-btn"
                        onclick="document.getElementById('photo-input').click()"
                        class="btn btn-sm btn-outline btn-primary gap-1"
                    >
                        <i data-lucide="upload" class="w-3.5 h-3.5"></i> Change
                    </button>
                    <button
                        type="button"
                        id="remove-photo-btn"
                        onclick="removePhoto()"
                        class="btn btn-sm btn-outline btn-error gap-1"
                    >
                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Remove Photo
                    </button>
                </div>
            </div>

            <div class="form-control w-full">
                <label class="label"><span class="label-text font-semibold">Buyer Classification</span></label>
                <select name="classification" class="select select-bordered w-full" required>
                    <option value="" disabled {{ old('classification', $shop->classification) ? '' : 'selected' }}>Select your classification...</option>
                    @foreach([
                        'trader'      => 'Trader / Dealer — Private commercial buyer',
                        'miller'      => 'Miller — Rice / corn mill operator',
                        'wholesaler'  => 'Wholesaler — Bulk buyer / reseller',
                        'retailer'    => 'Retailer — Direct-to-consumer seller',
                        'government'  => 'Government-Accredited — NFA / DA-accredited buyer',
                        'cooperative' => 'Cooperative — Farmer coop / consolidator',
                    ] as $value => $label)
                        <option value="{{ $value }}" {{ old('classification', $shop->classification) === $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('classification') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="flex justify-end pt-4 border-t border-base-200">
                <button type="submit" class="btn btn-primary">Save Shop Profile</button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        function previewPhoto(input) {
            const preview     = document.getElementById('photo-preview');
            const placeholder = document.getElementById('photo-placeholder');
            const actions     = document.getElementById('photo-actions');
            const removeFlag  = document.getElementById('remove-photo-flag');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    if (placeholder) { placeholder.classList.add('hidden'); }
                    if (actions)     { actions.classList.remove('hidden'); }
                    if (removeFlag)  { removeFlag.value = '0'; }
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function removePhoto() {
            const preview     = document.getElementById('photo-preview');
            const placeholder = document.getElementById('photo-placeholder');
            const actions     = document.getElementById('photo-actions');
            const fileInput   = document.getElementById('photo-input');
            const removeFlag  = document.getElementById('remove-photo-flag');
            const dropZone    = document.getElementById('photo-drop-zone');

            // Reset the image preview
            preview.src = '';
            preview.classList.add('hidden');

            // Show placeholder
            if (placeholder) { placeholder.classList.remove('hidden'); }

            // Hide action buttons
            if (actions) { actions.classList.add('hidden'); }

            // Clear file input
            if (fileInput) { fileInput.value = ''; }

            // Signal the server to delete the photo on save
            if (removeFlag) { removeFlag.value = '1'; }

            // Remove any overlay div that only appears when a photo exists
            const overlay = dropZone ? dropZone.querySelector('div[class*="absolute"]') : null;
            if (overlay) { overlay.remove(); }
        }

        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                if (typeof L !== 'undefined') {
                    const latInput = document.getElementById('lat-input');
                    const lngInput = document.getElementById('lng-input');
                    
                    const startLat = latInput.value ? parseFloat(latInput.value) : 14.5995;
                    const startLng = lngInput.value ? parseFloat(lngInput.value) : 120.9842;
                    
                    // Fix Leaflet's default icon path issue with Vite
                    delete L.Icon.Default.prototype._getIconUrl;
                    L.Icon.Default.mergeOptions({
                        iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
                        iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
                        shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png'
                    });

                    const map = L.map('location-picker-map').setView([startLat, startLng], 10);
                    
                    // Delay map resize calculation until after any container animation
                    setTimeout(() => map.invalidateSize(), 1500);
                    
                    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                    }).addTo(map);

                    let marker;
                    if (latInput.value && lngInput.value) {
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
                                
                                const successCallback = function(position) {
                                    const lat = position.coords.latitude.toFixed(7);
                                    const lng = position.coords.longitude.toFixed(7);

                                    latInput.value = lat;
                                    lngInput.value = lng;

                                    const latlng = [lat, lng];
                                    map.setView(latlng, 17);

                                    if (marker) {
                                        marker.setLatLng(latlng);
                                    } else {
                                        marker = L.marker(latlng).addTo(map);
                                    }

                                    // --- Reverse geocode to fill the Complete Address field ---
                                    const addressField = document.querySelector('textarea[name="address"]');
                                    if (addressField) {
                                        addressField.placeholder = 'Fetching address…';
                                    }

                                    fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json&addressdetails=1`, {
                                        headers: { 'Accept-Language': 'en', 'User-Agent': 'PricelyApp/1.0' }
                                    })
                                    .then(res => res.json())
                                    .then(geo => {
                                        if (addressField && geo && geo.display_name) {
                                            // Build a structured address: house number + road, barangay, city, province, region
                                            const a = geo.address || {};
                                            const parts = [
                                                a.house_number,
                                                a.road || a.pedestrian || a.footway,
                                                a.neighbourhood || a.suburb || a.village || a.hamlet,
                                                a.city_district || a.borough || a.quarter,
                                                a.city || a.town || a.municipality,
                                                a.state_district || a.county,
                                                a.state || a.region,
                                                a.postcode,
                                                a.country,
                                            ].filter(Boolean);

                                            addressField.value = parts.length > 0
                                                ? parts.join(', ')
                                                : geo.display_name;
                                            addressField.placeholder = '';
                                        }
                                    })
                                    .catch(() => {
                                        if (addressField) { addressField.placeholder = ''; }
                                    });

                                    detectBtn.innerHTML = originalHtml;
                                    detectBtn.disabled = false;
                                    if(typeof lucide !== 'undefined') lucide.createIcons();
                                };

                                const errorCallback = function(error) {
                                    let errorMsg = "Unable to detect location.";
                                    if(error.code === 1) errorMsg = "Location access denied. Please allow location permissions in your browser.";
                                    else if(error.code === 2) errorMsg = "Location unavailable. Please ensure your GPS/location services are turned on.";
                                    else if(error.code === 3) errorMsg = "Location detection timed out. Please try again or click the map manually.";
                                    
                                    alert(errorMsg);
                                    detectBtn.innerHTML = originalHtml;
                                    detectBtn.disabled = false;
                                    if(typeof lucide !== 'undefined') lucide.createIcons();
                                };

                                // Try high accuracy first
                                navigator.geolocation.getCurrentPosition(successCallback, function(error) {
                                    // If high accuracy times out (code 3), try again with low accuracy
                                    if (error.code === 3) {
                                        navigator.geolocation.getCurrentPosition(successCallback, errorCallback, {
                                            enableHighAccuracy: false,
                                            timeout: 10000,
                                            maximumAge: 0
                                        });
                                    } else {
                                        errorCallback(error);
                                    }
                                }, {
                                    enableHighAccuracy: true,
                                    timeout: 5000, // 5 seconds for high accuracy
                                    maximumAge: 0
                                });
                            } else {
                                alert("Geolocation is not supported by your browser.");
                            }
                        });
                    }
                }
            }, 300);
        });
    </script>
    @endpush
</x-app-layout>
