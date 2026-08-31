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

        window.initLocationPickerMap = function () {
            const latInput = document.getElementById('lat-input');
            const lngInput = document.getElementById('lng-input');

            const startLat = latInput.value ? parseFloat(latInput.value) : 16.916;
            const startLng = lngInput.value ? parseFloat(lngInput.value) : 121.575;

            const map = new google.maps.Map(document.getElementById('location-picker-map'), {
                center: { lat: startLat, lng: startLng },
                zoom: 10,
                mapTypeId: 'roadmap',
                mapTypeControl: false,
                streetViewControl: false,
                fullscreenControl: false,
            });

            let marker = null;
            const geocoder = new google.maps.Geocoder();

            function placeMarker(latLng) {
                if (marker) {
                    marker.position = latLng;
                } else {
                    const markerDiv = document.createElement('div');
                    markerDiv.innerHTML = `
                        <svg viewBox="0 0 40 50" width="32" height="40" xmlns="http://www.w3.org/2000/svg" style="filter:drop-shadow(0 2px 4px rgba(0,0,0,0.3));">
                            <path d="M20 0C11.163 0 4 7.163 4 16c0 10.917 13.393 27.915 15.13 30.018a1.2 1.2 0 0 0 1.74 0C22.607 43.915 36 26.917 36 16 36 7.163 28.837 0 20 0z" fill="#059669"/>
                            <circle cx="20" cy="16" r="8" fill="white"/>
                        </svg>`;
                    marker = new google.maps.marker.AdvancedMarkerElement({
                        map,
                        position: latLng,
                        content: markerDiv.firstElementChild,
                    });
                }

                latInput.value = latLng.lat.toFixed(7);
                lngInput.value = latLng.lng.toFixed(7);
            }

            // If coordinates already exist, place a marker
            if (latInput.value && lngInput.value) {
                placeMarker({ lat: startLat, lng: startLng });
            }

            // Click on map to set coordinates
            map.addListener('click', (e) => {
                placeMarker({ lat: e.latLng.lat(), lng: e.latLng.lng() });
            });

            // Detect Location feature
            const detectBtn = document.getElementById('detect-location-btn');
            if (detectBtn) {
                detectBtn.addEventListener('click', function () {
                    if (!('geolocation' in navigator)) {
                        alert('Geolocation is not supported by your browser.');
                        return;
                    }

                    const originalHtml = detectBtn.innerHTML;
                    detectBtn.innerHTML = '<span class="loading loading-spinner loading-xs"></span> Detecting...';
                    detectBtn.disabled = true;

                    const successCallback = function (position) {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;

                        latInput.value = lat.toFixed(7);
                        lngInput.value = lng.toFixed(7);

                        map.setCenter({ lat, lng });
                        map.setZoom(17);
                        placeMarker({ lat, lng });

                        // Reverse geocode using Google Geocoder
                        const addressField = document.querySelector('textarea[name="address"]');
                        if (addressField) {
                            addressField.placeholder = 'Fetching address…';
                            geocoder.geocode({ location: { lat, lng } }, (results, status) => {
                                if (status === 'OK' && results[0]) {
                                    addressField.value = results[0].formatted_address;
                                }
                                addressField.placeholder = '';
                            });
                        }

                        detectBtn.innerHTML = originalHtml;
                        detectBtn.disabled = false;
                        if (typeof lucide !== 'undefined') { lucide.createIcons(); }
                    };

                    const errorCallback = function (error) {
                        let errorMsg = 'Unable to detect location.';
                        if (error.code === 1)      { errorMsg = 'Location access denied. Please allow location permissions in your browser.'; }
                        else if (error.code === 2) { errorMsg = 'Location unavailable. Please ensure your GPS/location services are turned on.'; }
                        else if (error.code === 3) { errorMsg = 'Location detection timed out. Please try again or click the map manually.'; }

                        alert(errorMsg);
                        detectBtn.innerHTML = originalHtml;
                        detectBtn.disabled = false;
                        if (typeof lucide !== 'undefined') { lucide.createIcons(); }
                    };

                    // Try high accuracy first, fall back to low accuracy on timeout
                    navigator.geolocation.getCurrentPosition(successCallback, function (error) {
                        if (error.code === 3) {
                            navigator.geolocation.getCurrentPosition(successCallback, errorCallback, {
                                enableHighAccuracy: false, timeout: 10000, maximumAge: 0,
                            });
                        } else {
                            errorCallback(error);
                        }
                    }, { enableHighAccuracy: true, timeout: 5000, maximumAge: 0 });
                });
            }
        };
    </script>
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_key') }}&libraries=marker&callback=initLocationPickerMap&loading=async"
        async defer
    ></script>
    @endpush
</x-app-layout>
