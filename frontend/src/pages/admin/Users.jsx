import React, { useEffect, useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { fetchAdminUsers, suspendAdminUser, activateAdminUser } from '../../api/client.js';
import { FiSearch, FiFilter, FiUser, FiShield, FiHome, FiMail, FiCalendar, FiSlash, FiCheckCircle, FiAlertTriangle, FiX } from 'react-icons/fi';

const AdminUsers = () => {
  const [users, setUsers] = useState([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState('');
  const [roleFilter, setRoleFilter] = useState('all'); // all, admin, owner, tenant
  const [actionLoading, setActionLoading] = useState(null); // userId being processed

  // Modal State
  const [confirmModal, setConfirmModal] = useState({
    open: false,
    type: null, // 'suspend' | 'activate'
    user: null
  });

  useEffect(() => {
    loadData();
  }, []);

  const loadData = async () => {
    setLoading(true);
    try {
      const res = await fetchAdminUsers();
      setUsers(res.data ?? res ?? []);
    } catch (error) {
      console.error('Failed to load users:', error);
    } finally {
      setLoading(false);
    }
  };

  const openConfirmModal = (type, user) => {
    setConfirmModal({ open: true, type, user });
  };

  const closeConfirmModal = () => {
    setConfirmModal({ open: false, type: null, user: null });
  };

  const handleConfirmAction = async () => {
    const { type, user } = confirmModal;
    if (!user) return;

    setActionLoading(user.id);
    closeConfirmModal();

    try {
      if (type === 'suspend') {
        await suspendAdminUser(user.id);
      } else {
        await activateAdminUser(user.id);
      }
      await loadData();
    } catch (error) {
      alert(`Gagal ${type === 'suspend' ? 'mensuspend' : 'meng-unsuspend'} user: ` + (error.response?.data?.message || error.message));
    } finally {
      setActionLoading(null);
    }
  };

  const filteredUsers = users.filter(user => {
    const matchesSearch = user.name.toLowerCase().includes(search.toLowerCase()) ||
      user.email.toLowerCase().includes(search.toLowerCase());
    const matchesRole = roleFilter === 'all' || user.role === roleFilter;
    return matchesSearch && matchesRole;
  });

  return (
    <div className="page pt-32 pb-20">
      <div className="container">
        <motion.div
          initial={{ opacity: 0, y: -20 }}
          animate={{ opacity: 1, y: 0 }}
          className="mb-8"
        >
          <h1 className="text-4xl font-display font-bold mb-2">Daftar Pengguna</h1>
          <p className="text-text-secondary text-lg">
            Kelola dan pantau seluruh pengguna terdaftar di platform.
          </p>
        </motion.div>

        <div className="card p-6 mb-8">
          <div className="flex flex-col md:flex-row gap-4 justify-between items-center">
            <div className="relative w-full md:w-96">
              <FiSearch className="absolute left-4 top-1/2 -translate-y-1/2 text-text-tertiary" />
              <input
                type="text"
                value={search}
                onChange={(e) => setSearch(e.target.value)}
                placeholder="Cari nama atau email..."
                className="w-full bg-surface-highlight border border-border rounded-xl pl-10 pr-4 py-3 focus:border-primary outline-none transition-colors"
              />
            </div>

            <div className="flex gap-2 w-full md:w-auto overflow-x-auto pb-2 md:pb-0">
              {['all', 'admin', 'owner', 'tenant'].map((role) => (
                <button
                  key={role}
                  onClick={() => setRoleFilter(role)}
                  className={`px-4 py-2 rounded-xl text-sm font-bold border transition-all whitespace-nowrap ${roleFilter === role
                    ? 'bg-primary text-white border-primary shadow-lg shadow-primary/20'
                    : 'bg-surface-highlight text-text-secondary border-border hover:border-primary/50'
                    }`}
                >
                  {role === 'all' ? 'Semua Role' : role.charAt(0).toUpperCase() + role.slice(1)}
                </button>
              ))}
            </div>
          </div>
        </div>

        <div className="card overflow-hidden">
          <div className="overflow-x-auto">
            <table className="w-full">
              <thead>
                <tr className="border-b border-border bg-surface-highlight/50">
                  <th className="text-left py-4 px-6 text-xs font-bold text-text-secondary uppercase tracking-wider">User</th>
                  <th className="text-left py-4 px-6 text-xs font-bold text-text-secondary uppercase tracking-wider">Role</th>
                  <th className="text-left py-4 px-6 text-xs font-bold text-text-secondary uppercase tracking-wider">Email</th>
                  <th className="text-left py-4 px-6 text-xs font-bold text-text-secondary uppercase tracking-wider">Status</th>
                  <th className="text-right py-4 px-6 text-xs font-bold text-text-secondary uppercase tracking-wider">Aksi</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-border">
                {loading ? (
                  <tr>
                    <td colSpan="5" className="py-12 text-center text-text-secondary">Memuat data...</td>
                  </tr>
                ) : filteredUsers.length === 0 ? (
                  <tr>
                    <td colSpan="5" className="py-12 text-center text-text-secondary">Tidak ada pengguna ditemukan.</td>
                  </tr>
                ) : (
                  filteredUsers.map((user) => (
                    <motion.tr
                      key={user.id}
                      initial={{ opacity: 0 }}
                      animate={{ opacity: 1 }}
                      className={`group hover:bg-surface-highlight transition-colors ${user.suspended_at ? 'opacity-75 bg-red-500/5' : ''}`}
                    >
                      <td className="py-4 px-6">
                        <div className="flex items-center gap-3">
                          <div className={`w-10 h-10 rounded-full flex items-center justify-center font-bold ${user.suspended_at ? 'bg-red-500/20 text-red-500' : 'bg-primary/10 text-primary'
                            }`}>
                            {user.name.charAt(0).toUpperCase()}
                          </div>
                          <div>
                            <span className="font-bold block">{user.name}</span>
                            <span className="text-xs text-text-tertiary">ID: {user.id}</span>
                          </div>
                        </div>
                      </td>
                      <td className="py-4 px-6">
                        <RoleBadge role={user.role} />
                      </td>
                      <td className="py-4 px-6 text-text-secondary text-sm">
                        <div className="flex items-center gap-2">
                          <FiMail className="text-text-tertiary" />
                          {user.email}
                        </div>
                      </td>
                      <td className="py-4 px-6">
                        {user.suspended_at ? (
                          <span className="px-2 py-1 rounded text-xs font-bold bg-red-500/10 text-red-500 border border-red-500/20">
                            Suspended
                          </span>
                        ) : (
                          <span className="px-2 py-1 rounded text-xs font-bold bg-green-500/10 text-green-500 border border-green-500/20">
                            Active
                          </span>
                        )}
                      </td>
                      <td className="py-4 px-6 text-right">
                        {user.role !== 'admin' && (
                          <button
                            onClick={() => openConfirmModal(user.suspended_at ? 'activate' : 'suspend', user)}
                            disabled={actionLoading === user.id}
                            className={`btn btn-sm ${user.suspended_at
                              ? 'bg-green-500 text-white hover:bg-green-600 border-transparent shadow-lg shadow-green-500/20'
                              : 'bg-red-500 text-white hover:bg-red-600 border-transparent shadow-lg shadow-red-500/20'
                              }`}
                            title={user.suspended_at ? "Unsuspend User" : "Suspend User"}
                          >
                            {actionLoading === user.id ? (
                              <span className="text-xs">Processing...</span>
                            ) : user.suspended_at ? (
                              <><FiCheckCircle className="mr-1" /> Unsuspend</>
                            ) : (
                              <><FiSlash className="mr-1" /> Suspend</>
                            )}
                          </button>
                        )}
                      </td>
                    </motion.tr>
                  ))
                )}
              </tbody>
            </table>
          </div>
        </div>

        {/* Confirmation Modal */}
        <AnimatePresence>
          {confirmModal.open && (
            <div className="fixed inset-0 z-[2000] flex items-center justify-center p-4">
              <motion.div
                initial={{ opacity: 0 }}
                animate={{ opacity: 1 }}
                exit={{ opacity: 0 }}
                onClick={closeConfirmModal}
                className="absolute inset-0 bg-black/80 backdrop-blur-sm"
              />
              <motion.div
                initial={{ opacity: 0, scale: 0.95 }}
                animate={{ opacity: 1, scale: 1 }}
                exit={{ opacity: 0, scale: 0.95 }}
                className="relative bg-surface border border-border rounded-2xl w-full max-w-md p-6 shadow-2xl"
              >
                <button onClick={closeConfirmModal} className="absolute top-4 right-4 text-text-tertiary hover:text-text-primary transition-colors">
                  <FiX className="text-2xl" />
                </button>
                <div className={`w-12 h-12 rounded-full flex items-center justify-center mb-4 ${confirmModal.type === 'suspend' ? 'bg-red-500/10 text-red-500' : 'bg-green-500/10 text-green-500'
                  }`}>
                  {confirmModal.type === 'suspend' ? <FiAlertTriangle className="text-2xl" /> : <FiCheckCircle className="text-2xl" />}
                </div>

                <h3 className="text-xl font-bold font-display mb-2">
                  {confirmModal.type === 'suspend' ? 'Suspend User?' : 'Unsuspend User?'}
                </h3>

                <p className="text-text-secondary mb-6">
                  {confirmModal.type === 'suspend'
                    ? `Apakah Anda yakin ingin mensuspend user ${confirmModal.user?.name}? User tidak akan bisa login ke aplikasi.`
                    : `Apakah Anda yakin ingin meng-unsuspend user ${confirmModal.user?.name}? User akan dapat login kembali.`
                  }
                </p>

                <div className="flex gap-3">
                  <button onClick={closeConfirmModal} className="btn ghost flex-1 justify-center">
                    Batal
                  </button>
                  <button
                    onClick={handleConfirmAction}
                    className={`btn flex-1 justify-center ${confirmModal.type === 'suspend' ? 'bg-red-500 hover:bg-red-600 text-white' : 'bg-green-500 hover:bg-green-600 text-white'
                      }`}
                  >
                    {confirmModal.type === 'suspend' ? 'Ya, Suspend' : 'Ya, Unsuspend'}
                  </button>
                </div>
              </motion.div>
            </div>
          )}
        </AnimatePresence>
      </div>
    </div>
  );
};

const RoleBadge = ({ role }) => {
  const styles = {
    admin: 'bg-red-500/10 text-red-500 border-red-500/20',
    owner: 'bg-blue-500/10 text-blue-500 border-blue-500/20',
    tenant: 'bg-green-500/10 text-green-500 border-green-500/20',
  };

  const icons = {
    admin: <FiShield className="mr-1" />,
    owner: <FiHome className="mr-1" />,
    tenant: <FiUser className="mr-1" />,
  };

  return (
    <span className={`inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold border ${styles[role] || styles.tenant}`}>
      {icons[role]}
      {role.charAt(0).toUpperCase() + role.slice(1)}
    </span>
  );
};

export default AdminUsers;
