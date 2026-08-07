document.addEventListener('DOMContentLoaded', function () {
    console.log('📍 Map.js loaded');
    const mapElement = document.getElementById('price-map');
    if (!mapElement) {
        console.error('❌ Map element not found');
        return;
    }

    if (typeof L === 'undefined') {
        console.error('❌ Leaflet is not loaded.');
        return;
    }

    console.log('✅ Leaflet loaded, initializing map with CARTO Voyager basemap');

    // Initialize Leaflet map
    const map = L.map('price-map').setView([16.916, 121.575], 12);

    // Option 2: Leaflet + CARTO Voyager Basemap (Beautiful modern style)
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        maxZoom: 19,
        subdomains: 'abcd',
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
    }).addTo(map);

    let markers = [];
    let shopsData = [];
    let userLatLng = null;
    const cropFilter = document.getElementById('crop-filter');
    const classificationFilter = document.getElementById('classification-filter');
    const shopSearch = document.getElementById('shop-search');
    const searchResults = document.getElementById('shop-search-results');
    const markerMap = new Map();
    const viewedShops = new Set();

    // ─── Classification Config ───────────────────────────────────────────────────
    const CLASSIFICATIONS = {
        trader: {
            label: 'Trader / Dealer',
            color: '#7c3aed',
            bgColor: '#ede9fe',
            innerSvg: `<path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z" stroke-width="1.5"/><line x1="3" y1="6" x2="21" y2="6" stroke-width="1.5"/><path d="M16 10a4 4 0 0 1-8 0" stroke-width="1.5"/>`,
        },
        miller: {
            label: 'Miller',
            color: '#ea580c',
            bgColor: '#ffedd5',
            innerSvg: `<circle cx="12" cy="12" r="3" stroke-width="1.5"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z" stroke-width="1.5"/>`,
        },
        wholesaler: {
            label: 'Wholesaler',
            color: '#2563eb',
            bgColor: '#dbeafe',
            innerSvg: `<line x1="16.5" y1="9.4" x2="7.5" y2="4.21" stroke-width="1.5"/><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z" stroke-width="1.5"/><polyline points="3.27 6.96 12 12.01 20.73 6.96" stroke-width="1.5"/><line x1="12" y1="22.08" x2="12" y2="12" stroke-width="1.5"/>`,
        },
        retailer: {
            label: 'Retailer',
            color: '#0d9488',
            bgColor: '#ccfbf1',
            innerSvg: `<circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6" stroke-width="1.5"/>`,
        },
        government: {
            label: "Gov't-Accredited",
            color: '#dc2626',
            bgColor: '#fee2e2',
            innerSvg: `<line x1="3" y1="22" x2="21" y2="22" stroke-width="1.5"/><line x1="6" y1="18" x2="6" y2="11" stroke-width="1.5"/><line x1="10" y1="18" x2="10" y2="11" stroke-width="1.5"/><line x1="14" y1="18" x2="14" y2="11" stroke-width="1.5"/><line x1="18" y1="18" x2="18" y2="11" stroke-width="1.5"/><polygon points="12 2 20 7 4 7" stroke-width="1.5"/>`,
        },
        cooperative: {
            label: 'Cooperative',
            color: '#d97706',
            bgColor: '#fef3c7',
            innerSvg: `<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke-width="1.5"/><circle cx="9" cy="7" r="4" stroke-width="1.5"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75" stroke-width="1.5"/>`,
        },
        exporter: {
            label: 'Exporter',
            color: '#0891b2',
            bgColor: '#cffafe',
            innerSvg: `<path d="M2 21c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1 .6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1" stroke-width="1.5"/><path d="M19.38 20A11.6 11.6 0 0 0 21 14l-9-4-9 4c0 2.9.94 5.34 2.81 7.76" stroke-width="1.5"/><path d="M19 13V7a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v6" stroke-width="1.5"/><line x1="12" y1="10" x2="12" y2="10" stroke-width="2"/>`,
        },
    };

    const DEFAULT_CLASSIFICATION = {
        label: 'Buyer',
        color: '#3b82f6',
        bgColor: '#dbeafe',
        innerSvg: `<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" stroke-width="1.5"/><polyline points="9 22 9 12 15 12 15 22" stroke-width="1.5"/>`,
    };

    function getClassificationConfig(classification) {
        return CLASSIFICATIONS[classification] || DEFAULT_CLASSIFICATION;
    }

    // ─── Shop Info Panel ─────────────────────────────────────────────────────────
    const panel = document.getElementById('shop-info-panel');
    const panelClose = document.getElementById('shop-info-close');

    if (panelClose) {
        panelClose.addEventListener('click', () => {
            panel.classList.add('translate-x-full', 'opacity-0');
            panel.classList.remove('translate-x-0', 'opacity-100');
        });
    }

    function haversineDistance(lat1, lon1, lat2, lon2) {
        const R = 6371;
        const dLat = ((lat2 - lat1) * Math.PI) / 180;
        const dLon = ((lon2 - lon1) * Math.PI) / 180;
        const a =
            Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos((lat1 * Math.PI) / 180) *
                Math.cos((lat2 * Math.PI) / 180) *
                Math.sin(dLon / 2) *
                Math.sin(dLon / 2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return R * c;
    }

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (pos) => {
                userLatLng = { lat: pos.coords.latitude, lng: pos.coords.longitude };
                console.log('📍 User location acquired:', userLatLng);
                if (shopsData.length > 0) {
                    populateNearbyShops(shopsData);
                }
            },
            () => {
                console.warn('⚠️ Geolocation denied or unavailable');
                showNearbyUnavailable();
            }
        );
    } else {
        showNearbyUnavailable();
    }

    function openShopPanel(shop) {
        if (!panel) { return; }

        if (!viewedShops.has(shop.id)) {
            viewedShops.add(shop.id);
            fetch(`/api/shops/${shop.id}/view`, { method: 'POST' }).catch(() => {});
            shop.views = (shop.views || 0) + 1;
        }

        const cfg = getClassificationConfig(shop.classification);

        const classificationBadge = `
            <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:999px;background:${cfg.bgColor};color:${cfg.color};font-size:11px;font-weight:700;letter-spacing:0.04em;">
                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">${cfg.innerSvg}</svg>
                ${cfg.label}
            </span>`;

        let distanceHtml = '';
        if (userLatLng) {
            const dist = haversineDistance(userLatLng.lat, userLatLng.lng, shop.latitude, shop.longitude);
            distanceHtml = `
                <div class="shop-info-row">
                    <span class="shop-info-label"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> Distance</span>
                    <span class="shop-info-value">${dist.toFixed(2)} km away</span>
                </div>`;
        } else {
            distanceHtml = `
                <div class="shop-info-row">
                    <span class="shop-info-label"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> Distance</span>
                    <span class="shop-info-value shop-info-muted">Location not available</span>
                </div>`;
        }

        let cropsHtml = '';
        if (shop.prices && shop.prices.length > 0) {
            cropsHtml = shop.prices.map((p) => `
                <div class="shop-crop-row">
                    <span class="shop-crop-name">${p.crop_name}</span>
                    <span class="shop-crop-price">₱${parseFloat(p.price).toFixed(2)}<span class="shop-crop-unit">/kg</span></span>
                </div>
            `).join('');
        } else {
            cropsHtml = '<p class="shop-no-crops">No crops listed yet.</p>';
        }

        const subscribersHtml = shop.subscribers_count > 0
            ? `<div class="shop-info-row">
                <span class="shop-info-label">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    Farmers
                </span>
                <span class="shop-info-value">${shop.subscribers_count} subscribed</span>
            </div>`
            : '';

        document.getElementById('panel-shop-name').textContent = shop.name;
        document.getElementById('panel-shop-body').innerHTML = `
            <div class="shop-info-section">
                <p class="shop-section-label">Shop Details</p>
                <div style="margin-bottom:8px;">${classificationBadge}</div>
                <div class="shop-info-rows">
                    <div class="shop-info-row">
                        <span class="shop-info-label"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg> Owner</span>
                        <span class="shop-info-value">${shop.owner}</span>
                    </div>
                    <div class="shop-info-row">
                        <span class="shop-info-label"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg> Location</span>
                        <span class="shop-info-value">${shop.address}</span>
                    </div>
                    ${distanceHtml}
                    ${subscribersHtml}
                </div>
            </div>
            <div class="shop-info-section">
                <p class="shop-section-label">Crops &amp; Prices</p>
                <div class="shop-crops-list">
                    ${cropsHtml}
                </div>
            </div>
        `;

        panel.classList.remove('translate-x-full', 'opacity-0');
        panel.classList.add('translate-x-0', 'opacity-100');
    }

    // ─── Discovery Panels ────────────────────────────────────────────────────────

    function shopDiscoveryItemHtml(shop, metricHtml) {
        const cfg = getClassificationConfig(shop.classification);
        return `
            <button
                class="discovery-shop-item w-full px-4 py-3 flex items-center gap-3 hover:bg-slate-50/80 transition-colors text-left border-b border-slate-50 last:border-0"
                data-shop-id="${shop.id}"
            >
                <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background:${cfg.color};"></span>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-800 truncate">${shop.name}</p>
                    <p class="text-[11px] text-slate-400 truncate">${shop.address || '—'}</p>
                </div>
                <span class="text-xs font-bold flex-shrink-0" style="color:${cfg.color};">${metricHtml}</span>
            </button>`;
    }

    function attachDiscoveryClickHandlers() {
        document.querySelectorAll('.discovery-shop-item').forEach((btn) => {
            btn.addEventListener('click', () => {
                const shopId = parseInt(btn.dataset.shopId);
                const shop = shopsData.find((s) => s.id === shopId);
                if (shop) { navigateToShop(shop); }
            });
        });
    }

    function populateNearbyShops(shops) {
        const el = document.getElementById('nearby-shops-list');
        if (!el || !userLatLng) { return; }

        const sorted = shops
            .filter((s) => s.latitude !== 0 && s.longitude !== 0)
            .map((s) => ({ ...s, _distance: haversineDistance(userLatLng.lat, userLatLng.lng, s.latitude, s.longitude) }))
            .sort((a, b) => a._distance - b._distance)
            .slice(0, 6);

        if (sorted.length === 0) {
            el.innerHTML = emptyDiscoveryHtml('No shops found nearby.');
            return;
        }

        el.innerHTML = sorted.map((s) => shopDiscoveryItemHtml(s, `${s._distance.toFixed(1)} km`)).join('');
        attachDiscoveryClickHandlers();
    }

    function populatePopularShops(shops) {
        const el = document.getElementById('popular-shops-list');
        if (!el) { return; }

        const sorted = [...shops]
            .filter((s) => s.latitude !== 0 && s.longitude !== 0)
            .sort((a, b) => (b.subscribers_count + b.views) - (a.subscribers_count + a.views))
            .slice(0, 6);

        if (sorted.length === 0) {
            el.innerHTML = emptyDiscoveryHtml('No shops to display.');
            return;
        }

        el.innerHTML = sorted.map((s) => {
            const label = s.subscribers_count > 0
                ? `${s.subscribers_count} subscriber${s.subscribers_count !== 1 ? 's' : ''}`
                : `${s.views || 0} view${s.views !== 1 ? 's' : ''}`;
            return shopDiscoveryItemHtml(s, label);
        }).join('');

        attachDiscoveryClickHandlers();
    }

    function populateRecentShops(shops) {
        const el = document.getElementById('recent-shops-list');
        if (!el) { return; }

        const sorted = [...shops]
            .filter((s) => s.latest_price_at && s.latitude !== 0 && s.longitude !== 0)
            .sort((a, b) => new Date(b.latest_price_at) - new Date(a.latest_price_at))
            .slice(0, 6);

        if (sorted.length === 0) {
            el.innerHTML = emptyDiscoveryHtml('No recent price updates.');
            return;
        }

        el.innerHTML = sorted.map((s) => {
            const date = new Date(s.latest_price_at);
            const now = new Date();
            const diffDays = Math.floor((now - date) / (1000 * 60 * 60 * 24));
            const label = diffDays === 0 ? 'Today' : diffDays === 1 ? 'Yesterday' : `${diffDays}d ago`;
            return shopDiscoveryItemHtml(s, label);
        }).join('');

        attachDiscoveryClickHandlers();
    }

    function populatePriceLeaderShops(shops) {
        const el = document.getElementById('best-price-shops-list');
        if (!el) { return; }

        const shopsWithMax = shops
            .filter((s) => s.prices && s.prices.length > 0 && s.latitude !== 0 && s.longitude !== 0)
            .map((s) => ({
                ...s,
                _maxPrice: Math.max(...s.prices.map((p) => parseFloat(p.price))),
            }))
            .sort((a, b) => b._maxPrice - a._maxPrice)
            .slice(0, 6);

        if (shopsWithMax.length === 0) {
            el.innerHTML = emptyDiscoveryHtml('No price data available.');
            return;
        }

        el.innerHTML = shopsWithMax.map((s) => shopDiscoveryItemHtml(s, `₱${s._maxPrice.toFixed(2)}`)).join('');
        attachDiscoveryClickHandlers();
    }

    function emptyDiscoveryHtml(message) {
        return `<div class="px-4 py-6 text-center text-xs text-slate-400">${message}</div>`;
    }

    function showNearbyUnavailable() {
        const el = document.getElementById('nearby-shops-list');
        if (!el) { return; }
        el.innerHTML = `
            <div class="px-4 py-6 text-center">
                <p class="text-xs text-slate-400 mb-2">Location access is required</p>
                <button id="retry-geolocation" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 transition-colors">
                    Enable Location
                </button>
            </div>`;

        const retryBtn = document.getElementById('retry-geolocation');
        if (retryBtn) {
            retryBtn.addEventListener('click', () => {
                navigator.geolocation?.getCurrentPosition(
                    (pos) => {
                        userLatLng = { lat: pos.coords.latitude, lng: pos.coords.longitude };
                        populateNearbyShops(shopsData);
                    },
                    () => {}
                );
            });
        }
    }

    // ─── Data Fetching ────────────────────────────────────────────────────────────
    fetch('/api/shops')
        .then((response) => response.json())
        .then((data) => {
            console.log(`✅ ${data.length} shops loaded`);
            shopsData = data;
            renderMarkers(data);

            populatePopularShops(data);
            populateRecentShops(data);
            populatePriceLeaderShops(data);
            if (userLatLng) { populateNearbyShops(data); }

            if (cropFilter) {
                cropFilter.addEventListener('change', () => applyFilters(data));
            }

            if (classificationFilter) {
                classificationFilter.addEventListener('change', () => applyFilters(data));
            }

            if (shopSearch) {
                shopSearch.addEventListener('input', (e) => {
                    searchShops(e.target.value, data);
                });
            }
        })
        .catch((err) => console.error('❌ Error fetching shop data:', err));

    function applyFilters(shops) {
        const cropVal = cropFilter ? cropFilter.value : 'all';
        const classVal = classificationFilter ? classificationFilter.value : 'all';
        renderMarkers(shops, cropVal, classVal);
    }

    // ─── Search ───────────────────────────────────────────────────────────────────
    function searchShops(query, shops) {
        const resultsContainer = searchResults.querySelector('.space-y-2');
        resultsContainer.innerHTML = '';

        if (!query.trim()) {
            searchResults.classList.add('hidden');
            return;
        }

        const filtered = shops.filter(
            (shop) =>
                shop.name.toLowerCase().includes(query.toLowerCase()) ||
                shop.address.toLowerCase().includes(query.toLowerCase())
        );

        if (filtered.length === 0) {
            resultsContainer.innerHTML = '<p class="text-sm text-slate-600">No shops found</p>';
            searchResults.classList.remove('hidden');
            return;
        }

        filtered.forEach((shop) => {
            const cfg = getClassificationConfig(shop.classification);
            const resultDiv = document.createElement('div');
            resultDiv.className = 'p-2 bg-base-100 hover:bg-base-200 rounded cursor-pointer transition-colors';
            resultDiv.innerHTML = `
                <div class="flex items-center gap-2">
                    <span style="width:8px;height:8px;border-radius:50%;background:${cfg.color};flex-shrink:0;display:inline-block;"></span>
                    <div class="font-semibold text-sm">${shop.name}</div>
                </div>
                <div class="text-xs text-base-content/70 ml-4">${shop.address}</div>
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
        map.setView([shop.latitude, shop.longitude], 16);
        if (markerMap.has(shop.id)) {
            const marker = markerMap.get(shop.id);
            marker.openPopup();
        }
    }

    // ─── Leaflet Marker Icon Generator ─────────────────────────────────────────────
    function buildMarkerIcon(classification) {
        const cfg = getClassificationConfig(classification);

        const svgPin = `
            <svg viewBox="0 0 40 50" width="40" height="50" xmlns="http://www.w3.org/2000/svg" style="filter: drop-shadow(0px 3px 5px rgba(0,0,0,0.35));">
                <path d="M20 0C11.163 0 4 7.163 4 16c0 10.917 13.393 27.915 15.13 30.018a1.2 1.2 0 0 0 1.74 0C22.607 43.915 36 26.917 36 16 36 7.163 28.837 0 20 0z" fill="${cfg.color}"/>
                <circle cx="20" cy="16" r="10" fill="white"/>
                <g transform="translate(20,16) scale(0.55) translate(-12,-12)" stroke="${cfg.color}" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    ${cfg.innerSvg}
                </g>
            </svg>
        `;

        return L.divIcon({
            className: 'custom-div-icon bg-transparent border-0',
            html: svgPin,
            iconSize: [40, 50],
            iconAnchor: [20, 50],
            popupAnchor: [0, -48],
        });
    }

    // ─── Render Markers for Leaflet ───────────────────────────────────────────────
    function renderMarkers(shops, filterCrop = 'all', filterClassification = 'all') {
        markers.forEach((m) => map.removeLayer(m));
        markers = [];
        markerMap.clear();

        shops.forEach((shop) => {
            if (filterClassification !== 'all' && shop.classification !== filterClassification) { return; }
            if (shop.latitude === 0 && shop.longitude === 0) { return; }

            let hasPrice = false;
            let cropInfoHtml = '';

            if (shop.prices && shop.prices.length > 0) {
                shop.prices.forEach((p) => {
                    const baseCropName = p.crop_name.split(' (')[0];
                    if (filterCrop === 'all' || baseCropName === filterCrop) {
                        hasPrice = true;
                        cropInfoHtml += `<div class="flex justify-between border-b border-base-200 py-1">
                            <span class="font-medium">${p.crop_name}</span>
                            <span class="font-bold text-primary">₱${parseFloat(p.price).toFixed(2)}/kg</span>
                        </div>
                        <div class="text-xs text-base-content/60 text-right mb-2">Updated: ${p.date}</div>`;
                    }
                });
            }

            if (filterCrop !== 'all' && !hasPrice) { return; }

            const cfg = getClassificationConfig(shop.classification);
            const customIcon = buildMarkerIcon(shop.classification);

            const popupContent = `
                <div class="p-1 min-w-[200px]">
                    <h3 class="font-bold text-lg mb-1 text-slate-800">${shop.name}</h3>
                    <p class="text-sm text-slate-500 mb-2 leading-tight">${shop.address}</p>
                    <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:999px;background:${cfg.bgColor};color:${cfg.color};font-size:11px;font-weight:700;letter-spacing:0.04em;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">${cfg.innerSvg}</svg>
                        ${cfg.label}
                    </span>
                    <div class="mt-3 text-xs text-slate-500">
                        <span>Owner: ${shop.owner}</span>
                    </div>
                    <button class="btn btn-primary btn-sm w-full mt-3 text-white" data-view-shop="${shop.id}" type="button">
                        View Shop Info &amp; Prices
                    </button>
                    <button class="btn btn-sm w-full mt-2 bg-red-100 hover:bg-red-200 text-red-600 border-0" data-close-popup type="button">
                        Close
                    </button>
                </div>
            `;

            const marker = L.marker([shop.latitude, shop.longitude], { icon: customIcon })
                .bindPopup(popupContent, { maxWidth: 300, className: 'custom-popup', closeButton: false })
                .on('popupopen', function () {
                    setTimeout(() => {
                        const popup = this.getPopup();
                        if (!popup) { return; }
                        const popupEl = popup.getElement();
                        if (!popupEl) { return; }

                        const viewBtn = popupEl.querySelector('[data-view-shop]');
                        if (viewBtn) {
                            viewBtn.onclick = (e) => {
                                e.preventDefault();
                                e.stopPropagation();
                                openShopPanel(shop);
                            };
                        }

                        const closeBtn = popupEl.querySelector('[data-close-popup]');
                        if (closeBtn) {
                            closeBtn.onclick = (e) => {
                                e.preventDefault();
                                e.stopPropagation();
                                marker.closePopup();
                            };
                        }
                    }, 50);
                })
                .addTo(map);

            markers.push(marker);
            markerMap.set(shop.id, marker);
        });
    }
});
