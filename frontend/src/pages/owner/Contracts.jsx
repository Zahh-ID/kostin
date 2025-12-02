import React, { useEffect, useState } from 'react';
import { motion } from 'framer-motion';
import { FiFileText, FiClock, FiXCircle, FiCheckCircle, FiAlertCircle, FiUser, FiHome, FiCalendar } from 'react-icons/fi';
import { fetchOwnerContracts, terminateOwnerContract } from '../../api/client';

const OwnerContracts = () => {
  const [contracts, setContracts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [terminatingContract, setTerminatingContract] = useState(null);
  const [viewingContract, setViewingContract] = useState(null);

  useEffect(() => {
    loadData();
  }, []);

  const loadData = async () => {
    try {
      const data = await fetchOwnerContracts();
      setContracts(data);
    } catch (error) {
      console.error('Failed to fetch contracts:', error);
    } finally {
      setLoading(false);
    }
  };

  const handleTerminate = (contract) => {
    setTerminatingContract(contract);
  };

  const handleView = (contract) => {
    setViewingContract(contract);
  };

  const stats = {
    active: contracts.filter((c) => c.status === 'active').length,
    endingSoon: contracts.filter((c) => c.status === 'ending_soon').length,
    terminations: contracts.filter((c) => c.status === 'terminated').length,
  };

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
            <h1 className="text-4xl font-display font-bold mb-2">Kontrak & Terminasi</h1>
            <p className="text-text-secondary text-lg">
              Pantau masa berlaku kontrak dan kelola pengajuan terminasi.
            </p>
          </motion.div>

          <motion.div
            initial={{ opacity: 0, x: 20 }}
            animate={{ opacity: 1, x: 0 }}
            className="flex gap-3"
          >
            <a href="/owner/contract-terminations" className="btn ghost">
              <FiXCircle className="mr-2" /> Terminasi
            </a>
          </motion.div>
        </div>

        {/* Stats Overview */}
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.2 }}
          className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12"
        >
          <StatusCard
            icon={<FiCheckCircle />}
            label="Kontrak Aktif"
            value={stats.active}
            desc="Sedang berjalan"
            color="text-green-400"
            bg="bg-green-400/10"
            border="border-green-400/20"
          />
          <StatusCard
            icon={<FiClock />}
            label="Segera Berakhir"
            value={stats.endingSoon}
            desc="Perlu tindak lanjut"
            color="text-yellow-400"
            bg="bg-yellow-400/10"
            border="border-yellow-400/20"
          />
          <StatusCard
            icon={<FiXCircle />}
            label="Terminasi"
            value={stats.terminations}
            desc="Kontrak dihentikan"
            color="text-red-400"
            bg="bg-red-400/10"
            border="border-red-400/20"
          />
        </motion.div>

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
          {/* Main Content: Active Contracts */}
          <div className="lg:col-span-2 space-y-8">
            <section>
              <h2 className="text-xl font-bold font-display mb-6 flex items-center gap-2">
                <FiFileText className="text-primary" /> Daftar Kontrak
              </h2>

              <motion.div
                variants={containerVariants}
                initial="hidden"
                animate="visible"
                className="space-y-4"
              >
                {loading ? (
                  <p className="text-text-secondary">Memuat data...</p>
                ) : contracts.length === 0 ? (
                  <p className="text-text-secondary">Belum ada kontrak aktif.</p>
                ) : (
                  contracts.map((contract) => (
                    <ContractItem
                      key={contract.id}
                      contract={contract}
                      onTerminate={() => handleTerminate(contract)}
                      onView={() => handleView(contract)}
                    />
                  ))
                )}
              </motion.div>
            </section>
          </div>

          {/* Sidebar: Terminations */}
          <div className="lg:col-span-1">
            <div className="sticky top-32">
              <div className="card p-6">
                <div className="flex justify-between items-center mb-6">
                  <h3 className="text-xl font-bold font-display">Terminasi Terakhir</h3>
                  <a href="/owner/contract-terminations" className="text-sm text-primary hover:underline">Lihat Semua</a>
                </div>

                <div className="space-y-4">
                  {contracts.filter(c => c.status === 'terminated').slice(0, 5).map((item) => (
                    <div key={item.id} className="p-4 rounded-xl bg-surface-highlight border border-border">
                      <div className="flex justify-between items-start mb-2">
                        <span className="px-2 py-1 rounded text-xs font-bold border text-red-400 bg-red-400/10 border-red-400/20">
                          Terminated
                        </span>
                        <span className="text-xs text-text-tertiary">{new Date(item.terminated_at).toLocaleDateString()}</span>
                      </div>
                      <h4 className="font-bold text-sm mb-1">{item.property_name} · {item.room_name}</h4>
                      <p className="text-xs text-text-secondary line-clamp-2">{item.termination_reason || 'No reason provided'}</p>
                    </div>
                  ))}
                  {contracts.filter(c => c.status === 'terminated').length === 0 && (
                    <p className="text-sm text-text-secondary">Belum ada terminasi.</p>
                  )}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* Termination Modal */}
      {terminatingContract && (
        <TerminationModal
          contract={terminatingContract}
          onClose={() => setTerminatingContract(null)}
          onSuccess={() => {
            setTerminatingContract(null);
            loadData();
          }}
        />
      )}

      {/* Detail Modal */}
      {viewingContract && (
        <ContractDetailModal
          contract={viewingContract}
          onClose={() => setViewingContract(null)}
        />
      )}
    </div>
  );
};

