import React from 'react';

const TenantNotifications = () => (
  <div className="container dashboard">
    <header className="dash-header">
      <div>
        <p className="badge dash-badge">Tenant</p>
        <h1 className="h4 text-white">Notifikasi</h1>
        <p className="muted">Kelola preferensi notifikasi untuk pembayaran, kontrak, tiket, dan chat.</p>
      </div>
    </header>

    <section className="panel">
      <div className="panel-head">
        <div>
          <div className="pill">Notifikasi</div>
          <div className="section-title">Preferensi notifikasi</div>
        </div>
      </div>
      <div className="card placeholder">
        <div className="card-title">Pengaturan notifikasi</div>
        <div className="muted">Aktifkan email/push untuk tagihan, kontrak, tiket, dan chat.</div>
      </div>
    </section>
  </div>
);

export default TenantNotifications;
