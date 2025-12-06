import React, { useState } from 'react';
import { Link, useNavigate, useLocation } from 'react-router-dom';
import { motion } from 'framer-motion';
import { login } from '../api/client.js';
import SEO from '../components/SEO.jsx';

import { FiAlertCircle } from 'react-icons/fi';

const EyeIcon = ({ hidden = false }) => (
  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
    {hidden ? (
      <>
        <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-5.5 0-10-4-11-8 0 0 2-5 7-7m3-1c5.5 0 10 4 11 8 0 0-1 2.5-3.5 4.5" />
        <path d="M1 1l22 22" />
        <path d="M14.12 14.12A3 3 0 0 1 9.88 9.88" />
      </>
    ) : (
      <>
        <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z" />
        <circle cx="12" cy="12" r="3" />
      </>
    )}
  </svg>
);

const LoginPage = () => {
  const navigate = useNavigate();
  const location = useLocation();
  const [form, setForm] = useState({ email: '', password: '' });
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);
  const [remember, setRemember] = useState(false);
  const [showPassword, setShowPassword] = useState(false);

  const handleChange = (event) => {
    const { name, value } = event.target;
    setForm((prev) => ({ ...prev, [name]: value }));
  };

  const handleSubmit = async (event) => {
    event.preventDefault();
    setError('');
    setLoading(true);

    try {
      const user = await login({ ...form, remember });
      const role = user?.role;

      if (role === 'admin') {
        window.location.href = '/admin';
        return;
      }

      if (role === 'owner') {
        window.location.href = '/owner';
        return;
      }

      if (location.state?.from) {
        navigate(location.state.from);
        return;
      }

      window.location.href = '/dashboard';
    } catch (err) {
      setError(err.response?.data?.message ?? 'Login gagal. Periksa data Anda.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="auth-split-layout">
      <SEO
        title="Login - KostIn"
        description="Login to your KostIn account to manage your boarding house or view your rental status."
      />

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
              Kelola Kost Jadi <br />
              <span className="text-primary">Lebih Simpel.</span>
            </h1>
            <p className="text-lg text-text-secondary mb-8 max-w-md">
              Satu platform untuk semua kebutuhan manajemen properti Anda.
              Tagihan otomatis, kontrak digital, dan laporan keuangan real-time.
            </p>

            <div className="flex gap-3 flex-wrap">
              <span className="px-3 py-1 rounded-full bg-surface-highlight border border-border text-xs font-medium text-text-secondary">
                QRIS Otomatis
              </span>
              <span className="px-3 py-1 rounded-full bg-surface-highlight border border-border text-xs font-medium text-text-secondary">
                Kontrak Digital
              </span>
              <span className="px-3 py-1 rounded-full bg-surface-highlight border border-border text-xs font-medium text-text-secondary">
                Tiket & Chat
              </span>
            </div>
          </motion.div>
        </div>

        <div className="auth-visual-footer">
          &copy; {new Date().getFullYear()} KostIn. All rights reserved.
        </div>
      </div>

      {/* Form Side */}
      <div className="auth-form-side">
        <div className="absolute top-6 right-6">
          <Link className="btn ghost btn-sm" to="/">
            Kembali ke Beranda
          </Link>
        </div>

        <div className="auth-form-container">
          <div className="mb-8">
            <h2 className="text-2xl font-bold font-display mb-2">Selamat Datang Kembali</h2>
            <p className="text-text-secondary">Masuk untuk mengakses dashboard Anda.</p>
          </div>

          <div className="auth-card">
            {error && (
              <motion.div
                initial={{ opacity: 0, y: -10 }}
                animate={{ opacity: 1, y: 0 }}
                className="p-3 rounded-lg bg-red-500/10 border border-red-500/20 text-red-500 text-sm flex items-start gap-2 mb-4"
              >
                <FiAlertCircle className="mt-0.5 flex-shrink-0" />
                <span>{error}</span>
              </motion.div>
            )}

            <form className="auth-form" onSubmit={handleSubmit}>
              <label className="auth-label">
                Email
                <input
                  type="email"
                  name="email"
                  autoComplete="email"
                  value={form.email}
                  onChange={handleChange}
                  required
                  className={error ? 'border-red-500/50 focus:border-red-500' : ''}
                  placeholder="nama@email.com"
                />
              </label>
              <label className="auth-label">
                Kata sandi
                <div className="input-wrap">
                  <input
                    type={showPassword ? 'text' : 'password'}
                    name="password"
                    autoComplete="current-password"
                    value={form.password}
                    onChange={handleChange}
                    required
                    minLength={8}
                    className={error ? 'border-red-500/50 focus:border-red-500' : ''}
                    placeholder="••••••••"
                  />
                  <button
                    type="button"
                    className="toggle-password"
                    onClick={() => setShowPassword((prev) => !prev)}
                    aria-label="Toggle password visibility"
                  >
                    <EyeIcon hidden={showPassword} />
                  </button>
                </div>
              </label>

              <div className="flex justify-between items-center">
                <label className="auth-check">
                  <input
                    type="checkbox"
                    checked={remember}
                    onChange={(event) => setRemember(event.target.checked)}
                  />
                  <span>Ingat saya</span>
                </label>
                <Link to="/forgot-password" className="text-xs text-primary hover:underline">Lupa password?</Link>
              </div>

              <motion.button
                whileHover={{ y: -1 }}
                whileTap={{ scale: 0.98 }}
                type="submit"
                className="btn primary full w-full"
                disabled={loading}
              >
                {loading ? 'Memproses...' : 'Masuk'}
              </motion.button>
            </form>

            <div className="divider">atau</div>
            <motion.button
              whileHover={{ y: -1 }}
              whileTap={{ scale: 0.98 }}
              className="btn ghost full w-full flex items-center justify-center gap-2"
              onClick={async () => {
                try {
                  const response = await import('axios').then(m => m.default.get(`${import.meta.env.VITE_API_BASE_URL}/v1/auth/google/redirect`));
                  window.location.href = response.data.url;
                } catch (error) {
                  console.error("Google Auth Error", error);
                  setError("Gagal menghubungkan ke Google.");
                }
              }}
              type="button"
            >
              <svg className="w-5 h-5" viewBox="0 0 24 24">
                <path
                  d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                  fill="#4285F4"
                />
                <path
                  d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                  fill="#34A853"
                />
                <path
                  d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                  fill="#FBBC05"
                />
                <path
                  d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                  fill="#EA4335"
                />
              </svg>
              Masuk dengan Google
            </motion.button>

            <div className="mt-6 text-center text-sm text-text-secondary">
              Belum punya akun?{' '}
              <Link to="/register" className="text-primary hover:underline font-medium">
                Daftar sekarang
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default LoginPage;
