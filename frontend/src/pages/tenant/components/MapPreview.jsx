import React, { useEffect, useMemo, useState } from 'react';

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

const MapPreview = ({ lat, lng, name, height = '180px' }) => {
  const [ready, setReady] = useState(false);
  const mapId = useMemo(() => `map-${Math.random().toString(36).slice(2, 8)}`, []);

  useEffect(() => {
    let map;
    ensureLeaflet()
      .then((L) => {
        map = L.map(mapId, { attributionControl: false, zoomControl: false }).setView([lat, lng], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);
        L.marker([lat, lng]).addTo(map).bindPopup(name ?? 'Lokasi');
        setReady(true);
      })
      .catch(() => setReady(false));

    return () => {
      if (map) {
        map.remove();
      }
    };
  }, [lat, lng, mapId, name]);

  return (
    <div style={{ width: '100%', height, position: 'relative' }}>
      {!ready && <div className="muted tiny" style={{ padding: 8 }}>Memuat peta...</div>}
      <div id={mapId} style={{ width: '100%', height: '100%' }} />
    </div>
  );
};

export const toCoords = (lat, lng) => {
  const parsedLat = Number(lat);
  const parsedLng = Number(lng);
  if (Number.isFinite(parsedLat) && Number.isFinite(parsedLng)) {
    return { lat: parsedLat, lng: parsedLng };
  }

  return null;
};

export default MapPreview;
