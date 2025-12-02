import React, { useEffect, useState } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { motion, AnimatePresence } from 'framer-motion';
import { FiArrowLeft, FiHome, FiLayers, FiGrid, FiPlus, FiSettings, FiSave, FiTrash2, FiCheckCircle, FiAlertCircle, FiX } from 'react-icons/fi';
import { fetchOwnerProperty, updateOwnerProperty, deleteOwnerProperty, fetchPropertyRooms, createPropertyRoomsBulk, createOwnerRoom, updateOwnerRoom, deleteOwnerRoom, createOwnerRoomType, updateOwnerRoomType, deleteOwnerRoomType, api } from '../../api/client';
import ConfirmationModal from '../../ui/ConfirmationModal';

const PropertyDetail = () => {
    const { id } = useParams();
    const navigate = useNavigate();
    const [property, setProperty] = useState(null);
    const [rooms, setRooms] = useState([]);
    const [loading, setLoading] = useState(true);
    const [activeTab, setActiveTab] = useState('overview');
    const [bulkModalOpen, setBulkModalOpen] = useState(false);

    const loadData = async () => {
        setLoading(true);
        try {
            const propData = await fetchOwnerProperty(id);
            setProperty(propData.data ?? propData);
            const roomData = await fetchPropertyRooms(id);
            setRooms(roomData.data ?? []);
        } catch (error) {
            console.error(error);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        loadData();
    }, [id]);

    if (loading) return <div className="page pt-32 pb-20 container text-center">Loading...</div>;
    if (!property) return <div className="page pt-32 pb-20 container text-center">Properti tidak ditemukan</div>;

    return (
        <div className="page pt-32 pb-20">
            <div className="container">
                <div className="mb-8">
                    <button onClick={() => navigate('/owner/properties')} className="flex items-center text-text-secondary hover:text-primary mb-4 transition-colors">
                        <FiArrowLeft className="mr-2" /> Kembali
                    </button>
                    <div className="flex justify-between items-end">
                        <div>
                            <h1 className="text-4xl font-display font-bold mb-2">{property.name}</h1>
                            <p className="text-text-secondary text-lg">{property.address}</p>
                        </div>
                        <div className="flex gap-3">
                            <button onClick={() => setBulkModalOpen(true)} className="btn secondary">
                                <FiPlus className="mr-2" /> Bulk Add Rooms
                            </button>
                        </div>
                    </div>
                </div>

                {/* Tabs */}
                <div className="flex gap-6 border-b border-white/10 mb-8">
                    {['overview', 'rooms', 'room types', 'settings'].map(tab => (
                        <button
                            key={tab}
                            onClick={() => setActiveTab(tab)}
                            className={`pb-4 text-sm font-bold uppercase tracking-wider border-b-2 transition-colors ${activeTab === tab ? 'border-primary text-primary' : 'border-transparent text-text-secondary hover:text-white'
                                }`}
                        >
                            {tab}
                        </button>
                    ))}
                </div>

                <div className="min-h-[400px]">
                    {activeTab === 'overview' && <OverviewTab property={property} rooms={rooms} />}
                    {activeTab === 'rooms' && <RoomsTab rooms={rooms} propertyId={id} onUpdate={loadData} />}
                    {activeTab === 'room types' && <RoomTypesTab property={property} onUpdate={loadData} />}
                    {activeTab === 'settings' && <SettingsTab property={property} onUpdate={loadData} />}
                </div>
            </div>

            <AnimatePresence>
                {bulkModalOpen && (
                    <BulkCreateModal
                        propertyId={id}
                        roomTypes={property.room_types ?? []}
                        onClose={() => setBulkModalOpen(false)}
                        onSuccess={() => {
                            setBulkModalOpen(false);
                            loadData();
                        }}
                    />
                )}
            </AnimatePresence>
        </div>
    );
};

