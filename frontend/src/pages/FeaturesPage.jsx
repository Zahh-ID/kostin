import React from 'react';
import { motion } from 'framer-motion';
import { FiShield, FiHome, FiCreditCard, FiSearch, FiMessageSquare, FiTrendingUp } from 'react-icons/fi';

const features = [
    {
        icon: <FiSearch className="w-8 h-8" />,
        title: "Pencarian Cerdas",
        description: "Temukan kost impianmu dengan filter lengkap: lokasi, harga, fasilitas, dan rating."
    },
    {
        icon: <FiShield className="w-8 h-8" />,
        title: "Transaksi Aman",
        description: "Sistem pembayaran terintegrasi yang menjamin keamanan dana penyewa dan pemilik."
    },
    {
        icon: <FiHome className="w-8 h-8" />,
        title: "Manajemen Properti",
        description: "Dashboard lengkap untuk pemilik kost mengelola kamar, penyewa, dan laporan keuangan."
    },
    {
        icon: <FiMessageSquare className="w-8 h-8" />,
        title: "Chat Terintegrasi",
        description: "Komunikasi langsung antara penyewa dan pemilik tanpa perlu keluar aplikasi."
    },
    {
        icon: <FiCreditCard className="w-8 h-8" />,
        title: "Pembayaran Digital",
        description: "Dukung berbagai metode pembayaran: transfer bank, e-wallet, dan QRIS."
    },
    {
        icon: <FiTrendingUp className="w-8 h-8" />,
        title: "Analitik Bisnis",
        description: "Laporan performa bisnis kost untuk membantu pemilik mengambil keputusan tepat."
    }
];

const FeaturesPage = () => {
    return (
        <div className="page pt-32 pb-20">
            <div className="container">
                <div className="text-center max-w-3xl mx-auto mb-20">
                    <motion.h1
                        initial={{ opacity: 0, y: 20 }}
                        animate={{ opacity: 1, y: 0 }}
                        className="hero-title mb-6"
                    >
                        Fitur Unggulan <span>KostIn</span>
                    </motion.h1>
                    <motion.p
                        initial={{ opacity: 0, y: 20 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ delay: 0.1 }}
                        className="hero-lead"
                    >
                        Platform manajemen kost modern dengan fitur terlengkap untuk pengalaman sewa menyewa yang lebih baik.
                    </motion.p>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    {features.map((feature, index) => (
                        <motion.div
                            key={index}
                            initial={{ opacity: 0, y: 20 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ delay: index * 0.1 + 0.2 }}
                            className="card hover:border-primary/50 transition-colors group"
                        >
                            <div className="mb-6 p-4 rounded-2xl bg-primary/10 w-fit text-primary group-hover:bg-primary group-hover:text-black transition-colors">
                                {feature.icon}
                            </div>
                            <h3 className="text-xl font-bold mb-3 font-display">{feature.title}</h3>
                            <p className="text-text-secondary leading-relaxed">
                                {feature.description}
                            </p>
                        </motion.div>
                    ))}
                </div>

                <motion.div
                    initial={{ opacity: 0, y: 40 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ delay: 0.8 }}
                    className="cta-section mt-32 rounded-3xl border border-border relative overflow-hidden"
                >
                    <div className="absolute inset-0 bg-gradient-to-r from-primary/5 to-secondary/5" />
                    <div className="relative z-10">
                        <h2 className="text-4xl font-bold mb-6 font-display">Siap Memulai?</h2>
                        <p className="text-xl text-text-secondary mb-8 max-w-2xl mx-auto">
                            Bergabunglah dengan ribuan pengguna lain yang telah merasakan kemudahan mencari dan mengelola kost.
                        </p>
                        <div className="flex gap-4 justify-center">
                            <a href="/register" className="btn primary">Daftar Sekarang</a>
                            <a href="/login" className="btn ghost">Masuk</a>
                        </div>
                    </div>
                </motion.div>
            </div>
        </div>
    );
};

export default FeaturesPage;
