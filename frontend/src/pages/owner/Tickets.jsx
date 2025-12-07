import React, { useEffect, useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { fetchOwnerTickets, updateOwnerTicket, createOwnerTicket, currentUser } from '../../api/client.js';
import { FiAlertCircle, FiCheckCircle, FiClock, FiXCircle, FiMessageSquare, FiUser, FiHome, FiEdit2, FiX, FiPlus } from 'react-icons/fi';

const statusClasses = {
  open: { label: 'Open', color: 'text-yellow-400', bg: 'bg-yellow-400/10', border: 'border-yellow-400/20', icon: <FiAlertCircle /> },
  in_review: { label: 'In Review', color: 'text-blue-400', bg: 'bg-blue-400/10', border: 'border-blue-400/20', icon: <FiClock /> },
  escalated: { label: 'Escalated', color: 'text-red-400', bg: 'bg-red-400/10', border: 'border-red-400/20', icon: <FiAlertCircle /> },
  resolved: { label: 'Resolved', color: 'text-green-400', bg: 'bg-green-400/10', border: 'border-green-400/20', icon: <FiCheckCircle /> },
  rejected: { label: 'Rejected', color: 'text-text-secondary', bg: 'bg-surface-highlight', border: 'border-border', icon: <FiXCircle /> },
};

const OwnerTickets = () => {
  const [tickets, setTickets] = useState([]);
  const [meta, setMeta] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const [search, setSearch] = useState('');
  const [status, setStatus] = useState('all');
  const [type, setType] = useState('received'); // 'received' | 'sent'
  const [user, setUser] = useState(null);
  const [modal, setModal] = useState({ open: false, ticket: null, status: 'open', notes: '' });
  const [createModal, setCreateModal] = useState({ open: false });
  const [actionError, setActionError] = useState('');
  const [actionLoading, setActionLoading] = useState(false);

  // Fetch user
  useEffect(() => {
    currentUser().then(setUser);
  }, []);

  // Debounce search
  useEffect(() => {
    const timer = setTimeout(() => {
      fetchTickets();
    }, 500);
    return () => clearTimeout(timer);
  }, [search, status, type]);

  const fetchTickets = () => {
    setLoading(true);
    setError('');
    fetchOwnerTickets({ search, status, type })
      .then((data) => {
        setTickets(data.data);
        setMeta(data.meta);
      })
      .catch(() => setError('Gagal memuat tiket.'))
      .finally(() => setLoading(false));
  };

  const openCount = tickets.filter((t) => ['open', 'in_review', 'escalated'].includes(t.status)).length;
  const resolvedCount = tickets.filter((t) => t.status === 'resolved').length;
  const rejectedCount = tickets.filter((t) => t.status === 'rejected').length;

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
          className="mb-12 flex justify-between items-end"
        >
          <div>
            <h1 className="text-4xl font-display font-bold mb-2">Tiket & Bantuan</h1>
            <p className="text-text-secondary text-lg">
              Kelola laporan masalah dan permintaan bantuan dari penyewa.
            </p>
          </div>
          <button
            onClick={() => setCreateModal({ open: true })}
            className="btn primary"
          >
            <FiPlus className="mr-2" /> Buat Tiket Bantuan
          </button>
        </motion.div>

        {/* Stats Overview */}
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.2 }}
          className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12"
        >
          <StatusCard
            icon={<FiAlertCircle />}
            label="Tiket Aktif"
            value={openCount}
            desc="Perlu penanganan"
            color="text-blue-400"
            bg="bg-blue-400/10"
            border="border-blue-400/20"
          />
          <StatusCard
            icon={<FiCheckCircle />}
            label="Selesai"
            value={resolvedCount}
            desc="Masalah teratasi"
            color="text-green-400"
            bg="bg-green-400/10"
            border="border-green-400/20"
          />
          <StatusCard
            icon={<FiXCircle />}
            label="Ditolak"
            value={rejectedCount}
            desc="Permintaan tidak valid"
            color="text-red-400"
            bg="bg-red-400/10"
            border="border-red-400/20"
          />
        </motion.div>

        {error && (
          <div className="p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-500 mb-8 flex items-center gap-2">
            <FiAlertCircle /> {error}
          </div>
        )}

        <section>
          <div className="flex flex-col gap-6 mb-6">
            <div className="flex justify-between items-center">
              <h2 className="text-xl font-bold font-display flex items-center gap-2">
                <FiMessageSquare className="text-primary" /> Daftar Tiket
              </h2>
            </div>

            {/* Type Tabs (Sent vs Received) */}
            <div className="flex border-b border-border">
              <button
                onClick={() => setType('received')}
                className={`px-6 py-3 text-sm font-medium border-b-2 transition-colors ${type === 'received'
                  ? 'border-primary text-primary'
                  : 'border-transparent text-text-secondary hover:text-text-primary'
                  }`}
              >
                Tiket Masuk (Dari Penyewa)
              </button>
              <button
                onClick={() => setType('sent')}
                className={`px-6 py-3 text-sm font-medium border-b-2 transition-colors ${type === 'sent'
                  ? 'border-primary text-primary'
                  : 'border-transparent text-text-secondary hover:text-text-primary'
                  }`}
              >
                Tiket Keluar (Ke Admin)
              </button>
            </div>

            <div className="flex flex-col md:flex-row justify-between items-center gap-4">
              <div className="flex flex-col md:flex-row gap-4 w-full md:w-auto">
                {/* Search Input */}
                <div className="relative w-full md:w-auto">
                  <input
                    type="text"
                    placeholder="Cari tiket..."
                    value={search}
                    onChange={(e) => setSearch(e.target.value)}
                    className="pl-10 pr-4 py-2 rounded-xl bg-surface-highlight border border-border focus:outline-none focus:border-primary w-full md:w-64 transition-all"
                  />
                  <div className="absolute left-3 top-1/2 -translate-y-1/2 text-text-tertiary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><circle cx="11" cy="11" r="8" /><line x1="21" y1="21" x2="16.65" y2="16.65" /></svg>
                  </div>
                </div>

                {/* Status Tabs */}
                <div className="flex bg-surface-highlight p-1 rounded-xl overflow-x-auto">
                  {['all', 'open', 'resolved'].map((s) => (
                    <button
                      key={s}
                      onClick={() => setStatus(s)}
                      className={`px-4 py-1.5 rounded-lg text-sm font-medium transition-all capitalize whitespace-nowrap ${status === s
                        ? 'bg-surface text-primary shadow-sm'
                        : 'text-text-secondary hover:text-text-primary'
                        }`}
                    >
                      {s === 'all' ? 'Semua' : s}
                    </button>
                  ))}
                </div>
              </div>
            </div>
          </div>

          {loading ? (
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {[1, 2, 3].map((i) => (
                <div key={i} className="h-48 rounded-xl bg-surface-highlight animate-pulse" />
              ))}
            </div>
          ) : tickets.length === 0 ? (
            <div className="text-center py-12 card border-dashed border-2 border-border bg-transparent">
              <div className="inline-flex p-4 rounded-full bg-surface-highlight mb-4">
                <FiMessageSquare className="text-4xl text-text-tertiary" />
              </div>
              <p className="text-text-secondary font-medium">Tidak ada tiket ditemukan.</p>
              <p className="text-sm text-text-tertiary mt-1">
                {type === 'received' ? 'Belum ada tiket dari penyewa.' : 'Anda belum membuat tiket untuk admin.'}
              </p>
            </div>
          ) : (
            <motion.div
              variants={containerVariants}
              initial="hidden"
              animate="visible"
              className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
            >
              {tickets.map((ticket) => (
                <TicketCard
                  key={ticket.id}
                  ticket={ticket}
                  user={user}
                  onUpdate={() => setModal({ open: true, ticket, status: ticket.status, notes: '' })}
                />
              ))}
            </motion.div>
          )}

          {/* Simple Pagination Info if needed */}
          {meta && meta.total > 20 && (
            <div className="mt-6 text-center text-sm text-text-secondary">
              Menampilkan {tickets.length} dari {meta.total} tiket
            </div>
          )}
        </section>

        <AnimatePresence>
          {modal.open && modal.ticket && (
            <UpdateStatusModal
              modal={modal}
              setModal={setModal}
              setTickets={setTickets}
              actionLoading={actionLoading}
              setActionLoading={setActionLoading}
              actionError={actionError}
              setActionError={setActionError}
            />
          )}
          {createModal.open && (
            <CreateTicketModal
              modal={createModal}
              setModal={setCreateModal}
              setTickets={setTickets}
            />
          )}
        </AnimatePresence>
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

