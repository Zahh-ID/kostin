import React, { useEffect, useMemo, useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { FiFileText, FiEye, FiX, FiCalendar, FiDollarSign, FiHome, FiClock, FiAlertCircle, FiAlertTriangle } from 'react-icons/fi';
import { downloadTenantContractPdf, fetchTenantContract, fetchTenantContracts } from '../../api/client.js';

const formatDate = (dateStr) => {
  if (!dateStr) return '-';
  const date = new Date(dateStr);
  if (Number.isNaN(date.getTime())) return dateStr;
  return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
};

const Contracts = () => {
  const [contracts, setContracts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const [detailModal, setDetailModal] = useState({ open: false, contract: null, loading: false, error: '' });
  const [downloadState, setDownloadState] = useState({ loading: false, error: '' });

  useEffect(() => {
    fetchTenantContracts()
      .then(setContracts)
      .catch(() => setError('Gagal memuat kontrak. Pastikan sudah login sebagai tenant.'))
      .finally(() => setLoading(false));
  }, []);

  const stats = useMemo(() => {
    const active = contracts.filter((c) => c.status === 'active').length;
    const ended = contracts.filter((c) => ['terminated', 'canceled', 'expired', 'pending_renewal'].includes(c.status)).length;
    return {
      active,
      ended,
      total: contracts.length,
    };
  }, [contracts]);

  const openDetail = async (id) => {
    setDetailModal({ open: true, contract: null, loading: true, error: '' });
    try {
      const data = await fetchTenantContract(id);
      setDetailModal({ open: true, contract: data, loading: false, error: '' });
    } catch (err) {
      setDetailModal({ open: true, contract: null, loading: false, error: 'Gagal memuat detail kontrak.' });
    }
  };

  const closeDetail = () => setDetailModal({ open: false, contract: null, loading: false, error: '' });

  const handleView = async (contractId) => {
    setDownloadState({ loading: true, error: '' });

    try {
      const blob = await downloadTenantContractPdf(contractId);
      const url = window.URL.createObjectURL(new Blob([blob], { type: 'application/pdf' }));
      const link = document.createElement('a');
      link.href = url;
      link.target = '_blank';
      document.body.appendChild(link);
      link.click();
      link.remove();
      setTimeout(() => window.URL.revokeObjectURL(url), 1000);
    } catch (err) {
      const message = err?.response?.data?.message ?? 'Gagal memuat kontrak.';
      setDownloadState({ loading: false, error: message });
      return;
    }

    setDownloadState({ loading: false, error: '' });
  };

  return (
    <div className="page pt-32 pb-20">
      <div className="container">
        <div className="text-center max-w-2xl mx-auto mb-12">
          <motion.h1
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            className="text-4xl font-display font-bold mb-4"
          >
            Kontrak & <span className="text-primary">Dokumen</span>
          </motion.h1>
          <motion.p
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ delay: 0.1 }}
            className="text-text-secondary text-lg"
          >
            Kelola semua kontrak sewa dan dokumen legal propertimu.
          </motion.p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
          <StatCard
            icon={<FiFileText />}
            label="Kontrak Aktif"
            value={stats.active}
            desc="Sedang berjalan"
            delay={0.2}
            highlight
          />
          <StatCard
            icon={<FiClock />}
            label="Riwayat"
            value={stats.ended}
            desc="Berakhir/Terminasi"
            delay={0.3}
          />
          <StatCard
            icon={<FiFileText />}
            label="Total Kontrak"
            value={stats.total}
            desc="Semua dokumen"
            delay={0.4}
          />
        </div>

        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.5 }}
          className="card p-6 border-primary/20"
        >
          <div className="flex justify-between items-center mb-8">
            <h2 className="text-2xl font-display font-bold">Daftar Kontrak</h2>
            <a href="/tenant/search" className="btn ghost text-sm">
              Cari Kos Baru
            </a>
          </div>

          {error && (
            <div className="p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-500 mb-6 text-center">
              {error}
            </div>
          )}

          {loading ? (
            <div className="space-y-4">
              {[1, 2].map((i) => (
                <div key={i} className="h-24 rounded-xl bg-surface-highlight animate-pulse" />
              ))}
            </div>
          ) : contracts.length ? (
            <div className="grid gap-4">
              {contracts.map((contract, index) => (
                <ContractItem
                  key={contract.id}
                  contract={contract}
                  index={index}
                  onDetail={() => openDetail(contract.id)}
                />
              ))}
            </div>
          ) : (
            <div className="text-center py-12 border-2 border-dashed border-border rounded-xl">
              <div className="inline-flex p-4 rounded-full bg-surface-highlight mb-4">
                <FiFileText className="text-4xl text-text-tertiary" />
              </div>
              <h3 className="text-xl font-bold mb-2">Belum ada kontrak</h3>
              <p className="text-text-secondary">Kontrak aktif akan muncul di sini setelah pengajuan disetujui.</p>
            </div>
          )}
        </motion.div>
      </div>

      <AnimatePresence>
        {detailModal.open && (
          <ContractDetailModal
            contract={detailModal.contract}
            loading={detailModal.loading}
            error={detailModal.error}
            onClose={closeDetail}
            onView={handleView}
            viewing={downloadState.loading}
            viewError={downloadState.error}
          />
        )}
      </AnimatePresence>
    </div>
  );
};

