import React, { useState, useEffect } from 'react';
import { Link, useSearchParams, useNavigate } from 'react-router-dom';
import { motion, AnimatePresence } from 'framer-motion';
import { resetPassword } from '../api/client.js';
import { FiAlertCircle, FiCheckCircle, FiEye, FiEyeOff } from 'react-icons/fi';

const ResetPasswordPage = () => {
    const [searchParams] = useSearchParams();
    const navigate = useNavigate();
    const [form, setForm] = useState({
        email: searchParams.get('email') || '',
        password: '',
        password_confirmation: '',
        token: searchParams.get('token') || '',
    });
    const [status, setStatus] = useState('');
    const [error, setError] = useState('');
    const [loading, setLoading] = useState(false);
    const [showPassword, setShowPassword] = useState(false);

    useEffect(() => {
        if (!form.token) {
            setError('Token tidak valid atau kadaluarsa.');
        }
    }, [form.token]);

    const handleChange = (e) => {
        setForm({ ...form, [e.target.name]: e.target.value });
    };

    const handleSubmit = async (event) => {
        event.preventDefault();
        setError('');
        setStatus('');
        setLoading(true);

        try {
            const response = await resetPassword(form);
            setStatus(response.status);
            setTimeout(() => {
                navigate('/login');
            }, 3000);
        } catch (err) {
            setError(err.response?.data?.message || 'Gagal mereset password.');
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
                            Buat Kata Sandi <br />
                            <span className="text-primary">Baru Anda.</span>
                        </h1>
                        <p className="text-lg text-text-secondary mb-8 max-w-md">
                            Pastikan kata sandi baru Anda kuat dan mudah diingat.
                        </p>
                    </motion.div>
                </div>
                <div className="auth-visual-footer">
                    &copy; {new Date().getFullYear()} KostIn. All rights reserved.
                </div>
            </div>

            {/* Form Side */}
            <div className="auth-form-side">
                <div className="auth-form-container">
                    <div className="mb-8">
                        <h2 className="text-2xl font-bold font-display mb-2">Reset Kata Sandi</h2>
                        <p className="text-text-secondary">Masukkan kata sandi baru.</p>
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
                                        <span>{status}. Mengalihkan ke halaman login...</span>
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
                            <input type="hidden" name="token" value={form.token} />
                            <input type="hidden" name="email" value={form.email} />

                            <label className="auth-label">
                                Kata Sandi Baru
                                <div className="input-wrap">
                                    <input
                                        type={showPassword ? 'text' : 'password'}
                                        name="password"
                                        value={form.password}
                                        onChange={handleChange}
                                        required
                                        className={error ? 'input-error' : ''}
                                        placeholder="••••••••"
                                    />
                                    <button
                                        type="button"
                                        className="toggle-password"
                                        onClick={() => setShowPassword(!showPassword)}
                                    >
                                        {showPassword ? <FiEyeOff /> : <FiEye />}
                                    </button>
                                </div>
                            </label>

                            <label className="auth-label">
                                Konfirmasi Kata Sandi
                                <div className="input-wrap">
                                    <input
                                        type={showPassword ? 'text' : 'password'}
                                        name="password_confirmation"
                                        value={form.password_confirmation}
                                        onChange={handleChange}
                                        required
                                        className={error ? 'input-error' : ''}
                                        placeholder="••••••••"
                                    />
                                </div>
                            </label>

                            <motion.button
                                whileHover={{ y: -1 }}
                                whileTap={{ scale: 0.98 }}
                                type="submit"
                                className="btn primary full w-full"
                                disabled={loading}
                            >
                                {loading ? 'Memproses...' : 'Reset Password'}
                            </motion.button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default ResetPasswordPage;
