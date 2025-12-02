import React, { useState } from 'react';
import { Link, useNavigate, useLocation } from 'react-router-dom';
import { motion } from 'framer-motion';
import { login } from '../api/client.js';

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
    <div className="auth-page">
      <div className="auth-back">
        <Link className="btn ghost small-link" to="/">
          Kembali
        </Link>
      </div>
      <div className="container auth auth-grid">
        <div className="auth-hero no-card">
          <div className="pill">Masuk</div>
          <h1 className="section-title">Akses dashboard KostIn</h1>
          <p className="muted">Kelola tagihan, kontrak, tiket, dan moderasi properti.</p>
          <div className="hero-tags">
            <span className="mini-chip">QRIS & Manual</span>
            <span className="mini-chip">Kontrak Digital</span>
            <span className="mini-chip">Tiket & Chat</span>
          </div>
          <ul className="hero-list">
            <li>Status tagihan dan bukti bayar selalu jelas</li>
            <li>Kontrak digital, PDF, dan terminasi terpantau</li>
            <li>Tiket & chat cepat dengan unread badge</li>
          </ul>
        </div>

        <div className="auth-card">
          {error && <div className="alert">{error}</div>}

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
            <label className="auth-check">
              <input
                type="checkbox"
                checked={remember}
                onChange={(event) => setRemember(event.target.checked)}
              />
              <span>Ingat saya</span>
            </label>
            <motion.button
              whileHover={{ y: -1 }}
              whileTap={{ scale: 0.98 }}
              type="submit"
              className="btn primary full"
              disabled={loading}
            >
              {loading ? 'Memproses...' : 'Masuk'}
            </motion.button>
          </form>

          <div className="divider">atau</div>
          <motion.a
            whileHover={{ y: -1 }}
            whileTap={{ scale: 0.98 }}
            className="btn ghost full"
            href="/auth/redirect"
          >
            Masuk dengan Google
          </motion.a>

          <div className="muted small">
            Belum punya akun?{' '}
            <Link to="/register" className="link">
              Daftar sekarang
            </Link>
          </div>
        </div>
      </div>
    </div>
  );
};

export default LoginPage;