const OverviewTab = ({ property, rooms }) => {
    const stats = {
        total: rooms.length,
        available: rooms.filter(r => r.status === 'available').length,
        occupied: rooms.filter(r => r.status === 'occupied').length,
        maintenance: rooms.filter(r => r.status === 'maintenance').length,
    };

    return (
        <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div className="card p-6 bg-surface-highlight">
                <div className="text-text-secondary text-sm mb-1">Total Kamar</div>
                <div className="text-3xl font-bold">{stats.total}</div>
            </div>
            <div className="card p-6 bg-green-500/10 border-green-500/20">
                <div className="text-green-400 text-sm mb-1">Tersedia</div>
                <div className="text-3xl font-bold text-green-400">{stats.available}</div>
            </div>
            <div className="card p-6 bg-blue-500/10 border-blue-500/20">
                <div className="text-blue-400 text-sm mb-1">Terisi</div>
                <div className="text-3xl font-bold text-blue-400">{stats.occupied}</div>
            </div>
            <div className="card p-6 bg-yellow-500/10 border-yellow-500/20">
                <div className="text-yellow-400 text-sm mb-1">Maintenance</div>
                <div className="text-3xl font-bold text-yellow-400">{stats.maintenance}</div>
            </div>
        </div>
    );
};

const RoomsTab = ({ rooms, propertyId, onUpdate }) => {
    const [editingRoom, setEditingRoom] = useState(null);
    const [deletingRoom, setDeletingRoom] = useState(null);

    const handleDelete = async () => {
        if (!deletingRoom) return;
        try {
            await deleteOwnerRoom(deletingRoom.id);
            onUpdate();
            setDeletingRoom(null);
        } catch (err) {
            console.error(err);
        }
    };

    return (
        <div>
            <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                {rooms.map(room => (
                    <div key={room.id} className="card p-4 hover:bg-surface-highlight transition-colors group relative">
                        <div className="absolute top-2 right-2 flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity bg-surface/80 backdrop-blur rounded-lg p-1">
                            <button
                                onClick={() => setEditingRoom(room)}
                                className="p-1.5 hover:text-primary transition-colors"
                            >
                                <FiSettings size={14} />
                            </button>
                            <button
                                onClick={() => setDeletingRoom(room)}
                                className="p-1.5 hover:text-red-500 transition-colors"
                            >
                                <FiTrash2 size={14} />
                            </button>
                        </div>

                        <div className="flex justify-between items-start mb-2">
                            <h3 className="font-bold text-lg">{room.room_code}</h3>
                            <span className={`px-2 py-1 rounded text-xs font-bold uppercase ${room.status === 'available' ? 'bg-green-500/10 text-green-400' :
                                room.status === 'occupied' ? 'bg-blue-500/10 text-blue-400' :
                                    'bg-yellow-500/10 text-yellow-400'
                                }`}>
                                {room.status}
                            </span>
                        </div>
                        <p className="text-sm text-text-secondary">{room.room_type?.name}</p>
                        {room.custom_price && (
                            <p className="text-xs text-primary mt-1">
                                Custom: Rp{Number(room.custom_price).toLocaleString('id-ID')}
                            </p>
                        )}
                    </div>
                ))}
            </div>

            <AnimatePresence>
                {editingRoom && (
                    <EditRoomModal
                        room={editingRoom}
                        onClose={() => setEditingRoom(null)}
                        onSuccess={() => {
                            setEditingRoom(null);
                            onUpdate();
                        }}
                    />
                )}
                {deletingRoom && (
                    <ConfirmationModal
                        isOpen={!!deletingRoom}
                        onClose={() => setDeletingRoom(null)}
                        onConfirm={handleDelete}
                        title="Hapus Kamar?"
                        message={`Apakah Anda yakin ingin menghapus kamar "${deletingRoom.room_code}"?`}
                        confirmText="Ya, Hapus"
                        type="danger"
                    />
                )}
            </AnimatePresence>
        </div>
    );
};

