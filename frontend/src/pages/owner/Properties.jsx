import React, { useEffect, useMemo, useState, useCallback } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { FiPlus, FiHome, FiMapPin, FiEdit2, FiTrash2, FiEye, FiEyeOff, FiCheckCircle, FiXCircle, FiUpload, FiImage, FiX } from 'react-icons/fi';
import { fetchOwnerProperties, submitOwnerProperty, updateOwnerProperty, uploadOwnerPropertyPhoto, withdrawOwnerProperty, deleteOwnerProperty } from '../../api/client.js';
import ConfirmationModal from '../../ui/ConfirmationModal';

const OwnerProperties = () => {
  const [properties, setProperties] = useState([]);
  const [counts, setCounts] = useState({ approved: 0, pending: 0, draft: 0, rejected: 0 });
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const [manageModal, setManageModal] = useState({ open: false, property: null });

  const loadProperties = useCallback(async () => {
    setLoading(true);
    setError('');
    try {
      const response = await fetchOwnerProperties();
      setProperties(response.data ?? []);
      setCounts(response.meta?.counts ?? { approved: 0, pending: 0, draft: 0, rejected: 0 });
    } catch (err) {
      setError('Gagal memuat data properti. Pastikan login sebagai owner.');
      setProperties([]);
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    loadProperties();
  }, [loadProperties]);

  const containerVariants = {
    hidden: { opacity: 0 },
    visible: {
      opacity: 1,
      transition: { staggerChildren: 0.1 }
    }
  };

  return (
    <div className="page pt-32 pb-20">
      <div className="container">
        <div className="flex flex-col md:flex-row justify-between items-end mb-12 gap-6">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
          >
            <h1 className="text-4xl font-display font-bold mb-2">Kelola Properti</h1>
            <p className="text-text-secondary text-lg">
              Tambahkan, edit, dan pantau status moderasi kost Anda.
            </p>
          </motion.div>

          <motion.div
            initial={{ opacity: 0, x: 20 }}
            animate={{ opacity: 1, x: 0 }}
            className="flex gap-3"
          >
            <button onClick={loadProperties} className="btn ghost">
              Refresh
            </button>
            <a href="/owner/properties/create" className="btn primary">
              <FiPlus className="mr-2" /> Tambah Properti
            </a>
          </motion.div>
        </div>

        {/* Stats Overview */}
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.2 }}
          className="grid grid-cols-2 md:grid-cols-4 gap-4 mb-12"
        >
          <StatusCard label="Publish" value={counts.approved} color="text-green-400" bg="bg-green-400/10" border="border-green-400/20" />
          <StatusCard label="Pending" value={counts.pending} color="text-blue-400" bg="bg-blue-400/10" border="border-blue-400/20" />
          <StatusCard label="Draft" value={counts.draft} color="text-text-secondary" bg="bg-surface-highlight" border="border-border" />
          <StatusCard label="Ditolak" value={counts.rejected} color="text-red-400" bg="bg-red-400/10" border="border-red-400/20" />
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
        ) : properties.length === 0 ? (
          <div className="text-center py-20 card border-dashed border-2 border-border bg-transparent">
            <div className="inline-flex p-4 rounded-full bg-surface-highlight mb-4">
              <FiHome className="text-4xl text-text-tertiary" />
            </div>
            <h3 className="text-xl font-bold mb-2">Belum ada properti</h3>
            <p className="text-text-secondary mb-6">Mulai tambahkan kost Anda untuk menjangkau penyewa.</p>
            <a href="/owner/properties/create" className="btn primary">
              Tambah Properti Pertama
            </a>
          </div>
        ) : (
          <motion.div
            variants={containerVariants}
            initial="hidden"
            animate="visible"
            className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
          >
            {properties.map((property) => (
              <PropertyCard
                key={property.id}
                property={property}
                onManage={() => setManageModal({ open: true, property })}
              />
            ))}
          </motion.div>
        )}

        <AnimatePresence>
          {manageModal.open && manageModal.property && (
            <ManageModal
              property={manageModal.property}
              onClose={() => setManageModal({ open: false, property: null })}
              onUpdated={() => {
                loadProperties();
                setManageModal({ open: false, property: null });
              }}
            />
          )}
        </AnimatePresence>
      </div>
    </div>
  );
};