const StatCard = ({ icon, label, value, desc, delay, highlight }) => (
  <motion.div
    initial={{ opacity: 0, y: 20 }}
    animate={{ opacity: 1, y: 0 }}
    transition={{ delay }}
    className={`card p-6 relative overflow-hidden group ${highlight ? 'border-primary/50 bg-primary/5' : ''}`}
  >
    <div className="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity text-6xl text-primary">
      {icon}
    </div>
    <div className="relative z-10">
      <div className="text-text-secondary text-sm font-medium mb-1">{label}</div>
      <div className="text-3xl font-bold font-display mb-1">{value}</div>
      <div className="text-xs text-text-tertiary">{desc}</div>
    </div>
  </motion.div>
);

const ContractItem = ({ contract, index, onDetail }) => {
  const propertyName = contract.room?.room_type?.property?.name ?? contract.room?.roomType?.property?.name ?? '—';
  const roomType = contract.room?.room_type?.name ?? contract.room?.roomType?.name ?? '—';
  const roomCode = contract.room?.room_code ?? '—';
  const statusLabel = (contract.status ?? '').replace('_', ' ');

  const getStatusColor = (status) => {
    switch (status) {
      case 'active': return 'bg-green-500/20 text-green-500 border-green-500/20';
      case 'terminated': return 'bg-red-500/20 text-red-500 border-red-500/20';
      case 'pending_renewal': return 'bg-yellow-500/20 text-yellow-500 border-yellow-500/20';
      default: return 'bg-surface-highlight text-text-secondary border-border';
    }
  };

  return (
    <motion.div
      initial={{ opacity: 0, x: -20 }}
      animate={{ opacity: 1, x: 0 }}
      transition={{ delay: index * 0.1 }}
      className="card p-4 hover:border-primary/50 transition-colors cursor-pointer group"
      onClick={onDetail}
    >
      <div className="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div className="flex items-start gap-4">
          <div className="p-3 rounded-xl bg-surface-highlight group-hover:bg-primary group-hover:text-black transition-colors text-primary text-xl">
            <FiFileText />
          </div>
          <div>
            <div className="flex items-center gap-2 mb-1">
              <h3 className="font-bold text-lg">{propertyName}</h3>
              <span className={`text-xs px-2 py-0.5 rounded-full border uppercase font-bold ${getStatusColor(contract.status)}`}>
                {statusLabel}
              </span>
            </div>
            <p className="text-sm text-text-secondary flex items-center gap-2">
              <span className="font-mono bg-surface-highlight px-1.5 rounded text-xs">{roomCode}</span>
              {roomType}
            </p>
          </div>
        </div>

        <div className="flex items-center gap-6 w-full md:w-auto justify-between md:justify-end">
          <div className="text-right">
            <div className="text-sm text-text-secondary mb-0.5">Mulai Sewa</div>
            <div className="font-medium">{formatDate(contract.start_date)}</div>
          </div>
          <div className="text-right">
            <div className="text-sm text-text-secondary mb-0.5">Biaya Sewa</div>
            <div className="font-bold text-primary">Rp{(contract.price_per_month ?? 0).toLocaleString('id-ID')}</div>
          </div>
          <button className="btn ghost btn-sm hidden md:flex">
            Detail
          </button>
        </div>
      </div>
    </motion.div>
  );
};