const EditRoomModal = ({ room, onClose, onSuccess }) => {
    const [form, setForm] = useState({
        room_code: room.room_code,
        status: room.status,
        custom_price: room.custom_price || '',
    });
    const [loading, setLoading] = useState(false);

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        try {
            await updateOwnerRoom(room.id, form);
            onSuccess();
        } catch (err) {
            console.error(err);
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="fixed inset-0 z-[2000] flex items-center justify-center p-4">
            <div className="absolute inset-0 bg-black/80 backdrop-blur-sm" onClick={onClose} />
            <motion.div initial={{ opacity: 0, scale: 0.95 }} animate={{ opacity: 1, scale: 1 }} className="relative bg-surface border border-white/10 rounded-3xl w-full max-w-md p-6 shadow-2xl">
                <div className="flex justify-between items-center mb-6">
                    <h2 className="text-xl font-bold font-display">Edit Kamar {room.room_code}</h2>
                    <button onClick={onClose}><FiX className="text-xl text-text-secondary hover:text-white" /></button>
                </div>
                <form onSubmit={handleSubmit} className="space-y-4">
                    <div>
                        <label className="block text-sm text-text-secondary mb-2">Kode Kamar</label>
                        <input
                            value={form.room_code}
                            onChange={e => setForm({ ...form, room_code: e.target.value })}
                            className="w-full bg-surface-highlight border border-white/10 rounded-xl px-4 py-3 focus:border-primary outline-none"
                            required
                        />
                    </div>
                    <div>
                        <label className="block text-sm text-text-secondary mb-2">Status</label>
                        <select
                            value={form.status}
                            onChange={e => setForm({ ...form, status: e.target.value })}
                            className="w-full bg-surface-highlight border border-white/10 rounded-xl px-4 py-3 focus:border-primary outline-none"
                        >
                            <option value="available">Available</option>
                            <option value="occupied">Occupied</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                    </div>
                    <div>
                        <label className="block text-sm text-text-secondary mb-2">Harga Custom (Opsional)</label>
                        <input
                            type="number"
                            value={form.custom_price}
                            onChange={e => setForm({ ...form, custom_price: e.target.value })}
                            className="w-full bg-surface-highlight border border-white/10 rounded-xl px-4 py-3 focus:border-primary outline-none"
                            placeholder="Kosongkan untuk mengikuti harga tipe kamar"
                        />
                    </div>
                    <button type="submit" disabled={loading} className="btn primary w-full justify-center">
                        {loading ? 'Menyimpan...' : 'Simpan Perubahan'}
                    </button>
                </form>
            </motion.div>
        </div>
    );
};

