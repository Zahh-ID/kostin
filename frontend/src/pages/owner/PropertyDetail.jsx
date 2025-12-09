import React, { useEffect, useState, useMemo } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { motion, AnimatePresence } from 'framer-motion';
import { FiArrowLeft, FiCheckCircle, FiX, FiAlertCircle, FiCheck } from 'react-icons/fi';
import { fetchOwnerProperty, submitOwnerProperty, withdrawOwnerProperty, fetchPropertyRooms } from '../../api/client';

// Components
import PropertyOverview from './components/PropertyOverview';
import PropertyRooms from './components/PropertyRooms';
import PropertyRoomTypes from './components/PropertyRoomTypes';
import PropertyPhotos from './components/PropertyPhotos';
import PropertySettings from './components/PropertySettings';

const PropertyDetail = () => {
    const { id } = useParams();
    const navigate = useNavigate();
    const [property, setProperty] = useState(null);
    const [rooms, setRooms] = useState([]);
    const [loading, setLoading] = useState(true);
    const [activeTab, setActiveTab] = useState('overview');

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

    // Readiness Logic
    const readiness = useMemo(() => {
        if (!property) return { percent: 0, checks: [] };

        const checks = [
            { label: 'Informasi Dasar', valid: !!property.name && !!property.address && !!property.description },
            { label: 'Tipe Kamar', valid: property.room_types?.length > 0 },
            { label: 'Unit Kamar', valid: rooms.length > 0 },
            { label: 'Foto Properti', valid: property.photos?.length > 0 },
        ];

        const validCount = checks.filter(c => c.valid).length;
        const percent = Math.round((validCount / checks.length) * 100);

        return { percent, checks, isReady: percent === 100 };
    }, [property, rooms]);

    const handleSubmit = async () => {
        if (!readiness.isReady) {
            alert('Mohon lengkapi data properti sebelum mengajukan.');
            return;
        }
        if (!window.confirm('Apakah Anda yakin ingin mengajukan properti ini?')) return;

        try {
            await submitOwnerProperty(id);
            loadData();
        } catch (err) {
            alert(err.response?.data?.message || 'Gagal mengajukan properti.');
        }
    };

    const handleWithdraw = async () => {
        const isApproved = property.status === 'approved';
        const message = isApproved
            ? 'Apakah Anda yakin ingin meng-unpublish properti ini? Properti tidak akan terlihat lagi oleh pencari kost.'
            : 'Batalkan pengajuan? Properti akan kembali ke status draft.';

        if (!window.confirm(message)) return;

        try {
            await withdrawOwnerProperty(id);
            loadData();
        } catch (err) {
            alert(err.response?.data?.message || 'Gagal membatalkan.');
        }
    };

    if (loading) return <div className="page pt-32 pb-20 container text-center">Loading...</div>;
    if (!property) return <div className="page pt-32 pb-20 container text-center">Properti tidak ditemukan</div>;

    return (
        <div className="page pt-32 pb-20">
            <div className="container">
                {/* Header */}
                <div className="mb-8">
                    <button onClick={() => navigate('/owner/properties')} className="flex items-center text-text-secondary hover:text-primary mb-4 transition-colors">
                        <FiArrowLeft className="mr-2" /> Kembali
                    </button>

                    <div className="flex flex-col lg:flex-row justify-between items-start lg:items-end gap-4">
                        <div>
                            <div className="flex items-center gap-3 mb-2">
                                <h1 className="text-4xl font-display font-bold">{property.name}</h1>
                                <span className={`px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider border ${property.status === 'approved' ? 'bg-green-500/10 text-green-500 border-green-500/20' :
                                    property.status === 'pending' ? 'bg-yellow-500/10 text-yellow-500 border-yellow-500/20' :
                                        property.status === 'rejected' ? 'bg-red-500/10 text-red-500 border-red-500/20' :
                                            'bg-white/10 text-text-secondary border-white/10'
                                    }`}>
                                    {property.status}
                                </span>
                            </div>
                            <p className="text-text-secondary text-lg">{property.address}</p>
                        </div>

                        <div className="flex gap-3">
                            {property.status === 'draft' && (
                                <button
                                    onClick={handleSubmit}
                                    disabled={!readiness.isReady}
                                    className={`btn ${readiness.isReady ? 'primary' : 'bg-white/5 text-text-tertiary cursor-not-allowed'}`}
                                >
                                    <FiCheckCircle className="mr-2" /> Ajukan (Publish)
                                </button>
                            )}
                            {property.status === 'pending' && (
                                <button onClick={handleWithdraw} className="btn secondary text-yellow-500 border-yellow-500/20 hover:bg-yellow-500/10">
                                    <FiX className="mr-2" /> Batalkan Pengajuan
                                </button>
                            )}
                            {property.status === 'approved' && (
                                <button onClick={handleWithdraw} className="btn secondary text-red-500 border-red-500/20 hover:bg-red-500/10">
                                    <FiX className="mr-2" /> Unpublish
                                </button>
                            )}
                        </div>
                    </div>

                    {/* Readiness Checklist (Only for Draft) */}
                    {property.status === 'draft' && (
                        <motion.div
                            initial={{ opacity: 0, height: 0 }}
                            animate={{ opacity: 1, height: 'auto' }}
                            className="mt-6 p-4 rounded-xl bg-surface-highlight border border-white/5"
                        >
                            <div className="flex justify-between items-center mb-2">
                                <h4 className="font-bold text-sm text-text-secondary">Kelengkapan Properti</h4>
                                <span className={`text-sm font-bold ${readiness.isReady ? 'text-green-500' : 'text-primary'}`}>
                                    {readiness.percent}%
                                </span>
                            </div>
                            <div className="w-full bg-surface h-2 rounded-full overflow-hidden mb-4">
                                <div
                                    className={`h-full transition-all duration-500 ${readiness.isReady ? 'bg-green-500' : 'bg-primary'}`}
                                    style={{ width: `${readiness.percent}%` }}
                                />
                            </div>
                            <div className="flex flex-wrap gap-4">
                                {readiness.checks.map((check, idx) => (
                                    <div key={idx} className={`flex items-center gap-2 text-xs ${check.valid ? 'text-green-500' : 'text-text-tertiary'}`}>
                                        <div className={`w-4 h-4 rounded-full flex items-center justify-center border ${check.valid ? 'bg-green-500 border-green-500' : 'border-text-tertiary'}`}>
                                            {check.valid && <FiCheck className="text-black text-[10px]" />}
                                        </div>
                                        {check.label}
                                    </div>
                                ))}
                            </div>
                        </motion.div>
                    )}
                </div>

                {/* Tabs */}
                <div className="flex gap-6 border-b border-white/10 mb-8 overflow-x-auto">
                    {[
                        { id: 'overview', label: 'Overview' },
                        { id: 'photos', label: 'Foto & Galeri' },
                        { id: 'room types', label: 'Tipe Kamar' },
                        { id: 'rooms', label: 'Unit Kamar' },
                        { id: 'settings', label: 'Pengaturan' }
                    ].map(tab => (
                        <button
                            key={tab.id}
                            onClick={() => setActiveTab(tab.id)}
                            className={`pb-4 text-sm font-bold uppercase tracking-wider border-b-2 transition-colors whitespace-nowrap ${activeTab === tab.id ? 'border-primary text-primary' : 'border-transparent text-text-secondary hover:text-white'
                                }`}
                        >
                            {tab.label}
                        </button>
                    ))}
                </div>

                <div className="min-h-[400px]">
                    {activeTab === 'overview' && <PropertyOverview property={property} rooms={rooms} />}
                    {activeTab === 'photos' && <PropertyPhotos property={property} onUpdate={loadData} />}
                    {activeTab === 'room types' && <PropertyRoomTypes property={property} onUpdate={loadData} />}
                    {activeTab === 'rooms' && <PropertyRooms rooms={rooms} property={property} onUpdate={loadData} />}
                    {activeTab === 'settings' && <PropertySettings property={property} onUpdate={loadData} />}
                </div>
            </div>
        </div>
    );
};

export default PropertyDetail;