const ContractDetailModal = ({ contract, loading, error, onClose, onView, viewing, viewError }) => {
  const [terminationModal, setTerminationModal] = useState(false);

  return (
    <div className="fixed inset-0 z-[2000] flex items-center justify-center p-4">
      <motion.div
        initial={{ opacity: 0 }}
        animate={{ opacity: 1 }}
        exit={{ opacity: 0 }}
        className="absolute inset-0 bg-black/60 backdrop-blur-md"
        onClick={onClose}
      />
      <motion.div
        initial={{ opacity: 0, scale: 0.95, y: 20 }}
        animate={{ opacity: 1, scale: 1, y: 0 }}
        exit={{ opacity: 0, scale: 0.95, y: 20 }}
        className="relative bg-surface/95 backdrop-blur-2xl border border-white/10 rounded-3xl w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-2xl ring-1 ring-black/5"
      >
        <div className="sticky top-0 bg-surface/80 backdrop-blur-md border-b border-white/5 p-6 flex justify-between items-start z-10">
          <div>
            <div className="flex items-center gap-2 mb-2">
              <span className="px-2 py-0.5 rounded-full bg-primary/10 text-primary text-xs font-bold uppercase tracking-wider border border-primary/20">
                Kontrak Sewa
              </span>
              {contract && (
                <span className="text-xs text-text-tertiary font-mono">#{contract.id}</span>
              )}
            </div>
            <h2 className="text-2xl font-display font-bold text-text-primary">
              {contract ? (contract.room?.room_type?.property?.name ?? contract.room?.roomType?.property?.name ?? 'Detail Kontrak') : 'Memuat...'}
            </h2>
          </div>
          <div className="flex gap-2">
            {contract && (
              <button
                type="button"
                className="btn ghost btn-sm border border-white/10 hover:bg-white/5"
                onClick={() => onView(contract.id)}
                disabled={viewing}
              >
                {viewing ? <div className="w-4 h-4 border-2 border-current border-t-transparent rounded-full animate-spin" /> : <FiEye />}
                <span className="hidden sm:inline ml-2">PDF</span>
              </button>
            )}
            <button
              onClick={onClose}
              className="p-2 hover:bg-white/10 rounded-full transition-colors text-text-secondary hover:text-text-primary"
            >
              <FiX className="text-xl" />
            </button>
          </div>
        </div>

        {loading ? (
          <div className="p-12 text-center">
            <div className="w-10 h-10 border-2 border-primary border-t-transparent rounded-full animate-spin mx-auto mb-4" />
            <p className="text-text-secondary animate-pulse">Sedang memuat data kontrak...</p>
          </div>
        ) : error ? (
          <div className="p-12 text-center">
            <div className="w-12 h-12 bg-red-500/10 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
              <FiAlertCircle className="text-2xl" />
            </div>
            <p className="text-text-primary font-medium mb-1">Gagal memuat data</p>
            <p className="text-text-secondary text-sm">{error}</p>
          </div>
        ) : contract ? (
          <div className="p-6 space-y-8">
            {viewError && (
              <div className="p-4 rounded-2xl bg-red-500/10 border border-red-500/20 text-red-500 flex items-center gap-3">
                <FiAlertCircle className="text-xl flex-shrink-0" />
                <span className="text-sm font-medium">{viewError}</span>
              </div>
            )}

            {contract.property_status && contract.property_status !== 'approved' && (
              <div className="p-4 rounded-2xl bg-yellow-500/10 border border-yellow-500/20 text-yellow-500 flex items-start gap-3">
                <FiAlertTriangle className="mt-0.5 text-xl flex-shrink-0" />
                <span className="text-sm font-medium leading-relaxed">
                  Properti ini sedang tidak aktif. Anda tidak dapat memperpanjang kontrak saat ini.
                </span>
              </div>
            )}

            {/* Property Info */}
            <div className="flex items-start gap-5 p-5 rounded-2xl bg-surface-highlight/50 border border-white/5">
              <div className="p-4 rounded-xl bg-gradient-to-br from-primary/20 to-primary/5 text-primary text-2xl shadow-inner">
                <FiHome />
              </div>
              <div className="flex-1">
                <div className="text-xs font-bold text-text-tertiary uppercase tracking-wider mb-1">Unit Sewa</div>
                <h3 className="font-bold text-lg text-text-primary mb-1">
                  {contract.room?.room_type?.name ?? contract.room?.roomType?.name ?? '—'}
                </h3>
                <div className="flex items-center gap-2 text-sm text-text-secondary">
                  <span className="font-mono bg-white/5 px-2 py-0.5 rounded text-xs border border-white/5">
                    {contract.room?.room_code ?? '—'}
                  </span>
                  <span>·</span>
                  <span>{contract.room?.room_type?.property?.address ?? 'Alamat tidak tersedia'}</span>
                </div>
              </div>
            </div>

            {/* Dates Grid */}
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div className="p-5 rounded-2xl bg-surface border border-white/5 hover:border-primary/20 transition-colors group">
                <div className="text-sm text-text-secondary mb-2 flex items-center gap-2 group-hover:text-primary transition-colors">
                  <FiCalendar /> Tanggal Mulai
                </div>
                <div className="font-display font-bold text-xl text-text-primary">
                  {formatDate(contract.start_date)}
                </div>
              </div>
              <div className="p-5 rounded-2xl bg-surface border border-white/5 hover:border-primary/20 transition-colors group">
                <div className="text-sm text-text-secondary mb-2 flex items-center gap-2 group-hover:text-primary transition-colors">
                  <FiCalendar /> Tanggal Selesai
                </div>
                <div className="font-display font-bold text-xl text-text-primary">
                  {formatDate(contract.end_date)}
                </div>
              </div>
            </div>

            {/* Financials */}
            <div>
              <h4 className="text-sm font-bold text-text-secondary uppercase tracking-wider mb-4 px-1">Rincian Finansial</h4>
              <div className="space-y-3 bg-surface rounded-2xl p-5 border border-white/5">
                <div className="flex justify-between items-center py-1">
                  <span className="text-text-secondary">Biaya Sewa (Bulanan)</span>
                  <span className="font-display font-bold text-lg">Rp{(contract.price_per_month ?? 0).toLocaleString('id-ID')}</span>
                </div>
                <div className="flex justify-between items-center py-1 border-t border-white/5 pt-3">
                  <span className="text-text-secondary">Deposit</span>
                  <span className="font-display font-bold">Rp{(contract.deposit_amount ?? 0).toLocaleString('id-ID')}</span>
                </div>
                <div className="flex justify-between items-center py-1 border-t border-white/5 pt-3">
                  <span className="text-text-secondary">Denda Keterlambatan /hari</span>
                  <span className="font-display font-bold text-red-400">Rp{(contract.late_fee_per_day ?? 0).toLocaleString('id-ID')}</span>
                </div>
                <div className="flex justify-between items-center py-1 border-t border-white/5 pt-3">
                  <span className="text-text-secondary">Grace Period</span>
                  <span className="font-medium">{contract.grace_days ?? 0} Hari</span>
                </div>
                <div className="flex justify-between items-center py-1 border-t border-white/5 pt-3">
                  <span className="text-text-secondary">Jadwal Tagihan</span>
                  <span className="px-3 py-1 rounded-full bg-surface-highlight text-xs font-bold border border-white/5">
                    Setiap tgl {contract.billing_day ?? '-'}
                  </span>
                </div>
              </div>
            </div>

            {contract.status === 'active' && (
              <div className="pt-6 border-t border-white/5">
                <button
                  onClick={() => setTerminationModal(true)}
                  className="btn danger w-full justify-center py-4 text-base font-medium shadow-lg shadow-red-500/10 hover:shadow-red-500/20"
                >
                  Ajukan Pemutusan Kontrak
                </button>
                <p className="text-center text-xs text-text-tertiary mt-3">
                  Tindakan ini akan mengirimkan permintaan kepada pemilik properti.
                </p>
              </div>
            )}
          </div>
        ) : null}
      </motion.div>

      <AnimatePresence>
        {terminationModal && (
          <TerminationRequestModal
            contract={contract}
            onClose={() => setTerminationModal(false)}
            onSuccess={() => {
              setTerminationModal(false);
              onClose();
              window.location.reload();
            }}
          />
        )}
      </AnimatePresence>
    </div>
  );
};