const SettingsTab = ({ property, onUpdate }) => {
    const navigate = useNavigate();
    const [form, setForm] = useState({
        name: property.name,
        address: property.address,
        rules_text: property.rules_text,
    });
    const [saving, setSaving] = useState(false);
    const [deleting, setDeleting] = useState(false);
    const [showDeleteConfirm, setShowDeleteConfirm] = useState(false);

    const handleSave = async () => {
        setSaving(true);
        try {
            await updateOwnerProperty(property.id, form);
            onUpdate();
        } catch (err) {
            console.error(err);
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
        }
    };

    return (
        <div className="max-w-2xl space-y-8">
            <div className="space-y-6">
                <div>
                    <label className="block text-sm text-text-secondary mb-2">Nama Properti</label>
                    <input
                        value={form.name}
                        onChange={e => setForm({ ...form, name: e.target.value })}
                        className="w-full bg-surface border border-white/10 rounded-xl px-4 py-3 focus:border-primary outline-none"
                    />
                </div>
                <div>
                    <label className="block text-sm text-text-secondary mb-2">Alamat</label>
                    <textarea
                        value={form.address}
                        onChange={e => setForm({ ...form, address: e.target.value })}
                        className="w-full bg-surface border border-white/10 rounded-xl px-4 py-3 focus:border-primary outline-none"
                        rows={3}
                    />
                </div>
                <button onClick={handleSave} disabled={saving} className="btn primary">
                    {saving ? 'Menyimpan...' : 'Simpan Perubahan'}
                </button>
            </div>

            <div className="pt-8 border-t border-white/10">
                <h3 className="text-lg font-bold text-red-500 mb-2">Danger Zone</h3>
                <p className="text-text-secondary text-sm mb-4">
                    Menghapus properti akan menghapus semua data kamar dan tipe kamar yang terkait. Tindakan ini tidak dapat dibatalkan.
                </p>
                <button
                    onClick={() => setShowDeleteConfirm(true)}
                    className="btn border border-red-500/20 text-red-500 hover:bg-red-500/10"
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

const RoomTypesTab = ({ property, onUpdate }) => {
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
        }
    };

    return (
        <div>
            <div className="flex justify-between items-center mb-6">
                <h3 className="text-xl font-bold">Daftar Tipe Kamar</h3>
                <button
                    onClick={() => { setEditingType(null); setIsModalOpen(true); }}
                    className="btn secondary text-sm"
                >
                    <FiPlus className="mr-2" /> Tambah Tipe
                </button>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                {property.room_types?.map(type => (
                    <div key={type.id} className="card p-4 hover:bg-surface-highlight transition-colors group relative">
                        <div className="absolute top-4 right-4 flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button
                                onClick={() => handleEdit(type)}
                                className="p-2 bg-surface border border-white/10 rounded-lg hover:border-primary hover:text-primary transition-colors"
                            >
                                <FiSettings />
                            </button>
                            <button
                                onClick={() => setDeletingType(type)}
                                className="p-2 bg-surface border border-white/10 rounded-lg hover:border-red-500 hover:text-red-500 transition-colors"
                            >
                                <FiTrash2 />
                            </button>
                        </div>

                        <div className="flex justify-between items-start mb-2 pr-16">
                            <h4 className="font-bold text-lg">{type.name}</h4>
                        </div>
                        <div className="text-primary font-bold mb-2">
                            Rp{Number(type.price).toLocaleString('id-ID')}
                        </div>
                        <p className="text-sm text-text-secondary mb-4 line-clamp-2">{type.description}</p>
                        <div className="flex gap-2 text-xs text-text-secondary">
                            <span className="bg-surface-highlight px-2 py-1 rounded">
                                {type.area_m2} m²
                            </span>
                            <span className="bg-surface-highlight px-2 py-1 rounded">
                                {type.rooms_count ?? 0} Unit
                            </span>
                        </div>
                    </div>
                ))}
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
        price: roomType?.price || '',
        area_m2: roomType?.area_m2 || '',
        facilities: roomType?.facilities || [],
    });
    const [loading, setLoading] = useState(false);

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        try {
            if (isEdit) {
                await updateOwnerRoomType(roomType.id, form);
            } else {
                await createOwnerRoomType({ ...form, property_id: propertyId });
            }
            onSuccess();
        } catch (err) {
            console.error(err);
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="fixed inset-0 z-[2000] flex items-center justify-center p-4">
            <div className="absolute inset-0 bg-black/80 backdrop-blur-sm" onClick={onClose} />
            <motion.div initial={{ opacity: 0, scale: 0.95 }} animate={{ opacity: 1, scale: 1 }} className="relative bg-surface border border-white/10 rounded-3xl w-full max-w-lg p-6 shadow-2xl">
                <div className="flex justify-between items-center mb-6">
                    <h2 className="text-xl font-bold font-display">{isEdit ? 'Edit Tipe Kamar' : 'Tambah Tipe Kamar'}</h2>
                    <button onClick={onClose}><FiX className="text-xl text-text-secondary hover:text-white" /></button>
                </div>
                <form onSubmit={handleSubmit} className="space-y-4">
                    <div>
                        <label className="block text-sm text-text-secondary mb-2">Nama Tipe</label>
                        <input
                            value={form.name}
                            onChange={e => setForm({ ...form, name: e.target.value })}
                            className="w-full bg-surface-highlight border border-white/10 rounded-xl px-4 py-3 focus:border-primary outline-none"
                            required
                        />
                    </div>
                    <div>
                        <label className="block text-sm text-text-secondary mb-2">Harga Dasar (Bulanan)</label>
                        <input
                            type="number"
                            value={form.price}
                            onChange={e => setForm({ ...form, price: e.target.value })}
                            className="w-full bg-surface-highlight border border-white/10 rounded-xl px-4 py-3 focus:border-primary outline-none"
                            required
                        />
                    </div>
                    <div>
                        <label className="block text-sm text-text-secondary mb-2">Luas (m²)</label>
                        <input
                            type="number"
                            value={form.area_m2}
                            onChange={e => setForm({ ...form, area_m2: e.target.value })}
                            className="w-full bg-surface-highlight border border-white/10 rounded-xl px-4 py-3 focus:border-primary outline-none"
                        />
                    </div>
                    <div>
                        <label className="block text-sm text-text-secondary mb-2">Deskripsi</label>
                        <textarea
                            value={form.description}
                            onChange={e => setForm({ ...form, description: e.target.value })}
                            className="w-full bg-surface-highlight border border-white/10 rounded-xl px-4 py-3 focus:border-primary outline-none"
                            rows={3}
                        />
                    </div>
                    <button type="submit" disabled={loading} className="btn primary w-full justify-center">
                        {loading ? 'Menyimpan...' : (isEdit ? 'Simpan Perubahan' : 'Simpan Tipe Kamar')}
                    </button>
                </form>
            </motion.div>
        </div>
    );
};

