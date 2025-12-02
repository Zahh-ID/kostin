import React, { useEffect, useState } from 'react';
import { motion } from 'framer-motion';
import { FiCreditCard, FiFileText, FiMessageSquare, FiHeart, FiSearch, FiActivity, FiArrowRight, FiHome, FiClock, FiAlertCircle } from 'react-icons/fi';
import { currentUser, fetchProperties, fetchTenantOverview, fetchTenantTickets, fetchTenantWishlist } from '../../api/client.js';

const TenantDashboard = () => {
  const [overview, setOverview] = useState(null);
  const [properties, setProperties] = useState([]);
  const [tickets, setTickets] = useState([]);
  const [userName, setUserName] = useState('Tenant');

  useEffect(() => {
    currentUser().then((user) => {
      if (user?.name) setUserName(user.name.split(' ')[0]);
    }).catch(() => { });

    fetchTenantOverview().then(setOverview).catch(() => setOverview(null));
    fetchProperties().then((data) => setProperties(data.slice(0, 1))).catch(() => setProperties([]));
    fetchTenantTickets(3).then(setTickets).catch(() => setTickets([]));
  }, []);

  const invoicePending = (overview?.invoices?.unpaid ?? 0) + (overview?.invoices?.overdue ?? 0) + (overview?.invoices?.pending_verification ?? 0);

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
    <div className="page min-h-screen bg-bg relative overflow-x-hidden pt-28 pb-20">
      {/* Immersive Background */}
      <div className="fixed inset-0 pointer-events-none z-0">
        <div className="absolute top-[-20%] right-[-10%] w-[800px] h-[800px] bg-primary/5 rounded-full blur-[120px] opacity-50" />
        <div className="absolute bottom-[-20%] left-[-10%] w-[600px] h-[600px] bg-secondary/5 rounded-full blur-[120px] opacity-50" />
        <div className="absolute inset-0 bg-[url('/noise.svg')] opacity-[0.02] mix-blend-overlay" />
      </div>

      <div className="container relative z-10 max-w-7xl mx-auto px-6">

        {/* Header Section */}
        <motion.div
          initial={{ opacity: 0, y: -20 }}
          animate={{ opacity: 1, y: 0 }}
          className="flex flex-col md:flex-row justify-between items-end gap-8 mb-16"
        >
          <div>
            <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-surface border border-white/10 text-text-secondary text-xs font-medium mb-6 backdrop-blur-sm">
              <span className="w-1.5 h-1.5 rounded-full bg-primary animate-pulse" />
              Tenant Dashboard
            </div>
            <h1 className="text-5xl md:text-7xl font-display font-bold tracking-tight mb-4">
              Hello, <span className="text-transparent bg-clip-text bg-gradient-to-r from-primary via-primary-dim to-secondary">
                {userName}
              </span>
            </h1>
            <p className="text-text-secondary text-lg max-w-xl leading-relaxed">
              Welcome back to your personal space. Manage your stay, track payments, and discover new opportunities.
            </p>
          </div>

          <div className="flex gap-4">
            <a href="/tenant/search" className="group flex items-center gap-3 px-6 py-4 rounded-2xl bg-surface border border-white/10 hover:border-white/20 hover:bg-white/5 transition-all duration-300">
              <FiSearch className="text-xl text-text-secondary group-hover:text-white transition-colors" />
              <span className="font-medium text-text-secondary group-hover:text-white transition-colors">Find Kost</span>
            </a>
            <a href="/tenant/invoices" className="group flex items-center gap-3 px-6 py-4 rounded-2xl bg-primary text-black font-bold shadow-[0_0_20px_rgba(204,255,0,0.2)] hover:shadow-[0_0_30px_rgba(204,255,0,0.4)] hover:-translate-y-1 transition-all duration-300">
              <FiCreditCard className="text-xl" />
              <span>Pay Bills</span>
            </a>
          </div>
        </motion.div>

        {/* Bento Grid Layout */}
        <motion.div
          variants={containerVariants}
          initial="hidden"
          animate="visible"
          className="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6"
        >

          {/* Stats Row */}
          <motion.div variants={itemVariants} className="md:col-span-1">
            <StatCard
              icon={<FiCreditCard />}
              label="Pending Bills"
              value={invoicePending}
              desc={invoicePending > 0 ? "Action Required" : "All Paid"}
              highlight={invoicePending > 0}
            />
          </motion.div>
          <motion.div variants={itemVariants} className="md:col-span-1">
            <StatCard
              icon={<FiFileText />}
              label="Active Contracts"
              value={overview?.contracts?.active ?? 0}
              desc="Current Stay"
            />
          </motion.div>
          <motion.div variants={itemVariants} className="md:col-span-1">
            <StatCard
              icon={<FiMessageSquare />}
              label="Open Tickets"
              value={overview?.tickets?.open ?? 0}
              desc="Support"
            />
          </motion.div>
          <motion.div variants={itemVariants} className="md:col-span-1">
            <StatCard
              icon={<FiHeart />}
              label="Wishlist"
              value={overview?.wishlist?.count ?? 0}
              desc="Saved Items"
            />
          </motion.div>

          {/* Main Content Area */}

          {/* Quick Actions - Large Block */}
          <motion.div variants={itemVariants} className="md:col-span-2 lg:col-span-2 row-span-1">
            <div className="h-full p-8 rounded-[2rem] bg-surface/50 backdrop-blur-xl border border-white/5 flex flex-col justify-between group hover:border-white/10 transition-colors">
              <div>
                <h3 className="text-2xl font-display font-bold mb-2">Quick Actions</h3>
                <p className="text-text-secondary mb-8">Fast track to your most used features.</p>
              </div>
              <div className="grid grid-cols-2 sm:grid-cols-4 gap-4">
                {quickActions.map((action, index) => (
                  <QuickActionTile key={index} action={action} />
                ))}
              </div>
            </div>
          </motion.div>

          {/* Featured Property - Tall Block */}
          <motion.div variants={itemVariants} className="md:col-span-1 lg:col-span-2 row-span-2">
            <div className="h-full rounded-[2rem] overflow-hidden relative group border border-white/5 bg-surface">
              {properties.length > 0 ? (
                <>
                  <img
                    src={properties[0]?.photos?.[0] || 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1000&q=80'}
                    alt="Featured"
                    className="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                  />
                  <div className="absolute inset-0 bg-gradient-to-t from-bg via-bg/50 to-transparent opacity-90" />
                  <div className="absolute inset-0 p-8 flex flex-col justify-end">
                    <div className="mb-auto">
                      <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/20 backdrop-blur-md border border-primary/20 text-primary text-xs font-bold">
                        <FiHeart className="fill-current" /> Recommended
                      </div>
                    </div>
                    <h3 className="text-3xl font-display font-bold text-white mb-2 leading-tight">{properties[0].name}</h3>
                    <p className="text-text-secondary mb-6 line-clamp-2">{properties[0].address}</p>
                    <a href="/tenant/search" className="btn primary w-full justify-center rounded-xl py-4 font-bold">
                      View Details
                    </a>
                  </div>
                </>
              ) : (
                <div className="flex flex-col items-center justify-center h-full p-8 text-center">
                  <div className="w-16 h-16 rounded-full bg-white/5 flex items-center justify-center mb-4">
                    <FiHome className="text-2xl text-text-tertiary" />
                  </div>
                  <p className="text-text-secondary">No recommendations yet.</p>
                </div>
              )}
            </div>
          </motion.div>

          {/* Recent Tickets - Wide Block */}
          <motion.div variants={itemVariants} className="md:col-span-3 lg:col-span-2">
            <div className="h-full p-8 rounded-[2rem] bg-surface/50 backdrop-blur-xl border border-white/5 flex flex-col">
              <div className="flex justify-between items-center mb-6">
                <div>
                  <h3 className="text-xl font-display font-bold">Recent Updates</h3>
                  <p className="text-sm text-text-secondary">Track your support requests</p>
                </div>
                <a href="/tenant/tickets" className="p-2 rounded-full hover:bg-white/5 text-text-secondary hover:text-white transition-colors">
                  <FiArrowRight />
                </a>
              </div>

              <div className="space-y-3">
                {tickets.length > 0 ? (
                  tickets.map((ticket) => (
                    <div key={ticket.id} className="group p-4 rounded-2xl bg-bg/50 border border-white/5 hover:border-primary/20 transition-all flex items-center gap-4">
                      <div className={`w-10 h-10 rounded-full flex items-center justify-center shrink-0 ${ticket.status === 'open' ? 'bg-blue-500/10 text-blue-400' :
                        ticket.status === 'resolved' ? 'bg-green-500/10 text-green-400' : 'bg-gray-500/10 text-gray-400'
                        }`}>
                        <FiActivity />
                      </div>
                      <div className="flex-1 min-w-0">
                        <h4 className="font-bold text-white text-sm truncate">{ticket.subject}</h4>
                        <p className="text-xs text-text-secondary truncate">{ticket.message}</p>
                      </div>
                      <StatusBadge status={ticket.status} />
                    </div>
                  ))
                ) : (
                  <div className="flex flex-col items-center justify-center py-8 text-center">
                    <FiMessageSquare className="text-2xl text-text-tertiary mb-2" />
                    <p className="text-sm text-text-tertiary">No active tickets.</p>
                  </div>
                )}
              </div>
            </div>
          </motion.div>

        </motion.div>
      </div>
    </div>
  );
};

