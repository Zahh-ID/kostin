import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { FiTrash2 } from 'react-icons/fi';
import { updateOwnerProperty, deleteOwnerProperty } from '../../../api/client';
import ConfirmationModal from '../../../ui/ConfirmationModal';
import { AnimatePresence } from 'framer-motion';

const PropertySettings = ({ property, onUpdate }) => {
    const navigate = useNavigate();
    const [form, setForm] = useState({
        name: property.name,
        address: property.address,
        rules_text: property.rules_text,
        description: property.description,
    });
    const [saving, setSaving] = useState(false);
    const [deleting, setDeleting] = useState(false);
    const [showDeleteConfirm, setShowDeleteConfirm] = useState(false);

    const handleSave = async () => {
        setSaving(true);
        try {
            await updateOwnerProperty(property.id, form);
            onUpdate();
            alert('Perubahan berhasil disimpan.');
        } catch (err) {
            console.error(err);
            alert('Gagal menyimpan perubahan.');
        } finally {
            setSaving(false);
        }
    };

    const handleDelete = async () => {
        setDeleting(true);
        try {
            await deleteOwnerProperty(property.id);
            navigate('/owner/properties');
        } catch (err) {
            console.error(err);
            setDeleting(false);
            alert(err.response?.data?.message || 'Gagal menghapus properti.');
        }
    };

    return (
        <div className="max-w-3xl space-y-8">
            <div className="card p-6 space-y-6">
                <h3 className="text-xl font-bold font-display border-b border-white/10 pb-4">Informasi Umum</h3>
                <div>
                    <label className="block text-sm text-text-secondary mb-2">Nama Properti</label>
                    <input
                        value={form.name}
                        onChange={e => setForm({ ...form, name: e.target.value })}
                        className="w-full bg-surface-highlight border border-white/10 rounded-xl px-4 py-3 focus:border-primary outline-none"
                    />
                </div>
                <div>
                    <label className="block text-sm text-text-secondary mb-2">Alamat</label>
                    <textarea
                        value={form.address}
                        onChange={e => setForm({ ...form, address: e.target.value })}
                        className="w-full bg-surface-highlight border border-white/10 rounded-xl px-4 py-3 focus:border-primary outline-none"
                        rows={3}
                    />
                </div>
                <div>
                    <label className="block text-sm text-text-secondary mb-2">Deskripsi</label>
                    <textarea
                        value={form.description}
                        onChange={e => setForm({ ...form, description: e.target.value })}
                        className="w-full bg-surface-highlight border border-white/10 rounded-xl px-4 py-3 focus:border-primary outline-none"
                        rows={4}
                    />
                </div>
                <div>
                    <label className="block text-sm text-text-secondary mb-2">Peraturan Kost</label>
                    <textarea
                        value={form.rules_text}
                        onChange={e => setForm({ ...form, rules_text: e.target.value })}
                        className="w-full bg-surface-highlight border border-white/10 rounded-xl px-4 py-3 focus:border-primary outline-none"
                        rows={4}
                        placeholder="- Dilarang merokok&#10;- Jam malam 23:00"
                    />
                </div>
                <div className="flex justify-end">
                    <button onClick={handleSave} disabled={saving} className="btn primary px-8">
                        {saving ? 'Menyimpan...' : 'Simpan Perubahan'}
                    </button>
                </div>
            </div>

            <div className="card p-6">
                <h3 className="text-xl font-bold font-display border-b border-white/10 pb-4 mb-4">Status Properti</h3>
                <div className="flex justify-between items-center">
                    <div>
                        <div className="font-bold text-lg mb-1">Status Saat Ini: <span className="uppercase text-primary">{property.status}</span></div>
                        <p className="text-text-secondary text-sm">
                            {property.status === 'draft' && 'Properti belum dipublikasikan. Lengkapi data dan ajukan untuk mulai menyewakan.'}
                            {property.status === 'pending' && 'Properti sedang ditinjau oleh admin.'}
                            {property.status === 'approved' && 'Properti sudah tayang dan dapat dilihat oleh pencari kost.'}
                            {property.status === 'rejected' && 'Pengajuan properti ditolak. Silakan perbaiki dan ajukan kembali.'}
                        </p>
                    </div>
                    {/* Actions are handled in the main header, but we can add shortcuts here if needed */}
                </div>
            </div>

            <div className="card p-6 border border-red-500/20 bg-red-500/5">
                <h3 className="text-lg font-bold text-red-500 mb-2">Danger Zone</h3>
                <p className="text-text-secondary text-sm mb-4">
                    Menghapus properti akan menghapus semua data kamar dan tipe kamar yang terkait. Tindakan ini tidak dapat dibatalkan.
                </p>
                <button
                    onClick={() => setShowDeleteConfirm(true)}
                    className="btn bg-red-500/10 text-red-500 border-red-500/20 hover:bg-red-500/20"
                >
                    <FiTrash2 className="mr-2" /> Hapus Properti
                </button>
            </div>

            <AnimatePresence>
                {showDeleteConfirm && (
                    <ConfirmationModal
                        isOpen={showDeleteConfirm}
                        onClose={() => setShowDeleteConfirm(false)}
                        onConfirm={handleDelete}
                        title="Hapus Properti?"
                        message={`Apakah Anda yakin ingin menghapus properti "${property.name}"? Semua data terkait akan hilang permanen.`}
                        confirmText={deleting ? "Menghapus..." : "Ya, Hapus Permanen"}
                        type="danger"
                    />
                )}
            </AnimatePresence>
        </div>
    );
};

export default PropertySettings;
