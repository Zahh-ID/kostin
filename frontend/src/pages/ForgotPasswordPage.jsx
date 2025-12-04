import React, { useState } from 'react';
import { Link } from 'react-router-dom';
import { motion, AnimatePresence } from 'framer-motion';
import { forgotPassword } from '../api/client.js';
import { FiAlertCircle, FiCheckCircle, FiArrowLeft } from 'react-icons/fi';

const ForgotPasswordPage = () => {
    const [email, setEmail] = useState('');
    const [status, setStatus] = useState('');
    const [error, setError] = useState('');
    const [loading, setLoading] = useState(false);

    const handleSubmit = async (event) => {
        event.preventDefault();
        setError('');
        setStatus('');
        setLoading(true);

        try {
            const response = await forgotPassword(email);
            setStatus(response.status);
        } catch (err) {
            setError(err.response?.data?.message || 'Gagal mengirim link reset password.');
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="auth-split-layout">
            {/* Visual Side */}
            <div className="auth-visual-side">
                <div className="nav-logo">Kost<span>In</span>.</div>
                <div className="auth-visual-content">
                    <motion.div
                        initial={{ opacity: 0, y: 20 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ delay: 0.2 }}
                    >
                        <h1 className="text-4xl font-display font-bold mb-6">
                            Lupa Kata Sandi? <br />
                            <span className="text-primary">Kami Bantu Reset.</span>
                        </h1>
                        <p className="text-lg text-text-secondary mb-8 max-w-md">
                            Jangan khawatir, masukkan email Anda dan kami akan mengirimkan instruksi untuk membuat kata sandi baru.
                        </p>
                    </motion.div>
                </div>
                <div className="auth-visual-footer">
                    &copy; {new Date().getFullYear()} KostIn. All rights reserved.
                </div>
            </div>

            {/* Form Side */}
            <div className="auth-form-side">
                <div className="absolute top-6 right-6">
                    <Link className="btn ghost btn-sm gap-2" to="/login">
                        <FiArrowLeft /> Kembali ke Masuk
                    </Link>
                </div>

                <div className="auth-form-container">
                    <div className="mb-8">
                        <h2 className="text-2xl font-bold font-display mb-2">Reset Kata Sandi</h2>
                        <p className="text-text-secondary">Masukkan email yang terdaftar.</p>
                    </div>

                    <div className="auth-card">
                        <AnimatePresence>
                            {status && (
                                <motion.div
                                    initial={{ opacity: 0, height: 0 }}
                                    animate={{ opacity: 1, height: 'auto' }}
                                    exit={{ opacity: 0, height: 0 }}
                                    className="overflow-hidden mb-4"
                                >
                                    <div className="p-3 rounded-lg bg-green-500/10 border border-green-500/20 text-green-500 text-sm flex items-start gap-2">
                                        <FiCheckCircle className="mt-0.5 flex-shrink-0" />
                                        <span>{status}</span>
                                    </div>
                                </motion.div>
                            )}
                            {error && (
                                <motion.div
                                    initial={{ opacity: 0, height: 0 }}
                                    animate={{ opacity: 1, height: 'auto' }}
                                    exit={{ opacity: 0, height: 0 }}
                                    className="overflow-hidden mb-4"
                                >
                                    <div className="p-3 rounded-lg bg-red-500/10 border border-red-500/20 text-red-500 text-sm flex items-start gap-2">
                                        <FiAlertCircle className="mt-0.5 flex-shrink-0" />
                                        <span>{error}</span>
                                    </div>
                                </motion.div>
                            )}
                        </AnimatePresence>

                        <form className="auth-form" onSubmit={handleSubmit}>
                            <label className="auth-label">
                                Email
                                <input
                                    type="email"
                                    value={email}
                                    onChange={(e) => setEmail(e.target.value)}
                                    required
                                    className={error ? 'input-error' : ''}
                                    placeholder="nama@email.com"
                                    autoFocus
                                />
                            </label>

                            <motion.button
                                whileHover={{ y: -1 }}
                                whileTap={{ scale: 0.98 }}
                                type="submit"
                                className="btn primary full w-full"
                                disabled={loading}
                            >
                                {loading ? 'Mengirim...' : 'Kirim Link Reset'}
                            </motion.button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default ForgotPasswordPage;
