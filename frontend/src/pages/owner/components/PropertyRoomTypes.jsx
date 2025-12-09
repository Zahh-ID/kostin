import React, { useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { FiPlus, FiSettings, FiTrash2, FiX, FiCheck } from 'react-icons/fi';
import { createOwnerRoomType, updateOwnerRoomType, deleteOwnerRoomType } from '../../../api/client';
import ConfirmationModal from '../../../ui/ConfirmationModal';

const FACILITIES_LIST = [
    'WiFi', 'AC', 'Kamar Mandi Dalam', 'Kasur', 'Lemari Baju', 'Meja Belajar',
    'Kursi', 'Water Heater', 'TV', 'Kulkas', 'Jendela', 'Ventilasi',
    'Listrik Token', 'Termasuk Listrik'
];

const PropertyRoomTypes = ({ property, onUpdate }) => {
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [editingType, setEditingType] = useState(null);
    const [deletingType, setDeletingType] = useState(null);

    const handleEdit = (type) => {
        setEditingType(type);
        setIsModalOpen(true);
    };

    const handleDelete = async () => {
        if (!deletingType) return;
        try {
            await deleteOwnerRoomType(deletingType.id);
            onUpdate();
            setDeletingType(null);
        } catch (err) {
            console.error(err);
            alert('Gagal menghapus tipe kamar. Pastikan tidak ada kamar yang menggunakan tipe ini.');
        }
    };

    return (
        <div className="space-y-6">
            <div className="flex justify-between items-center">
                <div>
                    <h3 className="text-xl font-bold font-display">Tipe Kamar</h3>
                    <p className="text-text-secondary text-sm">Kelola variasi kamar yang tersedia di properti Anda.</p>
                </div>
                <button
                    onClick={() => { setEditingType(null); setIsModalOpen(true); }}
                    className="btn secondary"
                >
                    <FiPlus className="mr-2" /> Tambah Tipe
                </button>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                {property.room_types?.map(type => (
                    <div key={type.id} className="card p-5 hover:border-primary/50 transition-colors group relative flex flex-col h-full">
                        <div className="flex justify-between items-start mb-4">
                            <div>
                                <h4 className="font-bold text-lg">{type.name}</h4>
                                <p className="text-primary font-bold">
                                    Rp{Number(type.base_price).toLocaleString('id-ID')}
                                    <span className="text-xs text-text-tertiary font-normal"> / bulan</span>
                                </p>
                            </div>
                            <div className="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button
                                    onClick={() => handleEdit(type)}
                                    className="p-2 hover:bg-surface-highlight rounded-lg text-text-secondary hover:text-primary transition-colors"
                                >
                                    <FiSettings />
                                </button>
                                <button
                                    onClick={() => setDeletingType(type)}
                                    className="p-2 hover:bg-red-500/10 rounded-lg text-text-secondary hover:text-red-500 transition-colors"
                                >
                                    <FiTrash2 />
                                </button>
                            </div>
                        </div>

                        <div className="space-y-3 flex-grow">
                            <p className="text-sm text-text-secondary line-clamp-2">
                                {type.description || 'Tidak ada deskripsi'}
                            </p>

                            <div className="flex flex-wrap gap-2">
                                <span className="text-xs px-2 py-1 rounded bg-surface-highlight text-text-secondary border border-white/5">
                                    {type.area_m2 || '-'} m²
                                </span>
                                <span className="text-xs px-2 py-1 rounded bg-surface-highlight text-text-secondary border border-white/5">
                                    {type.rooms_count || 0} Unit
                                </span>
                            </div>

                            {type.facilities_json && type.facilities_json.length > 0 && (
                                <div className="flex flex-wrap gap-1 pt-2 border-t border-white/5">
                                    {type.facilities_json.slice(0, 3).map((fac, i) => (
                                        <span key={i} className="text-[10px] px-1.5 py-0.5 rounded bg-primary/10 text-primary border border-primary/20">
                                            {fac}
                                        </span>
                                    ))}
                                    {type.facilities_json.length > 3 && (
                                        <span className="text-[10px] px-1.5 py-0.5 rounded bg-surface-highlight text-text-tertiary">
                                            +{type.facilities_json.length - 3}
                                        </span>
                                    )}
                                </div>
                            )}
                        </div>
                    </div>
                ))}

                {(!property.room_types || property.room_types.length === 0) && (
                    <div className="col-span-full py-12 text-center border-2 border-dashed border-white/10 rounded-2xl">
                        <div className="w-12 h-12 bg-surface-highlight rounded-full flex items-center justify-center mx-auto mb-3 text-text-tertiary">
                            <FiSettings className="text-xl" />
                        </div>
                        <h4 className="text-lg font-bold text-text-secondary mb-1">Belum ada tipe kamar</h4>
                        <p className="text-text-tertiary text-sm mb-4">Buat tipe kamar terlebih dahulu sebelum menambahkan unit kamar.</p>
                        <button
                            onClick={() => { setEditingType(null); setIsModalOpen(true); }}
                            className="btn primary btn-sm"
                        >
                            <FiPlus className="mr-2" /> Buat Tipe Kamar
                        </button>
                    </div>
                )}
            </div>

            <AnimatePresence>
                {isModalOpen && (
                    <RoomTypeModal
                        propertyId={property.id}
                        roomType={editingType}
                        onClose={() => setIsModalOpen(false)}
                        onSuccess={() => {
                            setIsModalOpen(false);
                            onUpdate();
                        }}
                    />
                )}
                {deletingType && (
                    <ConfirmationModal
                        isOpen={!!deletingType}
                        onClose={() => setDeletingType(null)}
                        onConfirm={handleDelete}
                        title="Hapus Tipe Kamar?"
                        message={`Apakah Anda yakin ingin menghapus tipe kamar "${deletingType.name}"?`}
                        confirmText="Ya, Hapus"
                        type="danger"
                    />
                )}
            </AnimatePresence>
        </div>
    );
};

const RoomTypeModal = ({ propertyId, roomType, onClose, onSuccess }) => {
    const isEdit = !!roomType;
    const [form, setForm] = useState({
        name: roomType?.name || '',
        description: roomType?.description || '',
        price: roomType?.base_price || '',
        area_m2: roomType?.area_m2 || '',
        facilities: roomType?.facilities_json || [],
    });
    const [loading, setLoading] = useState(false);

    const toggleFacility = (fac) => {
        setForm(prev => ({
            ...prev,
            facilities: prev.facilities.includes(fac)
                ? prev.facilities.filter(f => f !== fac)
                : [...prev.facilities, fac]
        }));
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        try {
            const payload = {
                ...form,
                property_id: propertyId,
                facilities: form.facilities // Backend expects 'facilities' array which maps to facilities_json
            };

            if (isEdit) {
                await updateOwnerRoomType(roomType.id, payload);
            } else {
                await createOwnerRoomType(payload);
            }
            onSuccess();
        } catch (err) {
            console.error(err);
            alert('Gagal menyimpan tipe kamar.');
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="fixed inset-0 z-[2000] flex items-center justify-center p-4">
            <div className="absolute inset-0 bg-black/80 backdrop-blur-sm" onClick={onClose} />
            <motion.div initial={{ opacity: 0, scale: 0.95 }} animate={{ opacity: 1, scale: 1 }} className="relative bg-surface border border-white/10 rounded-3xl w-full max-w-2xl p-6 shadow-2xl max-h-[90vh] overflow-y-auto">
                <div className="flex justify-between items-center mb-6">
                    <h2 className="text-xl font-bold font-display">{isEdit ? 'Edit Tipe Kamar' : 'Tambah Tipe Kamar'}</h2>
                    <button onClick={onClose}><FiX className="text-xl text-text-secondary hover:text-white" /></button>
                </div>
                <form onSubmit={handleSubmit} className="space-y-6">
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div className="space-y-4">
                            <div>
                                <label className="block text-sm text-text-secondary mb-2">Nama Tipe</label>
                                <input
                                    value={form.name}
                                    onChange={e => setForm({ ...form, name: e.target.value })}
                                    className="w-full bg-surface-highlight border border-white/10 rounded-xl px-4 py-3 focus:border-primary outline-none"
                                    placeholder="Contoh: Deluxe Room A"
                                    required
                                />
                            </div>
                            <div>
                                <label className="block text-sm text-text-secondary mb-2">Harga Dasar (Bulanan)</label>
                                <div className="relative">
                                    <span className="absolute left-4 top-1/2 -translate-y-1/2 text-text-tertiary">Rp</span>
                                    <input
                                        type="number"
                                        min="0"
                                        value={form.price}
                                        onChange={e => setForm({ ...form, price: e.target.value })}
                                        className="w-full bg-surface-highlight border border-white/10 rounded-xl pl-10 pr-4 py-3 focus:border-primary outline-none"
                                        required
                                    />
                                </div>
                            </div>
                            <div>
                                <label className="block text-sm text-text-secondary mb-2">Luas Kamar (m²)</label>
                                <input
                                    type="number"
                                    min="0"
                                    value={form.area_m2}
                                    onChange={e => setForm({ ...form, area_m2: e.target.value })}
                                    className="w-full bg-surface-highlight border border-white/10 rounded-xl px-4 py-3 focus:border-primary outline-none"
                                    placeholder="Contoh: 12"
                                />
                            </div>
                        </div>

                        <div className="space-y-4">
                            <div>
                                <label className="block text-sm text-text-secondary mb-2">Deskripsi</label>
                                <textarea
                                    value={form.description}
                                    onChange={e => setForm({ ...form, description: e.target.value })}
                                    className="w-full bg-surface-highlight border border-white/10 rounded-xl px-4 py-3 focus:border-primary outline-none h-[132px]"
                                    placeholder="Jelaskan detail tipe kamar ini..."
                                />
                            </div>
                        </div>
                    </div>

                    <div>
                        <label className="block text-sm text-text-secondary mb-3">Fasilitas</label>
                        <div className="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            {FACILITIES_LIST.map(fac => (
                                <button
                                    key={fac}
                                    type="button"
                                    onClick={() => toggleFacility(fac)}
                                    className={`flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all border ${form.facilities.includes(fac)
                                            ? 'bg-primary/10 border-primary text-primary font-bold'
                                            : 'bg-surface-highlight border-transparent text-text-secondary hover:border-white/10'
                                        }`}
                                >
                                    <div className={`w-4 h-4 rounded border flex items-center justify-center ${form.facilities.includes(fac) ? 'bg-primary border-primary' : 'border-text-tertiary'
                                        }`}>
                                        {form.facilities.includes(fac) && <FiCheck className="text-black text-xs" />}
                                    </div>
                                    {fac}
                                </button>
                            ))}
                        </div>
                    </div>

                    <div className="pt-4 border-t border-white/10 flex justify-end gap-3">
                        <button type="button" onClick={onClose} className="btn ghost">Batal</button>
                        <button type="submit" disabled={loading} className="btn primary px-8">
                            {loading ? 'Menyimpan...' : (isEdit ? 'Simpan Perubahan' : 'Buat Tipe Kamar')}
                        </button>
                    </div>
                </form>
            </motion.div>
        </div>
    );
};

export default PropertyRoomTypes;
