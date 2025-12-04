import React, { useEffect, useState, useRef } from 'react';
import { motion, useScroll, useTransform, useSpring } from 'framer-motion';
import { Link } from 'react-router-dom';
import { gsap } from 'gsap';
import { fetchProperties, fetchStats } from '../api/client.js';
import { FiCreditCard, FiFileText, FiMessageSquare, FiPieChart, FiHome, FiArrowRight } from 'react-icons/fi';
import SEO from '../components/SEO.jsx';

// --- Components ---

const Hero = () => {
  return (
    <section className="hero">
      <div className="bg-grid" />
      <div className="orb orb-1" />
      <div className="orb orb-2" />

      <div className="hero-content">
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6 }}
        >
          <span className="pill">Platform Manajemen Kost #1</span>
        </motion.div>

        <motion.h1
          className="hero-title"
          initial={{ opacity: 0, y: 40 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.8, delay: 0.1 }}
        >
          Kelola Kost Jadi <br />
          <span>Lebih Simpel.</span>
        </motion.h1>

        <motion.p
          className="hero-lead"
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.8, delay: 0.2 }}
        >
          Sistem terintegrasi untuk tenant dan owner. Tagihan otomatis, kontrak digital,
          dan pembayaran QRIS dalam satu aplikasi modern.
        </motion.p>

        <motion.div
          className="flex justify-center flex-wrap"
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.8, delay: 0.3 }}
        >
          <Link to="/register" className="btn primary">Mulai Sekarang Gratis</Link>
          <Link to="/about" className="btn ghost">Pelajari Fitur</Link>
        </motion.div>
      </div>

      <HeroStats />
    </section>
  );
};

const HeroStats = () => {
  const [stats, setStats] = useState(null);

  useEffect(() => {
    fetchStats().then(setStats).catch(() => setStats(null));
  }, []);

  const items = [
    { label: 'Pembayaran Sukses', value: `${stats?.payments?.success_rate ?? 99}%` },
    { label: 'Kontrak Aktif', value: stats?.contracts?.active_count ?? '100+' },
    { label: 'Tiket Terlayani', value: stats?.tickets?.live_count ?? '24/7' },
  ];

  return (
    <div className="container" style={{ marginTop: '80px' }}>
      <div className="grid" style={{ gridTemplateColumns: 'repeat(auto-fit, minmax(200px, 1fr))', gap: '20px' }}>
        {items.map((item, i) => (
          <motion.div
            key={i}
            className="card text-center"
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ delay: 0.4 + (i * 0.1) }}
          >
            <h3 style={{ fontSize: '2.5rem', color: 'var(--primary)', marginBottom: '4px' }}>{item.value}</h3>
            <p>{item.label}</p>
          </motion.div>
        ))}
      </div>
    </div>
  );
};

const BentoFeature = ({ title, desc, icon, colSpan = 1 }) => {
  const ref = useRef(null);

  const handleMouseMove = (e) => {
    if (!ref.current) return;
    const rect = ref.current.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;
    ref.current.style.setProperty('--mouse-x', `${x}px`);
    ref.current.style.setProperty('--mouse-y', `${y}px`);
  };

  return (
    <motion.div
      ref={ref}
      className="bento-card"
      style={{ gridColumn: `span ${colSpan}` }}
      onMouseMove={handleMouseMove}
      initial={{ opacity: 0, scale: 0.95 }}
      whileInView={{ opacity: 1, scale: 1 }}
      viewport={{ once: true }}
      transition={{ duration: 0.5 }}
    >
      <div style={{ fontSize: '2rem', marginBottom: '16px', color: 'var(--primary)' }}>{icon}</div>
      <h3>{title}</h3>
      <p style={{ marginTop: '8px' }}>{desc}</p>
    </motion.div>
  );
};

const Features = () => (
  <section className="container" style={{ padding: '100px 24px' }}>
    <div className="text-center" style={{ marginBottom: '60px' }}>
      <span className="pill">Fitur Unggulan</span>
      <h2 style={{ fontSize: '2.5rem', marginTop: '16px' }}>Satu Aplikasi, Banyak Solusi</h2>
    </div>

    <div className="bento-grid">
      <BentoFeature
        colSpan={2}
        icon={<FiCreditCard />}
        title="Pembayaran QRIS Otomatis"
        desc="Terintegrasi dengan Midtrans. Tenant bayar pakai QRIS, status langsung update otomatis. Tidak perlu cek mutasi manual lagi."
      />
      <BentoFeature
        icon={<FiFileText />}
        title="Kontrak Digital"
        desc="Buat dan tanda tangani kontrak sewa secara digital. Aman, legal, dan tersimpan rapi di cloud."
      />
      <BentoFeature
        icon={<FiMessageSquare />}
        title="Live Chat & Tiket"
        desc="Komunikasi terpusat antara tenant dan owner. Komplain tertangani dengan sistem tiket yang rapi."
      />
      <BentoFeature
        colSpan={2}
        icon={<FiPieChart />}
        title="Dashboard Keuangan"
        desc="Pantau cashflow, tagihan belum lunas, dan okupansi kamar dalam satu dashboard yang intuitif dan real-time."
      />
    </div>
  </section>
);

