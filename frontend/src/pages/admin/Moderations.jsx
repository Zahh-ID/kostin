import React, { useEffect, useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { approveAdminModeration, fetchAdminModerations, rejectAdminModeration } from '../../api/client.js';
import { FiCheckCircle, FiXCircle, FiHome, FiMapPin, FiUser, FiInfo, FiFilter, FiSearch } from 'react-icons/fi';

const AdminModerations = () => {
  const [items, setItems] = useState([]);
  const [loading, setLoading] = useState(true);
  const [selectedProperty, setSelectedProperty] = useState(null);
  const [actionModal, setActionModal] = useState({ open: false, type: null }); // type: approve, reject
  const [notes, setNotes] = useState('');
  const [actionLoading, setActionLoading] = useState(false);

  useEffect(() => {
    loadData();
  }, []);

  const loadData = async () => {
    setLoading(true);
    try {
      const res = await fetchAdminModerations();
      setItems(res.data ?? res ?? []);
    } catch (error) {
      console.error('Failed to load moderations:', error);
    } finally {
      setLoading(false);
    }
  };

  const filteredItems = items.filter(item => item.status === 'pending');

  const handleAction = async () => {
    if (!selectedProperty || !actionModal.type) return;

    setActionLoading(true);
    try {
      if (actionModal.type === 'approve') {
        await approveAdminModeration(selectedProperty.id, { moderation_notes: notes });
      } else {
        await rejectAdminModeration(selectedProperty.id, { moderation_notes: notes });
      }

      await loadData();
      setActionModal({ open: false, type: null });
      setSelectedProperty(null);
      setNotes('');
    } catch (error) {
      alert('Gagal memproses moderasi: ' + (error.response?.data?.message || error.message));
    } finally {
      setActionLoading(false);
    }
  };

  const openActionModal = (type) => {
    setActionModal({ open: true, type });
    setNotes('');
  };

  return (
    <div className="page pt-32 pb-20">
      <div className="container">
        <motion.div
          initial={{ opacity: 0, y: -20 }}
          animate={{ opacity: 1, y: 0 }}
          className="mb-8"
        >
          <h1 className="text-4xl font-display font-bold mb-2">Moderasi Properti</h1>
          <p className="text-text-secondary text-lg">
            Tinjau dan verifikasi properti baru yang didaftarkan oleh owner.
          </p>
        </motion.div>

        {/* Content */}
        <div className="grid grid-cols-1 gap-4">
          {loading ? (
            <div className="text-center py-12 text-text-secondary">Memuat data...</div>
          ) : filteredItems.length === 0 ? (
            <div className="text-center py-12 text-text-secondary bg-surface-highlight rounded-2xl border border-dashed border-border">
              <FiHome className="mx-auto text-4xl mb-4 opacity-20" />
              <p>Tidak ada properti yang menunggu review.</p>
            </div>
          ) : (
            filteredItems.map((item) => (
              <motion.div
                key={item.id}
                initial={{ opacity: 0, y: 10 }}
                animate={{ opacity: 1, y: 0 }}
                className="card p-6 hover:bg-surface-highlight transition-colors group cursor-pointer"
                onClick={() => setSelectedProperty(item)}
              >
                <div className="flex flex-col md:flex-row justify-between gap-4">
                  <div className="flex items-start gap-4">
                    <div className="w-16 h-16 rounded-xl bg-surface border border-border overflow-hidden flex-shrink-0">
                      {item.photos?.[0] ? (
                        <img src={item.photos[0]} alt={item.name} className="w-full h-full object-cover" />
                      ) : (
                        <div className="w-full h-full flex items-center justify-center bg-surface-highlight text-text-tertiary">
                          <FiHome />
                        </div>
                      )}
                    </div>
                    <div>
                      <div className="flex items-center gap-2 mb-1">
                        <h3 className="font-bold font-display text-lg">{item.name}</h3>
                        <StatusBadge status={item.status} />
                      </div>
                      <div className="flex items-center gap-2 text-sm text-text-secondary mb-1">
                        <FiMapPin className="text-primary" />
                        <span>{item.address}</span>
                      </div>
                      <div className="flex items-center gap-2 text-xs text-text-tertiary">
                        <FiUser />
                        <span>Owner: {item.owner?.name}</span>
                        <span>•</span>
                        <span>Diajukan: {new Date(item.created_at).toLocaleDateString()}</span>
                      </div>
                    </div>
                  </div>

                  <div className="flex items-center">
                    <button className="btn ghost btn-sm">Lihat Detail</button>
                  </div>
                </div>
              </motion.div>
            ))
          )}
        </div>
      </div>

      {/* Detail Modal */}
      <AnimatePresence>
        {selectedProperty && !actionModal.open && (
          <PropertyDetailModal
            property={selectedProperty}
            onClose={() => setSelectedProperty(null)}
            onApprove={() => openActionModal('approve')}
            onReject={() => openActionModal('reject')}
          />
        )}
      </AnimatePresence>

      {/* Action Confirmation Modal */}
      <AnimatePresence>
        {actionModal.open && (
          <ActionModal
            type={actionModal.type}
            property={selectedProperty}
            notes={notes}
            setNotes={setNotes}
            loading={actionLoading}
            onClose={() => setActionModal({ open: false, type: null })}
            onConfirm={handleAction}
          />
        )}
      </AnimatePresence>
    </div>
  );
};

const StatusBadge = ({ status }) => {
  const styles = {
    pending: 'bg-yellow-400/10 text-yellow-400 border-yellow-400/20',
    approved: 'bg-green-400/10 text-green-400 border-green-400/20',
    rejected: 'bg-red-400/10 text-red-400 border-red-400/20',
  };

  return (
    <span className={`px-2 py-0.5 rounded text-[10px] font-bold uppercase border ${styles[status] || styles.pending}`}>
      {status}
    </span>
  );
};

const PropertyDetailModal = ({ property, onClose, onApprove, onReject }) => (
  <div className="fixed inset-0 z-[2000] flex items-center justify-center p-4">
    <div className="absolute inset-0 bg-black/80 backdrop-blur-sm" onClick={onClose} />
    <motion.div
      initial={{ opacity: 0, scale: 0.95 }}
      animate={{ opacity: 1, scale: 1 }}
      exit={{ opacity: 0, scale: 0.95 }}
      className="relative bg-surface border border-white/10 rounded-3xl w-[95%] md:w-full max-w-3xl p-6 shadow-2xl max-h-[90vh] overflow-y-auto"
    >
      <div className="flex justify-between items-start mb-6">
        <div>
          <h2 className="text-2xl font-bold font-display mb-1">{property.name}</h2>
          <p className="text-text-secondary flex items-center gap-2">
            <FiMapPin /> {property.address}
          </p>
        </div>
        <button onClick={onClose}><FiXCircle className="text-2xl text-text-secondary hover:text-white" /></button>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
        <div className="space-y-6">
          <div>
            <h3 className="text-sm font-bold uppercase text-text-secondary mb-3">Deskripsi</h3>
            <p className="text-sm leading-relaxed text-text-secondary bg-surface-highlight p-4 rounded-xl border border-border">
              {property.description || 'Tidak ada deskripsi.'}
            </p>
          </div>

          <div>
            <h3 className="text-sm font-bold uppercase text-text-secondary mb-3">Informasi Owner</h3>
            <div className="flex items-center gap-3 p-4 rounded-xl bg-surface-highlight border border-border">
              <div className="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center text-primary">
                <FiUser />
              </div>
              <div>
                <p className="font-bold">{property.owner?.name}</p>
                <p className="text-xs text-text-secondary">{property.owner?.email}</p>
              </div>
            </div>
          </div>
        </div>

        <div className="space-y-6">
          <div>
            <h3 className="text-sm font-bold uppercase text-text-secondary mb-3">Foto Properti</h3>
            <div className="grid grid-cols-2 gap-2">
              {property.photos?.length > 0 ? (
                property.photos.map((photo, idx) => (
                  <img key={idx} src={photo} alt={`Foto ${idx + 1}`} className="w-full h-32 object-cover rounded-lg border border-border" />
                ))
              ) : (
                <div className="col-span-2 h-32 flex items-center justify-center bg-surface-highlight rounded-lg border border-border text-text-tertiary">
                  Tidak ada foto
                </div>
              )}
            </div>
          </div>
        </div>
      </div>

      {property.status === 'pending' && (
        <div className="flex gap-4 pt-6 border-t border-border">
          <button onClick={onReject} className="btn ghost flex-1 text-red-400 hover:bg-red-400/10">
            <FiXCircle className="mr-2" /> Tolak
          </button>
          <button onClick={onApprove} className="btn primary flex-1 justify-center">
            <FiCheckCircle className="mr-2" /> Setujui Properti
          </button>
        </div>
      )}
    </motion.div>
  </div>
);

const ActionModal = ({ type, property, notes, setNotes, loading, onClose, onConfirm }) => (
  <div className="fixed inset-0 z-[2000] flex items-center justify-center p-4">
    <div className="absolute inset-0 bg-black/80 backdrop-blur-sm" onClick={onClose} />
    <motion.div
      initial={{ opacity: 0, scale: 0.95 }}
      animate={{ opacity: 1, scale: 1 }}
      exit={{ opacity: 0, scale: 0.95 }}
      className="relative bg-surface border border-white/10 rounded-3xl w-full max-w-md p-6 shadow-2xl"
    >
      <h2 className={`text-xl font-bold font-display mb-2 ${type === 'approve' ? 'text-green-400' : 'text-red-400'}`}>
        {type === 'approve' ? 'Setujui Properti' : 'Tolak Properti'}
      </h2>
      <p className="text-text-secondary mb-6">
        Anda akan {type === 'approve' ? 'menyetujui' : 'menolak'} properti <strong>{property.name}</strong>.
      </p>

      <div className="space-y-4 mb-6">
        <label className="block text-sm text-text-secondary mb-2">
          Catatan Moderasi {type === 'reject' && <span className="text-red-400">*</span>}
        </label>
        <textarea
          value={notes}
          onChange={(e) => setNotes(e.target.value)}
          className="w-full bg-surface-highlight border border-white/10 rounded-xl px-4 py-3 focus:border-primary outline-none min-h-[100px]"
          placeholder={type === 'approve' ? "Tambahkan catatan opsional..." : "Alasan penolakan..."}
          required={type === 'reject'}
        />
      </div>

      <div className="flex gap-3">
        <button onClick={onClose} className="btn ghost flex-1">Batal</button>
        <button
          onClick={onConfirm}
          disabled={loading || (type === 'reject' && !notes.trim())}
          className={`btn flex-1 justify-center ${type === 'approve' ? 'primary' : 'bg-red-500 hover:bg-red-600 text-white'}`}
        >
          {loading ? 'Memproses...' : 'Konfirmasi'}
        </button>
      </div>
    </motion.div>
  </div>
);

export default AdminModerations;
