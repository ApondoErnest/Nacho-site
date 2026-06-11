

import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

const escapeHtml = (value) => String(value ?? '')
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');

const initContactCentersMap = () => {
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
        const markerColor = isOperational ? '#e14a0a' : '#6b7280';
        const icon = L.divIcon({
            className: 'contact-leaflet-marker-icon',
            html: `
                <span class="contact-leaflet-marker contact-leaflet-marker--${statusClass}">
                    <svg viewBox="0 0 40 48" aria-hidden="true" focusable="false">
                        <path fill="${markerColor}" stroke="#ffffff" stroke-width="3" d="M20 46C13.4 36.1 6 28.6 6 18.9 6 10.6 12.3 4 20 4s14 6.6 14 14.9C34 28.6 26.6 36.1 20 46Z" />
                        <circle cx="20" cy="18.5" r="5.5" fill="#ffffff" />
                    </svg>
                </span>
            `,
            iconAnchor: [20, 46],
            iconSize: [40, 48],
            popupAnchor: [0, -36],
        });

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
            icon,
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
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initContactCentersMap);
} else {
    initContactCentersMap();
}
