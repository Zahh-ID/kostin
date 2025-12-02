import React, { useEffect, useState } from 'react';
import { motion } from 'framer-motion';
import { FiClipboard, FiCheckCircle, FiXCircle, FiClock, FiUser, FiHome, FiMessageSquare } from 'react-icons/fi';
import { fetchOwnerApplications, approveOwnerApplication, rejectOwnerApplication } from '../../api/client';

const OwnerApplications = () => {
  const [applications, setApplications] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    loadData();
  }, []);

  const loadData = async () => {
    try {
      const data = await fetchOwnerApplications();
      setApplications(data);
    } catch (error) {
      console.error('Failed to fetch applications:', error);
    } finally {
      setLoading(false);
    }
  };

  const handleApprove = async (id) => {
    if (window.confirm('Setujui pengajuan ini?')) {
      try {
        await approveOwnerApplication(id);
        loadData();
      } catch (error) {
        alert('Gagal menyetujui pengajuan');
      }
    }
  };

  const handleReject = async (id) => {
    if (window.confirm('Tolak pengajuan ini?')) {
      try {
        await rejectOwnerApplication(id);
        loadData();
      } catch (error) {
        alert('Gagal menolak pengajuan');
      }
    }
  };

  const pending = applications.filter((app) => app.status === 'pending').length;
  const approved = applications.filter((app) => app.status === 'approved').length;

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
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          className="mb-12"
        >
          <h1 className="text-4xl font-display font-bold mb-2">Aplikasi Sewa</h1>
          <p className="text-text-secondary text-lg">
            Tinjau dan kelola pengajuan sewa dari calon penyewa.
          </p>
        </motion.div>

        {/* Stats Overview */}
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.2 }}
          className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12"
        >
          <StatusCard
            icon={<FiClock />}
            label="Menunggu Review"
            value={pending}
            desc="Butuh keputusan segera"
            color="text-yellow-400"
            bg="bg-yellow-400/10"
            border="border-yellow-400/20"
          />
          <StatusCard
            icon={<FiCheckCircle />}
            label="Disetujui"
            value={approved}
            desc="Siap untuk kontrak"
            color="text-green-400"
            bg="bg-green-400/10"
            border="border-green-400/20"
          />
          <StatusCard
            icon={<FiXCircle />}
            label="Ditolak"
            value={applications.length - pending - approved}
            desc="Pengajuan tidak valid"
            color="text-red-400"
            bg="bg-red-400/10"
            border="border-red-400/20"
          />
        </motion.div>

        <section>
          <div className="flex justify-between items-center mb-6">
            <h2 className="text-xl font-bold font-display flex items-center gap-2">
              <FiClipboard className="text-primary" /> Daftar Pengajuan
            </h2>
          </div>

          <motion.div
            variants={containerVariants}
            initial="hidden"
            animate="visible"
            className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
          >
            {loading ? (
              <p className="text-text-secondary">Memuat data...</p>
            ) : applications.length === 0 ? (
              <p className="text-text-secondary">Belum ada pengajuan sewa.</p>
            ) : (
              applications.map((app) => (
                <ApplicationCard key={app.id} app={app} onApprove={() => handleApprove(app.id)} onReject={() => handleReject(app.id)} />
              ))
            )}
          </motion.div>
        </section>
      </div>
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

const ApplicationCard = ({ app, onApprove, onReject }) => {
  const statusConfig = {
    pending: { label: 'Pending', color: 'text-yellow-400', bg: 'bg-yellow-400/10', border: 'border-yellow-400/20', icon: <FiClock /> },
    approved: { label: 'Disetujui', color: 'text-green-400', bg: 'bg-green-400/10', border: 'border-green-400/20', icon: <FiCheckCircle /> },
    rejected: { label: 'Ditolak', color: 'text-red-400', bg: 'bg-red-400/10', border: 'border-red-400/20', icon: <FiXCircle /> },
  };

  const status = statusConfig[app.status] || statusConfig.pending;

  return (
    <motion.div
      variants={{ hidden: { opacity: 0, y: 10 }, visible: { opacity: 1, y: 0 } }}
      className="card p-5 hover:bg-surface-highlight transition-colors flex flex-col h-full"
    >
      <div className="flex justify-between items-start mb-4">
        <div className="flex items-center gap-2">
          <div className="w-8 h-8 rounded-full bg-surface border border-border flex items-center justify-center text-text-secondary">
            <FiUser />
          </div>
          <div>
            <h3 className="font-bold text-sm">{app.tenant_name}</h3>
            <span className="text-xs text-text-tertiary">{new Date(app.created_at).toLocaleDateString()}</span>
          </div>
        </div>
        <span className={`px-2 py-1 rounded text-xs font-bold border flex items-center gap-1 ${status.color} ${status.bg} ${status.border}`}>
          {status.icon} {status.label}
        </span>
      </div>

      <div className="space-y-3 mb-6 flex-grow">
        <div className="flex items-center gap-2 text-sm text-text-secondary">
          <FiHome className="text-text-tertiary flex-shrink-0" />
          <span className="truncate">{app.property_name}</span>
        </div>
        <div className="flex items-center gap-2 text-sm text-text-secondary">
          <span className="w-4 h-4 flex items-center justify-center text-xs font-mono border border-text-tertiary rounded text-text-tertiary">#</span>
          <span>{app.room_name}</span>
        </div>
        {app.tenant_notes && (
          <div className="p-3 rounded-lg bg-surface border border-border text-xs text-text-secondary italic">
            <FiMessageSquare className="inline mr-1 text-text-tertiary" />
            "{app.tenant_notes}"
          </div>
        )}
      </div>

      <div className="flex gap-2 mt-auto">
        <button onClick={onApprove} className="btn primary btn-sm flex-1 justify-center" disabled={app.status !== 'pending'}>
          Approve
        </button>
        <button onClick={onReject} className="btn ghost btn-sm flex-1 justify-center text-red-400 hover:bg-red-400/10" disabled={app.status !== 'pending'}>
          Reject
        </button>
      </div>
    </motion.div>
  );
};

export default OwnerApplications;
