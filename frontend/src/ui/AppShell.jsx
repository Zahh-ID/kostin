
import React, { useEffect, useState } from 'react';
import { Outlet, useLocation, Link } from 'react-router-dom';
import { motion } from 'framer-motion';
import { Logo } from './Logo.jsx';
import { currentUser, logout as apiLogout } from '../api/client.js';
import Navbar from './Navbar.jsx';



const MotionLink = motion.create(Link);

const AppShell = () => {
  const [user, setUser] = useState(null);
  const location = useLocation();

  useEffect(() => {
    currentUser()
      .then((data) => setUser(data ?? null))
      .catch(() => setUser(null));
  }, []);

  const handleLogout = async () => {
    try {
      await apiLogout();
    } catch (error) {
      // ignore
    }
    window.location.href = '/';
  };

  const { navLinks, ctaLinks, logoutHandler } = navConfig(user, handleLogout);

  return (
    <div className="page">
      <div className="bg-grid" />
      <div className="orb orb-1" />
      <div className="orb orb-2" />
      <div className="orb orb-3" />

      <Navbar
        brand={<Logo />}
        links={navLinks.map((link) => ({ label: link.label, href: link.to }))}
        activeHref={location.pathname}
        actions={
          <>
            {ctaLinks.map((cta) =>
              cta.type === 'button' ? (
                <motion.button
                  key={cta.label}
                  whileHover={{ y: -1 }}
                  whileTap={{ scale: 0.98 }}
                  type="button"
                  className={`btn ${cta.variant} `}
                  onClick={cta.onClick ?? logoutHandler}
                >
                  {cta.label}
                </motion.button>
              ) : (
                <MotionLink
                  key={cta.to}
                  whileHover={{ y: -1, scale: 1.01 }}
                  whileTap={{ scale: 0.98 }}
                  className={`btn ${cta.variant} `}
                  to={cta.to}
                >
                  {cta.label}
                </MotionLink>
              ),
            )}
          </>
        }
      />

      <main>
        <Outlet />
      </main>
    </div>
  );
};

const navConfig = (user, logoutHandler) => {
  if (!user) {
    return {
      navLinks: [
        { to: '/', label: 'Home' },
        { to: '/features', label: 'Fitur' },
        { to: '/about', label: 'Tentang Kami' },
        { to: '/contact', label: 'Kontak' },
        { to: '/search', label: 'Cari Kos' },
        { to: '/faq', label: 'FAQ' },
      ],
      ctaLinks: [
        { to: '/login', label: 'Masuk', variant: 'ghost' },
        { to: '/register', label: 'Daftar', variant: 'primary' },
      ],
    };
  }

  if (user.role === 'admin') {
    return {
      navLinks: [
        { to: '/admin', label: 'Admin' },
        { to: '/admin/moderations', label: 'Moderasi' },
        { to: '/admin/tickets', label: 'Tiket' },
        { to: '/admin/users', label: 'Users' },
      ],
      ctaLinks: [
        { type: 'button', label: 'Keluar', variant: 'primary', onClick: logoutHandler },
      ],
    };
  }

  if (user.role === 'owner') {
    return {
      navLinks: [
        { to: '/owner', label: 'Dashboard' },
        { to: '/owner/properties', label: 'Properti' },

        { to: '/owner/contracts', label: 'Kontrak' },
        { to: '/owner/payments', label: 'Pembayaran' },
        { to: '/owner/tickets', label: 'Tiket' },
        { to: '/owner/applications', label: 'Aplikasi' },
      ],
      ctaLinks: [
        { type: 'button', label: 'Keluar', variant: 'primary', onClick: logoutHandler },
      ],
    };
  }

  if (user.role === 'tenant') {
    return {
      navLinks: [
        { to: '/dashboard', label: 'Dashboard' },
        { to: '/tenant/invoices', label: 'Tagihan' },
        { to: '/tenant/contracts', label: 'Kontrak' },
        { to: '/search', label: 'Cari Kos' },
        { to: '/tenant/tickets', label: 'Tiket' },
      ],
      ctaLinks: [
        { type: 'button', label: 'Keluar', variant: 'primary', onClick: logoutHandler },
      ],
    };
  }

  return {
    navLinks: [
      { to: '/dashboard', label: 'Dashboard' },
    ],
    ctaLinks: [
      { type: 'button', label: 'Keluar', variant: 'primary', onClick: logoutHandler },
    ],
  };
};

export default AppShell;