const StatusCard = ({ label, value, color, bg, border }) => (
  <div className={`p-4 rounded-xl border ${border} ${bg} flex flex-col items-center justify-center text-center`}>
    <span className={`text-2xl font-bold font-display ${color}`}>{value}</span>
    <span className="text-xs text-text-secondary uppercase tracking-wider mt-1">{label}</span>
  </div>
);

const PropertyCard = ({ property, onManage }) => {
  const statusConfig = {
    draft: { label: 'Draft', color: 'text-text-secondary', bg: 'bg-surface-highlight', border: 'border-border' },
    pending: { label: 'Pending', color: 'text-blue-400', bg: 'bg-blue-400/10', border: 'border-blue-400/20' },
    approved: { label: 'Publish', color: 'text-green-400', bg: 'bg-green-400/10', border: 'border-green-400/20' },
    rejected: { label: 'Ditolak', color: 'text-red-400', bg: 'bg-red-400/10', border: 'border-red-400/20' },
    unpublished: { label: 'Unpublished', color: 'text-yellow-400', bg: 'bg-yellow-400/10', border: 'border-yellow-400/20' },
  };

  const status = statusConfig[property.status] || statusConfig.draft;

  return (
    <motion.div
      variants={{ hidden: { opacity: 0, y: 20 }, visible: { opacity: 1, y: 0 } }}
      className="card group overflow-hidden flex flex-col h-full cursor-pointer hover:border-primary transition-colors"
      onClick={() => window.location.href = `/owner/properties/${property.id}`}
    >
      <div className="aspect-video relative overflow-hidden bg-surface-highlight">
        {property.cover_photo ? (
          <img
            src={property.cover_photo}
            alt={property.name}
            className="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
          />
        ) : (
          <div className="flex items-center justify-center h-full text-text-tertiary">
            <FiImage className="text-4xl" />
          </div>
        )}
        <div className="absolute top-3 right-3">
          <span className={`px-3 py-1 rounded-full text-xs font-bold border backdrop-blur-md ${status.color} ${status.bg} ${status.border}`}>
            {status.label}
          </span>
        </div>
      </div>

      <div className="p-5 flex flex-col flex-grow">
        <h3 className="text-xl font-bold font-display mb-2 line-clamp-1 group-hover:text-primary transition-colors">{property.name}</h3>
        <div className="flex items-start gap-2 text-sm text-text-secondary mb-4 line-clamp-2 h-10">
          <FiMapPin className="flex-shrink-0 mt-0.5" />
          <span>{property.address || 'Alamat belum diisi'}</span>
        </div>

        {property.moderation_notes && (
          <div className="mb-4 p-3 rounded-lg bg-red-500/10 border border-red-500/20 text-xs text-red-400">
            <span className="font-bold block mb-1">Catatan Moderasi:</span>
            {property.moderation_notes}
          </div>
        )}

        <div className="mt-auto pt-4 border-t border-border flex justify-between items-center">
          <span className="text-sm text-text-secondary">
            {property.room_types_count || 0} Tipe Kamar
          </span>
          <button
            onClick={(e) => {
              e.stopPropagation();
              window.location.href = `/owner/properties/${property.id}`;
            }}
            className="btn ghost btn-sm"
          >
            Kelola
          </button>
        </div>
      </div>
    </motion.div>
  );
};

