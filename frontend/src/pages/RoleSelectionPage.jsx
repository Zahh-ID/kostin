import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { motion } from 'framer-motion';
import { updateUserRole } from '../api/client';
import { FiHome, FiKey, FiCheck } from 'react-icons/fi';

const RoleSelectionPage = () => {
    const navigate = useNavigate();
    const [loading, setLoading] = useState(false);
    const [selectedRole, setSelectedRole] = useState(null);

    const handleSelectRole = async (role) => {
        setSelectedRole(role);
    };

    const handleContinue = async () => {
        if (!selectedRole) return;
        setLoading(true);
        try {
            await updateUserRole(selectedRole);

            // Update local storage user object
            const userStr = localStorage.getItem('user');
            if (userStr) {
                const user = JSON.parse(userStr);
                user.role = selectedRole;
                localStorage.setItem('user', JSON.stringify(user));
            }

            if (selectedRole === 'owner') {
                window.location.href = '/owner';
            } else {
                window.location.href = '/dashboard';
            }
        } catch (error) {
            console.error('Failed to update role:', error);
            alert('Gagal menyimpan peran. Silakan coba lagi.');
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="min-h-screen bg-background flex items-center justify-center p-4">
            <div className="max-w-2xl w-full">
                <div className="text-center mb-10">
                    <h1 className="text-3xl font-display font-bold mb-3">Satu Langkah Lagi! 🚀</h1>
                    <p className="text-text-secondary text-lg">
                        Bagaimana Anda ingin menggunakan KostIn?
                    </p>
                </div>

                <div className="grid md:grid-cols-2 gap-6 mb-8">
                    {/* Tenant Card */}
                    <motion.div
                        whileHover={{ y: -4 }}
                        onClick={() => handleSelectRole('tenant')}
                        className={`cursor-pointer p-6 rounded-2xl border-2 transition-all duration-200 ${selectedRole === 'tenant'
                                ? 'border-primary bg-primary/5 ring-4 ring-primary/10'
                                : 'border-border bg-surface hover:border-primary/50'
                            }`}
                    >
                        <div className={`w-12 h-12 rounded-xl flex items-center justify-center mb-4 ${selectedRole === 'tenant' ? 'bg-primary text-white' : 'bg-surface-highlight text-text-secondary'
                            }`}>
                            <FiHome size={24} />
                        </div>
                        <h3 className="text-xl font-bold mb-2">Pencari Kost</h3>
                        <p className="text-text-secondary text-sm leading-relaxed">
                            Saya ingin mencari tempat tinggal, membayar sewa dengan mudah, dan mengajukan keluhan.
                        </p>
                        {selectedRole === 'tenant' && (
                            <div className="mt-4 flex items-center gap-2 text-primary text-sm font-medium">
                                <FiCheck /> Terpilih
                            </div>
                        )}
                    </motion.div>

                    {/* Owner Card */}
                    <motion.div
                        whileHover={{ y: -4 }}
                        onClick={() => handleSelectRole('owner')}
                        className={`cursor-pointer p-6 rounded-2xl border-2 transition-all duration-200 ${selectedRole === 'owner'
                                ? 'border-primary bg-primary/5 ring-4 ring-primary/10'
                                : 'border-border bg-surface hover:border-primary/50'
                            }`}
                    >
                        <div className={`w-12 h-12 rounded-xl flex items-center justify-center mb-4 ${selectedRole === 'owner' ? 'bg-primary text-white' : 'bg-surface-highlight text-text-secondary'
                            }`}>
                            <FiKey size={24} />
                        </div>
                        <h3 className="text-xl font-bold mb-2">Pemilik Kost</h3>
                        <p className="text-text-secondary text-sm leading-relaxed">
                            Saya ingin mengelola properti, memantau pembayaran, dan mengatur penyewa saya.
                        </p>
                        {selectedRole === 'owner' && (
                            <div className="mt-4 flex items-center gap-2 text-primary text-sm font-medium">
                                <FiCheck /> Terpilih
                            </div>
                        )}
                    </motion.div>
                </div>

                <div className="text-center">
                    <button
                        onClick={handleContinue}
                        disabled={!selectedRole || loading}
                        className={`btn primary lg w-full md:w-auto min-w-[200px] ${!selectedRole ? 'opacity-50 cursor-not-allowed' : ''
                            }`}
                    >
                        {loading ? 'Menyimpan...' : 'Lanjutkan'}
                    </button>
                </div>
            </div>
        </div>
    );
};

export default RoleSelectionPage;
