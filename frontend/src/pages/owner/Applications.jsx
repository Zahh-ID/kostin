import React, { useEffect, useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { FiClipboard, FiCheckCircle, FiXCircle, FiClock, FiUser, FiHome, FiMessageSquare, FiX, FiCalendar } from 'react-icons/fi';
import { fetchOwnerApplications, approveOwnerApplication, rejectOwnerApplication, fetchPropertyRooms } from '../../api/client';

const OwnerApplications = () => {
  const [applications, setApplications] = useState([]);
  const [loading, setLoading] = useState(true);
  const [selectedApp, setSelectedApp] = useState(null);

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

  const handleApprove = async (id, roomId) => {
    try {
      await approveOwnerApplication(id, roomId ? { room_id: roomId } : {});
      loadData();
      setSelectedApp(null);
    } catch (error) {
      alert('Gagal menyetujui pengajuan: ' + (error.response?.data?.message || error.message));
    }
  };

  const handleReject = async (id) => {
    if (window.confirm('Yakin ingin menolak pengajuan ini?')) {
      try {
        await rejectOwnerApplication(id);
        loadData();
        setSelectedApp(null);
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
                <ApplicationCard
                  key={app.id}
                  app={app}
                  onReview={() => setSelectedApp(app)}
                />
              ))
            )}
          </motion.div>
        </section>
      </div>

      {/* Review Modal */}
      <AnimatePresence>
        {selectedApp && (
          <ReviewModal
            app={selectedApp}
            onClose={() => setSelectedApp(null)}
            onApprove={(roomId) => handleApprove(selectedApp.id, roomId)}
            onReject={() => handleReject(selectedApp.id)}
          />
        )}
      </AnimatePresence>
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

const ApplicationCard = ({ app, onReview }) => {
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

      <div className="mt-auto">
        {app.status === 'pending' ? (
          <button onClick={onReview} className="btn primary btn-sm w-full justify-center">
            Review
          </button>
        ) : (
          <button className="btn ghost btn-sm w-full justify-center opacity-50 cursor-not-allowed" disabled>
            Selesai
          </button>
        )}
      </div>
    </motion.div>
  );
};