const BulkCreateModal = ({ propertyId, roomTypes, onClose, onSuccess }) => {
    const [form, setForm] = useState({
        room_type_id: (roomTypes && roomTypes.length > 0 && roomTypes[0]?.id) ? parseInt(roomTypes[0].id) : '',
        prefix: 'A-',
        start_number: 1,
        count: 10,
        suffix: '',
        status: 'available',
    });
    const [loading, setLoading] = useState(false);

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        try {
            await createPropertyRoomsBulk(propertyId, form);
            onSuccess();
        } catch (err) {
            console.error(err);
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="fixed inset-0 z-[2000] flex items-center justify-center p-4">
            <div className="absolute inset-0 bg-black/80 backdrop-blur-sm" onClick={onClose} />
            <motion.div initial={{ opacity: 0, scale: 0.95 }} animate={{ opacity: 1, scale: 1 }} className="relative bg-surface border border-white/10 rounded-3xl w-full max-w-lg p-6 shadow-2xl">
                <div className="flex justify-between items-center mb-6">
                    <h2 className="text-xl font-bold font-display">Bulk Add Rooms</h2>
                    <button onClick={onClose}><FiX className="text-xl text-text-secondary hover:text-white" /></button>
                </div>
                <form onSubmit={handleSubmit} className="space-y-4">
                    <div>
                        <label className="block text-sm text-text-secondary mb-2">Tipe Kamar</label>
                        <select
                            value={form.room_type_id}
                            onChange={e => setForm({ ...form, room_type_id: e.target.value })}
                            className="w-full bg-surface-highlight border border-white/10 rounded-xl px-4 py-3 focus:border-primary outline-none"
                        >
                            {roomTypes.map(type => <option key={type.id} value={type.id}>{type.name}</option>)}
                        </select>
                    </div>
                    <div className="grid grid-cols-2 gap-4">
                        <div>
                            <label className="block text-sm text-text-secondary mb-2">Prefix</label>
                            <input
                                value={form.prefix}
                                onChange={e => setForm({ ...form, prefix: e.target.value })}
                                className="w-full bg-surface-highlight border border-white/10 rounded-xl px-4 py-3 focus:border-primary outline-none"
                                placeholder="e.g. A-"
                            />
                        </div>
                        <div>
                            <label className="block text-sm text-text-secondary mb-2">Mulai Nomor</label>
                            <input
                                type="number"
                                value={form.start_number}
                                onChange={e => setForm({ ...form, start_number: parseInt(e.target.value) })}
                                className="w-full bg-surface-highlight border border-white/10 rounded-xl px-4 py-3 focus:border-primary outline-none"
                            />
                        </div>
                    </div>
                    <div>
                        <label className="block text-sm text-text-secondary mb-2">Jumlah Kamar</label>
                        <input
                            type="number"
                            value={form.count}
                            onChange={e => setForm({ ...form, count: parseInt(e.target.value) })}
                            className="w-full bg-surface-highlight border border-white/10 rounded-xl px-4 py-3 focus:border-primary outline-none"
                            max="50"
                        />
                    </div>
                    <button type="submit" disabled={loading} className="btn primary w-full justify-center">
                        {loading ? 'Creating...' : 'Create Rooms'}
                    </button>
                </form>
            </motion.div>
        </div>
    );
};

export default PropertyDetail;
