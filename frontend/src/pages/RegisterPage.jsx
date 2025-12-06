import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { motion, AnimatePresence } from 'framer-motion';
import { register } from '../api/client.js';
import { FiAlertCircle, FiCheck, FiX } from 'react-icons/fi';

const isValidEmail = (value) => /\S+@\S+\.\S+/.test(value);

const checkPasswordStrength = (password) => {
  let score = 0;
  if (password.length >= 8) score++;
  if (/[A-Z]/.test(password)) score++;
  if (/[0-9]/.test(password)) score++;
  return score;
};



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
  const [passwordScore, setPasswordScore] = useState(0);

  useEffect(() => {
    setPasswordScore(checkPasswordStrength(form.password));
  }, [form.password]);
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
    } else if (!/[A-Z]/.test(form.password)) {
      localErrors.password = ['Password harus mengandung huruf besar.'];
    } else if (!/[0-9]/.test(form.password)) {
      localErrors.password = ['Password harus mengandung angka.'];
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
              Mulai Perjalanan <br />
              <span className="text-primary">Bisnis Kost Anda.</span>
            </h1>
            <p className="text-lg text-text-secondary mb-8 max-w-md">
              Bergabung dengan ribuan pemilik kost dan penyewa yang telah beralih ke cara manajemen yang lebih cerdas.
            </p>

            <div className="space-y-6">
              {roles.map((role) => (
                <button
                  key={role.value}
                  type="button"
                  onClick={() => setForm((prev) => ({ ...prev, role: role.value }))}
                  className={`w-full text-left p-4 rounded-xl border transition-all duration-200 hover:scale-[1.02] ${form.role === role.value
                    ? 'bg-surface-highlight border-primary/50 shadow-lg shadow-primary/5'
                    : 'bg-transparent border-border opacity-60 hover:opacity-100 hover:border-text-secondary'
                    }`}
                >
                  <div className="flex items-center gap-3 mb-2">
                    <div className={`w-3 h-3 rounded-full transition-colors ${form.role === role.value ? 'bg-primary' : 'bg-text-tertiary'}`} />
                    <h3 className="font-bold">{role.title}</h3>
                  </div>
                  <p className="text-sm text-text-secondary">{role.desc}</p>
                </button>
              ))}
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
            <h2 className="text-2xl font-bold font-display mb-2">Buat Akun Baru</h2>
            <p className="text-text-secondary">Lengkapi data diri untuk mendaftar.</p>
          </div>

          <div className="auth-card">
            <AnimatePresence>
              {(error || errorList.length > 0) && (
                <motion.div
                  initial={{ opacity: 0, height: 0 }}
                  animate={{ opacity: 1, height: 'auto' }}
                  exit={{ opacity: 0, height: 0 }}
                  className="overflow-hidden mb-4"
                >
                  <div className="p-3 rounded-lg bg-red-500/10 border border-red-500/20 text-red-500 text-sm">
                    <div className="flex items-start gap-2 mb-2">
                      <FiAlertCircle className="mt-0.5 flex-shrink-0" />
                      <span className="font-bold">Registrasi Gagal</span>
                    </div>
                    {error && <div className="mb-1">{error}</div>}
                    {errorList.length > 0 && (
                      <ul className="list-disc list-inside opacity-80 text-xs space-y-1 ml-1">
                        {errorList.map((message, idx) => (
                          <li key={idx}>{message}</li>
                        ))}
                      </ul>
                    )}
                  </div>
                </motion.div>
              )}
            </AnimatePresence>

            <form className="auth-form" onSubmit={handleSubmit}>
              <label className="auth-label">
                Nama Lengkap
                <input
                  type="text"
                  name="name"
                  value={form.name}
                  onChange={handleChange}
                  required
                  minLength={3}
                  className={fieldErrors.name ? 'input-error' : ''}
                  placeholder="Nama Lengkap Anda"
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
                  placeholder="nama@email.com"
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
                    minLength={8}
                    className={fieldErrors.password ? 'input-error' : ''}
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
                {fieldErrors.password && <div className="field-error">{fieldErrors.password[0]}</div>}

                {/* Password Strength Meter */}
                <div className="mt-2 space-y-2">
                  <div className="flex gap-1 h-1.5">
                    {[1, 2, 3].map((level) => (
                      <div
                        key={level}
                        className={`h-full flex-1 rounded-full transition-colors duration-300 ${passwordScore >= level
                          ? passwordScore >= 3
                            ? 'bg-green-500'
                            : passwordScore >= 2
                              ? 'bg-yellow-500'
                              : 'bg-red-500'
                          : 'bg-gray-700'
                          }`}
                      />
                    ))}
                  </div>
                  <div className="flex flex-wrap gap-2 text-[10px] text-text-secondary">
                    <span className={`flex items-center gap-1 ${form.password.length >= 8 ? 'text-green-500' : ''}`}>
                      {form.password.length >= 8 ? <FiCheck /> : <div className="w-3 h-3 rounded-full border border-current" />}
                      8+ Karakter
                    </span>
                    <span className={`flex items-center gap-1 ${/[0-9]/.test(form.password) ? 'text-green-500' : ''}`}>
                      {/[0-9]/.test(form.password) ? <FiCheck /> : <div className="w-3 h-3 rounded-full border border-current" />}
                      Angka
                    </span>
                    <span className={`flex items-center gap-1 ${/[A-Z]/.test(form.password) ? 'text-green-500' : ''}`}>
                      {/[A-Z]/.test(form.password) ? <FiCheck /> : <div className="w-3 h-3 rounded-full border border-current" />}
                      Huruf Besar
                    </span>
                  </div>
                </div>
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
                    placeholder="••••••••"
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
                <div className="grid grid-cols-2 gap-3">
                  {roles.map((role) => (
                    <button
                      key={role.value}
                      type="button"
                      className={`p-3 rounded-xl border text-left transition-all ${form.role === role.value
                        ? 'bg-surface-highlight border-primary text-primary'
                        : 'bg-transparent border-border text-text-secondary hover:border-text-secondary'
                        }`}
                      onClick={() => setForm((prev) => ({ ...prev, role: role.value }))}
                    >
                      <div className="font-bold text-sm mb-1">{role.title}</div>
                      <div className="text-[10px] opacity-80 line-clamp-2">{role.desc}</div>
                    </button>
                  ))}
                </div>
                {fieldErrors.role && <div className="field-error">{fieldErrors.role[0]}</div>}
              </div>

              <motion.button
                whileHover={{ y: -1 }}
                whileTap={{ scale: 0.98 }}
                type="submit"
                className="btn primary full w-full"
                disabled={loading}
              >
                {loading ? 'Memproses...' : 'Daftar'}
              </motion.button>
            </form>

            <div className="divider">atau</div>
            <motion.a
              whileHover={{ y: -1 }}
              whileTap={{ scale: 0.98 }}
              className="btn ghost full w-full"
              href="/auth/redirect"
            >
              Daftar dengan Google
            </motion.a>

            <div className="mt-6 text-center text-sm text-text-secondary">
              Sudah punya akun?{' '}
              <Link to="/login" className="text-primary hover:underline font-medium">
                Masuk
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default RegisterPage;
