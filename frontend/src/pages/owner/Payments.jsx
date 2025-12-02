import React, { useEffect, useState } from 'react';
import { motion } from 'framer-motion';
import { FiDollarSign, FiCreditCard, FiClock, FiCheckCircle, FiXCircle, FiTrendingUp, FiArrowDownLeft, FiArrowUpRight } from 'react-icons/fi';
import { fetchOwnerPayments, fetchOwnerWallet, approveOwnerPayment, rejectOwnerPayment, withdrawOwnerWallet } from '../../api/client';

const OwnerPayments = () => {
  const [manualPayments, setManualPayments] = useState([]);
  const [wallet, setWallet] = useState({ available: 0, pending: 0, withdrawals: 0 });
  const [loading, setLoading] = useState(true);
  const [filter, setFilter] = useState('all'); // all, incoming, outgoing
  const [isWithdrawModalOpen, setIsWithdrawModalOpen] = useState(false);

  useEffect(() => {
    loadData();
  }, []);

  const loadData = async () => {
    try {
      const [paymentsData, walletData] = await Promise.all([
        fetchOwnerPayments(),
        fetchOwnerWallet()
      ]);
      setManualPayments(paymentsData);
      setWallet(walletData);
    } catch (error) {
      console.error('Failed to fetch payments data:', error);
    } finally {
      setLoading(false);
    }
  };

  const handleApprove = async (id) => {
    if (window.confirm('Setujui pembayaran ini?')) {
      try {
        await approveOwnerPayment(id);
        loadData();
      } catch (error) {
        alert('Gagal menyetujui pembayaran');
      }
    }
  };

  const handleReject = async (id) => {
    if (window.confirm('Tolak pembayaran ini?')) {
      try {
        await rejectOwnerPayment(id);
        loadData();
      } catch (error) {
        alert('Gagal menolak pembayaran');
      }
    }
  };

  const totalPending = manualPayments.filter((p) => p.status === 'pending').length;

  const filteredPayments = manualPayments.filter(payment => {
    if (filter === 'all') return true;
    if (filter === 'incoming') return true; // Assuming all manual payments are incoming for now
    if (filter === 'outgoing') return false; // Placeholder for withdrawals if they were in the same list
    return true;
  });

  const containerVariants = {
    hidden: { opacity: 0 },
    visible: {
      opacity: 1,
      transition: { staggerChildren: 0.1 }
    }
  };

  return (
    <div className="page pt-32 pb-20">
      <div className="container">
        <div className="flex flex-col md:flex-row justify-between items-end mb-12 gap-6">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
          >
            <h1 className="text-4xl font-display font-bold mb-2">Keuangan & Pembayaran</h1>
            <p className="text-text-secondary text-lg">
              Kelola wallet, verifikasi pembayaran manual, dan riwayat transaksi.
            </p>
          </motion.div>

          <motion.div
            initial={{ opacity: 0, x: 20 }}
            animate={{ opacity: 1, x: 0 }}
            className="flex gap-3"
          >
            <a href="/owner/manual-payments" className="btn ghost">
              <FiCheckCircle className="mr-2" /> Approval
            </a>
            <button onClick={() => setIsWithdrawModalOpen(true)} className="btn primary">
              <FiArrowUpRight className="mr-2" /> Tarik Dana
            </button>
          </motion.div>
        </div>

        {/* Wallet Overview */}
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.2 }}
          className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12"
        >
          <StatusCard
            icon={<FiDollarSign />}
            label="Saldo Siap Tarik"
            value={`Rp${parseInt(wallet.available).toLocaleString('id-ID')}`}
            desc="Tersedia di wallet"
            color="text-green-400"
            bg="bg-green-400/10"
            border="border-green-400/20"
          />
          <StatusCard
            icon={<FiClock />}
            label="Pending Settlement"
            value={`Rp${parseInt(wallet.pending).toLocaleString('id-ID')}`}
            desc="Menunggu pencairan"
            color="text-yellow-400"
            bg="bg-yellow-400/10"
            border="border-yellow-400/20"
          />
          <StatusCard
            icon={<FiTrendingUp />}
            label="Total Penarikan"
            value={wallet.withdrawals}
            desc="Request bulan ini"
            color="text-blue-400"
            bg="bg-blue-400/10"
            border="border-blue-400/20"
          />
        </motion.div>

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
          {/* Main Content: Payment History */}
          <div className="lg:col-span-2 space-y-8">
            <section>
              <div className="flex justify-between items-center mb-6">
                <h2 className="text-xl font-bold font-display flex items-center gap-2">
                  <FiCreditCard className="text-primary" /> Riwayat Pembayaran
                </h2>
                <div className="flex gap-2">
                  <button
                    onClick={() => setFilter('all')}
                    className={`px-3 py-1 rounded-full text-xs font-bold border transition-colors ${filter === 'all' ? 'bg-primary/20 text-primary border-primary/20' : 'bg-surface-highlight text-text-secondary border-border hover:bg-surface-highlight/80'}`}
                  >
                    Semua
                  </button>
                  <button
                    onClick={() => setFilter('incoming')}
                    className={`px-3 py-1 rounded-full text-xs font-bold border transition-colors ${filter === 'incoming' ? 'bg-primary/20 text-primary border-primary/20' : 'bg-surface-highlight text-text-secondary border-border hover:bg-surface-highlight/80'}`}
                  >
                    Masuk
                  </button>
                  <button
                    onClick={() => setFilter('outgoing')}
                    className={`px-3 py-1 rounded-full text-xs font-bold border transition-colors ${filter === 'outgoing' ? 'bg-primary/20 text-primary border-primary/20' : 'bg-surface-highlight text-text-secondary border-border hover:bg-surface-highlight/80'}`}
                  >
                    Keluar
                  </button>
                </div>
              </div>

              <motion.div
                variants={containerVariants}
                initial="hidden"
                animate="visible"
                className="space-y-4"
              >
                {loading ? (
                  <p className="text-text-secondary">Memuat data...</p>
                ) : filteredPayments.length === 0 ? (
                  <p className="text-text-secondary">Belum ada riwayat pembayaran.</p>
                ) : (
                  filteredPayments.map((payment) => (
                    <PaymentItem key={payment.id} payment={payment} />
                  ))
                )}
              </motion.div>
            </section>
          </div>

          {/* Sidebar: Pending Actions */}
          <div className="lg:col-span-1">
            <div className="sticky top-32">
              <div className="card p-6">
                <div className="flex justify-between items-center mb-6">
                  <h3 className="text-xl font-bold font-display">Perlu Approval</h3>
                  <span className="px-2 py-1 rounded-full bg-yellow-400/10 text-yellow-400 text-xs font-bold border border-yellow-400/20">
                    {totalPending} Pending
                  </span>
                </div>

                <div className="space-y-4">
                  {manualPayments.filter(p => p.status === 'pending').length === 0 ? (
                    <p className="text-text-secondary text-sm text-center py-4">Tidak ada pembayaran pending.</p>
                  ) : (
                    manualPayments.filter(p => p.status === 'pending').map((payment) => (
                      <div key={payment.id} className="p-4 rounded-xl bg-surface-highlight border border-border">
                        <div className="flex justify-between items-start mb-2">
                          <span className="text-xs font-bold text-primary">Rp{parseInt(payment.amount).toLocaleString('id-ID')}</span>
                          <span className="text-xs text-text-tertiary">{new Date(payment.created_at).toLocaleDateString()}</span>
                        </div>
                        <h4 className="font-bold text-sm mb-1">{payment.tenant_name}</h4>
                        <p className="text-xs text-text-secondary mb-3">{payment.contract_info}</p>
                        <div className="flex gap-2">
                          <button onClick={() => handleApprove(payment.id)} className="btn primary btn-sm flex-1 justify-center">Approve</button>
                          <button onClick={() => handleReject(payment.id)} className="btn ghost btn-sm flex-1 justify-center text-red-400 hover:bg-red-400/10">Reject</button>
                        </div>
                      </div>
                    ))
                  )}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      {isWithdrawModalOpen && (
        <WithdrawalModal
          balance={wallet.available}
          onClose={() => setIsWithdrawModalOpen(false)}
          onSuccess={() => {
            setIsWithdrawModalOpen(false);
            loadData();
          }}
        />
      )}
    </div>
  );
};