const TicketCard = ({ ticket, user, onUpdate }) => {
  const status = statusClasses[ticket.status] || statusClasses.open;
  const isReporter = user?.id === ticket.reporter_id;
  const isPlatformTicket = !ticket.related; // Assuming related is null for platform tickets

  return (
    <motion.div
      variants={{ hidden: { opacity: 0, y: 10 }, visible: { opacity: 1, y: 0 } }}
      className="card p-5 hover:bg-surface-highlight transition-colors flex flex-col h-full"
    >
      <div className="flex justify-between items-start mb-4">
        <span className="text-xs font-mono text-text-tertiary bg-surface border border-border px-2 py-1 rounded">
          {ticket.ticket_code ?? ticket.subject}
        </span>
        <span className={`px-2 py-1 rounded text-xs font-bold border flex items-center gap-1 ${status.color} ${status.bg} ${status.border}`}>
          {status.icon} {status.label}
        </span>
      </div>

      <h3 className="font-bold font-display text-lg mb-2 line-clamp-1">{ticket.subject}</h3>

      <div className="space-y-2 text-sm text-text-secondary mb-6 flex-grow">
        <div className="flex items-center gap-2">
          <FiUser className="text-text-tertiary" />
          <span>{ticket.reporter?.name ?? '—'}</span>
        </div>
        <div className="flex items-center gap-2">
          <FiHome className="text-text-tertiary" />
          <span className="truncate">{ticket.related?.property?.name ?? '—'}</span>
        </div>
      </div>

      {(!isReporter || !isPlatformTicket) && (
        <button
          onClick={onUpdate}
          className="btn ghost w-full justify-center border border-border hover:border-primary hover:bg-primary/10 hover:text-primary transition-all"
        >
          <FiEdit2 className="mr-2" /> Update Status
        </button>
      )}
    </motion.div>
  );
};

