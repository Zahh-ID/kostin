import React, { useEffect, useMemo, useState } from 'react';
import { motion } from 'framer-motion';
import { currentUser, fetchOwnerDashboard } from '../../api/client.js';
import { FiTrendingUp, FiUsers, FiHome, FiDollarSign, FiActivity, FiAlertCircle, FiCheckCircle, FiClock } from 'react-icons/fi';
import SEO from '../../components/SEO.jsx';

const currency = new Intl.NumberFormat('id-ID', {
  style: 'currency',
  currency: 'IDR',
  maximumFractionDigits: 0,
});

const OwnerDashboard = () => {
  const [ownerName, setOwnerName] = useState('Owner');
  const [metrics, setMetrics] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    currentUser().then((user) => {
      if (user?.name) {
        setOwnerName(user.name);
      }
    }).catch(() => { });

    fetchOwnerDashboard()
      .then((data) => {
        setMetrics(data);
        setLoading(false);
      })
      .catch(() => {
        setMetrics(null);
        setLoading(false);
      });
  }, []);

  const revenueThisMonth = metrics?.revenue_this_month ?? 0;
  const registrationsThisMonth = metrics?.registrations_this_month ?? 0;
  const publishedProperties = metrics?.room_types ?? 0;

  const roomStatus = metrics?.rooms ?? { occupied: 0, available: 0, maintenance: 0, total: 0 };
  const occupancyPercent = roomStatus.total ? Math.round((roomStatus.occupied / roomStatus.total) * 100) : 0;

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
        title="Owner Dashboard - KostIn"
        description="Manage your properties, track revenue, and handle tenant requests on your KostIn Owner Dashboard."
      />
      <div className="container">
        <motion.div
          initial={{ opacity: 0, y: -20 }}
          animate={{ opacity: 1, y: 0 }}
          className="mb-12"
        >
          <div className="flex items-center gap-3 mb-2">
            <span className="px-3 py-1 rounded-full bg-primary/20 text-primary text-xs font-bold border border-primary/20">
              OWNER DASHBOARD
            </span>
            <span className="text-text-secondary text-sm">
              {new Date().toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}
            </span>
          </div>
          <h1 className="text-4xl font-display font-bold mb-2">
            Selamat Datang, <span className="text-primary">{ownerName}</span>
          </h1>
          <p className="text-text-secondary text-lg max-w-2xl">
            Pantau performa bisnis kost Anda, kelola properti, dan tinjau pembayaran dalam satu tempat.
          </p>
        </motion.div>

        <motion.div
          variants={containerVariants}
          initial="hidden"
          animate="visible"
          className="space-y-8"
        >
          {/* Key Metrics */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <StatCard
              icon={<FiDollarSign className="text-2xl" />}
              label="Pendapatan Bulan Ini"
              value={currency.format(revenueThisMonth)}
              trend="+12% dari bulan lalu"
              color="text-green-400"
              bg="bg-green-400/10"
              border="border-green-400/20"
            />
            <StatCard
              icon={<FiHome className="text-2xl" />}
              label="Okupansi Kamar"
              value={`${occupancyPercent}%`}
              trend={`${roomStatus.occupied} dari ${roomStatus.total} kamar terisi`}
              color="text-blue-400"
              bg="bg-blue-400/10"
              border="border-blue-400/20"
            />
            <StatCard
              icon={<FiUsers className="text-2xl" />}
              label="Penyewa Baru"
              value={registrationsThisMonth}
              trend="Bulan ini"
              color="text-purple-400"
              bg="bg-purple-400/10"
              border="border-purple-400/20"
            />
            <StatCard
              icon={<FiActivity className="text-2xl" />}
              label="Properti Aktif"
              value={publishedProperties}
              trend="Siap disewa"
              color="text-orange-400"
              bg="bg-orange-400/10"
              border="border-orange-400/20"
            />
          </div>

          <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {/* Main Content Area */}
            <div className="lg:col-span-2 space-y-8">
              {/* Income Trend Chart Placeholder */}
              <motion.div variants={itemVariants} className="card p-6">
                <div className="flex justify-between items-center mb-6">
                  <div>
                    <h3 className="text-xl font-bold font-display">Tren Pendapatan</h3>
                    <p className="text-sm text-text-secondary">Performa pendapatan 6 bulan terakhir</p>
                  </div>
                  <select className="bg-surface-highlight border border-border rounded-lg px-3 py-1 text-sm focus:outline-none focus:border-primary">
                    <option>6 Bulan Terakhir</option>
                    <option>Tahun Ini</option>
                  </select>
                </div>

                <div className="h-64 flex items-end justify-between gap-2 px-2">
                  {(() => {
                    const trendData = metrics?.revenue_trend || [];
                    const maxVal = Math.max(...trendData.map(d => d.value), 1);

                    if (trendData.length === 0) {
                      return (
                        <div className="w-full h-full flex items-center justify-center text-text-tertiary">
                          Belum ada data pendapatan
                        </div>
                      );
                    }

                    return trendData.map((val, idx) => (
                      <div key={idx} className="w-full flex flex-col items-center gap-2 group">
                        <div className="w-full bg-surface-highlight rounded-t-lg relative h-full overflow-hidden flex items-end">
                          <motion.div
                            initial={{ height: 0 }}
                            animate={{ height: `${(val.value / maxVal) * 100}%` }}
                            transition={{ duration: 1, delay: idx * 0.1 }}
                            className="w-full bg-gradient-to-t from-primary/20 to-primary/60 group-hover:from-primary/40 group-hover:to-primary/80 transition-colors relative"
                          >
                            <div className="absolute -top-6 left-1/2 -translate-x-1/2 opacity-0 group-hover:opacity-100 transition-opacity text-xs font-bold bg-surface border border-border px-2 py-1 rounded shadow-lg whitespace-nowrap z-10">
                              {currency.format(val.value)}
                            </div>
                          </motion.div>
                        </div>
                        <span className="text-xs text-text-secondary">
                          {val.label}
                        </span>
                      </div>
                    ));
                  })()}
                </div>
              </motion.div>

              {/* Recent Activities / Tasks */}
              <motion.div variants={itemVariants} className="card p-6">
                <div className="flex justify-between items-center mb-6">
                  <div>
                    <h3 className="text-xl font-bold font-display">Aktivitas & Tugas</h3>
                    <p className="text-sm text-text-secondary">Hal yang perlu perhatian Anda</p>
                  </div>
                  <a href="/owner/tickets" className="text-primary text-sm hover:underline">Lihat Semua</a>
                </div>

                <div className="space-y-4">
                  <TaskItem
                    icon={<FiAlertCircle />}
                    title="Konfirmasi Pembayaran Manual"
                    desc="Tagihan #INV-2023001 menunggu konfirmasi"
                    time="2 jam yang lalu"
                    type="warning"
                  />
                  <TaskItem
                    icon={<FiClock />}
                    title="Kontrak Akan Berakhir"
                    desc="Kontrak penyewa Budi Santoso berakhir dalam 7 hari"
                    time="5 jam yang lalu"
                    type="info"
                  />
                  <TaskItem
                    icon={<FiCheckCircle />}
                    title="Properti Disetujui"
                    desc="Kost Mawar Indah telah disetujui admin"
                    time="1 hari yang lalu"
                    type="success"
                  />
                </div>
              </motion.div>
            </div>

            {/* Sidebar / Secondary Content */}
            <div className="space-y-8">
              {/* Room Status Donut */}
              <motion.div variants={itemVariants} className="card p-6">
                <h3 className="text-xl font-bold font-display mb-6">Status Kamar</h3>
                <div className="relative w-48 h-48 mx-auto mb-6">
                  <svg viewBox="0 0 36 36" className="w-full h-full transform -rotate-90">
                    {/* Background Circle */}
                    <path
                      d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                      fill="none"
                      stroke="#333"
                      strokeWidth="3"
                    />
                    {/* Occupied Segment */}
                    <motion.path
                      initial={{ pathLength: 0 }}
                      whileInView={{ pathLength: occupancyPercent / 100 }}
                      transition={{ duration: 1.5, ease: "easeOut" }}
                      d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                      fill="none"
                      stroke="var(--primary)"
                      strokeWidth="3"
                      strokeDasharray="100, 100"
                    />
                  </svg>
                  <div className="absolute inset-0 flex flex-col items-center justify-center">
                    <span className="text-3xl font-bold text-white">{occupancyPercent}%</span>
                    <span className="text-xs text-text-secondary">Terisi</span>
                  </div>
                </div>

                <div className="space-y-3">
                  <div className="flex justify-between items-center text-sm">
                    <div className="flex items-center gap-2">
                      <div className="w-3 h-3 rounded-full bg-primary" />
                      <span>Terisi</span>
                    </div>
                    <span className="font-bold">{roomStatus.occupied}</span>
                  </div>
                  <div className="flex justify-between items-center text-sm">
                    <div className="flex items-center gap-2">
                      <div className="w-3 h-3 rounded-full bg-green-500" />
                      <span>Kosong</span>
                    </div>
                    <span className="font-bold">{roomStatus.available}</span>
                  </div>
                  <div className="flex justify-between items-center text-sm">
                    <div className="flex items-center gap-2">
                      <div className="w-3 h-3 rounded-full bg-yellow-500" />
                      <span>Maintenance</span>
                    </div>
                    <span className="font-bold">{roomStatus.maintenance}</span>
                  </div>
                </div>

                <div className="mt-6 pt-6 border-t border-border">
                  <a href="/owner/rooms" className="btn ghost w-full justify-center">Kelola Kamar</a>
                </div>
              </motion.div>

              {/* Quick Actions */}
              <motion.div variants={itemVariants} className="card p-6">
                <h3 className="text-xl font-bold font-display mb-4">Aksi Cepat</h3>
                <div className="grid grid-cols-2 gap-3">
                  <a href="/owner/properties/create" className="p-3 rounded-xl bg-surface-highlight hover:bg-surface-highlight/80 border border-border transition-colors flex flex-col items-center text-center gap-2">
                    <FiHome className="text-xl text-primary" />
                    <span className="text-xs font-medium">Tambah Properti</span>
                  </a>
                  <a href="/owner/manual-payments" className="p-3 rounded-xl bg-surface-highlight hover:bg-surface-highlight/80 border border-border transition-colors flex flex-col items-center text-center gap-2">
                    <FiDollarSign className="text-xl text-green-400" />
                    <span className="text-xs font-medium">Cek Pembayaran</span>
                  </a>
                  <a href="/owner/tickets" className="p-3 rounded-xl bg-surface-highlight hover:bg-surface-highlight/80 border border-border transition-colors flex flex-col items-center text-center gap-2">
                    <FiAlertCircle className="text-xl text-yellow-400" />
                    <span className="text-xs font-medium">Tiket Masuk</span>
                  </a>
                  <a href="/owner/contracts" className="p-3 rounded-xl bg-surface-highlight hover:bg-surface-highlight/80 border border-border transition-colors flex flex-col items-center text-center gap-2">
                    <FiTrendingUp className="text-xl text-blue-400" />
                    <span className="text-xs font-medium">Laporan</span>
                  </a>
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

const TaskItem = ({ icon, title, desc, time, type }) => {
  const colors = {
    warning: 'text-yellow-400 bg-yellow-400/10',
    info: 'text-blue-400 bg-blue-400/10',
    success: 'text-green-400 bg-green-400/10',
    danger: 'text-red-400 bg-red-400/10',
  };

  return (
    <div className="flex gap-4 p-3 rounded-xl hover:bg-surface-highlight transition-colors cursor-pointer">
      <div className={`w-10 h-10 rounded-full flex-shrink-0 flex items-center justify-center ${colors[type]}`}>
        {icon}
      </div>
      <div className="flex-grow">
        <div className="flex justify-between items-start">
          <h4 className="font-medium text-sm">{title}</h4>
          <span className="text-xs text-text-tertiary">{time}</span>
        </div>
        <p className="text-xs text-text-secondary mt-1">{desc}</p>
      </div>
    </div>
  );
};

export default OwnerDashboard;
