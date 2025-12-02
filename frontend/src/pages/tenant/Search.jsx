import React, { useEffect, useMemo, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { motion, AnimatePresence } from 'framer-motion';
import { FiSearch, FiFilter, FiMapPin, FiHome, FiDollarSign, FiX, FiCheck, FiAlertCircle } from 'react-icons/fi';
import { fetchTenantProperty, searchTenantPropertiesApi, currentUser } from '../../api/client.js';
import MapPreview, { toCoords } from './components/MapPreview.jsx';

const formatPrice = (value) => (typeof value === 'number' ? `Rp${value.toLocaleString('id-ID')}` : 'Rp0');
const formatText = (text) => text || '—';

const TenantSearch = () => {
  const navigate = useNavigate();
  const [query, setQuery] = useState('');
  const [minPrice, setMinPrice] = useState('');
  const [maxPrice, setMaxPrice] = useState('');
  const [facilities, setFacilities] = useState([]);
  const [results, setResults] = useState([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');
  const [detailModal, setDetailModal] = useState({ open: false, property: null, loading: false, error: '' });
  const [applyModal, setApplyModal] = useState({ open: false, property: null, loading: false, error: '', roomId: null });

  const facilityOptions = useMemo(() => ['wifi', 'ac', 'laundry', 'parking'], []);

  const toggleFacility = (item) => {
    setFacilities((prev) => (prev.includes(item) ? prev.filter((f) => f !== item) : [...prev, item]));
  };

  const openDetail = async (propertyId) => {
    setDetailModal({ open: true, property: null, loading: true, error: '' });
    try {
      const data = await fetchTenantProperty(propertyId);
      setDetailModal({ open: true, property: data, loading: false, error: '' });
    } catch (err) {
      setDetailModal({ open: true, property: null, loading: false, error: 'Gagal memuat detail properti.' });
    }
  };

  const openApply = async (propertyId, roomId = null) => {
    const user = await currentUser();
    if (!user) {
      navigate('/login', {
        state: {
          from: `/tenant/apply/${propertyId}${roomId ? `?room=${roomId}` : ''}`,
        },
      });
      return;
    }
    setApplyModal({ open: true, property: null, loading: true, error: '', roomId });
    try {
      const data = await fetchTenantProperty(propertyId);
      setApplyModal({ open: true, property: data, loading: false, error: '', roomId });
    } catch (err) {
      setApplyModal({ open: true, property: null, loading: false, error: 'Gagal memuat data kamar.', roomId: null });
    }
  };

  const search = async () => {
    setLoading(true);
    setError('');
    try {
      const data = await searchTenantPropertiesApi({
        search: query,
        min_price: minPrice || undefined,
        max_price: maxPrice || undefined,
        facilities,
      });
      setResults(data);
    } catch (err) {
      setError('Gagal memuat pencarian. Coba lagi.');
      setResults([]);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    search();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  return (
    <div className="page pt-32 pb-20">
      <div className="container">
        <div className="text-center max-w-2xl mx-auto mb-12">
          <motion.h1
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            className="text-4xl font-display font-bold mb-4"
          >
            Cari Kost <span className="text-primary">Impianmu</span>
          </motion.h1>
          <motion.p
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ delay: 0.1 }}
            className="text-text-secondary text-lg"
          >
            Temukan tempat tinggal yang nyaman dengan fasilitas lengkap.
          </motion.p>
        </div>

        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.2 }}
          className="card p-6 mb-12 border-primary/20"
        >
          <div className="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div className="md:col-span-2 relative">
              <FiSearch className="absolute left-4 top-1/2 -translate-y-1/2 text-text-secondary" />
              <input
                type="text"
                value={query}
                onChange={(e) => setQuery(e.target.value)}
                placeholder="Cari lokasi atau nama kost..."
                className="w-full bg-surface border border-border rounded-xl py-3 pl-10 pr-4 focus:outline-none focus:border-primary transition-colors"
              />
            </div>
            <div className="relative">
              <FiDollarSign className="absolute left-4 top-1/2 -translate-y-1/2 text-text-secondary" />
              <input
                type="number"
                value={minPrice}
                onChange={(e) => setMinPrice(e.target.value)}
                placeholder="Harga Min"
                className="w-full bg-surface border border-border rounded-xl py-3 pl-10 pr-4 focus:outline-none focus:border-primary transition-colors"
              />
            </div>
            <div className="relative">
              <FiDollarSign className="absolute left-4 top-1/2 -translate-y-1/2 text-text-secondary" />
              <input
                type="number"
                value={maxPrice}
                onChange={(e) => setMaxPrice(e.target.value)}
                placeholder="Harga Max"
                className="w-full bg-surface border border-border rounded-xl py-3 pl-10 pr-4 focus:outline-none focus:border-primary transition-colors"
              />
            </div>
          </div>

          <div className="flex flex-col md:flex-row justify-between items-center gap-4">
            <div className="flex gap-2 flex-wrap">
              {facilityOptions.map((item) => (
                <button
                  key={item}
                  type="button"
                  className={`px-4 py-2 rounded-full text-sm font-medium transition-all border ${facilities.includes(item)
                    ? 'bg-primary text-black border-primary'
                    : 'bg-surface hover:bg-surface-highlight border-border text-text-secondary'
                    }`}
                  onClick={() => toggleFacility(item)}
                >
                  {item.toUpperCase()}
                </button>
              ))}
            </div>
            <div className="flex gap-2 w-full md:w-auto">
              <button
                type="button"
                className="btn ghost flex-1 md:flex-none justify-center"
                onClick={() => { setQuery(''); setMinPrice(''); setMaxPrice(''); setFacilities([]); search(); }}
              >
                Reset
              </button>
              <button
                type="button"
                className="btn primary flex-1 md:flex-none justify-center"
                onClick={search}
                disabled={loading}
              >
                {loading ? 'Mencari...' : 'Cari Kost'}
              </button>
            </div>
          </div>
        </motion.div>

        {error && (
          <div className="p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-500 mb-8 text-center">
            {error}
          </div>
        )}

        {loading ? (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {[1, 2, 3].map((i) => (
              <div key={i} className="card h-96 animate-pulse bg-surface-highlight" />
            ))}
          </div>
        ) : results.length ? (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {results.map((property, index) => (
              <PropertyCard
                key={property.id}
                property={property}
                index={index}
                onDetail={() => openDetail(property.id)}
              />
            ))}
          </div>
        ) : (
          <div className="text-center py-20">
            <div className="inline-flex p-4 rounded-full bg-surface-highlight mb-4">
              <FiSearch className="text-4xl text-text-tertiary" />
            </div>
            <h3 className="text-xl font-bold mb-2">Tidak ditemukan</h3>
            <p className="text-text-secondary">Coba ubah filter atau kata kunci pencarianmu.</p>
          </div>
        )}
      </div>

      <AnimatePresence>
        {detailModal.open && (
          <PropertyDetailModal
            key="detail-modal"
            property={detailModal.property}
            loading={detailModal.loading}
            error={detailModal.error}
            onClose={() => setDetailModal({ open: false, property: null, loading: false, error: '' })}
            onApplyRoom={(roomId, propertyId) => openApply(propertyId, roomId)}
          />
        )}
        {applyModal.open && (
          <ApplyModal
            key="apply-modal"
            property={applyModal.property}
            roomId={applyModal.roomId}
            loading={applyModal.loading}
            error={applyModal.error}
            onClose={() => setApplyModal({ open: false, property: null, loading: false, error: '', roomId: null })}
          />
        )}
      </AnimatePresence>
    </div>
  );
};

const PropertyCard = ({ property, index, onDetail }) => {
  const priceCandidates = property.room_types?.map((rt) => rt.base_price).filter(Boolean) ?? [];
  const price = priceCandidates.length ? Math.min(...priceCandidates) : property.base_price ?? property.price;
  const firstPhoto = property.photos?.[0];
  const availableRooms = (property.room_types ?? []).reduce(
    (total, rt) => total + (rt.rooms ? rt.rooms.filter((room) => room.status === 'available').length : 0),
    0,
  );

  return (
    <motion.div
      initial={{ opacity: 0, y: 20 }}
      animate={{ opacity: 1, y: 0 }}
      transition={{ delay: index * 0.1 }}
      className="card group cursor-pointer overflow-hidden flex flex-col h-full"
      onClick={onDetail}
    >
      <div className="aspect-[4/3] relative overflow-hidden">
        {firstPhoto ? (
          <img src={firstPhoto} alt={property.name} className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
        ) : (
          <div className="w-full h-full bg-surface-highlight flex items-center justify-center text-text-tertiary">
            <FiSearch className="text-4xl" />
          </div>
        )}
        <div className="absolute top-3 right-3 bg-black/60 backdrop-blur-md px-3 py-1 rounded-full text-xs font-semibold border border-white/10">
          {availableRooms > 0 ? `${availableRooms} Kamar` : 'Penuh'}
        </div>
      </div>
      <div className="p-5 flex flex-col flex-grow">
        <div className="flex justify-between items-start mb-2">
          <h3 className="font-bold text-lg line-clamp-1 group-hover:text-primary transition-colors">{property.name}</h3>
        </div>
        <div className="flex items-center gap-2 text-text-secondary text-sm mb-4">
          <FiMapPin className="flex-shrink-0" />
          <span className="line-clamp-1">{property.address ?? 'Alamat tidak tersedia'}</span>
        </div>

        <div className="mt-auto pt-4 border-t border-border flex justify-between items-center">
          <div>
            <div className="text-xs text-text-tertiary">Mulai dari</div>
            <div className="text-primary font-bold text-lg">{formatPrice(price)}</div>
          </div>
          <button className="btn ghost btn-sm">Detail</button>
        </div>
      </div>
    </motion.div>
  );
};

const PropertyDetailModal = ({ property, loading, error, onClose, onApplyRoom }) => {
  if (!property && !loading) return null;

  return (
    <div className="fixed inset-0 z-[2000] flex items-center justify-center p-4">
      <motion.div
        initial={{ opacity: 0 }}
        animate={{ opacity: 1 }}
        exit={{ opacity: 0 }}
        className="absolute inset-0 bg-black/60 backdrop-blur-md"
        onClick={onClose}
      />
      <motion.div
        initial={{ opacity: 0, scale: 0.95, y: 20 }}
        animate={{ opacity: 1, scale: 1, y: 0 }}
        exit={{ opacity: 0, scale: 0.95, y: 20 }}
        className="relative bg-surface/95 backdrop-blur-2xl border border-white/10 rounded-3xl w-full max-w-4xl max-h-[90vh] overflow-y-auto shadow-2xl ring-1 ring-black/5"
      >
        {loading ? (
          <div className="p-12 text-center">
            <div className="w-10 h-10 border-2 border-primary border-t-transparent rounded-full animate-spin mx-auto mb-4" />
            <p className="text-text-secondary animate-pulse">Memuat detail properti...</p>
          </div>
        ) : error ? (
          <div className="p-12 text-center">
            <div className="w-12 h-12 bg-red-500/10 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
              <FiAlertCircle className="text-2xl" />
            </div>
            <p className="text-text-primary font-medium mb-1">Gagal memuat data</p>
            <p className="text-text-secondary text-sm mb-4">{error}</p>
            <button onClick={onClose} className="btn ghost btn-sm">Tutup</button>
          </div>
        ) : (
          <div>
            <div className="sticky top-0 bg-surface/80 backdrop-blur-md border-b border-white/5 p-6 flex justify-between items-start z-10">
              <div>
                <div className="flex items-center gap-2 mb-2">
                  <span className="px-2 py-0.5 rounded-full bg-primary/10 text-primary text-xs font-bold uppercase tracking-wider border border-primary/20">
                    Detail Properti
                  </span>
                </div>
                <h2 className="text-2xl font-display font-bold text-text-primary">{property.name}</h2>
              </div>
              <button
                onClick={onClose}
                className="p-2 hover:bg-white/10 rounded-full transition-colors text-text-secondary hover:text-text-primary"
              >
                <FiX className="text-xl" />
              </button>
            </div>

            <div className="p-6 space-y-8">
              {/* Photos */}
              {property.photos?.length > 0 && (
                <div className="grid grid-cols-2 md:grid-cols-4 gap-3">
                  {property.photos.map((photo, idx) => (
                    <img
                      key={idx}
                      src={photo}
                      alt={`Foto ${idx + 1}`}
                      className={`w-full h-32 object-cover rounded-xl border border-white/5 ${idx === 0 ? 'col-span-2 row-span-2 h-66' : ''}`}
                    />
                  ))}
                </div>
              )}

              <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div className="lg:col-span-2 space-y-8">
                  <section>
                    <h3 className="text-lg font-bold text-text-primary mb-3 flex items-center gap-2">
                      <FiHome className="text-primary" /> Tentang Properti
                    </h3>
                    <p className="text-text-secondary leading-relaxed mb-4">
                      {property.description || 'Tidak ada deskripsi.'}
                    </p>
                    <div className="flex items-start gap-3 p-4 rounded-xl bg-surface-highlight/50 border border-white/5">
                      <FiMapPin className="mt-1 flex-shrink-0 text-primary" />
                      <span className="text-text-secondary">{property.address}</span>
                    </div>
                  </section>

                  <section>
                    <h3 className="text-lg font-bold text-text-primary mb-3 flex items-center gap-2">
                      <FiAlertCircle className="text-primary" /> Aturan Kost
                    </h3>
                    <div className="p-5 rounded-2xl bg-surface-highlight/30 border border-white/5">
                      <p className="text-text-secondary text-sm leading-relaxed">{property.rules_text || 'Tidak ada aturan khusus.'}</p>
                    </div>
                  </section>

                  <section>
                    <h3 className="text-lg font-bold text-text-primary mb-4 flex items-center gap-2">
                      <FiHome className="text-primary" /> Tipe Kamar
                    </h3>
                    <div className="space-y-4">
                      {property.room_types?.map((rt) => (
                        <div key={rt.id} className="p-5 rounded-2xl bg-surface border border-white/5 hover:border-primary/20 transition-colors">
                          <div className="flex justify-between items-start mb-4">
                            <div>
                              <h4 className="font-bold text-lg text-text-primary">{rt.name}</h4>
                              <p className="text-sm text-text-secondary mt-1">
                                {rt.area_m2 ? `${rt.area_m2} m²` : ''} • {rt.bathroom_type}
                              </p>
                            </div>
                            <div className="text-right">
                              <div className="text-primary font-display font-bold text-xl">{formatPrice(rt.base_price)}</div>
                              <div className="text-xs text-text-tertiary">/ bulan</div>
                            </div>
                          </div>

                          <div className="pt-4 border-t border-white/5">
                            <h5 className="text-xs font-bold text-text-secondary uppercase tracking-wider mb-3">Kamar Tersedia</h5>
                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-2">
                              {rt.rooms?.map((room) => (
                                <div key={room.id} className="flex justify-between items-center p-3 rounded-xl bg-surface-highlight/50 border border-white/5">
                                  <span className="font-mono text-sm font-medium text-text-secondary">{room.room_code}</span>
                                  {room.status === 'available' ? (
                                    <button
                                      onClick={() => onApplyRoom(room.id, property.id)}
                                      className="btn primary btn-sm py-1.5 px-4 text-xs shadow-none"
                                    >
                                      Pilih
                                    </button>
                                  ) : (
                                    <span className="text-xs text-text-tertiary uppercase font-bold px-2 py-1 rounded bg-white/5">
                                      {room.status}
                                    </span>
                                  )}
                                </div>
                              ))}
                            </div>
                          </div>
                        </div>
                      ))}
                    </div>
                  </section>
                </div>

                <div className="space-y-6">
                  <div className="p-6 rounded-3xl bg-surface border border-white/5 sticky top-24 shadow-xl">
                    <div className="text-center mb-6">
                      <div className="text-sm text-text-secondary mb-1">Mulai dari</div>
                      <div className="text-4xl font-display font-bold text-primary mb-1">
                        {formatPrice(property.room_types?.[0]?.base_price ?? property.base_price)}
                      </div>
                      <div className="text-sm text-text-tertiary">per bulan</div>
                    </div>
                    <button
                      onClick={() => onApplyRoom(null, property.id)}
                      className="btn primary w-full justify-center py-4 text-base font-bold shadow-lg shadow-primary/20 hover:shadow-primary/30 mb-4"
                    >
                      Ajukan Sewa
                    </button>
                    <p className="text-xs text-center text-text-tertiary leading-relaxed">
                      Pilih kamar spesifik di bagian Tipe Kamar untuk langsung memesan unit yang diinginkan.
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        )}
      </motion.div>
    </div>
  );
};

const ApplyModal = ({ property, roomId, loading, error, onClose }) => {
  if (!property && !loading) return null;

  return (
    <div className="fixed inset-0 z-[2000] flex items-center justify-center p-4">
      <motion.div
        initial={{ opacity: 0 }}
        animate={{ opacity: 1 }}
        exit={{ opacity: 0 }}
        className="absolute inset-0 bg-black/60 backdrop-blur-md"
        onClick={onClose}
      />
      <motion.div
        initial={{ opacity: 0, scale: 0.95, y: 20 }}
        animate={{ opacity: 1, scale: 1, y: 0 }}
        exit={{ opacity: 0, scale: 0.95, y: 20 }}
        className="relative bg-surface/95 backdrop-blur-2xl border border-white/10 rounded-3xl w-full max-w-lg p-8 shadow-2xl ring-1 ring-black/5"
      >
        <div className="flex justify-between items-start mb-6">
          <div>
            <h2 className="text-2xl font-display font-bold text-text-primary">Konfirmasi Pengajuan</h2>
            <p className="text-text-secondary text-sm mt-1">Pastikan detail pesanan Anda benar.</p>
          </div>
          <button
            onClick={onClose}
            className="p-2 hover:bg-white/10 rounded-full transition-colors text-text-secondary hover:text-text-primary"
          >
            <FiX className="text-xl" />
          </button>
        </div>

        {loading ? (
          <div className="text-center py-12">
            <div className="w-8 h-8 border-2 border-primary border-t-transparent rounded-full animate-spin mx-auto mb-4" />
            <p className="text-sm text-text-secondary animate-pulse">Memproses data...</p>
          </div>
        ) : (
          <div className="space-y-6">
            <div className="p-5 rounded-2xl bg-surface-highlight/50 border border-white/5">
              <div className="flex items-start gap-4">
                <div className="p-3 rounded-xl bg-primary/10 text-primary text-xl">
                  <FiHome />
                </div>
                <div>
                  <h3 className="font-bold text-lg text-text-primary mb-1">{property.name}</h3>
                  <p className="text-sm text-text-secondary mb-3">{property.address}</p>

                  {roomId ? (
                    <div className="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-primary/10 text-primary text-xs font-bold border border-primary/20">
                      <FiCheck /> Kamar ID: {roomId}
                    </div>
                  ) : (
                    <div className="text-xs text-text-tertiary italic">
                      Kamar akan dipilihkan oleh pemilik atau pilih manual di halaman detail.
                    </div>
                  )}
                </div>
              </div>
            </div>

            <div className="grid grid-cols-2 gap-3 pt-2">
              <button
                onClick={onClose}
                className="btn ghost justify-center hover:bg-white/5"
              >
                Batal
              </button>
              <a
                href={`/tenant/apply/${property.id}${roomId ? `?room=${roomId}` : ''}`}
                className="btn primary justify-center shadow-lg shadow-primary/20"
              >
                Lanjut ke Formulir
              </a>
            </div>
          </div>
        )}
      </motion.div>
    </div>
  );
};

export default TenantSearch;
