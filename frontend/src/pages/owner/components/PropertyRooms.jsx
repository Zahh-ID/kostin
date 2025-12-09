import React, { useState, useMemo } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { FiSearch, FiFilter, FiGrid, FiList, FiPlus, FiTrash2, FiSettings, FiCheck, FiX, FiMoreVertical, FiEdit2 } from 'react-icons/fi';
import { createOwnerRoom, updateOwnerRoom, deleteOwnerRoom, createPropertyRoomsBulk } from '../../../api/client';
import ConfirmationModal from '../../../ui/ConfirmationModal';

const PropertyRooms = ({ rooms, property, onUpdate }) => {
    const [viewMode, setViewMode] = useState('grid'); // 'grid' | 'table'
    const [search, setSearch] = useState('');
    const [filterStatus, setFilterStatus] = useState('all');
    const [filterType, setFilterType] = useState('all');
    const [selectedRooms, setSelectedRooms] = useState([]);

    // Modals
    const [isAddModalOpen, setIsAddModalOpen] = useState(false);
    const [isBulkModalOpen, setIsBulkModalOpen] = useState(false);
    const [editingRoom, setEditingRoom] = useState(null);
    const [deletingRoom, setDeletingRoom] = useState(null);
    const [bulkAction, setBulkAction] = useState(null); // 'delete' | 'status'

    // Derived Data
    const filteredRooms = useMemo(() => {
        return rooms.filter(room => {
            const matchSearch = room.room_code.toLowerCase().includes(search.toLowerCase());
            const matchStatus = filterStatus === 'all' || room.status === filterStatus;
            const matchType = filterType === 'all' || room.room_type_id === parseInt(filterType);
            return matchSearch && matchStatus && matchType;
        });
    }, [rooms, search, filterStatus, filterType]);

    // Selection Handlers
    const toggleSelect = (id) => {
        setSelectedRooms(prev => prev.includes(id) ? prev.filter(x => x !== id) : [...prev, id]);
    };

    const toggleSelectAll = () => {
        if (selectedRooms.length === filteredRooms.length) {
            setSelectedRooms([]);
        } else {
            setSelectedRooms(filteredRooms.map(r => r.id));
        }
    };

    // Bulk Actions
    const handleBulkDelete = async () => {
        if (!window.confirm(`Yakin hapus ${selectedRooms.length} kamar terpilih?`)) return;
        try {
            await Promise.all(selectedRooms.map(id => deleteOwnerRoom(id)));
            setSelectedRooms([]);
            onUpdate();
        } catch (err) {
            console.error(err);
            alert('Gagal menghapus beberapa kamar.');
        }
    };

    const handleBulkStatus = async (status) => {
        try {
            await Promise.all(selectedRooms.map(id => updateOwnerRoom(id, { status })));
            setSelectedRooms([]);
            setBulkAction(null);
            onUpdate();
        } catch (err) {
            console.error(err);
            alert('Gagal mengupdate status.');
        }
    };

    return (
        <div className="space-y-6">
            {/* Toolbar */}
            <div className="flex flex-col md:flex-row gap-4 justify-between items-end md:items-center">
                <div className="flex gap-2 w-full md:w-auto">
                    <div className="relative flex-grow md:flex-grow-0">
                        <FiSearch className="absolute left-3 top-1/2 -translate-y-1/2 text-text-tertiary" />
                        <input
                            value={search}
                            onChange={e => setSearch(e.target.value)}
                            placeholder="Cari nomor kamar..."
                            className="w-full md:w-64 bg-surface-highlight border border-white/10 rounded-xl pl-10 pr-4 py-2.5 focus:border-primary outline-none"
                        />
                    </div>
                    <div className="relative">
                        <select
                            value={filterStatus}
                            onChange={e => setFilterStatus(e.target.value)}
                            className="bg-surface-highlight border border-white/10 rounded-xl px-4 py-2.5 pr-8 focus:border-primary outline-none appearance-none cursor-pointer"
                        >
                            <option value="all">Semua Status</option>
                            <option value="available">Available</option>
                            <option value="occupied">Occupied</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                        <FiFilter className="absolute right-3 top-1/2 -translate-y-1/2 text-text-tertiary pointer-events-none" />
                    </div>
                </div>

                <div className="flex gap-2">
                    <div className="bg-surface-highlight rounded-lg p-1 flex border border-white/10">
                        <button
                            onClick={() => setViewMode('grid')}
                            className={`p-2 rounded-md transition-colors ${viewMode === 'grid' ? 'bg-white/10 text-white' : 'text-text-secondary hover:text-white'}`}
                        >
                            <FiGrid />
                        </button>
                        <button
                            onClick={() => setViewMode('table')}
                            className={`p-2 rounded-md transition-colors ${viewMode === 'table' ? 'bg-white/10 text-white' : 'text-text-secondary hover:text-white'}`}
                        >
                            <FiList />
                        </button>
                    </div>

                    <div className="h-full w-px bg-white/10 mx-2" />

                    <button onClick={() => setIsAddModalOpen(true)} className="btn secondary">
                        <FiPlus /> <span className="hidden sm:inline ml-2">Manual</span>
                    </button>
                    <button onClick={() => setIsBulkModalOpen(true)} className="btn primary">
                        <FiPlus /> <span className="hidden sm:inline ml-2">Bulk Add</span>
                    </button>
                </div>
            </div>

            {/* Bulk Action Bar */}
            <AnimatePresence>
                {selectedRooms.length > 0 && (
                    <motion.div
                        initial={{ opacity: 0, y: -20 }}
                        animate={{ opacity: 1, y: 0 }}
                        exit={{ opacity: 0, y: -20 }}
                        className="bg-primary/10 border border-primary/20 rounded-xl p-3 flex justify-between items-center"
                    >
                        <div className="flex items-center gap-3">
                            <span className="font-bold text-primary ml-2">{selectedRooms.length} terpilih</span>
                            <div className="h-4 w-px bg-primary/20" />
                            <button onClick={() => setSelectedRooms([])} className="text-sm text-text-secondary hover:text-white">
                                Batal
                            </button>
                        </div>
                        <div className="flex gap-2">
                            <select
                                onChange={(e) => { if (e.target.value) handleBulkStatus(e.target.value); }}
                                className="bg-surface border border-white/10 rounded-lg px-3 py-1.5 text-sm focus:border-primary outline-none"
                                defaultValue=""
                            >
                                <option value="" disabled>Ubah Status...</option>
                                <option value="available">Set Available</option>
                                <option value="occupied">Set Occupied</option>
                                <option value="maintenance">Set Maintenance</option>
                            </select>
                            <button onClick={handleBulkDelete} className="btn bg-red-500/10 text-red-500 border-red-500/20 hover:bg-red-500/20 py-1.5 px-3 text-sm">
                                <FiTrash2 className="mr-2" /> Hapus
                            </button>
                        </div>
                    </motion.div>
                )}
            </AnimatePresence>

            {/* Content */}
            {filteredRooms.length === 0 ? (
                <div className="text-center py-20 border-2 border-dashed border-white/10 rounded-2xl">
                    <p className="text-text-secondary">Tidak ada kamar yang ditemukan.</p>
                </div>
            ) : viewMode === 'grid' ? (
                <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                    {filteredRooms.map(room => (
                        <div
                            key={room.id}
                            className={`card p-4 group relative cursor-pointer transition-all ${selectedRooms.includes(room.id) ? 'ring-2 ring-primary bg-primary/5' : 'hover:bg-surface-highlight'}`}
                            onClick={() => toggleSelect(room.id)}
                        >
                            <div className="flex justify-between items-start mb-2">
                                <h3 className="font-bold text-lg">{room.room_code}</h3>
                                <div className={`w-3 h-3 rounded-full ${room.status === 'available' ? 'bg-green-500' :
                                        room.status === 'occupied' ? 'bg-blue-500' : 'bg-yellow-500'
                                    }`} />
                            </div>
                            <p className="text-xs text-text-secondary mb-1">{room.room_type?.name}</p>
                            {room.custom_price && (
                                <p className="text-xs text-primary font-bold">
                                    Rp{Number(room.custom_price).toLocaleString('id-ID')}
                                </p>
                            )}

                            <div className="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity flex gap-1 bg-surface/80 backdrop-blur rounded-lg p-1" onClick={e => e.stopPropagation()}>
                                <button onClick={() => setEditingRoom(room)} className="p-1.5 hover:text-primary"><FiSettings size={14} /></button>
                            </div>
                        </div>
                    ))}
                </div>
            ) : (
                <div className="overflow-x-auto rounded-xl border border-white/10">
                    <table className="w-full text-left border-collapse">
                        <thead className="bg-surface-highlight text-text-secondary text-xs uppercase tracking-wider">
                            <tr>
                                <th className="p-4 w-10">
                                    <input
                                        type="checkbox"
                                        checked={selectedRooms.length === filteredRooms.length && filteredRooms.length > 0}
                                        onChange={toggleSelectAll}
                                        className="rounded border-white/20 bg-surface focus:ring-primary"
                                    />
                                </th>
                                <th className="p-4">Nomor Kamar</th>
                                <th className="p-4">Tipe</th>
                                <th className="p-4">Status</th>
                                <th className="p-4">Harga</th>
                                <th className="p-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-white/5">
                            {filteredRooms.map(room => (
                                <tr key={room.id} className={`hover:bg-surface-highlight transition-colors ${selectedRooms.includes(room.id) ? 'bg-primary/5' : ''}`}>
                                    <td className="p-4">
                                        <input
                                            type="checkbox"
                                            checked={selectedRooms.includes(room.id)}
                                            onChange={() => toggleSelect(room.id)}
                                            className="rounded border-white/20 bg-surface focus:ring-primary"
                                        />
                                    </td>
                                    <td className="p-4 font-bold">{room.room_code}</td>
                                    <td className="p-4 text-sm text-text-secondary">{room.room_type?.name}</td>
                                    <td className="p-4">
                                        <span className={`px-2 py-1 rounded text-xs font-bold uppercase ${room.status === 'available' ? 'bg-green-500/10 text-green-400' :
                                                room.status === 'occupied' ? 'bg-blue-500/10 text-blue-400' :
                                                    'bg-yellow-500/10 text-yellow-400'
                                            }`}>
                                            {room.status}
                                        </span>
                                    </td>
                                    <td className="p-4 text-sm">
                                        {room.custom_price ? (
                                            <span className="text-primary font-bold">Rp{Number(room.custom_price).toLocaleString('id-ID')}</span>
                                        ) : (
                                            <span className="text-text-tertiary">Default</span>
                                        )}
                                    </td>
                                    <td className="p-4 text-right">
                                        <button onClick={() => setEditingRoom(room)} className="p-2 hover:bg-white/10 rounded-lg transition-colors text-text-secondary hover:text-primary">
                                            <FiEdit2 />
                                        </button>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            )}

            {/* Modals */}
            <AnimatePresence>
                {isAddModalOpen && (
                    <AddRoomModal
                        property={property}
                        onClose={() => setIsAddModalOpen(false)}
                        onSuccess={() => { setIsAddModalOpen(false); onUpdate(); }}
                    />
                )}
                {isBulkModalOpen && (
                    <BulkCreateModal
                        propertyId={property.id}
                        roomTypes={property.room_types}
                        onClose={() => setIsBulkModalOpen(false)}
                        onSuccess={() => { setIsBulkModalOpen(false); onUpdate(); }}
                    />
                )}
                {editingRoom && (
                    <EditRoomModal
                        room={editingRoom}
                        onClose={() => setEditingRoom(null)}
                        onSuccess={() => { setEditingRoom(null); onUpdate(); }}
                    />
                )}
            </AnimatePresence>
        </div>
    );
};

// --- Sub Components ---

const AddRoomModal = ({ property, onClose, onSuccess }) => {
    const [form, setForm] = useState({
        room_type_id: property.room_types?.[0]?.id || '',
        room_code: '',
        status: 'available',
        custom_price: '',
    });
    const [loading, setLoading] = useState(false);

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        try {
            await createOwnerRoom(form);
            onSuccess();
        } catch (err) {
            console.error(err);
            alert('Gagal membuat kamar.');
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="fixed inset-0 z-[2000] flex items-center justify-center p-4">
            <div className="absolute inset-0 bg-black/80 backdrop-blur-sm" onClick={onClose} />
            <motion.div initial={{ opacity: 0, scale: 0.95 }} animate={{ opacity: 1, scale: 1 }} className="relative bg-surface border border-white/10 rounded-3xl w-full max-w-md p-6 shadow-2xl">
                <div className="flex justify-between items-center mb-6">
                    <h2 className="text-xl font-bold font-display">Tambah Kamar Manual</h2>
                    <button onClick={onClose}><FiX className="text-xl text-text-secondary hover:text-white" /></button>
                </div>
                <form onSubmit={handleSubmit} className="space-y-4">
                    <div>
                        <label className="block text-sm text-text-secondary mb-2">Tipe Kamar</label>
                        <select
                            value={form.room_type_id}
                            onChange={e => setForm({ ...form, room_type_id: e.target.value })}
                            className="w-full bg-surface-highlight border border-white/10 rounded-xl px-4 py-3 focus:border-primary outline-none"
                            required
                        >
                            {property.room_types?.map(type => <option key={type.id} value={type.id}>{type.name}</option>)}
                        </select>
                    </div>
                    <div>
                        <label className="block text-sm text-text-secondary mb-2">Nomor Kamar</label>
                        <input
                            value={form.room_code}
                            onChange={e => setForm({ ...form, room_code: e.target.value })}
                            className="w-full bg-surface-highlight border border-white/10 rounded-xl px-4 py-3 focus:border-primary outline-none"
                            placeholder="Contoh: 101"
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
                            placeholder="Kosongkan untuk mengikuti harga tipe"
                        />
                    </div>
                    <button type="submit" disabled={loading} className="btn primary w-full justify-center">
                        {loading ? 'Menyimpan...' : 'Simpan Kamar'}
                    </button>
                </form>
            </motion.div>
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
            alert('Gagal mengupdate kamar.');
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
                        <label className="block text-sm text-text-secondary mb-2">Nomor Kamar</label>
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
                            placeholder="Kosongkan untuk mengikuti harga tipe"
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

const BulkCreateModal = ({ propertyId, roomTypes, onClose, onSuccess }) => {
    const [form, setForm] = useState({
        room_type_id: roomTypes?.[0]?.id || '',
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
            alert('Gagal membuat kamar bulk.');
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
                            {roomTypes?.map(type => <option key={type.id} value={type.id}>{type.name}</option>)}
                        </select>
                    </div>
                    <div className="grid grid-cols-2 gap-4">
                        <div>
                            <label className="block text-sm text-text-secondary mb-2">Prefix (Awalan)</label>
                            <input
                                value={form.prefix}
                                onChange={e => setForm({ ...form, prefix: e.target.value })}
                                className="w-full bg-surface-highlight border border-white/10 rounded-xl px-4 py-3 focus:border-primary outline-none"
                                placeholder="Contoh: A-"
                            />
                        </div>
                        <div>
                            <label className="block text-sm text-text-secondary mb-2">Mulai Nomor</label>
                            <input
                                type="number"
                                min="1"
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
                            min="1"
                            max="50"
                            value={form.count}
                            onChange={e => setForm({ ...form, count: parseInt(e.target.value) })}
                            className="w-full bg-surface-highlight border border-white/10 rounded-xl px-4 py-3 focus:border-primary outline-none"
                        />
                    </div>
                    <div className="p-4 rounded-xl bg-surface-highlight border border-white/5 text-sm text-text-secondary">
                        <p>Preview: <strong>{form.prefix}{form.start_number}</strong> sampai <strong>{form.prefix}{form.start_number + form.count - 1}</strong></p>
                    </div>
                    <button type="submit" disabled={loading} className="btn primary w-full justify-center">
                        {loading ? 'Creating...' : 'Generate Rooms'}
                    </button>
                </form>
            </motion.div>
        </div>
    );
};

export default PropertyRooms;
