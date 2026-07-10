document.addEventListener('DOMContentLoaded', function() {
    console.log('📍 Map.js loaded');
    const mapElement = document.getElementById('price-map');
    if (!mapElement) {
        console.error('❌ Map element not found');
        return;
    }

    // Wait for Leaflet to be available globally
    if (typeof L === 'undefined') {
        console.error('❌ Leaflet is not loaded.');
        return;
    }

    console.log('✅ Leaflet loaded, initializing map');

    // Initialize map centered on San Mateo and Cabatuan, Isabela
    const map = L.map('price-map').setView([16.916, 121.575], 12);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    let markers = [];
    let shopsData = [];
    const cropFilter = document.getElementById('crop-filter');
    const shopSearch = document.getElementById('shop-search');
    const searchResults = document.getElementById('shop-search-results');

    // Store markers in a map for easy access
    const markerMap = new Map();

    // Fetch shop data
    fetch('/api/shops')
        .then(response => response.json())
        .then(data => {
            console.log(`✅ ${data.length} shops loaded`);
            shopsData = data;
            renderMarkers(data);

            if (cropFilter) {
                cropFilter.addEventListener('change', (e) => {
                    renderMarkers(data, e.target.value);
                });
            }

            // Setup search
            if (shopSearch) {
                shopSearch.addEventListener('input', (e) => {
                    searchShops(e.target.value, data);
                });
            }
        })
        .catch(err => console.error('❌ Error fetching shop data:', err));

    function searchShops(query, shops) {
        const resultsContainer = searchResults.querySelector('.space-y-2');
        resultsContainer.innerHTML = '';

        if (!query.trim()) {
            searchResults.classList.add('hidden');
            return;
        }

        const filtered = shops.filter(shop =>
            shop.name.toLowerCase().includes(query.toLowerCase()) ||
            shop.address.toLowerCase().includes(query.toLowerCase())
        );

        if (filtered.length === 0) {
            resultsContainer.innerHTML = '<p class="text-sm text-slate-600">No shops found</p>';
            searchResults.classList.remove('hidden');
            return;
        }

        filtered.forEach(shop => {
            const resultDiv = document.createElement('div');
            resultDiv.className = 'p-2 bg-base-100 hover:bg-base-200 rounded cursor-pointer transition-colors';
            resultDiv.innerHTML = `
                <div class="font-semibold text-sm">${shop.name}</div>
                <div class="text-xs text-base-content/70">${shop.address}</div>
            `;
            resultDiv.addEventListener('click', () => {
                shopSearch.value = '';
                searchResults.classList.add('hidden');
                navigateToShop(shop);
            });
            resultsContainer.appendChild(resultDiv);
        });

        searchResults.classList.remove('hidden');
    }

    function navigateToShop(shop) {
        console.log('🎯 Navigating to shop:', shop.name);
        // Pan map to shop location
        map.setView([shop.latitude, shop.longitude], 16);

        // Find and open the marker popup
        if (markerMap.has(shop.id)) {
            const marker = markerMap.get(shop.id);
            marker.openPopup();
        }
    }

    function renderMarkers(shops, filterCrop = 'all') {
        console.log('🔄 Rendering markers for crop filter:', filterCrop);
        // Clear existing markers
        markers.forEach(marker => map.removeLayer(marker));
        markers = [];
        markerMap.clear();

        shops.forEach(shop => {
            // Check if shop has prices for the selected crop
            let hasPrice = false;
            let currentPrice = null;
            let cropInfoHtml = '';

            if (shop.prices && shop.prices.length > 0) {
                shop.prices.forEach(p => {
                    // Extract base crop name without specification (e.g., "Palay" from "Palay (Dry)")
                    const baseCropName = p.crop_name.split(' (')[0];

                    if (filterCrop === 'all' || baseCropName === filterCrop) {
                        hasPrice = true;
                        currentPrice = p.price;
                        cropInfoHtml += `<div class="flex justify-between border-b border-base-200 py-1">
                            <span class="font-medium">${p.crop_name}</span>
                            <span class="font-bold text-primary">₱${parseFloat(p.price).toFixed(2)}/kg</span>
                        </div>
                        <div class="text-xs text-base-content/60 text-right mb-2">Updated: ${p.date}</div>`;
                    }
                });
            }

            // Only show shops that have the filtered crop (unless 'all' is selected)
            if (filterCrop !== 'all' && !hasPrice) return;

            // Determine marker color (simple logic: over 50 is "high")
            let markerColor = (currentPrice && currentPrice > 50) ? '#10b981' : '#3b82f6';

            // Custom SVG Pin Icon
            const svgPin = `
                <svg viewBox="0 0 24 24" width="36" height="36" stroke="white" stroke-width="2" fill="${markerColor}" style="filter: drop-shadow(0px 4px 4px rgba(0,0,0,0.3));">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                    <circle cx="12" cy="10" r="3" fill="white"></circle>
                </svg>
            `;

            const customIcon = L.divIcon({
                className: 'custom-div-icon bg-transparent border-0',
                html: svgPin,
                iconSize: [36, 36],
                iconAnchor: [18, 36],
                popupAnchor: [0, -32]
            });

            const popupContent = `
                <div class="p-1 min-w-[200px]">
                    <h3 class="font-bold text-lg mb-1">${shop.name}</h3>
                    <p class="text-sm text-base-content/70 mb-3 leading-tight">${shop.address}</p>
                    <div class="bg-base-200 p-2 rounded-lg">
                        ${cropInfoHtml || '<p class="text-sm italic text-base-content/60">No prices recorded yet.</p>'}
                    </div>
                    <div class="mt-3 text-xs flex justify-between items-center text-base-content/60">
                        <span>Owner: ${shop.owner}</span>
                    </div>
                    <button class="btn btn-primary btn-sm w-full mt-3" data-subscribe-shop="${shop.id}" data-shop-name="${shop.name}" type="button">
                        Subscribe
                    </button>
                </div>
            `;

            const marker = L.marker([shop.latitude, shop.longitude], { icon: customIcon })
                .bindPopup(popupContent, { maxWidth: 300, className: 'custom-popup' })
                .on('popupopen', function() {
                    console.log('📂 Popup opened for:', shop.name);

                    setTimeout(() => {
                        const popup = this.getPopup();
                        if (!popup) {
                            console.error('❌ No popup found');
                            return;
                        }

                        const popupEl = popup.getElement();
                        if (!popupEl) {
                            console.error('❌ Popup element not found');
                            return;
                        }

                        const subscribeBtn = popupEl.querySelector('[data-subscribe-shop]');
                        console.log('🔍 Looking for button, found:', !!subscribeBtn);

                        if (subscribeBtn) {
                            console.log('✅ Button found for shop:', subscribeBtn.dataset.subscribeShop);

                            subscribeBtn.onclick = (e) => {
                                console.log('🖱️  Button clicked!');
                                e.preventDefault();
                                e.stopPropagation();

                                const shopId = subscribeBtn.dataset.subscribeShop;
                                const modalName = `subscribe-${shopId}`;
                                console.log('🎯 Triggering modal:', modalName);

                                // Dispatch event
                                const event = new CustomEvent('open-modal', { detail: modalName });
                                window.dispatchEvent(event);
                                console.log('✅ Event dispatched!');

                                return false;
                            };
                        } else {
                            console.error('❌ Subscribe button NOT found in popup');
                            console.log('Popup HTML:', popupEl.innerHTML);
                        }
                    }, 50);
                })
                .addTo(map);

            markers.push(marker);
            markerMap.set(shop.id, marker);
        });
    }
});
