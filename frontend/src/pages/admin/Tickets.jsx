import React, { useEffect, useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { fetchAdminTickets, updateAdminTicket } from '../../api/client.js';
import { FiAlertCircle, FiCheckCircle, FiClock, FiMessageSquare, FiUser, FiXCircle, FiFilter } from 'react-icons/fi';

const AdminTickets = () => {
  const [tickets, setTickets] = useState([]);
  const [loading, setLoading] = useState(true);
  const [filter, setFilter] = useState('all'); // all, open, in_progress, resolved
  const [selectedTicket, setSelectedTicket] = useState(null);
  const [updateModal, setUpdateModal] = useState({ open: false, status: '', notes: '' });
  const [actionLoading, setActionLoading] = useState(false);

  useEffect(() => {
    loadData();
  }, []);

  const loadData = async () => {
    setLoading(true);
    try {
      const res = await fetchAdminTickets();
      setTickets(res.data ?? res ?? []);
    } catch (error) {
      console.error('Failed to load tickets:', error);
    } finally {
      setLoading(false);
    }
  };

  const filteredTickets = tickets.filter(ticket => {
    if (filter === 'all') return true;
    return ticket.status === filter;
  });

  const handleUpdate = async () => {
    if (!selectedTicket) return;

    setActionLoading(true);
    try {
      await updateAdminTicket(selectedTicket.id, {
        status: updateModal.status,
        notes: updateModal.notes
      });
      await loadData();
      setUpdateModal({ open: false, status: '', notes: '' });
      setSelectedTicket(null);
    } catch (error) {
      alert('Gagal memperbarui tiket: ' + (error.response?.data?.message || error.message));
    } finally {
      setActionLoading(false);
    }
  };

  const openUpdateModal = (ticket) => {
    setSelectedTicket(ticket);
    setUpdateModal({
      open: true,
      status: ticket.status,
      notes: ''
    });
  };

  return (
    <div className="page pt-32 pb-20">
      <div className="container">
        <motion.div
          initial={{ opacity: 0, y: -20 }}
          animate={{ opacity: 1, y: 0 }}
          className="mb-8"
        >
          <h1 className="text-4xl font-display font-bold mb-2">Tiket Bantuan</h1>
          <p className="text-text-secondary text-lg">
            Kelola laporan masalah dan permintaan bantuan dari pengguna.
          </p>
        </motion.div>

        {/* Filters */}
        <div className="flex gap-2 mb-8 overflow-x-auto pb-2">
          {['all', 'open', 'in_progress', 'resolved', 'closed'].map((status) => (
            <button
              key={status}
              onClick={() => setFilter(status)}
              className={`px-4 py-2 rounded-xl text-sm font-bold border transition-all whitespace-nowrap ${filter === status
                  ? 'bg-primary text-white border-primary shadow-lg shadow-primary/20'
                  : 'bg-surface-highlight text-text-secondary border-border hover:border-primary/50'
                }`}
            >
              {status === 'all' ? 'Semua Tiket' : status.replace('_', ' ').toUpperCase()}
            </button>
          ))}
        </div>

        {/* Ticket List */}
        <div className="grid grid-cols-1 gap-4">
          {loading ? (
            <div className="text-center py-12 text-text-secondary">Memuat data...</div>
          ) : filteredTickets.length === 0 ? (
            <div className="text-center py-12 text-text-secondary bg-surface-highlight rounded-2xl border border-dashed border-border">
              <FiMessageSquare className="mx-auto text-4xl mb-4 opacity-20" />
              <p>Tidak ada tiket {filter !== 'all' ? `dengan status ${filter}` : ''}.</p>
            </div>
          ) : (
            filteredTickets.map((ticket) => (
              <motion.div
                key={ticket.id}
                initial={{ opacity: 0, y: 10 }}
                animate={{ opacity: 1, y: 0 }}
                className="card p-6 hover:bg-surface-highlight transition-colors group"
              >
                <div className="flex flex-col md:flex-row justify-between gap-6">
                  <div className="flex-grow">
                    <div className="flex items-center gap-3 mb-2">
                      <span className="text-xs font-mono text-text-tertiary">#{ticket.ticket_code || ticket.id}</span>
                      <StatusBadge status={ticket.status} />
                      <span className="text-xs text-text-tertiary flex items-center gap-1">
                        <FiClock /> {new Date(ticket.created_at).toLocaleDateString()}
                      </span>
                    </div>
                    <h3 className="font-bold font-display text-lg mb-2">{ticket.subject}</h3>
                    <p className="text-text-secondary text-sm mb-4 bg-surface p-3 rounded-lg border border-border">
                      {ticket.message}
                    </p>

                    <div className="flex flex-wrap gap-4 text-xs text-text-secondary">
                      <div className="flex items-center gap-2">
                        <FiUser className="text-primary" />
                        <span>Reporter: <span className="font-bold text-white">{ticket.reporter?.name || 'Unknown'}</span></span>
                      </div>
                      {ticket.assignee && (
                        <div className="flex items-center gap-2">
                          <FiUser className="text-blue-400" />
                          <span>Assignee: <span className="font-bold text-white">{ticket.assignee?.name}</span></span>
                        </div>
                      )}
                    </div>
                  </div>

                  <div className="flex flex-col justify-center min-w-[150px]">
                    <button
                      onClick={() => openUpdateModal(ticket)}
                      className="btn primary btn-sm w-full justify-center"
                    >
                      Update Status
                    </button>
                  </div>
                </div>
              </motion.div>
            ))
          )}
        </div>
      </div>

      {/* Update Modal */}
      <AnimatePresence>
        {updateModal.open && selectedTicket && (
          <div className="fixed inset-0 z-[2000] flex items-center justify-center p-4">
            <div className="absolute inset-0 bg-black/80 backdrop-blur-sm" onClick={() => setUpdateModal({ ...updateModal, open: false })} />
            <motion.div
              initial={{ opacity: 0, scale: 0.95 }}
              animate={{ opacity: 1, scale: 1 }}
              exit={{ opacity: 0, scale: 0.95 }}
              className="relative bg-surface border border-white/10 rounded-3xl w-full max-w-md p-6 shadow-2xl"
            >
              <div className="flex justify-between items-center mb-6">
                <h2 className="text-xl font-bold font-display">Update Status Tiket</h2>
                <button onClick={() => setUpdateModal({ ...updateModal, open: false })}><FiXCircle className="text-xl text-text-secondary hover:text-white" /></button>
              </div>

              <div className="mb-6">
                <h3 className="font-bold text-sm mb-1">{selectedTicket.subject}</h3>
                <p className="text-xs text-text-secondary">#{selectedTicket.ticket_code}</p>
              </div>

              <div className="space-y-4 mb-6">
                <div>
                  <label className="block text-sm text-text-secondary mb-2">Status Baru</label>
                  <select
                    value={updateModal.status}
                    onChange={(e) => setUpdateModal({ ...updateModal, status: e.target.value })}
                    className="w-full bg-surface-highlight border border-white/10 rounded-xl px-4 py-3 focus:border-primary outline-none"
                  >
                    <option value="open">Open</option>
                    <option value="in_progress">In Progress</option>
                    <option value="resolved">Resolved</option>
                    <option value="closed">Closed</option>
                  </select>
                </div>

                <div>
                  <label className="block text-sm text-text-secondary mb-2">Catatan Update</label>
                  <textarea
                    value={updateModal.notes}
                    onChange={(e) => setUpdateModal({ ...updateModal, notes: e.target.value })}
                    className="w-full bg-surface-highlight border border-white/10 rounded-xl px-4 py-3 focus:border-primary outline-none min-h-[100px]"
                    placeholder="Tambahkan catatan penyelesaian atau progress..."
                    required
                  />
                </div>
              </div>

              <div className="flex gap-3">
                <button onClick={() => setUpdateModal({ ...updateModal, open: false })} className="btn ghost flex-1">Batal</button>
                <button
                  onClick={handleUpdate}
                  disabled={actionLoading || !updateModal.notes.trim()}
                  className="btn primary flex-1 justify-center"
                >
                  {actionLoading ? 'Menyimpan...' : 'Simpan Perubahan'}
                </button>
              </div>
            </motion.div>
          </div>
        )}
      </AnimatePresence>
    </div>
  );
};

const StatusBadge = ({ status }) => {
  const styles = {
    open: 'bg-red-400/10 text-red-400 border-red-400/20',
    in_progress: 'bg-blue-400/10 text-blue-400 border-blue-400/20',
    resolved: 'bg-green-400/10 text-green-400 border-green-400/20',
    closed: 'bg-gray-400/10 text-gray-400 border-gray-400/20',
  };

  const icons = {
    open: <FiAlertCircle className="mr-1" />,
    in_progress: <FiClock className="mr-1" />,
    resolved: <FiCheckCircle className="mr-1" />,
    closed: <FiCheckCircle className="mr-1" />,
  };

  return (
    <span className={`inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase border ${styles[status] || styles.open}`}>
      {icons[status]}
      {status.replace('_', ' ')}
    </span>
  );
};

export default AdminTickets;
