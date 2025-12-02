import React, { useEffect, useMemo, useState } from 'react';
import { FiMapPin } from 'react-icons/fi';

let leafletLoader;

const ensureLeaflet = () => {
    if (window.L) {
        return Promise.resolve(window.L);
    }

    if (leafletLoader) {
        return leafletLoader;
    }

    leafletLoader = new Promise((resolve, reject) => {
        const existing = document.querySelector('script[data-leaflet]');
        if (existing) {
            existing.addEventListener('load', () => resolve(window.L));
            existing.addEventListener('error', reject);
            return;
        }

        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
        document.head.appendChild(link);

        const script = document.createElement('script');
        script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
        script.async = true;
        script.dataset.leaflet = 'true';
        script.onload = () => resolve(window.L);
        script.onerror = reject;
        document.body.appendChild(script);
    });

    return leafletLoader;
};

const MapPicker = ({ lat, lng, onChange, height = '300px' }) => {
    const [ready, setReady] = useState(false);
    const mapId = useMemo(() => `map-picker-${Math.random().toString(36).slice(2, 8)}`, []);
    const [mapInstance, setMapInstance] = useState(null);
    const [markerInstance, setMarkerInstance] = useState(null);

    useEffect(() => {
        ensureLeaflet()
            .then((L) => {
                if (!mapInstance) {
                    const map = L.map(mapId).setView([lat, lng], 13);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '© OpenStreetMap'
                    }).addTo(map);

                    const marker = L.marker([lat, lng], { draggable: true }).addTo(map);

                    marker.on('dragend', function (event) {
                        const position = event.target.getLatLng();
                        onChange(position.lat, position.lng);
                    });

                    map.on('click', function (e) {
                        marker.setLatLng(e.latlng);
                        onChange(e.latlng.lat, e.latlng.lng);
                    });

                    setMapInstance(map);
                    setMarkerInstance(marker);
                    setReady(true);
                }
            })
            .catch((e) => {
                console.error("Failed to load Leaflet", e);
                setReady(false);
            });

        return () => {
            // Cleanup if needed, but usually keeping the map instance alive is fine for this simple use case
            // or we can remove it if the component unmounts
            if (mapInstance) {
                mapInstance.remove();
                setMapInstance(null);
            }
        };
    }, [mapId]); // Only run once on mount/mapId creation

    // Update marker position if props change externally (and not by drag)
    useEffect(() => {
        if (markerInstance && mapInstance) {
            const currentLatLng = markerInstance.getLatLng();
            if (currentLatLng.lat !== lat || currentLatLng.lng !== lng) {
                markerInstance.setLatLng([lat, lng]);
                mapInstance.setView([lat, lng], mapInstance.getZoom());
            }
        }
    }, [lat, lng, markerInstance, mapInstance]);

    return (
        <div className="relative rounded-xl overflow-hidden border border-border">
            {!ready && (
                <div className="absolute inset-0 flex items-center justify-center bg-surface-highlight text-text-secondary">
                    <FiMapPin className="animate-bounce mr-2" /> Memuat Peta...
                </div>
            )}
            <div id={mapId} style={{ width: '100%', height }} />
            <div className="absolute bottom-4 left-4 right-4 bg-surface/90 backdrop-blur p-3 rounded-lg border border-border text-xs text-text-secondary z-[1000] shadow-lg">
                Geser pin atau klik pada peta untuk menyesuaikan lokasi.
            </div>
        </div>
    );
};

export default MapPicker;