const ManageModal = ({ property, onClose, onUpdated }) => {
  const [form, setForm] = useState({
    name: property.name ?? '',
    address: property.address ?? '',
    rules_text: property.rules_text ?? '',
  });
  const [photoList, setPhotoList] = useState(property.photos ?? []);
  const [saving, setSaving] = useState(false);
  const [actionLoading, setActionLoading] = useState(false);
  const [uploading, setUploading] = useState(false);
  const [error, setError] = useState('');
  const [success, setSuccess] = useState('');
  const [deleteModalOpen, setDeleteModalOpen] = useState(false);

  const handleInput = (e) => setForm({ ...form, [e.target.name]: e.target.value });

  const handleUpdate = async () => {
    setSaving(true);
    setError('');
    try {
      await updateOwnerProperty(property.id, { ...form, photos: photoList });
      setSuccess('Perubahan disimpan.');
      setTimeout(onUpdated, 1000);
    } catch (err) {
      setError(err?.response?.data?.message ?? 'Gagal menyimpan.');
    } finally {
      setSaving(false);
    }
  };

  const handleSubmit = async () => {
    setActionLoading(true);
    try {
      await submitOwnerProperty(property.id);
      setSuccess('Berhasil disubmit untuk moderasi.');
      setTimeout(onUpdated, 1000);
    } catch (err) {
      setError('Gagal submit moderasi.');
    } finally {
      setActionLoading(false);
    }
  };

  const handleWithdraw = async () => {
    setActionLoading(true);
    try {
      await withdrawOwnerProperty(property.id);
      setSuccess('Properti ditarik kembali ke draft.');
      setTimeout(onUpdated, 1000);
    } catch (err) {
      setError('Gagal menarik properti.');
    } finally {
      setActionLoading(false);
    }
  };

  const handleDelete = () => {
    setActionLoading(true);
    deleteOwnerProperty(property.id)
      .then(() => {
        setSuccess('Properti dihapus.');
        setDeleteModalOpen(false);
        setTimeout(onUpdated, 1000);
      })
      .catch(() => {
        setError('Gagal menghapus properti.');
        setActionLoading(false);
        setDeleteModalOpen(false);
      });
  };

  const handleUpload = async (e) => {
    const file = e.target.files?.[0];
    if (!file) return;
    setUploading(true);
    try {
      const res = await uploadOwnerPropertyPhoto(property.id, file);
      setPhotoList(res.property?.photos ?? photoList);
      setSuccess('Foto terupload.');
    } catch (err) {
      setError('Gagal upload foto.');
    } finally {
      setUploading(false);
    }
  };

  const canSubmit = ['draft', 'rejected'].includes(property.status);
  const canWithdraw = ['pending', 'approved'].includes(property.status);

  return (
    <div className="fixed inset-0 z-[2000] flex items-center justify-center p-4">
      <motion.div
        initial={{ opacity: 0 }}
        animate={{ opacity: 1 }}
        exit={{ opacity: 0 }}
        className="absolute inset-0 bg-black/80 backdrop-blur-sm"
        onClick={onClose}
      />
      <motion.div
        initial={{ opacity: 0, scale: 0.95 }}
        animate={{ opacity: 1, scale: 1 }}
        exit={{ opacity: 0, scale: 0.95 }}
        className="relative bg-surface border border-border rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-2xl flex flex-col"
      >
        <div className="sticky top-0 bg-surface/90 backdrop-blur-md border-b border-border p-4 flex justify-between items-center z-10">
          <h2 className="text-xl font-bold font-display">Kelola Properti</h2>
          <button onClick={onClose} className="p-2 hover:bg-white/5 rounded-full transition-colors">
            <FiX className="text-xl" />
          </button>
        </div>

        <div className="p-6 space-y-6">
          {error && <div className="alert bg-red-500/10 border-red-500/20 text-red-400">{error}</div>}
          {success && <div className="alert bg-green-500/10 border-green-500/20 text-green-400">{success}</div>}

          <div className="space-y-4">
            <div>
              <label className="block text-sm font-medium text-text-secondary mb-1">Nama Properti</label>
              <input
                type="text"
                name="name"
                value={form.name}
                onChange={handleInput}
                className="w-full bg-surface-highlight border border-border rounded-lg px-4 py-2 focus:outline-none focus:border-primary transition-colors"
              />
            </div>
            <div>
              <label className="block text-sm font-medium text-text-secondary mb-1">Alamat Lengkap</label>
              <textarea
                name="address"
                rows="3"
                value={form.address}
                onChange={handleInput}
                className="w-full bg-surface-highlight border border-border rounded-lg px-4 py-2 focus:outline-none focus:border-primary transition-colors"
              />
            </div>
            <div>
              <label className="block text-sm font-medium text-text-secondary mb-1">Peraturan Kost</label>
              <textarea
                name="rules_text"
                rows="3"
                value={form.rules_text}
                onChange={handleInput}
                className="w-full bg-surface-highlight border border-border rounded-lg px-4 py-2 focus:outline-none focus:border-primary transition-colors"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-text-secondary mb-2">Foto Properti</label>
              <div className="grid grid-cols-3 gap-2 mb-3">
                {photoList.map((url, idx) => (
                  <div key={idx} className="relative aspect-square rounded-lg overflow-hidden group">
                    <img src={url} alt="" className="w-full h-full object-cover" />
                    <button
                      onClick={() => setPhotoList(photoList.filter(p => p !== url))}
                      className="absolute top-1 right-1 p-1 bg-black/50 rounded-full text-white opacity-0 group-hover:opacity-100 transition-opacity"
                    >
                      <FiX size={12} />
                    </button>
                  </div>
                ))}
                <label className="aspect-square rounded-lg border-2 border-dashed border-border hover:border-primary hover:bg-surface-highlight transition-colors flex flex-col items-center justify-center cursor-pointer">
                  <FiUpload className="text-2xl mb-1 text-text-tertiary" />
                  <span className="text-xs text-text-tertiary">Upload</span>
                  <input type="file" accept="image/*" className="hidden" onChange={handleUpload} disabled={uploading} />
                </label>
              </div>
            </div>
          </div>
        </div>

        <div className="sticky bottom-0 bg-surface/90 backdrop-blur-md border-t border-border p-4 flex justify-between items-center z-10">
          <div className="flex gap-2">
            <button
              onClick={() => setDeleteModalOpen(true)}
              disabled={actionLoading}
              className="btn ghost text-red-400 hover:bg-red-400/10"
            >
              <FiTrash2 className="mr-2" /> Hapus
            </button>
            {canWithdraw && (
              <button
                onClick={handleWithdraw}
                disabled={actionLoading}
                className="btn ghost text-yellow-400 hover:bg-yellow-400/10"
              >
                {actionLoading ? '...' : 'Unpublish'}
              </button>
            )}
          </div>
          <div className="flex gap-2">
            <a href="/owner/rooms" className="btn secondary">
              <FiPlus className="mr-2" /> Tambah Kamar
            </a>
            {canSubmit && (
              <button
                onClick={handleSubmit}
                disabled={actionLoading}
                className="btn secondary"
              >
                {actionLoading ? '...' : 'Submit Moderasi'}
              </button>
            )}
            <button
              onClick={handleUpdate}
              disabled={saving}
              className="btn primary"
            >
              {saving ? 'Menyimpan...' : 'Simpan Perubahan'}
            </button>
          </div>
        </div>

        <ConfirmationModal
          isOpen={deleteModalOpen}
          onClose={() => setDeleteModalOpen(false)}
          onConfirm={handleDelete}
          title="Hapus Properti?"
          message={`Apakah Anda yakin ingin menghapus properti "${property.name}"? Tindakan ini tidak dapat dibatalkan.`}
          confirmLabel="Ya, Hapus"
          isLoading={actionLoading}
          isDanger={true}
        />
      </motion.div>
    </div>
  );
};

export default OwnerProperties;
