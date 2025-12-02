import React, { useState } from 'react';
import { motion } from 'framer-motion';
import { useNavigate } from 'react-router-dom';
import { FiHome, FiMapPin, FiFileText, FiList, FiSave, FiArrowLeft } from 'react-icons/fi';
import { createOwnerProperty } from '../../api/client';
import MapPicker from './components/MapPicker';

const OwnerAddProperty = () => {
    const navigate = useNavigate();
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');
    const [formData, setFormData] = useState({
        name: '',
        address: '',
        description: '',
        rules_text: '',
        lat: -6.200000, // Default to Jakarta
        lng: 106.816666,
    });

    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData((prev) => ({ ...prev, [name]: value }));
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        setError('');

        try {
            const response = await createOwnerProperty(formData);
            navigate(`/owner/properties/${response.data?.id ?? response.id}`);
        } catch (err) {
            setError(err.response?.data?.message || 'Gagal membuat properti.');
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="page pt-32 pb-20">
            <div className="container max-w-3xl">
                <motion.div
                    initial={{ opacity: 0, y: 20 }}
                    animate={{ opacity: 1, y: 0 }}
                    className="mb-8"
                >
                    <button
                        onClick={() => navigate('/owner/properties')}
                        className="flex items-center text-text-secondary hover:text-primary mb-4 transition-colors"
                    >
                        <FiArrowLeft className="mr-2" /> Kembali ke Properti
                    </button>
                    <h1 className="text-4xl font-display font-bold mb-2">Tambah Properti Baru</h1>
                    <p className="text-text-secondary text-lg">
                        Isi detail properti Anda untuk mulai menyewakan.
                    </p>
                </motion.div>

                <motion.form
                    initial={{ opacity: 0, y: 20 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ delay: 0.1 }}
                    onSubmit={handleSubmit}
                    className="space-y-8"
                >
                    {error && (
                        <div className="p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-500">
                            {error}
                        </div>
                    )}

                    {/* Basic Info */}
                    <div className="card p-6 space-y-6">
                        <h2 className="text-xl font-bold font-display flex items-center gap-2 border-b border-border pb-4">
                            <FiHome className="text-primary" /> Informasi Dasar
                        </h2>

                        <div>
                            <label className="block text-sm font-medium text-text-secondary mb-2">Nama Properti</label>
                            <input
                                type="text"
                                name="name"
                                value={formData.name}
                                onChange={handleChange}
                                required
                                placeholder="Contoh: Kost Eksklusif Mawar"
                                className="w-full bg-surface-highlight border border-border rounded-xl px-4 py-3 focus:outline-none focus:border-primary transition-colors"
                            />
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-text-secondary mb-2">Deskripsi</label>
                            <textarea
                                name="description"
                                value={formData.description}
                                onChange={handleChange}
                                required
                                rows="4"
                                placeholder="Jelaskan keunggulan properti Anda..."
                                className="w-full bg-surface-highlight border border-border rounded-xl px-4 py-3 focus:outline-none focus:border-primary transition-colors"
                            />
                        </div>
                    </div>

                    {/* Location */}
                    <div className="card p-6 space-y-6">
                        <h2 className="text-xl font-bold font-display flex items-center gap-2 border-b border-border pb-4">
                            <FiMapPin className="text-primary" /> Lokasi
                        </h2>

                        <div>
                            <label className="block text-sm font-medium text-text-secondary mb-2">Alamat Lengkap</label>
                            <textarea
                                name="address"
                                value={formData.address}
                                onChange={handleChange}
                                required
                                rows="3"
                                placeholder="Jl. Contoh No. 123, Jakarta Selatan..."
                                className="w-full bg-surface-highlight border border-border rounded-xl px-4 py-3 focus:outline-none focus:border-primary transition-colors"
                            />
                        </div>

                        <div className="space-y-4">
                            <label className="block text-sm font-medium text-text-secondary">Pin Lokasi di Peta</label>
                            <MapPicker
                                lat={parseFloat(formData.lat)}
                                lng={parseFloat(formData.lng)}
                                onChange={(lat, lng) => setFormData(prev => ({ ...prev, lat, lng }))}
                            />

                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-xs font-medium text-text-tertiary mb-1">Latitude</label>
                                    <input
                                        type="number"
                                        step="any"
                                        name="lat"
                                        value={formData.lat}
                                        onChange={handleChange}
                                        readOnly
                                        className="w-full bg-surface border border-border rounded-lg px-3 py-2 text-sm text-text-secondary focus:outline-none cursor-not-allowed"
                                    />
                                </div>
                                <div>
                                    <label className="block text-xs font-medium text-text-tertiary mb-1">Longitude</label>
                                    <input
                                        type="number"
                                        step="any"
                                        name="lng"
                                        value={formData.lng}
                                        onChange={handleChange}
                                        readOnly
                                        className="w-full bg-surface border border-border rounded-lg px-3 py-2 text-sm text-text-secondary focus:outline-none cursor-not-allowed"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Rules */}
                    <div className="card p-6 space-y-6">
                        <h2 className="text-xl font-bold font-display flex items-center gap-2 border-b border-border pb-4">
                            <FiList className="text-primary" /> Peraturan
                        </h2>

                        <div>
                            <label className="block text-sm font-medium text-text-secondary mb-2">Peraturan Kost</label>
                            <textarea
                                name="rules_text"
                                value={formData.rules_text}
                                onChange={handleChange}
                                required
                                rows="5"
                                placeholder="- Dilarang merokok di dalam kamar&#10;- Tamu menginap maksimal 2 hari&#10;- Jam malam pukul 23:00"
                                className="w-full bg-surface-highlight border border-border rounded-xl px-4 py-3 focus:outline-none focus:border-primary transition-colors"
                            />
                        </div>
                    </div>

                    <div className="flex justify-end pt-4">
                        <button
                            type="submit"
                            disabled={loading}
                            className="btn primary px-8 py-3 text-lg shadow-lg shadow-primary/20"
                        >
                            {loading ? 'Menyimpan...' : (
                                <>
                                    <FiSave className="mr-2" /> Simpan Properti
                                </>
                            )}
                        </button>
                    </div>
                </motion.form>
            </div>
        </div>
    );
};

export default OwnerAddProperty;
