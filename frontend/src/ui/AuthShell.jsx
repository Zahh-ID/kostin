import React from 'react';
import { Link, Outlet } from 'react-router-dom';
import { motion } from 'framer-motion';
import { Logo } from './Logo.jsx';

const AuthShell = () => (
  <div className="page">
    <div className="bg-grid" />
    <div className="orb orb-1" />
    <div className="orb orb-2" />
    <div className="orb orb-3" />

    <main>
      <Outlet />
    </main>
  </div>
);

export default AuthShell;