const WithdrawalModal = ({ balance, onClose, onSuccess }) => {
  const [amount, setAmount] = useState('');
  const [bankName, setBankName] = useState('');
  const [accountNumber, setAccountNumber] = useState('');
  const [accountHolder, setAccountHolder] = useState('');
  const [loading, setLoading] = useState(false);

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (parseInt(amount) > balance) {
      alert('Saldo tidak mencukupi');
      return;
    }

    setLoading(true);
    try {
      await withdrawOwnerWallet({
        amount: parseInt(amount),
        bank_name: bankName,
        account_number: accountNumber,
        account_holder: accountHolder,
      });
      alert('Permintaan penarikan berhasil dikirim');
      onSuccess();
    } catch (error) {
      console.error(error);
      alert('Gagal mengirim permintaan penarikan');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="fixed inset-0 z-[2000] flex items-center justify-center p-4">
      <div className="absolute inset-0 bg-black/80 backdrop-blur-sm" onClick={onClose} />
      <motion.div
        initial={{ opacity: 0, scale: 0.95 }}
        animate={{ opacity: 1, scale: 1 }}
        className="relative bg-surface border border-white/10 rounded-3xl w-[95%] md:w-full max-w-md p-6 shadow-2xl max-h-[90vh] overflow-y-auto"
      >
        <div className="flex justify-between items-center mb-6">
          <h2 className="text-xl font-bold font-display">Tarik Dana</h2>
          <button onClick={onClose}><FiXCircle className="text-xl text-text-secondary hover:text-white" /></button>
        </div>

        <div className="mb-6 p-4 rounded-xl bg-surface-highlight border border-white/5">
          <p className="text-sm text-text-secondary mb-1">Saldo Tersedia</p>
          <p className="text-2xl font-bold text-green-400">Rp{parseInt(balance).toLocaleString('id-ID')}</p>
        </div>

        <form onSubmit={handleSubmit} className="space-y-4">
          <div>
            <label className="block text-sm text-text-secondary mb-2">Jumlah Penarikan</label>
            <div className="relative">
              <span className="absolute left-4 top-1/2 -translate-y-1/2 text-text-tertiary">Rp</span>
              <input
                type="number"
                value={amount}
                onChange={e => setAmount(e.target.value)}
                className="w-full bg-surface-highlight border border-white/10 rounded-xl pl-10 pr-4 py-3 focus:border-primary outline-none"
                placeholder="0"
                min="10000"
                required
              />
            </div>
          </div>

          <div>
            <label className="block text-sm text-text-secondary mb-2">Nama Bank</label>
            <select
              value={bankName}
              onChange={e => setBankName(e.target.value)}
              className="w-full bg-surface-highlight border border-white/10 rounded-xl px-4 py-3 focus:border-primary outline-none"
              required
            >
              <option value="">Pilih Bank</option>
              <option value="BCA">BCA</option>
              <option value="BNI">BNI</option>
              <option value="BRI">BRI</option>
              <option value="Mandiri">Mandiri</option>
              <option value="Jago">Jago</option>
            </select>
          </div>

          <div>
            <label className="block text-sm text-text-secondary mb-2">Nomor Rekening</label>
            <input
              type="text"
              value={accountNumber}
              onChange={e => setAccountNumber(e.target.value)}
              className="w-full bg-surface-highlight border border-white/10 rounded-xl px-4 py-3 focus:border-primary outline-none"
              placeholder="Contoh: 1234567890"
              required
            />
          </div>

          <div>
            <label className="block text-sm text-text-secondary mb-2">Nama Pemilik Rekening</label>
            <input
              type="text"
              value={accountHolder}
              onChange={e => setAccountHolder(e.target.value)}
              className="w-full bg-surface-highlight border border-white/10 rounded-xl px-4 py-3 focus:border-primary outline-none"
              placeholder="Nama sesuai buku tabungan"
              required
            />
          </div>

          <div className="flex gap-3 pt-4">
            <button type="button" onClick={onClose} className="btn ghost flex-1">Batal</button>
            <button type="submit" disabled={loading} className="btn primary flex-1 justify-center">
              {loading ? 'Memproses...' : 'Tarik Dana'}
            </button>
          </div>
        </form>
      </motion.div>
    </div>
  );
};

