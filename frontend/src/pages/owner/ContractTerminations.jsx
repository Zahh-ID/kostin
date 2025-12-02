import React, { useEffect, useState } from 'react';
import { motion } from 'framer-motion';
import { FiFileText, FiArrowLeft, FiXCircle, FiUser, FiHome, FiCalendar } from 'react-icons/fi';
import { fetchOwnerContracts } from '../../api/client';
import { Link } from 'react-router-dom';

const ContractTerminations = () => {
    const [contracts, setContracts] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        loadData();
    }, []);

    const loadData = async () => {
        try {
            const data = await fetchOwnerContracts();
            // Filter for terminated contracts
            setContracts(data.filter(c => c.status === 'terminated'));
        } catch (error) {
            console.error('Failed to fetch contracts:', error);
        } finally {
            setLoading(false);
        }
    };

    const containerVariants = {
        hidden: { opacity: 0 },
        visible: {
            opacity: 1,
            transition: { staggerChildren: 0.1 }
        }
    };

    return (
        <div className="page pt-32 pb-20">
            <div className="container">
                <div className="flex items-center gap-4 mb-8">
                    <Link to="/owner/contracts" className="btn ghost">
                        <FiArrowLeft className="mr-2" /> Kembali
                    </Link>
                    <h1 className="text-3xl font-display font-bold">Riwayat Terminasi</h1>
                </div>

                <motion.div
                    variants={containerVariants}
                    initial="hidden"
                    animate="visible"
                    className="space-y-4"
                >
                    {loading ? (
                        <p className="text-text-secondary">Memuat data...</p>
                    ) : contracts.length === 0 ? (
                        <div className="text-center py-12 card border-dashed border-2 border-border bg-transparent">
                            <p className="text-text-secondary">Belum ada riwayat terminasi.</p>
                        </div>
                    ) : (
                        contracts.map((contract) => (
                            <TerminationItem key={contract.id} contract={contract} />
                        ))
                    )}
                </motion.div>
            </div>
        </div>
    );
};

const TerminationItem = ({ contract }) => {
    return (
        <motion.div
            variants={{ hidden: { opacity: 0, y: 10 }, visible: { opacity: 1, y: 0 } }}
            className="card p-5 hover:bg-surface-highlight transition-colors group"
        >
            <div className="flex flex-col md:flex-row justify-between gap-4">
                <div className="flex-grow">
                    <div className="flex items-center gap-3 mb-2">
                        <h3 className="text-lg font-bold font-display">{contract.property_name}</h3>
                        <span className="px-2 py-0.5 rounded text-xs font-bold border text-red-400 bg-red-400/10 border-red-400/20">
                            Terminated
                        </span>
                    </div>

                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-2 text-sm text-text-secondary mb-3">
                        <div className="flex items-center gap-2">
                            <FiHome className="text-text-tertiary" />
                            <span>{contract.room_name}</span>
                        </div>
                        <div className="flex items-center gap-2">
                            <FiUser className="text-text-tertiary" />
                            <span>{contract.tenant_name}</span>
                        </div>
                        <div className="flex items-center gap-2">
                            <FiCalendar className="text-text-tertiary" />
                            <span>Berakhir: {new Date(contract.terminated_at).toLocaleDateString()}</span>
                        </div>
                    </div>

                    <div className="p-3 rounded-lg bg-surface border border-white/5">
                        <p className="text-xs text-text-tertiary mb-1 uppercase tracking-wider">Alasan Terminasi</p>
                        <p className="text-sm text-text-secondary">{contract.termination_reason || 'Tidak ada alasan.'}</p>
                    </div>
                </div>
            </div>
        </motion.div>
    );
};

export default ContractTerminations;