const ReviewModal = ({ app, onClose, onApprove, onReject }) => {
  const [rooms, setRooms] = useState([]);
  const [selectedRoomId, setSelectedRoomId] = useState(app.room_id || '');
  const [loadingRooms, setLoadingRooms] = useState(false);

  useEffect(() => {
    if (app.property_id) {
      setLoadingRooms(true);
      fetchPropertyRooms(app.property_id)
        .then(data => {
          setRooms(data.filter(r => r.status === 'available' || r.id === app.room_id));
        })
        .catch(console.error)
        .finally(() => setLoadingRooms(false));
    }
  }, [app.property_id, app.room_id]);

  const handleApproveClick = () => {
    if (!selectedRoomId) {
      alert('Mohon pilih kamar untuk disewakan.');
      return;
    }
    onApprove(selectedRoomId);
  };

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
      <motion.div
        initial={{ opacity: 0, scale: 0.95 }}
        animate={{ opacity: 1, scale: 1 }}
        exit={{ opacity: 0, scale: 0.95 }}
        className="bg-background border border-border rounded-2xl shadow-2xl w-full max-w-3xl overflow-hidden"
      >
        <div className="p-6 border-b border-border flex justify-between items-center">
          <h3 className="text-xl font-display font-bold">Review Pengajuan</h3>
          <button onClick={onClose} className="text-text-secondary hover:text-text-primary">
            <FiX size={24} />
          </button>
        </div>

        <div className="p-6 space-y-6 max-h-[70vh] overflow-y-auto">
          <div className="flex items-center gap-4">
            <div className="w-16 h-16 rounded-full bg-surface-highlight flex items-center justify-center text-3xl text-text-secondary">
              <FiUser />
            </div>
            <div>
              <h4 className="text-lg font-bold">{app.tenant_name}</h4>
              <p className="text-text-secondary text-sm">Calon Penyewa</p>
            </div>
          </div>

          <div className="space-y-3 p-4 bg-surface rounded-xl border border-border">
            <div className="flex items-center gap-3 text-sm">
              <FiHome className="text-primary" />
              <span className="font-medium">{app.property_name}</span>
            </div>

            <div className="flex flex-col gap-2">
              <div className="flex items-center gap-3 text-sm">
                <span className="w-4 h-4 flex items-center justify-center text-xs font-mono border border-primary rounded text-primary font-bold">#</span>
                <span className="font-medium">Kamar Sewa</span>
              </div>
              {loadingRooms ? (
                <div className="text-xs text-text-secondary animate-pulse ml-7">Memuat daftar kamar...</div>
              ) : (
                <select
                  className="ml-7 input py-2 px-3 text-sm bg-surface-highlight border-border rounded-lg focus:border-primary focus:ring-1 focus:ring-primary"
                  value={selectedRoomId}
                  onChange={(e) => setSelectedRoomId(e.target.value)}
                >
                  <option value="">-- Pilih Kamar --</option>
                  {rooms.map(room => (
                    <option key={room.id} value={room.id}>
                      {room.room_code} ({room.status === 'available' ? 'Tersedia' : 'Terisi'})
                    </option>
                  ))}
                </select>
              )}
              {!selectedRoomId && (
                <div className="ml-7 text-xs text-red-400">
                  * Wajib pilih kamar sebelum menyetujui.
                </div>
              )}
            </div>

            <div className="flex items-center gap-3 text-sm">
              <FiCalendar className="text-primary" />
              <span className="text-text-secondary">Diajukan: {new Date(app.created_at).toLocaleDateString()}</span>
            </div>
          </div>

          {/* Tenant Details */}
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div className="p-4 bg-surface rounded-xl border border-border">
              <h5 className="text-xs font-bold text-text-secondary uppercase tracking-wider mb-3">Data Diri</h5>
              <div className="space-y-2 text-sm">
                <div>
                  <span className="text-text-secondary block text-xs">Email</span>
                  {app.contact_email}
                </div>
                <div>
                  <span className="text-text-secondary block text-xs">Telepon</span>
                  {app.contact_phone}
                </div>
                <div>
                  <span className="text-text-secondary block text-xs">Jumlah Penghuni</span>
                  {app.occupants_count} Orang
                </div>
              </div>
            </div>

            <div className="p-4 bg-surface rounded-xl border border-border">
              <h5 className="text-xs font-bold text-text-secondary uppercase tracking-wider mb-3">Pekerjaan</h5>
              <div className="space-y-2 text-sm">
                <div>
                  <span className="text-text-secondary block text-xs">Status</span>
                  <span className="capitalize">{app.employment_status}</span>
                </div>
                <div>
                  <span className="text-text-secondary block text-xs">Perusahaan</span>
                  {app.company_name || '-'}
                </div>
                <div>
                  <span className="text-text-secondary block text-xs">Jabatan</span>
                  {app.job_title || '-'}
                </div>
              </div>
            </div>

            <div className="p-4 bg-surface rounded-xl border border-border">
              <h5 className="text-xs font-bold text-text-secondary uppercase tracking-wider mb-3">Kontak Darurat</h5>
              <div className="space-y-2 text-sm">
                <div>
                  <span className="text-text-secondary block text-xs">Nama</span>
                  {app.emergency_contact_name}
                </div>
                <div>
                  <span className="text-text-secondary block text-xs">Telepon</span>
                  {app.emergency_contact_phone}
                </div>
              </div>
            </div>

            <div className="p-4 bg-surface rounded-xl border border-border">
              <h5 className="text-xs font-bold text-text-secondary uppercase tracking-wider mb-3">Lainnya</h5>
              <div className="space-y-2 text-sm">
                <div>
                  <span className="text-text-secondary block text-xs">Kendaraan</span>
                  {app.has_vehicle ? 'Ya' : 'Tidak'}
                </div>
                {app.has_vehicle && (
                  <div>
                    <span className="text-text-secondary block text-xs">Catatan Kendaraan</span>
                    {app.vehicle_notes}
                  </div>
                )}
                <div>
                  <span className="text-text-secondary block text-xs">Durasi Sewa</span>
                  {app.duration_months} Bulan
                </div>
              </div>
            </div>
          </div>

          {app.tenant_notes && (
            <div>
              <label className="text-xs font-bold text-text-secondary uppercase tracking-wider mb-2 block">Catatan Penyewa</label>
              <div className="p-4 bg-surface-highlight rounded-xl text-sm italic text-text-secondary">
                "{app.tenant_notes}"
              </div>
            </div>
          )}
        </div>

        <div className="p-6 border-t border-border bg-surface flex gap-3">
          <button onClick={onReject} className="btn ghost flex-1 justify-center text-red-500 hover:bg-red-500/10 hover:border-red-500/20">
            Tolak
          </button>
          <button onClick={handleApproveClick} className="btn primary flex-1 justify-center">
            Setujui
          </button>
        </div>
      </motion.div>
    </div>
  );
};

export default OwnerApplications;
