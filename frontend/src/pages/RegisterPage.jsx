import React, { useState } from 'react';
import { Link } from 'react-router-dom';
import { motion } from 'framer-motion';
import { register } from '../api/client.js';

const isValidEmail = (value) => /\S+@\S+\.\S+/.test(value);

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

const RegisterPage = () => {
  const [form, setForm] = useState({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'tenant',
  });
  const [error, setError] = useState('');
  const [fieldErrors, setFieldErrors] = useState({});
  const [errorList, setErrorList] = useState([]);
  const [loading, setLoading] = useState(false);
  const [showPassword, setShowPassword] = useState(false);
  const [showPasswordConfirm, setShowPasswordConfirm] = useState(false);
  const roles = [
    {
      value: 'tenant',
      title: 'Tenant',
      desc: 'Bayar kos, pantau kontrak, kirim tiket.',
      accent: '#d2ff00',
      chips: ['QRIS & Manual', 'Kontrak Digital', 'Tiket & Chat'],
      bullets: [
        'Status tagihan dan bukti bayar selalu jelas',
        'Kontrak digital, PDF, dan terminasi terpantau',
        'Tiket & chat cepat dengan unread badge',
      ],
    },
    {
      value: 'owner',
      title: 'Owner',
      desc: 'Kelola properti, approve pembayaran manual.',
      accent: '#b6ff4d',
      chips: ['Builder Properti', 'Wallet & Manual', 'Moderasi'],
      bullets: [
        'Draft → submit → approve/reject properti',
        'Pembayaran manual aman dengan verifikasi',
        'Moderasi dan tiket tereskalasi terpantau',
      ],
    },
  ];
  const activeRole = roles.find((role) => role.value === form.role) ?? roles[0];

  const handleChange = (event) => {
    const { name, value } = event.target;
    setForm((prev) => ({ ...prev, [name]: value }));
  };

  const handleSubmit = async (event) => {
    event.preventDefault();
    setError('');
    setFieldErrors({});
    setErrorList([]);
    setLoading(true);

    const localErrors = {};

    if (!form.name.trim()) {
      localErrors.name = ['Nama wajib diisi.'];
    }

    if (!isValidEmail(form.email)) {
      localErrors.email = ['Email tidak valid.'];
    }

    if (form.password.length < 8) {
      localErrors.password = ['Password minimal 8 karakter.'];
    }

    if (form.password_confirmation !== form.password) {
      localErrors.password_confirmation = ['Konfirmasi password harus sama.'];
    }

    if (!form.role) {
      localErrors.role = ['Pilih peran.'];
    }

    if (Object.keys(localErrors).length > 0) {
      const combined = Object.values(localErrors).flat();
      setFieldErrors(localErrors);
      setError('Periksa kembali data Anda.');
      setErrorList(combined);
      setLoading(false);
      return;
    }

    try {
      await register(form);
      window.location.href = '/dashboard';
    } catch (err) {
      const responseErrors = err.response?.data?.errors ?? {};
      setError(err.response?.data?.message ?? 'Registrasi gagal. Periksa data Anda.');
      setFieldErrors(responseErrors);
      setErrorList(Object.values(responseErrors).flat());
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
        <div className="auth-hero no-card" style={{ '--role-accent': activeRole.accent }}>
          <div className="pill">Daftar</div>
          <h1 className="section-title">Buat akun KostIn</h1>
          <p className="muted">{activeRole.desc}</p>
          <div className="hero-tags">
            {activeRole.chips.map((chip) => (
              <span key={chip} className="mini-chip">
                {chip}
              </span>
            ))}
          </div>
          <ul className="hero-list">
            {activeRole.bullets.map((point) => (
              <li key={point}>{point}</li>
            ))}
          </ul>
        </div>

        <div className="auth-card">
          {(error || errorList.length > 0) && (
            <div className="alert">
              {error && <div>{error}</div>}
              {errorList.length > 0 && (
                <ul className="alert-list">
                  {errorList.map((message) => (
                    <li key={message}>{message}</li>
                  ))}
                </ul>
              )}
            </div>
          )}

          <form className="auth-form" onSubmit={handleSubmit}>
            <label className="auth-label">
              Nama
              <input
                type="text"
                name="name"
                value={form.name}
                onChange={handleChange}
                required
                className={fieldErrors.name ? 'input-error' : ''}
              />
              {fieldErrors.name && <div className="field-error">{fieldErrors.name[0]}</div>}
            </label>
            <label className="auth-label">
              Email
              <input
                type="email"
                name="email"
                autoComplete="email"
                value={form.email}
                onChange={handleChange}
                required
                className={fieldErrors.email ? 'input-error' : ''}
              />
              {fieldErrors.email && <div className="field-error">{fieldErrors.email[0]}</div>}
            </label>
          <label className="auth-label">
            Kata sandi
            <div className="input-wrap">
              <input
                type={showPassword ? 'text' : 'password'}
                name="password"
                autoComplete="new-password"
                value={form.password}
                onChange={handleChange}
                required
                className={fieldErrors.password ? 'input-error' : ''}
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
            {fieldErrors.password && <div className="field-error">{fieldErrors.password[0]}</div>}
          </label>
          <label className="auth-label">
            Konfirmasi kata sandi
            <div className="input-wrap">
              <input
                type={showPasswordConfirm ? 'text' : 'password'}
                name="password_confirmation"
                autoComplete="new-password"
                value={form.password_confirmation}
                onChange={handleChange}
                required
                className={fieldErrors.password_confirmation ? 'input-error' : ''}
              />
              <button
                type="button"
                className="toggle-password"
                onClick={() => setShowPasswordConfirm((prev) => !prev)}
                aria-label="Toggle password confirmation visibility"
              >
                <EyeIcon hidden={showPasswordConfirm} />
              </button>
            </div>
            {fieldErrors.password_confirmation && (
              <div className="field-error">{fieldErrors.password_confirmation[0]}</div>
            )}
          </label>
            <div className="auth-label">
              Peran
              <div className="role-carousel">
                {roles.map((role) => (
                  <motion.button
                    key={role.value}
                    type="button"
                    className={`role-slide ${form.role === role.value ? 'active' : ''}`}
                    onClick={() => setForm((prev) => ({ ...prev, role: role.value }))}
                    whileHover={{ y: -1 }}
                    whileTap={{ scale: 0.98 }}
                    style={{ '--role-accent': role.accent }}
                  >
                    <div className="role-dot" />
                    <div className="card-title">{role.title}</div>
                    <div className="muted small">{role.desc}</div>
                  </motion.button>
                ))}
              </div>
              {fieldErrors.role && <div className="field-error">{fieldErrors.role[0]}</div>}
            </div>
            <motion.button
              whileHover={{ y: -1 }}
              whileTap={{ scale: 0.98 }}
              type="submit"
              className="btn primary full"
              disabled={loading}
            >
              {loading ? 'Memproses...' : 'Daftar'}
            </motion.button>
          </form>

          <div className="divider">atau</div>
          <motion.a
            whileHover={{ y: -1 }}
            whileTap={{ scale: 0.98 }}
            className="btn ghost full"
            href="/auth/redirect"
          >
            Daftar dengan Google
          </motion.a>

          <div className="muted small">
            Sudah punya akun?{' '}
            <Link to="/login" className="link">
              Masuk
            </Link>
          </div>
        </div>
      </div>
    </div>
  );
};

export default RegisterPage;