const ContractDetailModal = ({ contract, onClose }) => {
  return (
    <div className="fixed inset-0 z-[2000] flex items-center justify-center p-4">
      <div className="absolute inset-0 bg-black/80 backdrop-blur-sm" onClick={onClose} />
      <motion.div
        initial={{ opacity: 0, scale: 0.95 }}
        animate={{ opacity: 1, scale: 1 }}
        className="relative bg-surface border border-white/10 rounded-3xl w-[95%] md:w-full max-w-2xl p-6 shadow-2xl max-h-[90vh] overflow-y-auto"
      >
        <div className="flex justify-between items-center mb-6">
          <h2 className="text-xl font-bold font-display">Detail Kontrak</h2>
          <button onClick={onClose}><FiXCircle className="text-xl text-text-secondary hover:text-white" /></button>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div className="space-y-6">
            <div>
              <h3 className="text-sm text-text-secondary uppercase tracking-wider mb-2">Properti & Kamar</h3>
              <div className="p-4 rounded-xl bg-surface-highlight border border-white/5">
                <p className="font-bold text-lg">{contract.property_name}</p>
                <p className="text-text-secondary">{contract.room_name}</p>
                <p className="text-primary font-bold mt-2">Rp{parseInt(contract.price).toLocaleString('id-ID')} / bulan</p>
              </div>
            </div>

            <div>
              <h3 className="text-sm text-text-secondary uppercase tracking-wider mb-2">Penyewa</h3>
              <div className="p-4 rounded-xl bg-surface-highlight border border-white/5">
                <div className="flex items-center gap-3 mb-2">
                  <div className="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center text-primary">
                    <FiUser />
                  </div>
                  <div>
                    <p className="font-bold">{contract.tenant_name}</p>
                    <p className="text-xs text-text-secondary">Penyewa Utama</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div className="space-y-6">
            <div>
              <h3 className="text-sm text-text-secondary uppercase tracking-wider mb-2">Periode Kontrak</h3>
              <div className="p-4 rounded-xl bg-surface-highlight border border-white/5 space-y-3">
                <div className="flex justify-between">
                  <span className="text-text-secondary">Mulai</span>
                  <span className="font-medium">{new Date(contract.start_date).toLocaleDateString('id-ID', { dateStyle: 'long' })}</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-text-secondary">Berakhir</span>
                  <span className="font-medium">{new Date(contract.end_date).toLocaleDateString('id-ID', { dateStyle: 'long' })}</span>
                </div>
                <div className="flex justify-between border-t border-white/10 pt-2">
                  <span className="text-text-secondary">Tagihan Setiap Tgl</span>
                  <span className="font-medium">{contract.billing_day}</span>
                </div>
              </div>
            </div>

            <div>
              <h3 className="text-sm text-text-secondary uppercase tracking-wider mb-2">Status</h3>
              <div className="p-4 rounded-xl bg-surface-highlight border border-white/5">
                <div className="flex justify-between items-center">
                  <span className="text-text-secondary">Status Saat Ini</span>
                  <span className={`px-3 py-1 rounded-full text-xs font-bold uppercase ${contract.status === 'active' ? 'bg-green-500/10 text-green-400' :
                    contract.status === 'ending_soon' ? 'bg-yellow-500/10 text-yellow-400' :
                      'bg-red-500/10 text-red-400'
                    }`}>
                    {contract.status.replace('_', ' ')}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div className="mt-8 flex justify-end">
          <button onClick={onClose} className="btn secondary">Tutup</button>
        </div>
      </motion.div>
    </div>
  );
};

