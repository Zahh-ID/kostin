import React from 'react';
import { motion } from 'framer-motion';

const Wishlist = () => (
  <div className="container dashboard">
    <header className="dash-header">
      <div>
        <p className="badge dash-badge">Tenant</p>
        <h1 className="h4 text-white">Wishlist</h1>
        <p className="muted">Kost yang kamu simpan untuk dipantau atau diajukan.</p>
      </div>
      <div className="dash-actions">
        <a href="/tenant/wishlist" className="btn primary">
          Lihat wishlist
        </a>
        <a href="/tenant/search" className="btn ghost">
          Cari kos baru
        </a>
      </div>
    </header>

    <section className="panel">
      <div className="panel-head">
        <div>
          <div className="pill">Wishlist</div>
          <div className="section-title">Daftar kost simpanan</div>
        </div>
      </div>
      <div className="card placeholder">
        <div className="card-title">Belum ada kost di wishlist</div>
        <div className="muted">Tambahkan dari pencarian untuk memantau status dan harga.</div>
      </div>
    </section>
  </div>
);

export default Wishlist;
