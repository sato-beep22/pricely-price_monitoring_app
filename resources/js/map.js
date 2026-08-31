// Google Maps-based interactive price map
// All Leaflet dependencies removed — uses Google Maps JavaScript API loaded in the blade view

window.initPriceMap = function () {
    console.log('📍 initPriceMap() called — Google Maps loaded');

    const mapElement = document.getElementById('price-map');
    if (!mapElement) { console.error('❌ Map element not found'); return; }

    // ─── Shared Styles ────────────────────────────────────────────────────────────
    const style = document.createElement('style');
    style.textContent = `
        @keyframes locModalIn {
            from { opacity:0; transform:scale(0.88) translateY(24px); }
            to   { opacity:1; transform:scale(1) translateY(0); }
        }
        #grant-location-btn:hover { transform:translateY(-2px); box-shadow:0 8px 20px rgba(5,150,105,0.45) !important; }
        #deny-location-btn:hover  { background:#f8fafc !important; color:#64748b !important; }
        #location-denied-banner { display:none; }
        @keyframes userPulse {
            0%   { transform:translate(-50%,-50%) scale(1);   opacity:0.7; }
            70%  { transform:translate(-50%,-50%) scale(2.4); opacity:0; }
            100% { transform:translate(-50%,-50%) scale(2.4); opacity:0; }
        }
        /* Google Maps custom popup */
        .gm-style .gm-style-iw-c { border-radius: 14px !important; padding: 0 !important; box-shadow: 0 8px 32px rgba(0,0,0,0.18) !important; }
        .gm-style .gm-style-iw-d { overflow: hidden !important; padding: 0 !important; }
        .gm-style .gm-style-iw-t::after { display:none; }
        .gm-ui-hover-effect { display:none !important; }
    `;
    document.head.appendChild(style);

    // ─── Modal Builder ────────────────────────────────────────────────────────────
    function buildLocationModal(state) {
        const isBlocked = state === 'blocked';
        const headerBg   = isBlocked
            ? 'linear-gradient(135deg,#b91c1c 0%,#dc2626 100%)'
            : 'linear-gradient(135deg,#059669 0%,#0d9488 100%)';
        const iconSvg    = isBlocked
            ? `<svg xmlns='http://www.w3.org/2000/svg' width='32' height='32' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><circle cx='12' cy='12' r='10'/><line x1='4.93' y1='4.93' x2='19.07' y2='19.07'/></svg>`
            : `<svg xmlns='http://www.w3.org/2000/svg' width='32' height='32' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><path d='M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z'/><circle cx='12' cy='10' r='3'/></svg>`;
        const title      = isBlocked ? 'Location is Blocked' : 'Location Required';
        const subtitle   = isBlocked
            ? 'Your browser has blocked location access for this site. Follow the steps below to re-enable it.'
            : 'This map needs your location to show nearby shops and accurate distances.';

        const bodyHtml = isBlocked ? `
            <p style="font-size:0.8rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#64748b;margin:0 0 0.75rem;">How to unblock location:</p>
            <ol style="margin:0 0 1.25rem;padding-left:1.25rem;display:flex;flex-direction:column;gap:0.6rem;">
                <li style="font-size:0.875rem;color:#334155;">Click the <strong>🔒 lock icon</strong> or <strong>ⓘ info icon</strong> in the address bar</li>
                <li style="font-size:0.875rem;color:#334155;">Find <strong>"Location"</strong> in the site permissions list</li>
                <li style="font-size:0.875rem;color:#334155;">Change it from <span style="color:#dc2626;font-weight:600;">Block</span> to <span style="color:#059669;font-weight:600;">Allow</span></li>
                <li style="font-size:0.875rem;color:#334155;"><strong>Reload</strong> the page and try again</li>
            </ol>
            <button id="grant-location-btn" style="width:100%;padding:0.875rem;background:linear-gradient(135deg,#2563eb,#1d4ed8);color:white;border:none;border-radius:0.875rem;font-size:0.95rem;font-weight:700;cursor:pointer;box-shadow:0 4px 14px rgba(37,99,235,0.35);transition:transform 0.15s,box-shadow 0.15s;margin-bottom:0.625rem;">🔄 Reload Page</button>
            <button id="deny-location-btn" style="width:100%;padding:0.625rem;background:transparent;color:#94a3b8;border:1.5px solid #e2e8f0;border-radius:0.875rem;font-size:0.85rem;font-weight:600;cursor:pointer;transition:background 0.15s,color 0.15s;">Continue without location</button>
        ` : `
            <ul style="list-style:none;margin:0 0 1.5rem;padding:0;display:flex;flex-direction:column;gap:0.75rem;">
                <li style="display:flex;align-items:center;gap:0.75rem;font-size:0.875rem;color:#334155;">
                    <span style="width:32px;height:32px;border-radius:50%;background:#ecfdf5;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='#059669' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><path d='M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z'/><circle cx='12' cy='10' r='3'/></svg>
                    </span>
                    <span>Find shops closest to you</span>
                </li>
                <li style="display:flex;align-items:center;gap:0.75rem;font-size:0.875rem;color:#334155;">
                    <span style="width:32px;height:32px;border-radius:50%;background:#ecfdf5;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='#059669' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><circle cx='12' cy='12' r='10'/><polyline points='12 6 12 12 16 14'/></svg>
                    </span>
                    <span>See real-time distances to each shop</span>
                </li>
                <li style="display:flex;align-items:center;gap:0.75rem;font-size:0.875rem;color:#334155;">
                    <span style="width:32px;height:32px;border-radius:50%;background:#ecfdf5;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='#059669' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><polygon points='3 11 22 2 13 21 11 13 3 11'/></svg>
                    </span>
                    <span>Center the map on your current position</span>
                </li>
            </ul>
            <button id="grant-location-btn" style="width:100%;padding:0.875rem;background:linear-gradient(135deg,#059669,#0d9488);color:white;border:none;border-radius:0.875rem;font-size:0.95rem;font-weight:700;cursor:pointer;box-shadow:0 4px 14px rgba(5,150,105,0.4);transition:transform 0.15s,box-shadow 0.15s;margin-bottom:0.625rem;">Allow Location Access</button>
            <button id="deny-location-btn" style="width:100%;padding:0.625rem;background:transparent;color:#94a3b8;border:1.5px solid #e2e8f0;border-radius:0.875rem;font-size:0.85rem;font-weight:600;cursor:pointer;transition:background 0.15s,color 0.15s;">Skip for now</button>
            <p style="text-align:center;font-size:0.75rem;color:#94a3b8;margin-top:1rem;margin-bottom:0;">Your location is never stored or shared.</p>
        `;

        const el = document.createElement('div');
        el.id = 'location-permission-modal';
        el.innerHTML = `
            <div style="position:fixed;inset:0;z-index:9999;background:rgba(15,23,42,0.7);backdrop-filter:blur(6px);display:flex;align-items:center;justify-content:center;padding:1rem;">
                <div style="background:#fff;border-radius:1.5rem;box-shadow:0 25px 50px -12px rgba(0,0,0,0.4);max-width:420px;width:100%;overflow:hidden;animation:locModalIn 0.35s cubic-bezier(.34,1.56,.64,1) both;">
                    <div style="background:${headerBg};padding:2rem 2rem 1.5rem;text-align:center;">
                        <div style="width:64px;height:64px;border-radius:50%;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">${iconSvg}</div>
                        <h2 style="color:white;font-size:1.3rem;font-weight:800;margin:0 0 0.4rem;">${title}</h2>
                        <p style="color:rgba(255,255,255,0.85);font-size:0.875rem;margin:0;">${subtitle}</p>
                    </div>
                    <div style="padding:1.75rem 2rem;">${bodyHtml}</div>
                </div>
            </div>
        `;
        return el;
    }

    let locationModal = null;

    // ─── Location Denied Banner ───────────────────────────────────────────────────
    const deniedBanner = document.createElement('div');
    deniedBanner.id = 'location-denied-banner';
    deniedBanner.style.cssText = `position:fixed;bottom:1.5rem;left:0;right:0;margin:0 auto;z-index:8000;background:#1e293b;color:white;border-radius:1rem;padding:0.75rem 1.25rem;display:flex;align-items:center;gap:0.75rem;box-shadow:0 10px 30px rgba(0,0,0,0.3);font-size:0.875rem;font-weight:500;max-width:480px;width:calc(100% - 2rem);animation:locModalIn 0.3s ease both;`;
    deniedBanner.innerHTML = `
        <svg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='#f59e0b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' style='flex-shrink:0'><path d='M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z'/><circle cx='12' cy='10' r='3'/></svg>
        <span style='flex:1;color:#cbd5e1;'>Location access is limited. Nearby features disabled.</span>
        <button id='banner-retry-btn' style='background:#059669;color:white;border:none;border-radius:0.5rem;padding:0.35rem 0.75rem;font-size:0.8rem;font-weight:700;cursor:pointer;white-space:nowrap;'>Fix</button>
        <button id='banner-close-btn' style='background:transparent;border:none;color:#94a3b8;cursor:pointer;font-size:1.1rem;padding:0.1rem 0.25rem;'>✕</button>
    `;
    document.body.appendChild(deniedBanner);

    // ─── Initialize Google Map ────────────────────────────────────────────────────
    const map = new google.maps.Map(mapElement, {
        center: { lat: 16.916, lng: 121.575 },
        zoom: 12,
        mapTypeId: 'roadmap',
        mapTypeControl: false,
        streetViewControl: false,
        fullscreenControl: true,
        zoomControl: true,
        zoomControlOptions: { position: google.maps.ControlPosition.LEFT_TOP },
        styles: [
            { featureType: 'poi', elementType: 'labels', stylers: [{ visibility: 'off' }] },
            { featureType: 'transit', elementType: 'labels.icon', stylers: [{ visibility: 'off' }] },
        ],
    });

    // ─── Routing ──────────────────────────────────────────────────────────────────
    const directionsService  = new google.maps.DirectionsService();
    const directionsRenderer = new google.maps.DirectionsRenderer({
        suppressMarkers: true,
        polylineOptions: { strokeColor: '#059669', strokeWeight: 6, strokeOpacity: 0.85 },
    });
    directionsRenderer.setMap(map);

    let activeRouteShop  = null;
    let routeActive      = false;

    const routeBar      = document.getElementById('route-active-bar');
    const routeLabel    = document.getElementById('route-label');
    const routeMeta     = document.getElementById('route-meta');
    const clearRouteBtn = document.getElementById('clear-route-btn');
    const getDirectionsBtn = document.getElementById('get-directions-btn');

    function formatDuration(seconds) {
        const h = Math.floor(seconds / 3600);
        const m = Math.floor((seconds % 3600) / 60);
        return h > 0 ? `${h}h ${m}m` : `${m} min`;
    }

    function formatDistance(meters) {
        return meters >= 1000 ? `${(meters / 1000).toFixed(1)} km` : `${Math.round(meters)} m`;
    }

    function clearRoute() {
        directionsRenderer.setDirections({ routes: [] });
        routeActive     = false;
        activeRouteShop = null;
        if (routeBar) { routeBar.classList.add('hidden'); }
        if (getDirectionsBtn) {
            getDirectionsBtn.innerHTML = `<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'><polygon points='3 11 22 2 13 21 11 13 3 11'/></svg> Get Directions`;
            getDirectionsBtn.classList.remove('bg-red-500', 'hover:bg-red-600');
            getDirectionsBtn.classList.add('bg-emerald-600', 'hover:bg-emerald-700');
        }
    }

    function startRoute(shop) {
        if (!userLatLng) {
            alert('Location access is required to get directions. Please enable location first.');
            return;
        }
        activeRouteShop = shop;
        routeActive = true;

        if (routeBar) {
            routeLabel.textContent = `Directions to ${shop.name}`;
            routeMeta.textContent  = 'Calculating route…';
            routeBar.classList.remove('hidden');
        }

        if (getDirectionsBtn) {
            getDirectionsBtn.innerHTML = `<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'><line x1='18' y1='6' x2='6' y2='18'/><line x1='6' y1='6' x2='18' y2='18'/></svg> Clear Route`;
            getDirectionsBtn.classList.remove('bg-emerald-600', 'hover:bg-emerald-700');
            getDirectionsBtn.classList.add('bg-red-500', 'hover:bg-red-600');
        }

        directionsService.route({
            origin:      new google.maps.LatLng(userLatLng.lat, userLatLng.lng),
            destination: new google.maps.LatLng(shop.latitude, shop.longitude),
            travelMode:  google.maps.TravelMode.DRIVING,
        }, (result, status) => {
            if (status === 'OK') {
                directionsRenderer.setDirections(result);
                const leg = result.routes[0].legs[0];
                if (routeLabel) { routeLabel.textContent = `To: ${shop.name}`; }
                if (routeMeta)  { routeMeta.textContent  = `${leg.distance.text}  ·  ${leg.duration.text} by road`; }
            } else {
                if (routeLabel) { routeLabel.textContent = 'Route not available'; }
                if (routeMeta)  { routeMeta.textContent  = 'Could not calculate a route.'; }
            }
        });
    }

    if (clearRouteBtn) { clearRouteBtn.addEventListener('click', clearRoute); }
    if (getDirectionsBtn) {
        getDirectionsBtn.addEventListener('click', () => {
            const panelShop = window._currentPanelShop;
            if (routeActive && panelShop && activeRouteShop && activeRouteShop.id === panelShop.id) {
                clearRoute();
            } else if (panelShop) {
                startRoute(panelShop);
            }
            if (window.innerWidth < 1024) {
                const panel = document.getElementById('shop-info-panel');
                if (panel) {
                    panel.classList.add('translate-x-full', 'opacity-0');
                    panel.classList.remove('translate-x-0', 'opacity-100');
                }
            }
        });
    }

    // ─── Locate Me Control ────────────────────────────────────────────────────────
    const locateBtn = document.createElement('button');
    locateBtn.id    = 'locate-me-btn';
    locateBtn.title = 'Center on my location';
    locateBtn.style.cssText = `margin:10px;width:36px;height:36px;border-radius:8px;background:white;border:2px solid rgba(0,0,0,0.2);cursor:pointer;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 6px rgba(0,0,0,0.15);transition:background 0.15s;`;
    locateBtn.innerHTML = `<svg xmlns='http://www.w3.org/2000/svg' width='18' height='18' viewBox='0 0 24 24' fill='none' stroke='#059669' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'><circle cx='12' cy='12' r='3'/><line x1='12' y1='2' x2='12' y2='5'/><line x1='12' y1='19' x2='12' y2='22'/><line x1='2' y1='12' x2='5' y2='12'/><line x1='19' y1='12' x2='22' y2='12'/></svg>`;
    locateBtn.onmouseover = () => { locateBtn.style.background = '#f0fdf4'; };
    locateBtn.onmouseout  = () => { locateBtn.style.background = 'white'; };
    locateBtn.addEventListener('click', () => {
        if (userLatLng) { map.panTo(new google.maps.LatLng(userLatLng.lat, userLatLng.lng)); map.setZoom(15); }
        else { requestUserLocation(); }
    });
    map.controls[google.maps.ControlPosition.LEFT_TOP].push(locateBtn);

    // ─── Classification Config ────────────────────────────────────────────────────
    const CLASSIFICATIONS = {
        trader:      { label: 'Trader / Dealer',   color: '#7c3aed', bgColor: '#ede9fe', innerSvg: `<path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z" stroke-width="1.5"/><line x1="3" y1="6" x2="21" y2="6" stroke-width="1.5"/><path d="M16 10a4 4 0 0 1-8 0" stroke-width="1.5"/>` },
        miller:      { label: 'Miller',             color: '#ea580c', bgColor: '#ffedd5', innerSvg: `<circle cx="12" cy="12" r="3" stroke-width="1.5"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z" stroke-width="1.5"/>` },
        wholesaler:  { label: 'Wholesaler',         color: '#2563eb', bgColor: '#dbeafe', innerSvg: `<line x1="16.5" y1="9.4" x2="7.5" y2="4.21" stroke-width="1.5"/><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z" stroke-width="1.5"/><polyline points="3.27 6.96 12 12.01 20.73 6.96" stroke-width="1.5"/><line x1="12" y1="22.08" x2="12" y2="12" stroke-width="1.5"/>` },
        retailer:    { label: 'Retailer',           color: '#0d9488', bgColor: '#ccfbf1', innerSvg: `<circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6" stroke-width="1.5"/>` },
        government:  { label: "Gov't-Accredited",   color: '#dc2626', bgColor: '#fee2e2', innerSvg: `<line x1="3" y1="22" x2="21" y2="22" stroke-width="1.5"/><line x1="6" y1="18" x2="6" y2="11" stroke-width="1.5"/><line x1="10" y1="18" x2="10" y2="11" stroke-width="1.5"/><line x1="14" y1="18" x2="14" y2="11" stroke-width="1.5"/><line x1="18" y1="18" x2="18" y2="11" stroke-width="1.5"/><polygon points="12 2 20 7 4 7" stroke-width="1.5"/>` },
        cooperative: { label: 'Cooperative',        color: '#d97706', bgColor: '#fef3c7', innerSvg: `<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke-width="1.5"/><circle cx="9" cy="7" r="4" stroke-width="1.5"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75" stroke-width="1.5"/>` },
    };

    const DEFAULT_CLASSIFICATION = {
        label: 'Buyer', color: '#3b82f6', bgColor: '#dbeafe',
        innerSvg: `<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" stroke-width="1.5"/><polyline points="9 22 9 12 15 12 15 22" stroke-width="1.5"/>`,
    };

    function getClassificationConfig(classification) {
        return CLASSIFICATIONS[classification] || DEFAULT_CLASSIFICATION;
    }

    // ─── Shop Info Panel ──────────────────────────────────────────────────────────
    const panel     = document.getElementById('shop-info-panel');
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
        const a = Math.sin(dLat / 2) ** 2 + Math.cos((lat1 * Math.PI) / 180) * Math.cos((lat2 * Math.PI) / 180) * Math.sin(dLon / 2) ** 2;
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    }

    let markers   = [];
    let shopsData = [];
    let userLatLng = null;
    let userMarker = null;
    let activeInfoWindow = null;
    const markerMap   = new Map();
    const viewedShops = new Set();

    const cropFilter           = document.getElementById('crop-filter');
    const classificationFilter = document.getElementById('classification-filter');
    const shopSearch           = document.getElementById('shop-search');
    const searchResults        = document.getElementById('shop-search-results');

    // ─── Geolocation ─────────────────────────────────────────────────────────────
    function placeUserMarker(lat, lng) {
        if (userMarker) { userMarker.setMap(null); }

        const pulseDiv = document.createElement('div');
        pulseDiv.innerHTML = `
            <div style="position:relative;width:24px;height:24px;display:flex;align-items:center;justify-content:center;">
                <div style="position:absolute;width:36px;height:36px;border-radius:50%;background:rgba(37,99,235,0.2);top:50%;left:50%;transform:translate(-50%,-50%);animation:userPulse 2s ease-out infinite;"></div>
                <div style="width:14px;height:14px;border-radius:50%;background:#2563eb;border:3px solid white;box-shadow:0 2px 8px rgba(37,99,235,0.6);position:relative;z-index:1;"></div>
            </div>`;

        userMarker = new google.maps.marker.AdvancedMarkerElement({
            map,
            position: { lat, lng },
            title: 'Your Location',
            content: pulseDiv.firstElementChild,
        });
    }

    function showModal(state) {
        const existing = document.getElementById('location-permission-modal');
        if (existing) { existing.remove(); }
        locationModal = buildLocationModal(state);
        document.body.appendChild(locationModal);

        const grantBtn = document.getElementById('grant-location-btn');
        const denyBtn  = document.getElementById('deny-location-btn');

        if (state === 'blocked') {
            if (grantBtn) { grantBtn.addEventListener('click', () => { window.location.reload(); }); }
            if (denyBtn)  { denyBtn.addEventListener('click',  () => { locationModal.remove(); locationModal = null; deniedBanner.style.display = 'flex'; showNearbyUnavailable(); }); }
        } else {
            if (grantBtn) {
                grantBtn.addEventListener('click', () => {
                    grantBtn.textContent = 'Requesting...'; grantBtn.style.opacity = '0.7'; grantBtn.disabled = true;
                    requestUserLocation();
                });
            }
            if (denyBtn) { denyBtn.addEventListener('click', () => { onLocationDenied(false); }); }
        }
    }

    function onLocationGranted(pos) {
        userLatLng = { lat: pos.coords.latitude, lng: pos.coords.longitude };
        placeUserMarker(userLatLng.lat, userLatLng.lng);
        map.panTo(new google.maps.LatLng(userLatLng.lat, userLatLng.lng));
        map.setZoom(14);
        if (locationModal) { locationModal.remove(); locationModal = null; }
        deniedBanner.style.display = 'none';
        if (shopsData.length > 0) { populateNearbyShops(shopsData); }
    }

    function onLocationDenied(blocked = false) {
        if (locationModal) { locationModal.remove(); locationModal = null; }
        deniedBanner.style.display = 'flex';
        showNearbyUnavailable();
    }

    function requestUserLocation() {
        if (!navigator.geolocation) { onLocationDenied(false); return; }
        navigator.geolocation.getCurrentPosition(onLocationGranted, (err) => {
            if (err.code === err.PERMISSION_DENIED) { showModal('blocked'); showNearbyUnavailable(); }
            else { onLocationDenied(false); }
        }, { enableHighAccuracy: true, timeout: 10000 });
    }

    function checkPermissionState() {
        if (!navigator.permissions) { showModal('prompt'); return; }
        navigator.permissions.query({ name: 'geolocation' }).then((result) => {
            if (result.state === 'granted')   { requestUserLocation(); }
            else if (result.state === 'denied') { showModal('blocked'); }
            else                               { showModal('prompt'); }

            result.addEventListener('change', () => {
                if (result.state === 'granted' && !userLatLng) {
                    const existing = document.getElementById('location-permission-modal');
                    if (existing) { existing.remove(); }
                    requestUserLocation();
                } else if (result.state === 'denied') {
                    showModal('blocked');
                }
            });
        }).catch(() => { showModal('prompt'); });
    }

    checkPermissionState();

    document.getElementById('banner-retry-btn').addEventListener('click', () => {
        deniedBanner.style.display = 'none'; checkPermissionState();
    });
    document.getElementById('banner-close-btn').addEventListener('click', () => {
        deniedBanner.style.display = 'none';
    });

    // ─── Shop Panel ───────────────────────────────────────────────────────────────
    function openShopPanel(shop) {
        if (!panel) { return; }

        if (!viewedShops.has(shop.id)) {
            viewedShops.add(shop.id);
            fetch(`/api/shops/${shop.id}/view`, { method: 'POST' }).catch(() => {});
            shop.views = (shop.views || 0) + 1;
        }

        const cfg = getClassificationConfig(shop.classification);
        const classificationBadge = `<span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:999px;background:${cfg.bgColor};color:${cfg.color};font-size:11px;font-weight:700;letter-spacing:0.04em;"><svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">${cfg.innerSvg}</svg>${cfg.label}</span>`;

        let distanceHtml = '';
        if (userLatLng) {
            const dist = haversineDistance(userLatLng.lat, userLatLng.lng, shop.latitude, shop.longitude);
            distanceHtml = `<div class="shop-info-row"><span class="shop-info-label"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> Distance</span><span class="shop-info-value">${dist.toFixed(2)} km away</span></div>`;
        }

        let cropsHtml = shop.prices && shop.prices.length > 0
            ? shop.prices.map((p) => `<div class="shop-crop-row"><span class="shop-crop-name">${p.crop_name}</span><span class="shop-crop-price">₱${parseFloat(p.price).toFixed(2)}<span class="shop-crop-unit">/kg</span></span></div>`).join('')
            : '<p class="shop-no-crops">No crops listed yet.</p>';

        const subscribersHtml = shop.subscribers_count > 0
            ? `<div class="shop-info-row"><span class="shop-info-label"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg> Farmers</span><span class="shop-info-value">${shop.subscribers_count} subscribed</span></div>`
            : '';

        document.getElementById('panel-shop-name').textContent = shop.name;
        document.getElementById('panel-shop-body').innerHTML = `
            ${shop.photo_url ? `<div style="margin:-1.25rem -1.25rem 1rem;overflow:hidden;"><img src="${shop.photo_url}" alt="${shop.name}" style="width:100%;height:160px;object-fit:cover;display:block;"></div>` : ''}
            <div class="shop-info-section">
                <p class="shop-section-label">Shop Details</p>
                <div style="margin-bottom:8px;">${classificationBadge}</div>
                <div class="shop-info-rows">
                    <div class="shop-info-row"><span class="shop-info-label"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg> Owner</span><span class="shop-info-value">${shop.owner}</span></div>
                    <div class="shop-info-row"><span class="shop-info-label"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg> Location</span><span class="shop-info-value">${shop.address}</span></div>
                    ${distanceHtml}
                    ${subscribersHtml}
                </div>
            </div>
            <div class="shop-info-section">
                <p class="shop-section-label">Crops &amp; Prices</p>
                <div class="shop-crops-list">${cropsHtml}</div>
            </div>
        `;

        window._currentPanelShop = shop;

        if (getDirectionsBtn) {
            const isActiveRoute = routeActive && activeRouteShop && activeRouteShop.id === shop.id;
            if (isActiveRoute) {
                getDirectionsBtn.innerHTML = `<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'><line x1='18' y1='6' x2='6' y2='18'/><line x1='6' y1='6' x2='18' y2='18'/></svg> Clear Route`;
                getDirectionsBtn.classList.remove('bg-emerald-600', 'hover:bg-emerald-700');
                getDirectionsBtn.classList.add('bg-red-500', 'hover:bg-red-600');
            } else {
                getDirectionsBtn.innerHTML = `<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'><polygon points='3 11 22 2 13 21 11 13 3 11'/></svg> Get Directions`;
                getDirectionsBtn.classList.remove('bg-red-500', 'hover:bg-red-600');
                getDirectionsBtn.classList.add('bg-emerald-600', 'hover:bg-emerald-700');
            }
        }

        panel.classList.remove('translate-x-full', 'opacity-0');
        panel.classList.add('translate-x-0', 'opacity-100');
    }

    // ─── Discovery Panels ─────────────────────────────────────────────────────────
    function shopDiscoveryItemHtml(shop, metricHtml) {
        const cfg = getClassificationConfig(shop.classification);
        return `
            <button class="discovery-shop-item w-full px-4 py-3 flex items-center gap-3 hover:bg-slate-50/80 transition-colors text-left border-b border-slate-50 last:border-0" data-shop-id="${shop.id}">
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
                const shop   = shopsData.find((s) => s.id === shopId);
                if (shop) { navigateToShop(shop); }
            });
        });
    }

    function emptyDiscoveryHtml(message) {
        return `<div class="px-4 py-6 text-center text-xs text-slate-400">${message}</div>`;
    }

    function populateNearbyShops(shops) {
        const el = document.getElementById('nearby-shops-list');
        if (!el || !userLatLng) { return; }
        const sorted = shops
            .filter((s) => parseFloat(s.latitude) !== 0 && parseFloat(s.longitude) !== 0)
            .map((s) => ({ ...s, _distance: haversineDistance(userLatLng.lat, userLatLng.lng, s.latitude, s.longitude) }))
            .sort((a, b) => a._distance - b._distance).slice(0, 6);
        if (sorted.length === 0) { el.innerHTML = emptyDiscoveryHtml('No shops found nearby.'); return; }
        el.innerHTML = sorted.map((s) => shopDiscoveryItemHtml(s, `${s._distance.toFixed(1)} km`)).join('');
        attachDiscoveryClickHandlers();
    }

    function populatePopularShops(shops) {
        const el = document.getElementById('popular-shops-list');
        if (!el) { return; }
        const sorted = [...shops].filter((s) => parseFloat(s.latitude) !== 0 && parseFloat(s.longitude) !== 0)
            .sort((a, b) => (b.subscribers_count + b.views) - (a.subscribers_count + a.views)).slice(0, 6);
        if (sorted.length === 0) { el.innerHTML = emptyDiscoveryHtml('No shops to display.'); return; }
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
        const sorted = [...shops].filter((s) => s.latest_price_at && parseFloat(s.latitude) !== 0 && parseFloat(s.longitude) !== 0)
            .sort((a, b) => new Date(b.latest_price_at) - new Date(a.latest_price_at)).slice(0, 6);
        if (sorted.length === 0) { el.innerHTML = emptyDiscoveryHtml('No recent price updates.'); return; }
        el.innerHTML = sorted.map((s) => {
            const date = new Date(s.latest_price_at);
            const diffDays = Math.floor((new Date() - date) / (1000 * 60 * 60 * 24));
            const label = diffDays === 0 ? 'Today' : diffDays === 1 ? 'Yesterday' : `${diffDays}d ago`;
            return shopDiscoveryItemHtml(s, label);
        }).join('');
        attachDiscoveryClickHandlers();
    }

    function populatePriceLeaderShops(shops) {
        const el = document.getElementById('best-price-shops-list');
        if (!el) { return; }
        const shopsWithMax = shops.filter((s) => s.prices && s.prices.length > 0 && parseFloat(s.latitude) !== 0 && parseFloat(s.longitude) !== 0)
            .map((s) => ({ ...s, _maxPrice: Math.max(...s.prices.map((p) => parseFloat(p.price))) }))
            .sort((a, b) => b._maxPrice - a._maxPrice).slice(0, 6);
        if (shopsWithMax.length === 0) { el.innerHTML = emptyDiscoveryHtml('No price data available.'); return; }
        el.innerHTML = shopsWithMax.map((s) => shopDiscoveryItemHtml(s, `₱${s._maxPrice.toFixed(2)}`)).join('');
        attachDiscoveryClickHandlers();
    }

    function showNearbyUnavailable() {
        const el = document.getElementById('nearby-shops-list');
        if (!el) { return; }
        el.innerHTML = `
            <div class="px-4 py-6 text-center">
                <p class="text-xs text-slate-400 mb-2">Allow location access to see nearby shops</p>
                <button id="retry-geolocation" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 transition-colors">Enable Location ↗</button>
            </div>`;
        const retryBtn = document.getElementById('retry-geolocation');
        if (retryBtn) { retryBtn.addEventListener('click', () => { requestUserLocation(); }); }
    }

    // ─── Marker SVG Builder ───────────────────────────────────────────────────────
    function buildMarkerSvg(classification) {
        const cfg = getClassificationConfig(classification);
        return `
            <svg viewBox="0 0 40 50" width="40" height="50" xmlns="http://www.w3.org/2000/svg" style="filter: drop-shadow(0px 3px 5px rgba(0,0,0,0.35));">
                <path d="M20 0C11.163 0 4 7.163 4 16c0 10.917 13.393 27.915 15.13 30.018a1.2 1.2 0 0 0 1.74 0C22.607 43.915 36 26.917 36 16 36 7.163 28.837 0 20 0z" fill="${cfg.color}"/>
                <circle cx="20" cy="16" r="10" fill="white"/>
                <g transform="translate(20,16) scale(0.55) translate(-12,-12)" stroke="${cfg.color}" fill="none" stroke-linecap="round" stroke-linejoin="round">${cfg.innerSvg}</g>
            </svg>`;
    }

    // ─── Render Markers ───────────────────────────────────────────────────────────
    function renderMarkers(shops, filterCrop = 'all', filterClassification = 'all') {
        markers.forEach((m) => { m.map = null; });
        markers = [];
        markerMap.clear();
        if (activeInfoWindow) { activeInfoWindow.close(); activeInfoWindow = null; }

        shops.forEach((shop) => {
            if (filterClassification !== 'all' && shop.classification !== filterClassification) { return; }
            if (parseFloat(shop.latitude) === 0 && parseFloat(shop.longitude) === 0) { return; }

            let hasPrice = false;
            let cropInfoHtml = '';

            if (shop.prices && shop.prices.length > 0) {
                shop.prices.forEach((p) => {
                    const baseCropName = p.crop_name.split(' (')[0];
                    if (filterCrop === 'all' || baseCropName === filterCrop) {
                        hasPrice = true;
                        cropInfoHtml += `<div class="flex justify-between border-b border-base-200 py-1"><span class="font-medium">${p.crop_name}</span><span class="font-bold text-primary">₱${parseFloat(p.price).toFixed(2)}/kg</span></div><div class="text-xs text-base-content/60 text-right mb-2">Updated: ${p.date}</div>`;
                    }
                });
            }

            if (filterCrop !== 'all' && !hasPrice) { return; }

            const cfg = getClassificationConfig(shop.classification);
            const markerDiv = document.createElement('div');
            markerDiv.innerHTML = buildMarkerSvg(shop.classification);

            const marker = new google.maps.marker.AdvancedMarkerElement({
                map,
                position: { lat: parseFloat(shop.latitude), lng: parseFloat(shop.longitude) },
                title: shop.name,
                content: markerDiv.firstElementChild,
            });

            const popupContent = `
                <div style="padding:12px 16px;min-width:200px;font-family:inherit;">
                    <h3 style="font-weight:700;font-size:1rem;margin:0 0 4px;color:#1e293b;">${shop.name}</h3>
                    <p style="font-size:0.8rem;color:#64748b;margin:0 0 10px;line-height:1.4;">${shop.address}</p>
                    <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:999px;background:${cfg.bgColor};color:${cfg.color};font-size:11px;font-weight:700;letter-spacing:0.04em;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">${cfg.innerSvg}</svg>
                        ${cfg.label}
                    </span>
                    <div style="margin-top:10px;font-size:0.75rem;color:#64748b;">Owner: ${shop.owner}</div>
                    <button id="popup-view-${shop.id}" style="display:block;width:100%;margin-top:10px;padding:8px;border-radius:8px;background:#059669;color:white;border:none;font-weight:700;font-size:0.85rem;cursor:pointer;">View Shop Info &amp; Prices</button>
                    <button id="popup-close-${shop.id}" style="display:block;width:100%;margin-top:6px;padding:6px;border-radius:8px;background:#f1f5f9;color:#64748b;border:none;font-weight:600;font-size:0.85rem;cursor:pointer;">Close</button>
                </div>`;

            const infoWindow = new google.maps.InfoWindow({
                content: popupContent,
                disableAutoPan: false,
            });

            marker.addListener('click', () => {
                if (activeInfoWindow) { activeInfoWindow.close(); }
                infoWindow.open({ anchor: marker, map });
                activeInfoWindow = infoWindow;

                // Wire buttons after InfoWindow DOM renders
                setTimeout(() => {
                    const viewBtn  = document.getElementById(`popup-view-${shop.id}`);
                    const closeBtn = document.getElementById(`popup-close-${shop.id}`);
                    if (viewBtn)  { viewBtn.onclick  = () => { infoWindow.close(); openShopPanel(shop); }; }
                    if (closeBtn) { closeBtn.onclick = () => { infoWindow.close(); }; }
                }, 100);
            });

            markers.push(marker);
            markerMap.set(shop.id, { marker, infoWindow });
        });
    }

    // ─── Search ───────────────────────────────────────────────────────────────────
    function searchShops(query, shops) {
        const resultsContainer = searchResults.querySelector('.space-y-2');
        resultsContainer.innerHTML = '';
        if (!query.trim()) { searchResults.classList.add('hidden'); return; }

        const filtered = shops.filter(
            (shop) => shop.name.toLowerCase().includes(query.toLowerCase()) || shop.address.toLowerCase().includes(query.toLowerCase())
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
                <div class="flex items-center gap-2"><span style="width:8px;height:8px;border-radius:50%;background:${cfg.color};flex-shrink:0;display:inline-block;"></span><div class="font-semibold text-sm">${shop.name}</div></div>
                <div class="text-xs text-base-content/70 ml-4">${shop.address}</div>`;
            resultDiv.addEventListener('click', () => { shopSearch.value = ''; searchResults.classList.add('hidden'); navigateToShop(shop); });
            resultsContainer.appendChild(resultDiv);
        });

        searchResults.classList.remove('hidden');
    }

    function navigateToShop(shop) {
        map.panTo(new google.maps.LatLng(parseFloat(shop.latitude), parseFloat(shop.longitude)));
        map.setZoom(16);

        if (window.innerWidth < 1024) {
            setTimeout(() => {
                const mapContainer = document.getElementById('price-map');
                if (mapContainer) {
                    const y = mapContainer.getBoundingClientRect().top + window.scrollY - 80;
                    window.scrollTo({ top: y, behavior: 'smooth' });
                }
            }, 250);
        }

        if (markerMap.has(shop.id)) {
            const { marker, infoWindow } = markerMap.get(shop.id);
            if (activeInfoWindow) { activeInfoWindow.close(); }
            infoWindow.open({ anchor: marker, map });
            activeInfoWindow = infoWindow;
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

            if (cropFilter)           { cropFilter.addEventListener('change',           () => applyFilters(data)); }
            if (classificationFilter) { classificationFilter.addEventListener('change', () => applyFilters(data)); }
            if (shopSearch)           { shopSearch.addEventListener('input', (e) => searchShops(e.target.value, data)); }
        })
        .catch((err) => console.error('❌ Error fetching shop data:', err));

    function applyFilters(shops) {
        const cropVal  = cropFilter           ? cropFilter.value           : 'all';
        const classVal = classificationFilter ? classificationFilter.value : 'all';
        renderMarkers(shops, cropVal, classVal);
    }
};
