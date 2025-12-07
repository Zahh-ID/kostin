import React, { useEffect, useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { FiMessageSquare, FiPlus, FiX, FiAlertCircle, FiCheckCircle, FiClock, FiUser, FiSend } from 'react-icons/fi';
import { createTenantTicket, fetchTenantContracts, fetchTenantTicket, fetchTenantTickets } from '../../api/client.js';

const statusConfig = (status) => {
  switch (status) {
    case 'closed':
    case 'resolved':
    case 'done':
      return { color: 'bg-green-500/20 text-green-500 border-green-500/20', icon: <FiCheckCircle /> };
    case 'pending':
    case 'open':
      return { color: 'bg-yellow-500/20 text-yellow-500 border-yellow-500/20', icon: <FiClock /> };
    case 'escalated':
    case 'blocked':
      return { color: 'bg-red-500/20 text-red-500 border-red-500/20', icon: <FiAlertCircle /> };
    default:
      return { color: 'bg-surface-highlight text-text-secondary border-border', icon: <FiMessageSquare /> };
  }
};

const formatDate = (value) => {
  if (!value) return '—';
  return new Date(value).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
};

const TenantTickets = () => {
  const [tickets, setTickets] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const [detail, setDetail] = useState(null);
  const [detailLoading, setDetailLoading] = useState(false);
  const [detailOpen, setDetailOpen] = useState(false);
  const [createOpen, setCreateOpen] = useState(false);
  const [createError, setCreateError] = useState('');
  const [createSubmitting, setCreateSubmitting] = useState(false);
  const [contracts, setContracts] = useState([]);
  const [createForm, setCreateForm] = useState({
    subject: '',
    description: '',
    category: 'technical',
    priority: 'medium',
    target: 'owner',
    property_id: '',
  });

  useEffect(() => {
    setLoading(true);
    setError('');
    fetchTenantTickets(20)
      .then((data) => setTickets(data))
      .catch(() => setError('Gagal memuat tiket. Pastikan Anda sudah login.'))
      .finally(() => setLoading(false));

    fetchTenantContracts({ per_page: 50 })
      .then((data) => {
        setContracts(data);
        const firstPropertyId = data?.[0]?.room?.room_type?.property?.id ?? '';
        setCreateForm((prev) => ({
          ...prev,
          target: data.length ? 'owner' : 'admin',
          property_id: firstPropertyId,
        }));
      })
      .catch(() => {
        setContracts([]);
        setCreateForm((prev) => ({ ...prev, target: 'admin', property_id: '' }));
      });
  }, []);

  const openDetail = async (ticketId) => {
    setDetailLoading(true);
    setDetail(null);
    setDetailOpen(true);
    try {
      const data = await fetchTenantTicket(ticketId);
      setDetail(data);
    } catch (err) {
      setError('Gagal memuat detail tiket.');
      setDetailOpen(false);
    } finally {
      setDetailLoading(false);
    }
  };

  const submitTicket = async () => {
    setCreateError('');
    if (!createForm.subject.trim() || !createForm.description.trim()) {
      setCreateError('Subjek dan deskripsi wajib diisi.');
      return;
    }
    if (createForm.target === 'owner' && !createForm.property_id) {
      setCreateError('Pilih properti kontrak untuk mengirim ke owner.');
      return;
    }
    setCreateSubmitting(true);
    try {
      const payload = {
        subject: createForm.subject,
        description: createForm.description,
        category: createForm.category,
        priority: createForm.priority,
      };
      if (createForm.target === 'owner') {
        payload.property_id = createForm.property_id;
      }
      const data = await createTenantTicket(payload);
      setTickets((prev) => [data, ...prev]);
      setCreateOpen(false);
      setCreateForm({
        subject: '',
        description: '',
        category: 'technical',
        priority: 'medium',
        target: contracts.length ? 'owner' : 'admin',
        property_id: contracts?.[0]?.room?.room_type?.property?.id ?? '',
      });
    } catch (err) {
      const msg = err?.response?.data?.message ?? 'Gagal membuat tiket.';
      setCreateError(msg);
    } finally {
      setCreateSubmitting(false);
    }
  };

  return (
    <div className="page pt-32 pb-20">
      <div className="container">
        <div className="flex flex-col md:flex-row justify-between items-end mb-12 gap-6">
          <div>
            <motion.h1
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              className="text-4xl font-display font-bold mb-4"
            >
              Pusat <span className="text-primary">Bantuan</span>
            </motion.h1>
            <motion.p
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ delay: 0.1 }}
              className="text-text-secondary text-lg"
            >
              Laporkan kendala atau ajukan pertanyaan terkait sewa kostmu.
            </motion.p>
          </div>
          <motion.button
            initial={{ opacity: 0, x: 20 }}
            animate={{ opacity: 1, x: 0 }}
            transition={{ delay: 0.2 }}
            className="btn primary"
            onClick={() => setCreateOpen(true)}
          >
            <FiPlus /> Buat Tiket Baru
          </motion.button>
        </div>

        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.3 }}
          className="card p-6 border-primary/20"
        >
          <div className="flex justify-between items-center mb-6">
            <h2 className="text-2xl font-display font-bold">Riwayat Tiket</h2>
            <div className="text-sm text-text-secondary">{tickets.length} tiket</div>
          </div>

          {error && (
            <div className="p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-500 mb-6 text-center">
              {error}
            </div>
          )}

          {loading ? (
            <div className="space-y-4">
              {[1, 2, 3].map((i) => (
                <div key={i} className="h-20 rounded-xl bg-surface-highlight animate-pulse" />
              ))}
            </div>
          ) : tickets.length === 0 ? (
            <div className="text-center py-12 border-2 border-dashed border-border rounded-xl">
              <div className="inline-flex p-4 rounded-full bg-surface-highlight mb-4">
                <FiMessageSquare className="text-4xl text-text-tertiary" />
              </div>
              <h3 className="text-xl font-bold mb-2">Belum ada tiket</h3>
              <p className="text-text-secondary mb-6">Semua kendala yang kamu laporkan akan muncul di sini.</p>
              <button className="btn primary" onClick={() => setCreateOpen(true)}>
                Buat Tiket Pertama
              </button>
            </div>
          ) : (
            <div className="space-y-4">
              {tickets.map((ticket, index) => {
                const status = statusConfig(ticket.status);
                return (
                  <motion.div
                    key={ticket.id}
                    initial={{ opacity: 0, y: 10 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ delay: index * 0.05 }}
                    className="p-4 rounded-xl bg-surface border border-border hover:border-primary/50 transition-all cursor-pointer group"
                    onClick={() => openDetail(ticket.id)}
                  >
                    <div className="flex flex-col md:flex-row justify-between gap-4">
                      <div className="flex items-start gap-4">
                        <div className={`p-3 rounded-xl ${status.color} bg-opacity-10`}>
                          {status.icon}
                        </div>
                        <div>
                          <div className="flex items-center gap-2 mb-1">
                            <h3 className="font-bold text-lg group-hover:text-primary transition-colors">{ticket.subject ?? '—'}</h3>
                            <span className={`text-xs px-2 py-0.5 rounded-full border uppercase font-bold ${status.color}`}>
                              {ticket.status ?? '—'}
                            </span>
                          </div>
                          <p className="text-sm text-text-secondary mb-2 line-clamp-1">
                            #{ticket.ticket_code ?? ticket.id} · {formatDate(ticket.created_at)}
                          </p>
                        </div>
                      </div>
                      <div className="flex items-center justify-end">
                        <button className="btn ghost btn-sm">Lihat Detail</button>
                      </div>
                    </div>
                  </motion.div>
                );
              })}
            </div>
          )}
        </motion.div>
      </div>

      <AnimatePresence>
        {detailOpen && (
          <div className="fixed inset-0 z-[2000] flex items-center justify-center p-4">
            <motion.div
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              exit={{ opacity: 0 }}
              className="absolute inset-0 bg-black/60 backdrop-blur-md"
              onClick={() => setDetailOpen(false)}
            />
            <motion.div
              initial={{ opacity: 0, scale: 0.95, y: 20 }}
              animate={{ opacity: 1, scale: 1, y: 0 }}
              exit={{ opacity: 0, scale: 0.95, y: 20 }}
              className="relative bg-surface/95 backdrop-blur-2xl border border-white/10 rounded-3xl w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-2xl ring-1 ring-black/5 flex flex-col"
            >
              <div className="sticky top-0 bg-surface/80 backdrop-blur-md border-b border-white/5 p-6 flex justify-between items-center z-10">
                <div>
                  <div className="flex items-center gap-2 mb-2">
                    <span className="px-2 py-0.5 rounded-full bg-primary/10 text-primary text-xs font-bold uppercase tracking-wider border border-primary/20">
                      Detail Tiket
                    </span>
                  </div>
                  <h2 className="text-2xl font-display font-bold text-text-primary">{detail?.subject ?? 'Tiket'}</h2>
                </div>
                <button
                  onClick={() => setDetailOpen(false)}
                  className="p-2 hover:bg-white/10 rounded-full transition-colors text-text-secondary hover:text-text-primary"
                >
                  <FiX className="text-xl" />
                </button>
              </div>

              <div className="p-6 overflow-y-auto flex-grow">
                {detailLoading ? (
                  <div className="text-center py-12">
                    <div className="w-10 h-10 border-2 border-primary border-t-transparent rounded-full animate-spin mx-auto mb-4" />
                    <p className="text-text-secondary animate-pulse">Memuat percakapan...</p>
                  </div>
                ) : detail ? (
                  <div className="space-y-8">
                    <div className="flex flex-wrap gap-3 text-sm">
                      <div className="px-4 py-1.5 rounded-full bg-surface-highlight/50 border border-white/5 flex items-center gap-2">
                        <span className="text-text-secondary">Status:</span>
                        <span className="font-bold uppercase text-text-primary">{detail.status}</span>
                      </div>
                      <div className="px-4 py-1.5 rounded-full bg-surface-highlight/50 border border-white/5 flex items-center gap-2">
                        <span className="text-text-secondary">Prioritas:</span>
                        <span className="font-bold uppercase text-text-primary">{detail.priority}</span>
                      </div>
                      <div className="px-4 py-1.5 rounded-full bg-surface-highlight/50 border border-white/5 flex items-center gap-2">
                        <span className="text-text-secondary">Kategori:</span>
                        <span className="font-bold uppercase text-text-primary">{detail.category}</span>
                      </div>
                    </div>

                    <div className="p-6 rounded-2xl bg-surface-highlight/30 border border-white/5">
                      <h3 className="text-sm font-bold text-text-secondary uppercase tracking-wider mb-3">Deskripsi Masalah</h3>
                      <p className="text-text-primary leading-relaxed">{detail.description}</p>
                    </div>

                    <div className="space-y-6">
                      <div className="flex items-center justify-between border-b border-white/5 pb-4">
                        <h3 className="font-bold text-lg text-text-primary">Percakapan</h3>
                        <span className="text-xs text-text-tertiary">{detail.comments?.length ?? 0} balasan</span>
                      </div>

                      {detail.comments?.length ? (
                        <div className="space-y-6">
                          {detail.comments.map((comment) => (
                            <div key={comment.id} className="flex gap-4 group">
                              <div className="w-10 h-10 rounded-full bg-gradient-to-br from-primary/20 to-primary/5 flex items-center justify-center text-primary flex-shrink-0 border border-primary/10">
                                <FiUser className="text-lg" />
                              </div>
                              <div className="flex-grow">
                                <div className="flex justify-between items-center mb-2">
                                  <span className="font-bold text-text-primary">{comment.user?.name ?? 'Pengguna'}</span>
                                  <span className="text-xs text-text-tertiary bg-surface-highlight/50 px-2 py-1 rounded-lg">
                                    {formatDate(comment.created_at)}
                                  </span>
                                </div>
                                <div className="p-4 rounded-2xl bg-surface-highlight/50 border border-white/5 text-text-secondary leading-relaxed group-hover:border-white/10 transition-colors">
                                  {comment.body}
                                </div>
                              </div>
                            </div>
                          ))}
                        </div>
                      ) : (
                        <div className="text-center py-8 border-2 border-dashed border-white/5 rounded-2xl">
                          <p className="text-text-tertiary">Belum ada balasan dari tim support.</p>
                        </div>
                      )}
                    </div>
                  </div>
                ) : (
                  <div className="text-center p-8 rounded-2xl bg-red-500/10 border border-red-500/20">
                    <FiAlertCircle className="text-3xl text-red-500 mx-auto mb-3" />
                    <p className="text-red-400 font-medium">Gagal memuat detail tiket.</p>
                  </div>
                )}
              </div>

              {/* Reply input could go here */}
            </motion.div>
          </div>
        )}
      </AnimatePresence>

      <AnimatePresence>
        {createOpen && (
          <div className="fixed inset-0 z-[2000] flex items-center justify-center p-4">
            <motion.div
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              exit={{ opacity: 0 }}
              className="absolute inset-0 bg-black/60 backdrop-blur-md"
              onClick={() => setCreateOpen(false)}
            />
            <motion.div
              initial={{ opacity: 0, scale: 0.95, y: 20 }}
              animate={{ opacity: 1, scale: 1, y: 0 }}
              exit={{ opacity: 0, scale: 0.95, y: 20 }}
              className="relative bg-surface/95 backdrop-blur-2xl border border-white/10 rounded-3xl w-full max-w-lg max-h-[90vh] overflow-y-auto p-6 md:p-8 shadow-2xl ring-1 ring-black/5 m-4"
            >
              <div className="flex justify-between items-start mb-8">
                <div>
                  <h2 className="text-2xl font-display font-bold text-text-primary">Buat Tiket Baru</h2>
                  <p className="text-text-secondary text-sm mt-1">Sampaikan kendala atau pertanyaan Anda.</p>
                </div>
                <button
                  onClick={() => setCreateOpen(false)}
                  className="p-2 hover:bg-white/10 rounded-full transition-colors text-text-secondary hover:text-text-primary"
                >
                  <FiX className="text-xl" />
                </button>
              </div>

              <div className="space-y-5">
                <div>
                  <label className="block text-sm font-bold text-text-secondary mb-2">Subjek</label>
                  <input
                    type="text"
                    value={createForm.subject}
                    onChange={(e) => setCreateForm((prev) => ({ ...prev, subject: e.target.value }))}
                    placeholder="Contoh: AC tidak dingin"
                    className="w-full bg-surface-highlight/50 border border-white/10 rounded-xl px-4 py-3 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/50 transition-all text-text-primary placeholder:text-text-tertiary"
                  />
                </div>

                <div>
                  <label className="block text-sm font-bold text-text-secondary mb-2">Deskripsi</label>
                  <textarea
                    rows={4}
                    value={createForm.description}
                    onChange={(e) => setCreateForm((prev) => ({ ...prev, description: e.target.value }))}
                    placeholder="Jelaskan detail masalahnya..."
                    className="w-full bg-surface-highlight/50 border border-white/10 rounded-xl px-4 py-3 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/50 transition-all resize-none text-text-primary placeholder:text-text-tertiary"
                  />
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label className="block text-sm font-bold text-text-secondary mb-2">Kategori</label>
                    <div className="relative">
                      <select
                        value={createForm.category}
                        onChange={(e) => setCreateForm((prev) => ({ ...prev, category: e.target.value }))}
                        className="w-full bg-surface-highlight/50 border border-white/10 rounded-xl px-4 py-3 focus:outline-none focus:border-primary appearance-none text-text-primary cursor-pointer"
                      >
                        <option value="technical">Teknis</option>
                        <option value="payment">Pembayaran</option>
                        <option value="content">Konten</option>
                        <option value="abuse">Pelanggaran</option>
                      </select>
                      <div className="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-text-tertiary">
                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2.5 4.5L6 8L9.5 4.5" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" /></svg>
                      </div>
                    </div>
                  </div>
                  <div>
                    <label className="block text-sm font-bold text-text-secondary mb-2">Prioritas</label>
                    <div className="relative">
                      <select
                        value={createForm.priority}
                        onChange={(e) => setCreateForm((prev) => ({ ...prev, priority: e.target.value }))}
                        className="w-full bg-surface-highlight/50 border border-white/10 rounded-xl px-4 py-3 focus:outline-none focus:border-primary appearance-none text-text-primary cursor-pointer"
                      >
                        <option value="low">Rendah</option>
                        <option value="medium">Sedang</option>
                        <option value="high">Tinggi</option>
                        <option value="urgent">Mendesak</option>
                      </select>
                      <div className="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-text-tertiary">
                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2.5 4.5L6 8L9.5 4.5" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" /></svg>
                      </div>
                    </div>
                  </div>
                </div>

                <div className="grid grid-cols-1 gap-4">
                  <div>
                    <label className="block text-sm font-bold text-text-secondary mb-2">Tujuan Tiket</label>
                    <div className="relative">
                      <select
                        value={createForm.target}
                        onChange={(e) => {
                          const target = e.target.value;
                          setCreateForm((prev) => ({
                            ...prev,
                            target,
                            property_id: target === 'owner' ? prev.property_id : '',
                          }));
                        }}
                        className="w-full bg-surface-highlight/50 border border-white/10 rounded-xl px-4 py-3 focus:outline-none focus:border-primary appearance-none text-text-primary cursor-pointer"
                      >
                        <option value="owner" disabled={!contracts.length}>Owner (Sesuai Kontrak)</option>
                        <option value="admin">Admin KostIn</option>
                      </select>
                      <div className="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-text-tertiary">
                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2.5 4.5L6 8L9.5 4.5" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" /></svg>
                      </div>
                    </div>
                    {!contracts.length && <p className="text-xs text-text-tertiary mt-2 flex items-center gap-1"><FiAlertCircle /> Tidak ada kontrak aktif, tiket akan dikirim ke Admin.</p>}
                  </div>

                  {createForm.target === 'owner' && (
                    <div>
                      <label className="block text-sm font-bold text-text-secondary mb-2">Pilih Properti</label>
                      <div className="relative">
                        <select
                          value={createForm.property_id}
                          onChange={(e) => setCreateForm((prev) => ({ ...prev, property_id: e.target.value }))}
                          className="w-full bg-surface-highlight/50 border border-white/10 rounded-xl px-4 py-3 focus:outline-none focus:border-primary appearance-none text-text-primary cursor-pointer"
                        >
                          <option value="">Pilih properti...</option>
                          {contracts.map((contract) => {
                            const property = contract.room?.room_type?.property;
                            const roomCode = contract.room?.room_code ?? '—';
                            return (
                              <option key={contract.id} value={property?.id ?? ''}>
                                {property?.name ?? 'Properti'} · Kamar {roomCode}
                              </option>
                            );
                          })}
                        </select>
                        <div className="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-text-tertiary">
                          <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2.5 4.5L6 8L9.5 4.5" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" /></svg>
                        </div>
                      </div>
                    </div>
                  )}
                </div>

                {createError && (
                  <div className="p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-500 text-sm flex items-center gap-3">
                    <FiAlertCircle className="text-xl flex-shrink-0" />
                    <span>{createError}</span>
                  </div>
                )}

                <div className="flex gap-3 pt-4">
                  <button
                    type="button"
                    className="btn ghost flex-1 justify-center hover:bg-white/5"
                    onClick={() => setCreateOpen(false)}
                  >
                    Batal
                  </button>
                  <button
                    type="button"
                    className="btn primary flex-1 justify-center shadow-lg shadow-primary/20 hover:shadow-primary/30"
                    onClick={submitTicket}
                    disabled={createSubmitting}
                  >
                    {createSubmitting ? 'Mengirim...' : 'Kirim Tiket'} <FiSend className="ml-2" />
                  </button>
                </div>
              </div>
            </motion.div>
          </div>
        )}
      </AnimatePresence>
    </div>
  );
};

export default TenantTickets;