const PropertyCard = ({ property }) => {
  const price = property?.room_types?.[0]?.base_price
    ? new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(property.room_types[0].base_price)
    : 'Hubungi Owner';

  return (
    <motion.div
      className="property-card"
      initial={{ opacity: 0, y: 20 }}
      whileInView={{ opacity: 1, y: 0 }}
      viewport={{ once: true }}
    >
      <img
        src={property.photos?.[0] || 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?ixlib=rb-4.0.3&auto=format&fit=crop&w=2340&q=80'}
        alt={property.name}
        className="property-img"
      />
      <div className="property-info">
        <div className="flex justify-between items-start">
          <div>
            <h3 style={{ fontSize: '1.2rem', marginBottom: '4px' }}>{property.name}</h3>
            <p style={{ fontSize: '0.9rem' }}>{property.address}</p>
          </div>
          <span className="pill" style={{ background: 'rgba(204,255,0,0.1)', color: 'var(--primary)', border: 'none' }}>
            {property.status || 'Available'}
          </span>
        </div>
        <div style={{ marginTop: '16px', paddingTop: '16px', borderTop: '1px solid var(--border)' }}>
          <p style={{ fontSize: '0.8rem', marginBottom: '4px' }}>Mulai dari</p>
          <div className="property-price">{price} / bulan</div>
        </div>
      </div>
    </motion.div>
  );
};

const PropertiesSection = () => {
  const [properties, setProperties] = useState([]);

  useEffect(() => {
    fetchProperties().then(setProperties).catch(() => setProperties([]));
  }, []);

  return (
    <section className="container" style={{ padding: '0 24px 100px' }}>
      <div className="flex justify-between items-center" style={{ marginBottom: '40px' }}>
        <h2>Properti Pilihan</h2>
        <Link to="/search" className="btn ghost">Lihat Semua</Link>
      </div>

      <div className="grid" style={{ gridTemplateColumns: 'repeat(auto-fit, minmax(300px, 1fr))' }}>
        {properties.slice(0, 3).map(p => (
          <PropertyCard key={p.id} property={p} />
        ))}
        {properties.length === 0 && (
          <div className="card text-center" style={{ gridColumn: '1/-1', padding: '60px' }}>
            <p>Belum ada properti yang tersedia saat ini.</p>
          </div>
        )}
      </div>
    </section>
  );
};

const CTA = () => (
  <section className="cta-section">
    <div className="container">
      <motion.div
        initial={{ opacity: 0, scale: 0.9 }}
        whileInView={{ opacity: 1, scale: 1 }}
        transition={{ duration: 0.5 }}
      >
        <h2 style={{ fontSize: '3rem', marginBottom: '24px' }}>Siap Mengelola Kost dengan Cara Baru?</h2>
        <p style={{ fontSize: '1.2rem', marginBottom: '40px', maxWidth: '600px', margin: '0 auto 40px' }}>
          Bergabung dengan ratusan owner dan tenant yang sudah beralih ke KostIn.
        </p>
        <Link to="/register" className="btn primary" style={{ fontSize: '1.2rem', padding: '16px 48px' }}>
          Daftar Sekarang
        </Link>
      </motion.div>
    </div>
  </section>
);

const Footer = () => (
  <footer style={{ borderTop: '1px solid var(--border)', padding: '60px 24px', background: '#050505' }}>
    <div className="container flex justify-between flex-wrap" style={{ gap: '40px' }}>
      <div>
        <div className="nav-logo" style={{ marginBottom: '16px' }}>Kost<span>In</span>.</div>
        <p style={{ maxWidth: '300px' }}>Platform manajemen kost modern untuk masa depan properti Anda.</p>
      </div>
      <div className="flex" style={{ gap: '40px' }}>
        <div className="flex flex-col" style={{ gap: '12px' }}>
          <h4 style={{ color: 'white' }}>Product</h4>
          <Link to="/features" style={{ color: 'var(--text-secondary)' }}>Fitur</Link>
          <Link to="/pricing" style={{ color: 'var(--text-secondary)' }}>Harga</Link>
        </div>
        <div className="flex flex-col" style={{ gap: '12px' }}>
          <h4 style={{ color: 'white' }}>Company</h4>
          <Link to="/about" style={{ color: 'var(--text-secondary)' }}>Tentang Kami</Link>
          <Link to="/contact" style={{ color: 'var(--text-secondary)' }}>Kontak</Link>
        </div>
      </div>
    </div>
    <div className="container text-center" style={{ marginTop: '60px', paddingTop: '24px', borderTop: '1px solid var(--border)', fontSize: '0.9rem', color: 'var(--text-tertiary)' }}>
      &copy; {new Date().getFullYear()} KostIn. All rights reserved.
    </div>
  </footer>
);

const HomePage = () => {
  return (
    <div className="page">
      <SEO
        title="KostIn - Modern Boarding House Management"
        description="Manage your boarding house business efficiently with KostIn. Automated billing, digital contracts, and seamless communication."
      />
      <Hero />
      <Features />
      <PropertiesSection />
      <CTA />
      <Footer />
    </div>
  );
};

export default HomePage;