const TerminationRequestModal = ({ contract, onClose, onSuccess }) => {
  const [reason, setReason] = useState('');
  const [date, setDate] = useState('');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    setError('');

    try {
      await import('../../api/client.js').then(m => m.requestContractTermination(contract.id, {
        reason,
        requested_end_date: date
      }));
      onSuccess();
    } catch (err) {
      setError(err?.response?.data?.message ?? 'Gagal mengajukan pemutusan kontrak.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="fixed inset-0 z-[2100] flex items-center justify-center p-4">
      <motion.div
        initial={{ opacity: 0 }}
        animate={{ opacity: 1 }}
        exit={{ opacity: 0 }}
        className="absolute inset-0 bg-black/60 backdrop-blur-md"
        onClick={onClose}
      />
      <motion.div
        initial={{ opacity: 0, scale: 0.95, y: 20 }}
        animate={{ opacity: 1, scale: 1, y: 0 }}
        exit={{ opacity: 0, scale: 0.95, y: 20 }}
        className="relative bg-surface/95 backdrop-blur-2xl border border-white/10 rounded-3xl w-full max-w-md p-8 shadow-2xl ring-1 ring-black/5"
      >
        <div className="text-center mb-6">
          <div className="w-16 h-16 bg-red-500/10 text-red-500 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-red-500/20">
            <FiAlertTriangle className="text-3xl" />
          </div>
          <h3 className="text-2xl font-display font-bold text-text-primary mb-2">Putus Kontrak?</h3>
          <p className="text-text-secondary text-sm">
            Pengajuan ini akan dikirim ke pemilik untuk disetujui. Kontrak akan tetap aktif hingga tanggal yang disepakati.
          </p>
        </div>

        {error && (
          <div className="p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-500 text-sm mb-6 flex items-start gap-3">
            <FiAlertCircle className="mt-0.5 flex-shrink-0" />
            <span>{error}</span>
          </div>
        )}

        <form onSubmit={handleSubmit} className="space-y-5">
          <div>
            <label className="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-2">
              Tanggal Berakhir yang Diajukan
            </label>
            <input
              type="date"
              required
              min={new Date().toISOString().split('T')[0]}
              value={date}
              onChange={(e) => setDate(e.target.value)}
              className="input w-full bg-surface-highlight border-white/10 focus:border-primary/50 rounded-xl p-3"
            />
          </div>

          <div>
            <label className="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-2">
              Alasan Pemutusan
            </label>
            <textarea
              required
              rows={4}
              value={reason}
              onChange={(e) => setReason(e.target.value)}
              className="input w-full bg-surface-highlight border-white/10 focus:border-primary/50 rounded-xl p-3 resize-none"
              placeholder="Jelaskan alasan Anda ingin mengakhiri kontrak..."
            />
          </div>

          <div className="grid grid-cols-2 gap-3 pt-4">
            <button
              type="button"
              onClick={onClose}
              className="btn ghost justify-center hover:bg-white/5"
              disabled={loading}
            >
              Batal
            </button>
            <button
              type="submit"
              className="btn danger justify-center shadow-lg shadow-red-500/20"
              disabled={loading}
            >
              {loading ? 'Mengirim...' : 'Ajukan Pemutusan'}
            </button>
          </div>
        </form>
      </motion.div>
    </div>
  );
};

export default Contracts;
