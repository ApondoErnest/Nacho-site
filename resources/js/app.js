
import Alpine from 'alpinejs';

const escapeHtml = (value) => String(value ?? '')
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');

const normalizeSearch = (value) => String(value ?? '')
    .toLowerCase()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '');

const toNumber = (value) => {
    const number = Number(value);

    return Number.isFinite(number) ? number : null;
};

const hasCoordinates = (center) => toNumber(center.latitude) !== null && toNumber(center.longitude) !== null;

const distanceKm = (origin, destination) => {
    const earthRadiusKm = 6371;
    const toRadians = (degrees) => degrees * Math.PI / 180;
    const lat1 = toRadians(origin.latitude);
    const lat2 = toRadians(destination.latitude);
    const deltaLat = toRadians(destination.latitude - origin.latitude);
    const deltaLng = toRadians(destination.longitude - origin.longitude);
    const haversine = Math.sin(deltaLat / 2) ** 2
        + Math.cos(lat1) * Math.cos(lat2) * Math.sin(deltaLng / 2) ** 2;

    return earthRadiusKm * 2 * Math.atan2(Math.sqrt(haversine), Math.sqrt(1 - haversine));
};

let leafletPromise;

const loadLeaflet = async () => {
    if (! leafletPromise) {
        leafletPromise = Promise.all([
            import('leaflet'),
            import('leaflet/dist/leaflet.css'),
        ]).then(([leaflet]) => leaflet.default ?? leaflet);
    }

    return leafletPromise;
};

const createMapMarkerIcon = (L, center, classPrefix, selected = false) => {
    const isOperational = center.status === 'operational' || center.is_operational;
    const statusClass = isOperational ? 'operational' : 'coming';
    const markerColor = isOperational ? '#e14a0a' : '#6b7280';
    const fillColor = isOperational ? markerColor : '#f8fafc';
    const strokeColor = isOperational ? '#ffffff' : '#e14a0a';
    const circleColor = isOperational ? '#ffffff' : '#6b7280';
    const selectedClass = selected ? `${classPrefix}-marker--selected` : '';

    return L.divIcon({
        className: `${classPrefix}-marker-icon`,
        html: `
            <span class="${classPrefix}-marker ${classPrefix}-marker--${statusClass} ${selectedClass}">
                <svg viewBox="0 0 40 48" aria-hidden="true" focusable="false">
                    <path fill="${fillColor}" stroke="${strokeColor}" stroke-width="${isOperational ? 3 : 3.8}" d="M20 46C13.4 36.1 6 28.6 6 18.9 6 10.6 12.3 4 20 4s14 6.6 14 14.9C34 28.6 26.6 36.1 20 46Z" />
                    <circle cx="20" cy="18.5" r="5.5" fill="${circleColor}" />
                </svg>
            </span>
        `,
        iconAnchor: [20, 46],
        iconSize: [40, 48],
        popupAnchor: [0, -36],
    });
};

const initContactCentersMap = async () => {
    const mapElement = document.getElementById('contact-centers-map');
    const dataElement = document.getElementById('contact-centers-map-data');

    if (!mapElement || !dataElement || mapElement.dataset.initialized === 'true') {
        return;
    }

    let centers = [];

    try {
        centers = JSON.parse(dataElement.textContent || '[]');
    } catch {
        centers = [];
    }

    centers = centers.filter((center) => Number.isFinite(Number(center.latitude)) && Number.isFinite(Number(center.longitude)));

    if (centers.length === 0) {
        return;
    }

    mapElement.dataset.initialized = 'true';

    const loadingElement = document.querySelector('[data-contact-map-loading]');

    try {
        const L = await loadLeaflet();
        const map = L.map(mapElement, {
            scrollWheelZoom: false,
            zoomControl: true,
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
            maxZoom: 18,
        }).addTo(map);

        const bounds = L.latLngBounds([]);

        centers.forEach((center) => {
            const latitude = Number(center.latitude);
            const longitude = Number(center.longitude);
            const isOperational = center.status === 'operational';
            const statusClass = isOperational ? 'operational' : 'coming';
            const popupRows = [
                center.address,
                [center.city, center.region].filter(Boolean).join(', '),
                center.status_label,
                center.is_approximate ? center.approximate_label : null,
            ].filter(Boolean);
            const tooltipPlacement = {
                'nacho-nkwen-bamenda': { direction: 'top', offset: [0, -48] },
                'nacho-mankon-bamenda': { direction: 'right', offset: [14, -28] },
            }[center.slug] ?? { direction: 'right', offset: [14, -28] };

            L.marker([latitude, longitude], {
                icon: createMapMarkerIcon(L, center, 'contact-leaflet'),
                title: center.name,
            })
                .addTo(map)
                .bindPopup(`
                    <div class="contact-map-popup">
                        <strong>${escapeHtml(center.name)}</strong>
                        ${popupRows.map((row, index) => `<span class="${index === 2 ? 'contact-map-popup-status' : ''}">${escapeHtml(row)}</span>`).join('')}
                    </div>
                `)
                .bindTooltip(escapeHtml(center.name), {
                    className: `contact-leaflet-tooltip contact-leaflet-tooltip--${statusClass}`,
                    permanent: true,
                    ...tooltipPlacement,
                });

            bounds.extend([latitude, longitude]);
        });

        if (bounds.isValid()) {
            map.fitBounds(bounds, {
                maxZoom: 8,
                paddingBottomRight: [44, 44],
                paddingTopLeft: [44, 104],
            });
        }

        loadingElement?.classList.add('is-hidden');

        window.setTimeout(() => {
            map.invalidateSize();

            if (bounds.isValid()) {
                map.fitBounds(bounds, {
                    maxZoom: 8,
                    paddingBottomRight: [44, 44],
                    paddingTopLeft: [44, 104],
                });
            }
        }, 200);
    } catch {
        if (loadingElement) {
            loadingElement.textContent = 'Map could not load.';
        }
    }
};

