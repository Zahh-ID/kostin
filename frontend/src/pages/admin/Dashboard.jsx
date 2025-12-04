import React, { useEffect, useState } from 'react';
import { motion } from 'framer-motion';
import { currentUser, fetchAdminDashboard, fetchAdminModerations, fetchAdminTickets } from '../../api/client.js';
import { FiTrendingUp, FiUsers, FiHome, FiDollarSign, FiActivity, FiAlertCircle, FiCheckCircle, FiClock, FiShield, FiFileText } from 'react-icons/fi';
import SEO from '../../components/SEO.jsx';

const currency = new Intl.NumberFormat('id-ID', {
  style: 'currency',
  currency: 'IDR',
  maximumFractionDigits: 0,
});

const AdminDashboard = () => {
  const [adminName, setAdminName] = useState('Admin');
  const [metrics, setMetrics] = useState(null);
  const [loading, setLoading] = useState(true);
  const [recentModerations, setRecentModerations] = useState([]);
  const [recentTickets, setRecentTickets] = useState([]);

  useEffect(() => {
    currentUser().then((user) => {
      if (user?.name) {
        setAdminName(user.name);
      }
    }).catch(() => { });

    Promise.all([
      fetchAdminDashboard(),
      fetchAdminModerations(),
      fetchAdminTickets()
    ]).then(([dashboardData, moderationsData, ticketsData]) => {
      setMetrics(dashboardData);
      setRecentModerations(moderationsData.data || moderationsData || []);
      setRecentTickets(ticketsData.data || ticketsData || []);
      setLoading(false);
    }).catch((err) => {
      console.error(err);
      setLoading(false);
    });
  }, []);

  const revenueThisMonth = metrics?.revenue_this_month ?? 0;
  const registrationsThisMonth = metrics?.registrations_this_month ?? 0;
  const pendingModerations = metrics?.pending_moderations ?? 0;
  const ticketsOpen = metrics?.tickets_open ?? 0;

  const containerVariants = {
    hidden: { opacity: 0 },
    visible: {
      opacity: 1,
      transition: {
        staggerChildren: 0.1
      }
    }
  };

  const itemVariants = {
    hidden: { opacity: 0, y: 20 },
    visible: { opacity: 1, y: 0 }
  };

  return (
    <div className="page pt-32 pb-20">
      <SEO
        title="Admin Dashboard - KostIn"
        description="KostIn Admin Dashboard for platform moderation, user management, and system monitoring."
      />
      <div className="container">
        <motion.div
          initial={{ opacity: 0, y: -20 }}
          animate={{ opacity: 1, y: 0 }}
          className="mb-12"
        >
          <div className="flex items-center gap-3 mb-2">
            <span className="px-3 py-1 rounded-full bg-red-500/20 text-red-500 text-xs font-bold border border-red-500/20">
              ADMIN DASHBOARD
            </span>
            <span className="text-text-secondary text-sm">
              {new Date().toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}
            </span>
          </div>
          <h1 className="text-4xl font-display font-bold mb-2">
            Halo, <span className="text-primary">{adminName}</span>
          </h1>
          <p className="text-text-secondary text-lg max-w-2xl">
            Pantau seluruh aktivitas platform, moderasi properti, dan kelola tiket bantuan.
          </p>
        </motion.div>

        <motion.div
          variants={containerVariants}
          initial="hidden"
          animate="visible"
          className="space-y-8"
        >
          {/* Key Metrics */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <StatCard
              icon={<FiUsers className="text-2xl" />}
              label="Registrasi Baru"
              value={registrationsThisMonth}
              trend="User baru bulan ini"
              color="text-blue-400"
              bg="bg-blue-400/10"
              border="border-blue-400/20"
            />
            <StatCard
              icon={<FiHome className="text-2xl" />}
              label="Moderasi Pending"
              value={pendingModerations}
              trend="Properti menunggu review"
              color="text-yellow-400"
              bg="bg-yellow-400/10"
              border="border-yellow-400/20"
            />
            <StatCard
              icon={<FiAlertCircle className="text-2xl" />}
              label="Tiket Terbuka"
              value={ticketsOpen}
              trend="Perlu penanganan"
              color="text-red-400"
              bg="bg-red-400/10"
              border="border-red-400/20"
            />
          </div>

          <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {/* Main Content Area */}
            <div className="lg:col-span-2 space-y-8">
              {/* Recent Moderations */}
              <motion.div variants={itemVariants} className="card p-6">
                <div className="flex justify-between items-center mb-6">
                  <div>
                    <h3 className="text-xl font-bold font-display">Antrian Moderasi</h3>
                    <p className="text-sm text-text-secondary">Properti terbaru yang perlu direview</p>
                  </div>
                  <a href="/admin/moderations" className="text-primary text-sm hover:underline">Lihat Semua</a>
                </div>

                <div className="space-y-4">
                  {recentModerations.slice(0, 3).map((mod) => (
                    <TaskItem
                      key={mod.id}
                      icon={<FiHome />}
                      title={mod.name}
                      desc={`Owner: ${mod.owner?.name} • ${mod.address}`}
                      time={new Date(mod.created_at).toLocaleDateString()}
                      type={mod.status === 'pending' ? 'warning' : mod.status === 'approved' ? 'success' : 'danger'}
                      statusLabel={mod.status}
                    />
                  ))}
                  {recentModerations.length === 0 && (
                    <p className="text-text-secondary text-center py-4">Tidak ada antrian moderasi.</p>
                  )}
                </div>
              </motion.div>
            </div>

            {/* Sidebar / Secondary Content */}
            <div className="space-y-8">
              {/* Quick Actions */}
              <motion.div variants={itemVariants} className="card p-6">
                <h3 className="text-xl font-bold font-display mb-4">Aksi Cepat</h3>
                <div className="grid grid-cols-2 gap-3">
                  <a href="/admin/moderations" className="p-3 rounded-xl bg-surface-highlight hover:bg-surface-highlight/80 border border-border transition-colors flex flex-col items-center text-center gap-2">
                    <FiCheckCircle className="text-xl text-green-400" />
                    <span className="text-xs font-medium">Moderasi</span>
                  </a>
                  <a href="/admin/tickets" className="p-3 rounded-xl bg-surface-highlight hover:bg-surface-highlight/80 border border-border transition-colors flex flex-col items-center text-center gap-2">
                    <FiAlertCircle className="text-xl text-yellow-400" />
                    <span className="text-xs font-medium">Tiket</span>
                  </a>
                  <a href="/admin/users" className="p-3 rounded-xl bg-surface-highlight hover:bg-surface-highlight/80 border border-border transition-colors flex flex-col items-center text-center gap-2">
                    <FiUsers className="text-xl text-blue-400" />
                    <span className="text-xs font-medium">Users</span>
                  </a>
                  <a href="/admin/settings" className="p-3 rounded-xl bg-surface-highlight hover:bg-surface-highlight/80 border border-border transition-colors flex flex-col items-center text-center gap-2">
                    <FiActivity className="text-xl text-purple-400" />
                    <span className="text-xs font-medium">System</span>
                  </a>
                </div>
              </motion.div>

              {/* Recent Tickets */}
              <motion.div variants={itemVariants} className="card p-6">
                <div className="flex justify-between items-center mb-4">
                  <h3 className="text-xl font-bold font-display">Tiket Terbaru</h3>
                  <a href="/admin/tickets" className="text-xs text-primary hover:underline">Lihat Semua</a>
                </div>
                <div className="space-y-3">
                  {recentTickets.slice(0, 4).map((ticket) => (
                    <div key={ticket.id} className="p-3 rounded-xl bg-surface-highlight border border-border">
                      <div className="flex justify-between items-start mb-1">
                        <span className={`px-2 py-0.5 rounded text-[10px] font-bold uppercase border ${ticket.status === 'open' ? 'text-red-400 bg-red-400/10 border-red-400/20' :
                          ticket.status === 'in_progress' ? 'text-blue-400 bg-blue-400/10 border-blue-400/20' :
                            'text-green-400 bg-green-400/10 border-green-400/20'
                          }`}>
                          {ticket.status}
                        </span>
                        <span className="text-[10px] text-text-tertiary">{new Date(ticket.created_at).toLocaleDateString()}</span>
                      </div>
                      <h4 className="font-bold text-sm mb-1 line-clamp-1">{ticket.subject}</h4>
                      <p className="text-xs text-text-secondary line-clamp-2">{ticket.message}</p>
                    </div>
                  ))}
                  {recentTickets.length === 0 && (
                    <p className="text-text-secondary text-xs text-center">Belum ada tiket.</p>
                  )}
                </div>
              </motion.div>
            </div>
          </div>
        </motion.div>
      </div>
    </div>
  );
};

const StatCard = ({ icon, label, value, trend, color, bg, border }) => (
  <motion.div
    variants={{ hidden: { opacity: 0, y: 20 }, visible: { opacity: 1, y: 0 } }}
    className={`card p-5 border ${border} relative overflow-hidden group`}
  >
    <div className={`absolute top-0 right-0 p-24 rounded-full ${bg} blur-3xl -mr-10 -mt-10 transition-all group-hover:scale-110`} />

    <div className="relative z-10">
      <div className={`w-10 h-10 rounded-lg ${bg} ${color} flex items-center justify-center mb-4`}>
        {icon}
      </div>
      <div className="text-text-secondary text-sm mb-1">{label}</div>
      <div className="text-2xl font-bold font-display mb-2">{value}</div>
      <div className="text-xs text-text-tertiary flex items-center gap-1">
        <FiTrendingUp className={color} />
        <span>{trend}</span>
      </div>
    </div>
  </motion.div>
);

const TaskItem = ({ icon, title, desc, time, type, statusLabel }) => {
  const colors = {
    warning: 'text-yellow-400 bg-yellow-400/10',
    info: 'text-blue-400 bg-blue-400/10',
    success: 'text-green-400 bg-green-400/10',
    danger: 'text-red-400 bg-red-400/10',
  };

  return (
    <div className="flex gap-4 p-3 rounded-xl hover:bg-surface-highlight transition-colors cursor-pointer border border-transparent hover:border-border">
      <div className={`w-10 h-10 rounded-full flex-shrink-0 flex items-center justify-center ${colors[type]}`}>
        {icon}
      </div>
      <div className="flex-grow">
        <div className="flex justify-between items-start">
          <h4 className="font-medium text-sm">{title}</h4>
          <span className="text-xs text-text-tertiary">{time}</span>
        </div>
        <p className="text-xs text-text-secondary mt-1 mb-1">{desc}</p>
        {statusLabel && (
          <span className={`text-[10px] font-bold uppercase px-2 py-0.5 rounded ${colors[type]}`}>
            {statusLabel}
          </span>
        )}
      </div>
    </div>
  );
};

export default AdminDashboard;