const UpdateStatusModal = ({ modal, setModal, setTickets, actionLoading, setActionLoading, actionError, setActionError }) => {
  const handleUpdate = async () => {
    setActionError('');
    setActionLoading(true);
    try {
      const updated = await updateOwnerTicket(modal.ticket.id, { status: modal.status, notes: modal.notes.trim() });
      setTickets((prev) => prev.map((t) => (t.id === modal.ticket.id ? updated : t)));
      setModal({ open: false, ticket: null, status: 'open', notes: '' });
    } catch (err) {
      setActionError(err?.response?.data?.message ?? 'Gagal memperbarui tiket.');
    } finally {
      setActionLoading(false);
    }
  };

  return (
    <div className="fixed inset-0 z-[2000] flex items-center justify-center p-4">
      <motion.div
        initial={{ opacity: 0 }}
        animate={{ opacity: 1 }}
        exit={{ opacity: 0 }}
        className="absolute inset-0 bg-black/80 backdrop-blur-sm"
        onClick={() => setModal({ ...modal, open: false })}
      />
      <motion.div
        initial={{ opacity: 0, scale: 0.95 }}
        animate={{ opacity: 1, scale: 1 }}
        exit={{ opacity: 0, scale: 0.95 }}
        className="relative bg-surface border border-border rounded-2xl w-full max-w-md shadow-2xl p-6"
      >
        <div className="flex justify-between items-center mb-6">
          <h3 className="text-xl font-bold font-display">Update Status Tiket</h3>
          <button onClick={() => setModal({ ...modal, open: false })} className="p-2 hover:bg-white/5 rounded-full transition-colors">
            <FiX />
          </button>
        </div>

        {actionError && (
          <div className="p-3 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400 text-sm mb-4">
            {actionError}
          </div>
        )}

        <div className="space-y-4">
          <div>
            <label className="block text-sm font-medium text-text-secondary mb-1">Status Baru</label>
            <select
              value={modal.status}
              onChange={(e) => setModal((prev) => ({ ...prev, status: e.target.value }))}
              className="w-full bg-surface-highlight border border-border rounded-lg px-4 py-2 focus:outline-none focus:border-primary transition-colors appearance-none"
            >
              <option value="open">Open</option>
              <option value="in_review">In Review</option>
              <option value="escalated">Escalated</option>
              <option value="resolved">Resolved</option>
              <option value="rejected">Rejected</option>
            </select>
          </div>

          <div>
            <label className="block text-sm font-medium text-text-secondary mb-1">Catatan (Wajib)</label>
            <textarea
              rows="3"
              value={modal.notes}
              onChange={(e) => setModal((prev) => ({ ...prev, notes: e.target.value }))}
              placeholder="Tambahkan catatan penyelesaian atau alasan..."
              className="w-full bg-surface-highlight border border-border rounded-lg px-4 py-2 focus:outline-none focus:border-primary transition-colors"
            />
          </div>

          <div className="flex gap-3 pt-2">
            <button
              onClick={() => setModal({ ...modal, open: false })}
              className="btn ghost flex-1 justify-center"
            >
              Batal
            </button>
            <button
              onClick={handleUpdate}
              disabled={actionLoading || !modal.notes.trim()}
              className="btn primary flex-1 justify-center"
            >
              {actionLoading ? 'Menyimpan...' : 'Simpan'}
            </button>
          </div>
        </div>
      </motion.div>
    </div>
  );
};