window.Alpine = Alpine;

Alpine.data('centersLocator', (config = {}) => ({
    centers: Array.isArray(config.centers) ? config.centers : [],
    labels: config.labels ?? {},
    query: '',
    region: 'all',
    service: 'all',
    viewMode: 'list',
    selectedSlug: null,
    map: null,
    leaflet: null,
    markerLayer: null,
    markers: {},
    mapLoading: false,
    mapFailed: false,
    statusMessage: '',
    locationLoading: false,

    get filteredCenters() {
        const query = normalizeSearch(this.query).trim();

        return this.centers.filter((center) => {
            const matchesQuery = query === '' || normalizeSearch(center.search_index).includes(query);
            const matchesRegion = this.region === 'all' || center.region === this.region;
            const matchesService = this.service === 'all' || (center.services ?? []).includes(this.service);

            return matchesQuery && matchesRegion && matchesService;
        });
    },

    get currentCenters() {
        return this.filteredCenters.filter((center) => center.is_operational);
    },

    get selectedCenter() {
        return this.centers.find((center) => center.slug === this.selectedSlug)
            || this.currentCenters[0]
            || this.filteredCenters[0]
            || null;
    },

    get resultCountLabel() {
        return String(this.labels.result_count ?? ':count centers found')
            .replace(':count', this.filteredCenters.length);
    },

    init() {
        this.selectedSlug = this.centers[0]?.slug ?? null;
        this.viewMode = window.matchMedia('(min-width: 1024px)').matches
            ? (config.desktopDefaultView ?? 'map')
            : 'list';

        this.$watch('query', () => this.refreshMapMarkers());
        this.$watch('region', () => this.refreshMapMarkers());
        this.$watch('service', () => this.refreshMapMarkers());

        if (this.viewMode === 'map') {
            this.$nextTick(() => this.ensureMap());
        }
    },

    setViewMode(mode) {
        this.viewMode = mode;

        if (mode === 'map') {
            this.ensureMap();
        }
    },

    resetFilters() {
        this.query = '';
        this.region = 'all';
        this.service = 'all';
        this.statusMessage = '';

        this.$nextTick(() => this.refreshMapMarkers());
    },

    selectCenter(slug) {
        this.selectedSlug = slug;
        this.updateMarkerStyles();

        if (this.viewMode === 'map') {
            this.focusMarker(slug);
        }
    },

    async ensureMap() {
        if (this.mapFailed) {
            this.viewMode = 'list';

            return;
        }

        this.mapLoading = true;

        try {
            this.leaflet = await loadLeaflet();
            await this.$nextTick();

            if (! this.$refs.map) {
                throw new Error('Centers map container is unavailable.');
            }

            if (! this.map) {
                this.map = this.leaflet.map(this.$refs.map, {
                    scrollWheelZoom: false,
                    zoomControl: true,
                });
                this.markerLayer = this.leaflet.layerGroup().addTo(this.map);

                const tileLayer = this.leaflet.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors',
                    maxZoom: 18,
                });
                let hasLoadedTile = false;

                tileLayer.on('tileload', () => {
                    hasLoadedTile = true;
                });
                tileLayer.on('tileerror', () => {
                    if (! hasLoadedTile) {
                        this.failMap();
                    }
                });
                tileLayer.addTo(this.map);
            }

            this.refreshMapMarkers();
            this.mapLoading = false;
        } catch {
            this.failMap();
        }
    },

    failMap() {
        this.mapFailed = true;
        this.mapLoading = false;
        this.viewMode = 'list';
        this.statusMessage = this.labels.map_unavailable ?? 'The map could not load.';
    },

    refreshMapMarkers() {
        if (! this.map || ! this.leaflet || this.mapFailed) {
            return;
        }

        if (! this.markerLayer) {
            this.markerLayer = this.leaflet.layerGroup().addTo(this.map);
        }

        this.markerLayer.clearLayers();
        this.markers = {};

        const bounds = this.leaflet.latLngBounds([]);
        const seenSlugs = new Set();

        this.filteredCenters
            .filter(hasCoordinates)
            .filter((center) => {
                if (seenSlugs.has(center.slug)) {
                    return false;
                }

                seenSlugs.add(center.slug);

                return true;
            })
            .forEach((center) => {
                const latitude = toNumber(center.latitude);
                const longitude = toNumber(center.longitude);
                const marker = this.leaflet.marker([latitude, longitude], {
                    icon: createMapMarkerIcon(this.leaflet, center, 'centers-leaflet', center.slug === this.selectedSlug),
                    title: center.name,
                });
                const popupRows = [
                    center.address_line,
                    center.status_label,
                    center.status_note,
                ].filter(Boolean);

                marker
                    .addTo(this.markerLayer)
                    .bindPopup(`
                        <div class="centers-map-popup">
                            <strong>${escapeHtml(center.name)}</strong>
                            ${popupRows.map((row, index) => `<span class="${index === 1 ? 'centers-map-popup-status' : ''}">${escapeHtml(row)}</span>`).join('')}
                            <a href="${escapeHtml(center.maps_url)}" target="_blank" rel="noopener">${escapeHtml(this.labels.directions ?? 'Get directions')}</a>
                        </div>
                    `)
                    .on('click', () => {
                        this.selectedSlug = center.slug;
                        this.updateMarkerStyles();
                    });

                this.markers[center.slug] = marker;
                bounds.extend([latitude, longitude]);
            });

        this.map.invalidateSize();

        if (bounds.isValid()) {
            this.map.fitBounds(bounds, {
                maxZoom: 8,
                padding: [34, 34],
            });
        }

        if (this.selectedSlug && this.markers[this.selectedSlug]) {
            this.focusMarker(this.selectedSlug, false);
        }
    },

    updateMarkerStyles() {
        if (! this.leaflet || ! this.markers) {
            return;
        }

        this.filteredCenters
            .filter(hasCoordinates)
            .forEach((center) => {
                const marker = this.markers[center.slug];

                if (marker) {
                    marker.setIcon(createMapMarkerIcon(this.leaflet, center, 'centers-leaflet', center.slug === this.selectedSlug));
                }
            });
    },

    focusMarker(slug, openPopup = true) {
        const marker = this.markers[slug];

        if (! marker || ! this.map) {
            return;
        }

        this.map.setView(marker.getLatLng(), Math.max(this.map.getZoom(), 10), {
            animate: true,
        });

        if (openPopup) {
            marker.openPopup();
        }
    },

    findNearest() {
        if (! navigator.geolocation) {
            this.statusMessage = this.labels.location_unavailable ?? 'Your browser could not provide a location.';

            return;
        }

        this.locationLoading = true;
        this.statusMessage = this.labels.locating ?? 'Requesting your location...';

        navigator.geolocation.getCurrentPosition(
            (position) => {
                const origin = {
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude,
                };
                const candidates = this.centers.filter((center) => center.is_operational && hasCoordinates(center));
                const nearest = candidates
                    .map((center) => ({
                        center,
                        distance: distanceKm(origin, {
                            latitude: toNumber(center.latitude),
                            longitude: toNumber(center.longitude),
                        }),
                    }))
                    .sort((a, b) => a.distance - b.distance)[0];

                this.locationLoading = false;

                if (! nearest) {
                    this.statusMessage = this.labels.location_unavailable ?? 'Your browser could not provide a location.';

                    return;
                }

                this.query = '';
                this.region = 'all';
                this.service = 'all';
                this.selectedSlug = nearest.center.slug;
                this.statusMessage = String(this.labels.nearest_found ?? ':center is about :distance km away.')
                    .replace(':center', nearest.center.name)
                    .replace(':distance', Math.max(1, Math.round(nearest.distance)));

                this.$nextTick(() => {
                    if (this.viewMode === 'map') {
                        this.ensureMap().then(() => this.focusMarker(nearest.center.slug));
                    } else {
                        document.getElementById(`center-card-${nearest.center.slug}`)
                            ?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                });
            },
            () => {
                this.locationLoading = false;
                this.statusMessage = this.labels.location_denied ?? 'Location permission was not shared.';
            },
            {
                enableHighAccuracy: false,
                maximumAge: 300000,
                timeout: 10000,
            },
        );
    },
}));

