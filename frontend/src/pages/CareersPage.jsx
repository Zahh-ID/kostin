import React from 'react';
import { motion } from 'framer-motion';
import { FiBriefcase, FiArrowRight, FiUsers, FiCpu, FiSmile } from 'react-icons/fi';

const CareersPage = () => {
    const benefits = [
        {
            icon: <FiCpu className="w-6 h-6" />,
            title: "Teknologi Terbaru",
            desc: "Bekerja dengan tech stack modern (React, Laravel, Cloud)."
        },
        {
            icon: <FiUsers className="w-6 h-6" />,
            title: "Tim Kolaboratif",
            desc: "Lingkungan kerja yang suportif, inklusif, dan menyenangkan."
        },
        {
            icon: <FiSmile className="w-6 h-6" />,
            title: "Work-Life Balance",
            desc: "Jam kerja fleksibel dan opsi remote working."
        }
    ];

    const openings = [
        {
            title: "Frontend Engineer",
            type: "Full-time",
            dept: "Engineering",
            location: "Remote / Jakarta"
        },
        {
            title: "Backend Engineer",
            type: "Full-time",
            dept: "Engineering",
            location: "Remote / Jakarta"
        },
        {
            title: "Product Designer",
            type: "Full-time",
            dept: "Product",
            location: "Jakarta"
        }
    ];

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
                        Ingin Bergabung dengan <span>Tim Kami?</span>
                    </motion.h1>
                    <motion.p
                        initial={{ opacity: 0, y: 20 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ delay: 0.1 }}
                        className="hero-lead"
                    >
                        Kami selalu mencari talenta berbakat untuk membangun masa depan hunian digital.
                    </motion.p>
                    <motion.div
                        initial={{ opacity: 0, y: 20 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ delay: 0.2 }}
                    >
                        <a href="#openings" className="btn primary">
                            Lihat Lowongan <FiArrowRight />
                        </a>
                    </motion.div>
                </div>

                {/* Benefits Grid */}
                <div className="grid grid-cols-1 md:grid-cols-3 gap-8 mb-32">
                    {benefits.map((item, i) => (
                        <motion.div
                            key={i}
                            initial={{ opacity: 0, y: 20 }}
                            whileInView={{ opacity: 1, y: 0 }}
                            transition={{ delay: i * 0.1 }}
                            viewport={{ once: true }}
                            className="card p-8 text-center hover:border-primary/50 transition-colors"
                        >
                            <div className="w-12 h-12 mx-auto bg-primary/10 text-primary rounded-xl flex items-center justify-center mb-6">
                                {item.icon}
                            </div>
                            <h3 className="text-xl font-bold font-display mb-3">{item.title}</h3>
                            <p className="text-text-secondary">{item.desc}</p>
                        </motion.div>
                    ))}
                </div>

                {/* Openings Section */}
                <div id="openings" className="max-w-4xl mx-auto">
                    <h2 className="text-3xl font-bold font-display mb-8 text-center">Posisi Tersedia</h2>
                    <div className="space-y-4">
                        {openings.map((job, i) => (
                            <motion.div
                                key={i}
                                initial={{ opacity: 0, x: -20 }}
                                whileInView={{ opacity: 1, x: 0 }}
                                transition={{ delay: i * 0.1 }}
                                viewport={{ once: true }}
                                className="card p-6 flex flex-col md:flex-row items-center justify-between gap-6 hover:border-primary transition-colors group cursor-pointer"
                            >
                                <div>
                                    <h3 className="text-xl font-bold font-display mb-2 group-hover:text-primary transition-colors">
                                        {job.title}
                                    </h3>
                                    <div className="flex flex-wrap gap-4 text-sm text-text-secondary">
                                        <span className="flex items-center gap-1">
                                            <FiBriefcase /> {job.dept}
                                        </span>
                                        <span className="w-1 h-1 rounded-full bg-text-tertiary self-center" />
                                        <span>{job.type}</span>
                                        <span className="w-1 h-1 rounded-full bg-text-tertiary self-center" />
                                        <span>{job.location}</span>
                                    </div>
                                </div>
                                <button className="btn ghost group-hover:bg-primary group-hover:text-black group-hover:border-primary whitespace-nowrap">
                                    Apply Now
                                </button>
                            </motion.div>
                        ))}
                    </div>
                </div>
            </div>
        </div>
    );
};

export default CareersPage;
