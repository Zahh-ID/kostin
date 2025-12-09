import React from 'react';

const PropertyOverview = ({ property, rooms }) => {
    const stats = {
        total: rooms.length,
        available: rooms.filter(r => r.status === 'available').length,
        occupied: rooms.filter(r => r.status === 'occupied').length,
        maintenance: rooms.filter(r => r.status === 'maintenance').length,
    };

    return (
        <div className="space-y-8">
            <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div className="card p-6 bg-surface-highlight">
                    <div className="text-text-secondary text-sm mb-1">Total Kamar</div>
                    <div className="text-3xl font-bold">{stats.total}</div>
                </div>
                <div className="card p-6 bg-green-500/10 border-green-500/20">
                    <div className="text-green-400 text-sm mb-1">Tersedia</div>
                    <div className="text-3xl font-bold text-green-400">{stats.available}</div>
                </div>
                <div className="card p-6 bg-blue-500/10 border-blue-500/20">
                    <div className="text-blue-400 text-sm mb-1">Terisi</div>
                    <div className="text-3xl font-bold text-blue-400">{stats.occupied}</div>
                </div>
                <div className="card p-6 bg-yellow-500/10 border-yellow-500/20">
                    <div className="text-yellow-400 text-sm mb-1">Maintenance</div>
                    <div className="text-3xl font-bold text-yellow-400">{stats.maintenance}</div>
                </div>
            </div>

            <div className="card p-6">
                <h3 className="text-lg font-bold font-display mb-4">Detail Properti</h3>
                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label className="text-xs text-text-tertiary uppercase font-bold">Deskripsi</label>
                        <p className="text-text-secondary mt-1 whitespace-pre-wrap">{property.description || '-'}</p>
                    </div>
                    <div>
                        <label className="text-xs text-text-tertiary uppercase font-bold">Alamat</label>
                        <p className="text-text-secondary mt-1">{property.address || '-'}</p>
                    </div>
                    <div>
                        <label className="text-xs text-text-tertiary uppercase font-bold">Peraturan</label>
                        <p className="text-text-secondary mt-1 whitespace-pre-wrap">{property.rules_text || '-'}</p>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default PropertyOverview;
