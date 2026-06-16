<x-app-layout>
    @push('styles')
        <link
            rel="stylesheet"
            href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
            crossorigin=""
        />

        <style>
            /* Ensure the map uses the full available width and a large height. */
            #aedMap {
                width: 100%;
                height: 720px;
                border-radius: 0.5rem;
            }
        </style>
    @endpush

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('AED Kaart') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="container">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div id="aedMap" class="bg-white"></div>
                        <p class="text-muted small mb-0 mt-2" id="aedLoadStatus">
                            Laden AED locaties...
                        </p>

                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script
            src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            crossorigin=""
        ></script>


        <script>
            // Leaflet initialization
            const initialCenter = [51.97, 4.95];

            const initialZoom = 13;

            // Note: we geocode addresses client-side using Nominatim.
            const nominatimEndpoint = 'https://nominatim.openstreetmap.org/search';

            // Map base layer
            const map = L.map('aedMap', {
                zoomControl: true
            }).setView(initialCenter, initialZoom);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            // Store markers so we can fit bounds once we have them.
            const markers = [];

            function sleep(ms) {
                return new Promise(resolve => setTimeout(resolve, ms));
            }

            async function geocodeWithNominatim(query) {
                const url = new URL(nominatimEndpoint);
                url.searchParams.set('q', query);
                url.searchParams.set('format', 'json');
                url.searchParams.set('addressdetails', '0');
                url.searchParams.set('limit', '1');
                url.searchParams.set('countrycodes', 'nl');

                const response = await fetch(url.toString(), {
                    method: 'GET',
                    headers: {
                        // Nominatim requires a valid User-Agent/Referer.
                        // In browsers, we cannot fully control User-Agent, but we can at least send a Referer.
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Nominatim request failed with status ' + response.status);
                }

                const results = await response.json();
                if (!Array.isArray(results) || results.length === 0) {
                    return null;
                }

                const top = results[0];
                const lat = parseFloat(top.lat);
                const lon = parseFloat(top.lon);

                if (!Number.isFinite(lat) || !Number.isFinite(lon)) {
                    return null;
                }

                return { lat, lon, displayName: top.display_name || null };
            }

            function escapeHtml(text) {
                return String(text)
                    .replaceAll('&', '&amp;')
                    .replaceAll('<', '<')
                    .replaceAll('>', '>')
                    .replaceAll('"', '"')
                    .replaceAll("'", '&#039;');
            }

            // Load AED data from backend and geocode each address.
            (async function loadAeds() {
                let payload;
                try {
                    const response = await fetch(@json(route('aeds.map.locations')));
                    if (!response.ok) {
                        throw new Error('Failed to load AED locations: HTTP ' + response.status);
                    }
                    payload = await response.json();
                } catch (e) {
                    console.error(e);
                    const loadStatusEl = document.getElementById('aedLoadStatus');
                    if (loadStatusEl) {
                        loadStatusEl.textContent = 'AED locaties konden niet worden geladen.';
                    }
                    return;
                }

                const aeds = payload?.aeds ?? [];

                // Default view stays on Lopik/Benschop in case no markers are added.
                const loadStatusEl = document.getElementById('aedLoadStatus');
                if (loadStatusEl) {
                    loadStatusEl.textContent = 'Laden AED locaties...';
                }

                let bounds = null;
                let geocodedCount = 0;
                let geocodeFailedCount = 0;

                // Render markers
                for (let i = 0; i < aeds.length; i++) {
                    const aed = aeds[i];


                    const fullAddress = [
                        aed.adres,
                        aed.huisnummer,
                        aed.plaats,
                        'Nederland'
                    ].filter(Boolean).join(' ');

                    // Delay between Nominatim requests to reduce rate limit issues.
                    // (700-900ms per requirement)
                    await sleep(700 + Math.floor(Math.random() * 200));

                    try {
                        const geo = await geocodeWithNominatim(fullAddress);
                        if (!geo) {
                            continue;
                        }

                        const popupHtml = [
                            `<div class="small">`,
                            `<div class="fw-bold">AED #${escapeHtml(aed.id)}</div>`,
                            `<div>Type: ${escapeHtml(aed.aed_type ?? '-')}</div>`,
                            `<div><span class="text-muted">Adres:</span><br>${escapeHtml(fullAddress)}</div>`,
                            `<div class="mt-1">${escapeHtml(aed.beschrijving ? aed.beschrijving : '')}</div>`,
                            `<div class="mt-2"><a href="/aeds/${encodeURIComponent(aed.id)}" class="link-primary">Bekijk details</a></div>`,

                            `</div>`
                        ].join('');

                        const marker = L.marker([geo.lat, geo.lon]).addTo(map);
                        marker.bindPopup(popupHtml);
                        markers.push(marker);
                        geocodedCount++;

                        if (!bounds) {
                            bounds = L.latLngBounds([geo.lat, geo.lon]);
                        } else {
                            bounds.extend([geo.lat, geo.lon]);
                        }
                    } catch (e) {
                        console.warn('Geocoding failed for AED #' + aed.id, e);
                        geocodeFailedCount++;
                        continue;
                    }
                }

                // Update load status after all geocoding attempts.
                if (loadStatusEl) {
                    if (aeds.length === 0) {
                        loadStatusEl.textContent = 'Geen AED\'s gevonden.';
                    } else if (geocodedCount > 0 && geocodeFailedCount === 0) {
                        loadStatusEl.textContent = `AED locaties geladen: ${geocodedCount}`;
                    } else if (geocodedCount > 0 && geocodeFailedCount > 0) {
                        loadStatusEl.textContent = `AED locaties geladen: ${geocodedCount} (met ${geocodeFailedCount} geocode fouten)`;
                    } else {
                        loadStatusEl.textContent = `AED locaties niet te geocoden (${geocodeFailedCount} fouten).`;
                    }
                }

                if (bounds && bounds.isValid()) {
                    // Fit all markers to view, but keep it reasonably zoomed.
                    map.fitBounds(bounds.pad(0.15));
                } else {
                    map.setView(initialCenter, initialZoom);
                }
            })();
        </script>
    @endpush
</x-app-layout>