const StatCard = ({ icon, label, value, desc, highlight }) => (
  <div className={`h-full p-6 rounded-[2rem] border transition-all duration-300 group hover:-translate-y-1 ${highlight
    ? 'bg-gradient-to-br from-primary/10 to-transparent border-primary/20'
    : 'bg-surface/50 backdrop-blur-xl border-white/5 hover:border-white/10'
    }`}>
    <div className="flex justify-between items-start mb-8">
      <div className={`p-3 rounded-2xl text-xl ${highlight ? 'bg-primary text-black' : 'bg-white/5 text-text-secondary group-hover:text-white'
        } transition-colors`}>
        {icon}
      </div>
      {highlight && (
        <div className="w-2 h-2 rounded-full bg-primary animate-pulse" />
      )}
    </div>
    <div>
      <div className="text-4xl font-display font-bold text-white mb-1 tracking-tight">{value}</div>
      <div className="text-text-secondary font-medium mb-1">{label}</div>
      <div className={`text-xs ${highlight ? 'text-primary' : 'text-text-tertiary'}`}>{desc}</div>
    </div>
  </div>
);

const QuickActionTile = ({ action }) => (
  <a
    href={action.href}
    className="flex flex-col items-center justify-center p-4 rounded-2xl bg-bg/50 border border-white/5 hover:border-primary/30 hover:bg-surface-highlight transition-all group text-center aspect-square"
  >
    <div className="text-2xl text-text-secondary group-hover:text-primary group-hover:scale-110 transition-all mb-2">
      {action.icon}
    </div>
    <span className="text-xs font-semibold text-text-secondary group-hover:text-white transition-colors">{action.label}</span>
  </a>
);

const StatusBadge = ({ status }) => {
  const styles = {
    open: 'bg-blue-500/10 text-blue-400 border-blue-500/20',
    resolved: 'bg-green-500/10 text-green-400 border-green-500/20',
    closed: 'bg-gray-500/10 text-gray-400 border-gray-500/20',
  };
  return (
    <span className={`text-[10px] px-2.5 py-1 rounded-full border font-bold uppercase tracking-wider ${styles[status] ?? styles.closed}`}>
      {status}
    </span>
  );
};

const quickActions = [
  { label: 'Pay Bills', href: '/tenant/invoices', icon: <FiCreditCard /> },
  { label: 'Contracts', href: '/tenant/contracts', icon: <FiFileText /> },
  { label: 'Support', href: '/tenant/tickets', icon: <FiMessageSquare /> },
  { label: 'Wishlist', href: '/tenant/wishlist', icon: <FiHeart /> },
];

export default TenantDashboard;