const StatusCard = ({ icon, label, value, desc, color, bg, border }) => (
  <div className={`p-6 rounded-xl border ${border} ${bg} flex items-center gap-4`}>
    <div className={`w-12 h-12 rounded-full flex items-center justify-center text-2xl ${bg} ${color}`}>
      {icon}
    </div>
    <div>
      <div className={`text-3xl font-bold font-display ${color}`}>{value}</div>
      <div className="font-medium text-sm">{label}</div>
      <div className="text-xs text-text-secondary mt-1">{desc}</div>
    </div>
  </div>
);

const PaymentItem = ({ payment }) => {
  const statusConfig = {
    pending: { label: 'Pending', color: 'text-yellow-400', bg: 'bg-yellow-400/10', border: 'border-yellow-400/20', icon: <FiClock /> },
    success: { label: 'Berhasil', color: 'text-green-400', bg: 'bg-green-400/10', border: 'border-green-400/20', icon: <FiCheckCircle /> },
    rejected: { label: 'Ditolak', color: 'text-red-400', bg: 'bg-red-400/10', border: 'border-red-400/20', icon: <FiXCircle /> },
  };

  const status = statusConfig[payment.status] || statusConfig.pending;

  return (
    <motion.div
      variants={{ hidden: { opacity: 0, y: 10 }, visible: { opacity: 1, y: 0 } }}
      className="card p-5 hover:bg-surface-highlight transition-colors group"
    >
      <div className="flex flex-col sm:flex-row justify-between gap-4">
        <div className="flex items-start gap-4">
          <div className={`w-10 h-10 rounded-full flex items-center justify-center text-lg flex-shrink-0 ${status.bg} ${status.color}`}>
            <FiArrowDownLeft />
          </div>
          <div>
            <h3 className="font-bold font-display text-lg mb-1">{payment.tenant_name}</h3>
            <p className="text-sm text-text-secondary mb-1">{payment.contract_info}</p>
            <div className="flex items-center gap-2 text-xs text-text-tertiary">
              <span>{new Date(payment.created_at).toLocaleDateString()}</span>
              <span>•</span>
              <span>{payment.payment_type}</span>
            </div>
          </div>
        </div>

        <div className="flex flex-col items-end justify-center">
          <div className={`text-lg font-bold mb-1 ${payment.status === 'rejected' ? 'text-text-tertiary line-through' : 'text-primary'}`}>
            Rp{parseInt(payment.amount).toLocaleString('id-ID')}
          </div>
          <span className={`px-2 py-0.5 rounded text-xs font-bold border flex items-center gap-1 ${status.color} ${status.bg} ${status.border}`}>
            {status.icon} {status.label}
          </span>
        </div>
      </div>
    </motion.div>
  );
};

export default OwnerPayments;