Alpine.data('careersVacancies', (config = {}) => ({
    vacancies: Array.isArray(config.vacancies) ? config.vacancies : [],
    hasOpenVacancies: Boolean(config.hasOpenVacancies),
    labels: config.labels ?? {},
    query: '',
    department: 'all',
    center: 'all',
    employmentType: 'all',
    selectedSlug: config.initialSlug ?? null,
    mobileOpenSlug: null,
    statusMessage: '',

    init() {
        if (! this.selectedVacancy && this.vacancies.length > 0) {
            this.selectedSlug = this.vacancies[0].slug;
        }

        this.mobileOpenSlug = this.selectedSlug;

        this.$watch('query', () => this.syncSelectedVacancy());
        this.$watch('department', () => this.syncSelectedVacancy());
        this.$watch('center', () => this.syncSelectedVacancy());
        this.$watch('employmentType', () => this.syncSelectedVacancy());
    },

    get filteredVacancies() {
        const query = normalizeSearch(this.query).trim();

        return this.vacancies.filter((vacancy) => {
            const matchesQuery = query === '' || normalizeSearch(vacancy.search_index).includes(query);
            const matchesDepartment = this.department === 'all' || vacancy.department_key === this.department;
            const matchesCenter = this.center === 'all' || vacancy.center_key === this.center;
            const matchesType = this.employmentType === 'all' || vacancy.employment_type_key === this.employmentType;

            return matchesQuery && matchesDepartment && matchesCenter && matchesType;
        });
    },

    get selectedVacancy() {
        return this.vacancies.find((vacancy) => vacancy.slug === this.selectedSlug) ?? null;
    },

    get hasVacancies() {
        return this.vacancies.length > 0;
    },

    get hasFilteredVacancies() {
        return this.filteredVacancies.length > 0;
    },

    get showingCountLabel() {
        const label = this.hasOpenVacancies
            ? (this.labels.showing_count ?? 'Showing :visible of :total vacancies')
            : (this.labels.showing_profile_count ?? 'Showing :visible of :total vacancy cards');

        return String(label)
            .replace(':visible', this.filteredVacancies.length)
            .replace(':total', this.vacancies.length);
    },

    syncSelectedVacancy() {
        const currentIsVisible = this.filteredVacancies.some((vacancy) => vacancy.slug === this.selectedSlug);

        if (! currentIsVisible) {
            this.selectedSlug = this.filteredVacancies[0]?.slug ?? null;
            this.mobileOpenSlug = this.selectedSlug;
        }
    },

    selectVacancy(slug) {
        this.selectedSlug = slug;
        this.mobileOpenSlug = slug;
        this.statusMessage = '';

        const url = new URL(window.location.href);
        url.searchParams.set('vacancy', slug);
        window.history.replaceState({}, '', url.toString());
    },

    toggleMobileDetails(slug) {
        const shouldClose = this.mobileOpenSlug === slug;

        this.selectVacancy(slug);
        this.mobileOpenSlug = shouldClose ? null : slug;
    },

    resetFilters() {
        this.query = '';
        this.department = 'all';
        this.center = 'all';
        this.employmentType = 'all';
        this.selectedSlug = this.vacancies[0]?.slug ?? null;
        this.mobileOpenSlug = this.selectedSlug;
        this.statusMessage = '';
    },

    canApply(vacancy) {
        return Boolean(vacancy?.mailto) && ['published', 'closing_soon'].includes(vacancy.status);
    },

    statusMessageFor(vacancy) {
        if (! vacancy) {
            return '';
        }

        return this.labels.status_messages?.[vacancy.status] ?? '';
    },

    async shareVacancy(vacancy) {
        if (! vacancy) {
            return;
        }

        const url = new URL(window.location.href);
        url.searchParams.set('vacancy', vacancy.slug);
        const sharePayload = {
            title: vacancy.title,
            text: `${vacancy.title} - ${vacancy.reference}`,
            url: url.toString(),
        };

        if (navigator.share) {
            try {
                await navigator.share(sharePayload);

                return;
            } catch {
                // Fall back to copying below when sharing is cancelled or unavailable.
            }
        }

        if (navigator.clipboard) {
            await navigator.clipboard.writeText(sharePayload.url);
            this.statusMessage = this.labels.share_copied ?? 'Vacancy link copied.';
        }
    },

    printVacancy() {
        window.print();
    },
}));

Alpine.start();

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initContactCentersMap);
} else {
    initContactCentersMap();
}