const CreateTicketModal = ({ modal, setModal, setTickets }) => {
  const [form, setForm] = useState({
    subject: '',
    description: '',
    category: 'technical',
    priority: 'medium',
  });
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');

  const handleSubmit = async () => {
    if (!form.subject || !form.description) {
      setError('Subjek dan deskripsi wajib diisi.');
      return;
    }
    setLoading(true);
    setError('');
    try {
      const newTicket = await createOwnerTicket(form);
      setTickets((prev) => [newTicket, ...prev]);
      setModal({ open: false });
    } catch (err) {
      setError(err?.response?.data?.message ?? 'Gagal membuat tiket.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="fixed inset-0 z-[2000] flex items-center justify-center p-4">
      <motion.div
        initial={{ opacity: 0 }}
        animate={{ opacity: 1 }}
        exit={{ opacity: 0 }}
        className="absolute inset-0 bg-black/80 backdrop-blur-sm"
        onClick={() => setModal({ open: false })}
      />
      <motion.div
        initial={{ opacity: 0, scale: 0.95 }}
        animate={{ opacity: 1, scale: 1 }}
        exit={{ opacity: 0, scale: 0.95 }}
        className="relative bg-surface border border-border rounded-2xl w-full max-w-lg shadow-2xl p-6"
      >
        <div className="flex justify-between items-center mb-6">
          <h3 className="text-xl font-bold font-display">Buat Tiket Bantuan</h3>
          <button onClick={() => setModal({ open: false })} className="p-2 hover:bg-white/5 rounded-full transition-colors">
            <FiX />
          </button>
        </div>

        {error && (
          <div className="p-3 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400 text-sm mb-4">
            {error}
          </div>
        )}

        <div className="space-y-4">
          <div>
            <label className="block text-sm font-medium text-text-secondary mb-1">Subjek</label>
            <input
              type="text"
              value={form.subject}
              onChange={(e) => setForm({ ...form, subject: e.target.value })}
              placeholder="Contoh: Kendala pembayaran"
              className="w-full bg-surface-highlight border border-border rounded-lg px-4 py-2 focus:outline-none focus:border-primary transition-colors"
            />
          </div>

          <div>
            <label className="block text-sm font-medium text-text-secondary mb-1">Deskripsi</label>
            <textarea
              rows="4"
              value={form.description}
              onChange={(e) => setForm({ ...form, description: e.target.value })}
              placeholder="Jelaskan detail masalah Anda..."
              className="w-full bg-surface-highlight border border-border rounded-lg px-4 py-2 focus:outline-none focus:border-primary transition-colors"
            />
          </div>

          <div className="grid grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-text-secondary mb-1">Kategori</label>
              <select
                value={form.category}
                onChange={(e) => setForm({ ...form, category: e.target.value })}
                className="w-full bg-surface-highlight border border-border rounded-lg px-4 py-2 focus:outline-none focus:border-primary transition-colors appearance-none"
              >
                <option value="technical">Teknis</option>
                <option value="payment">Pembayaran</option>
                <option value="content">Konten</option>
                <option value="abuse">Pelanggaran</option>
              </select>
            </div>
            <div>
              <label className="block text-sm font-medium text-text-secondary mb-1">Prioritas</label>
              <select
                value={form.priority}
                onChange={(e) => setForm({ ...form, priority: e.target.value })}
                className="w-full bg-surface-highlight border border-border rounded-lg px-4 py-2 focus:outline-none focus:border-primary transition-colors appearance-none"
              >
                <option value="low">Rendah</option>
                <option value="medium">Sedang</option>
                <option value="high">Tinggi</option>
                <option value="urgent">Mendesak</option>
              </select>
            </div>
          </div>

          <div className="flex gap-3 pt-4">
            <button
              onClick={() => setModal({ open: false })}
              className="btn ghost flex-1 justify-center"
            >
              Batal
            </button>
            <button
              onClick={handleSubmit}
              disabled={loading}
              className="btn primary flex-1 justify-center"
            >
              {loading ? 'Mengirim...' : 'Kirim Tiket'}
            </button>
          </div>
        </div>
      </motion.div>
    </div>
  );
};

export default OwnerTickets;
