import React from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { FiAlertTriangle, FiX } from 'react-icons/fi';

const ConfirmationModal = ({ isOpen, onClose, onConfirm, title, message, confirmLabel = 'Ya, Hapus', cancelLabel = 'Batal', isLoading = false, isDanger = false }) => {
    return (
        <AnimatePresence>
            {isOpen && (
                <div className="fixed inset-0 z-[2100] flex items-center justify-center p-4">
                    <motion.div
                        initial={{ opacity: 0 }}
                        animate={{ opacity: 1 }}
                        exit={{ opacity: 0 }}
                        className="absolute inset-0 bg-black/80 backdrop-blur-sm"
                        onClick={isLoading ? undefined : onClose}
                    />
                    <motion.div
                        initial={{ opacity: 0, scale: 0.95, y: 20 }}
                        animate={{ opacity: 1, scale: 1, y: 0 }}
                        exit={{ opacity: 0, scale: 0.95, y: 20 }}
                        className="relative bg-surface border border-border rounded-2xl w-full max-w-md shadow-2xl overflow-hidden"
                    >
                        <div className="p-6 text-center">
                            <div className={`mx-auto w-16 h-16 rounded-full flex items-center justify-center mb-4 ${isDanger ? 'bg-red-500/10 text-red-500' : 'bg-primary/10 text-primary'}`}>
                                <FiAlertTriangle className="text-3xl" />
                            </div>
                            <h3 className="text-xl font-bold font-display mb-2">{title}</h3>
                            <p className="text-text-secondary mb-6">{message}</p>

                            <div className="flex gap-3 justify-center">
                                <button
                                    onClick={onClose}
                                    disabled={isLoading}
                                    className="btn ghost"
                                >
                                    {cancelLabel}
                                </button>
                                <button
                                    onClick={onConfirm}
                                    disabled={isLoading}
                                    className={`btn ${isDanger ? 'bg-red-500 hover:bg-red-600 text-white' : 'primary'}`}
                                >
                                    {isLoading ? 'Memproses...' : confirmLabel}
                                </button>
                            </div>
                        </div>
                    </motion.div>
                </div>
            )}
        </AnimatePresence>
    );
};

export default ConfirmationModal;