const TerminationModal = ({ contract, onClose, onSuccess }) => {
  const [reason, setReason] = useState('');
  const [loading, setLoading] = useState(false);

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    try {
      await terminateOwnerContract(contract.id, reason);
      onSuccess();
    } catch (error) {
      console.error(error);
      alert('Gagal mengakhiri kontrak');
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
          <h2 className="text-xl font-bold font-display text-red-500">Akhiri Kontrak</h2>
          <button onClick={onClose}><FiXCircle className="text-xl text-text-secondary hover:text-white" /></button>
        </div>

        <div className="mb-6 p-4 rounded-xl bg-surface-highlight border border-white/5">
          <h3 className="font-bold mb-1">{contract.property_name}</h3>
          <p className="text-sm text-text-secondary">{contract.room_name} · {contract.tenant_name}</p>
        </div>

        <form onSubmit={handleSubmit} className="space-y-4">
          <div>
            <label className="block text-sm text-text-secondary mb-2">Alasan Terminasi</label>
            <textarea
              value={reason}
              onChange={e => setReason(e.target.value)}
              className="w-full bg-surface-highlight border border-white/10 rounded-xl px-4 py-3 focus:border-red-500 outline-none"
              rows={3}
              placeholder="Contoh: Pelanggaran aturan kost..."
              required
            />
          </div>

          <div className="flex gap-3 pt-4">
            <button type="button" onClick={onClose} className="btn ghost flex-1">Batal</button>
            <button type="submit" disabled={loading} className="btn bg-red-500 hover:bg-red-600 text-white flex-1 justify-center">
              {loading ? 'Memproses...' : 'Akhiri Kontrak'}
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

const ContractItem = ({ contract, onTerminate, onView }) => {
  const statusColors = {
    active: 'text-green-400 bg-green-400/10 border-green-400/20',
    ending_soon: 'text-yellow-400 bg-yellow-400/10 border-yellow-400/20',
    terminated: 'text-red-400 bg-red-400/10 border-red-400/20',
  };

  return (
    <motion.div
      variants={{ hidden: { opacity: 0, y: 10 }, visible: { opacity: 1, y: 0 } }}
      className="card p-5 hover:bg-surface-highlight transition-colors group"
    >
      <div className="flex flex-col md:flex-row justify-between gap-4">
        <div className="flex-grow">
          <div className="flex items-center gap-3 mb-2">
            <h3 className="text-lg font-bold font-display">{contract.property_name}</h3>
            <span className={`px-2 py-0.5 rounded text-xs font-bold border ${statusColors[contract.status] || statusColors.active}`}>
              {contract.status}
            </span>
          </div>

          <div className="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-2 text-sm text-text-secondary">
            <div className="flex items-center gap-2">
              <FiHome className="text-text-tertiary" />
              <span>{contract.room_name}</span>
            </div>
            <div className="flex items-center gap-2">
              <FiUser className="text-text-tertiary" />
              <span>{contract.tenant_name}</span>
            </div>
            <div className="flex items-center gap-2">
              <FiCalendar className="text-text-tertiary" />
              <span>Tagihan tgl {contract.billing_day}</span>
            </div>
          </div>
        </div>

        <div className="flex flex-col items-end justify-between gap-4 min-w-[140px]">
          <div className="text-right">
            <div className="text-lg font-bold text-primary">
              Rp{parseInt(contract.price).toLocaleString('id-ID')}
            </div>
            <div className="text-xs text-text-tertiary">per bulan</div>
          </div>

          <div className="flex gap-2">
            <button onClick={onView} className="btn ghost btn-sm">Detail</button>
            {contract.status === 'active' && (
              <button onClick={onTerminate} className="btn ghost btn-sm text-red-400 hover:bg-red-400/10">Akhiri</button>
            )}
          </div>
        </div>
      </div>
    </motion.div>
  );
};

export default OwnerContracts;
