import React from 'react';
import { motion } from 'framer-motion';
import { FiUsers, FiTarget, FiHeart } from 'react-icons/fi';

const values = [
    {
        icon: <FiUsers className="w-8 h-8" />,
        title: "Berpusat pada Pengguna",
        description: "Kami mendengarkan dan membangun solusi berdasarkan kebutuhan nyata pencari dan pemilik kost."
    },
    {
        icon: <FiTarget className="w-8 h-8" />,
        title: "Inovasi Berkelanjutan",
        description: "Terus mengembangkan teknologi untuk membuat pengalaman sewa menyewa semakin mudah."
    },
    {
        icon: <FiHeart className="w-8 h-8" />,
        title: "Kepercayaan & Keamanan",
        description: "Memprioritaskan keamanan data dan transaksi untuk ketenangan pikiran semua pengguna."
    }
];

const AboutPage = () => {
    return (
        <div className="page pt-32 pb-20">
            <div className="container">
                {/* Hero Section */}
                <div className="text-center max-w-3xl mx-auto mb-20">
                    <motion.h1
                        initial={{ opacity: 0, y: 20 }}
                        animate={{ opacity: 1, y: 0 }}
                        className="hero-title mb-6"
                    >
                        Tentang <span>KostIn</span>
                    </motion.h1>
                    <motion.p
                        initial={{ opacity: 0, y: 20 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ delay: 0.1 }}
                        className="hero-lead"
                    >
                        Misi kami adalah menghubungkan pencari kost dengan hunian impian mereka melalui teknologi yang aman, mudah, dan transparan.
                    </motion.p>
                </div>

                {/* Story Section */}
                <div className="grid grid-cols-1 md:grid-cols-2 gap-12 items-center mb-24">
                    <motion.div
                        initial={{ opacity: 0, x: -20 }}
                        animate={{ opacity: 1, x: 0 }}
                        transition={{ delay: 0.2 }}
                    >
                        <h2 className="text-3xl font-bold font-display mb-6">Cerita Kami</h2>
                        <div className="space-y-4 text-text-secondary leading-relaxed">
                            <p>
                                KostIn bermula dari pengalaman pribadi pendiri kami yang kesulitan mencari tempat tinggal yang layak, aman, dan sesuai budget saat merantau untuk kuliah.
                            </p>
                            <p>
                                Kami menyadari bahwa proses pencarian kost seringkali tidak efisien, penuh ketidakpastian, dan kurang transparan. Di sisi lain, pemilik kost juga kesulitan mengelola properti mereka secara manual.
                            </p>
                            <p>
                                Lahir pada tahun 2024, KostIn hadir sebagai solusi jembatan digital. Kami tidak hanya sekadar platform listing, tetapi ekosistem manajemen properti yang komprehensif.
                            </p>
                        </div>
                    </motion.div>
                    <motion.div
                        initial={{ opacity: 0, x: 20 }}
                        animate={{ opacity: 1, x: 0 }}
                        transition={{ delay: 0.3 }}
                        className="relative h-[400px] rounded-3xl overflow-hidden bg-surface-highlight border border-border"
                    >
                        {/* Placeholder for team/office image */}
                        <div className="absolute inset-0 flex items-center justify-center text-text-tertiary">
                            <span className="text-lg">Team Photo Placeholder</span>
                        </div>
                    </motion.div>
                </div>

                {/* Values Section */}
                <div className="mb-20">
                    <h2 className="text-3xl font-bold font-display text-center mb-12">Nilai Kami</h2>
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
                        {values.map((value, index) => (
                            <motion.div
                                key={index}
                                initial={{ opacity: 0, y: 20 }}
                                animate={{ opacity: 1, y: 0 }}
                                transition={{ delay: index * 0.1 + 0.4 }}
                                className="card p-8 text-center"
                            >
                                <div className="w-16 h-16 rounded-2xl bg-primary/10 text-primary flex items-center justify-center mx-auto mb-6">
                                    {value.icon}
                                </div>
                                <h3 className="text-xl font-bold mb-3 font-display">{value.title}</h3>
                                <p className="text-text-secondary text-sm leading-relaxed">
                                    {value.description}
                                </p>
                            </motion.div>
                        ))}
                    </div>
                </div>

                {/* Team CTA */}
                <motion.div
                    initial={{ opacity: 0, y: 20 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ delay: 0.6 }}
                    className="text-center"
                >
                    <h2 className="text-2xl font-bold mb-4 font-display">Ingin Bergabung dengan Tim Kami?</h2>
                    <p className="text-text-secondary mb-8">Kami selalu mencari talenta berbakat untuk membangun masa depan hunian digital.</p>
                    <a href="/careers" className="btn ghost">Lihat Lowongan</a>
                </motion.div>
            </div>
        </div>
    );
};

export default AboutPage;
