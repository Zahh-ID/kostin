import React, { useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { FiPlus, FiMinus, FiSearch } from 'react-icons/fi';

const faqs = [
    {
        question: "Bagaimana cara memesan kost di KostIn?",
        answer: "Cari kost yang Anda inginkan, klik tombol 'Ajukan Sewa', isi formulir pengajuan, dan tunggu persetujuan dari pemilik kost. Setelah disetujui, Anda dapat melakukan pembayaran."
    },
    {
        question: "Apakah ada biaya admin?",
        answer: "KostIn tidak membebankan biaya admin kepada penyewa. Biaya yang Anda bayarkan murni untuk sewa kost dan deposit (jika ada)."
    },
    {
        question: "Bagaimana sistem pembayarannya?",
        answer: "Kami mendukung berbagai metode pembayaran termasuk transfer bank (Virtual Account), e-wallet (GoPay, OVO, Dana), dan kartu kredit. Semua transaksi dijamin aman."
    },
    {
        question: "Apakah saya bisa membatalkan pesanan?",
        answer: "Kebijakan pembatalan bergantung pada masing-masing pemilik kost. Anda dapat melihat kebijakan pembatalan di halaman detail kost sebelum mengajukan sewa."
    },
    {
        question: "Bagaimana jika saya pemilik kost?",
        answer: "Anda dapat mendaftar sebagai pemilik kost, melengkapi profil usaha, dan mulai mengiklankan kamar kost Anda. Kami menyediakan dashboard lengkap untuk mengelola properti Anda."
    },
    {
        question: "Apakah ada verifikasi pengguna?",
        answer: "Ya, demi keamanan bersama, kami mewajibkan verifikasi identitas (KTP) bagi penyewa dan pemilik kost sebelum dapat melakukan transaksi."
    }
];

const FAQItem = ({ item, isOpen, onClick }) => {
    return (
        <motion.div
            initial={false}
            className={`border border-border rounded-2xl overflow-hidden transition-colors ${isOpen ? 'bg-surface-highlight border-primary/30' : 'bg-surface hover:border-border-hover'}`}
        >
            <button
                onClick={onClick}
                className="w-full flex items-center justify-between p-6 text-left"
            >
                <span className={`font-display font-semibold text-lg ${isOpen ? 'text-primary' : 'text-text'}`}>
                    {item.question}
                </span>
                <span className={`p-2 rounded-full ${isOpen ? 'bg-primary text-black' : 'bg-white/5 text-text-secondary'}`}>
                    {isOpen ? <FiMinus /> : <FiPlus />}
                </span>
            </button>
            <AnimatePresence>
                {isOpen && (
                    <motion.div
                        initial={{ height: 0, opacity: 0 }}
                        animate={{ height: 'auto', opacity: 1 }}
                        exit={{ height: 0, opacity: 0 }}
                        transition={{ duration: 0.3, ease: "easeInOut" }}
                    >
                        <div className="px-6 pb-6 text-text-secondary leading-relaxed border-t border-white/5 pt-4">
                            {item.answer}
                        </div>
                    </motion.div>
                )}
            </AnimatePresence>
        </motion.div>
    );
};

const FAQPage = () => {
    const [openIndex, setOpenIndex] = useState(0);
    const [searchQuery, setSearchQuery] = useState('');

    const filteredFaqs = faqs.filter(faq =>
        faq.question.toLowerCase().includes(searchQuery.toLowerCase()) ||
        faq.answer.toLowerCase().includes(searchQuery.toLowerCase())
    );

    return (
        <div className="page pt-32 pb-20">
            <div className="container max-w-4xl">
                <div className="text-center mb-16">
                    <motion.h1
                        initial={{ opacity: 0, y: 20 }}
                        animate={{ opacity: 1, y: 0 }}
                        className="hero-title mb-6"
                    >
                        Pertanyaan <span>Umum</span>
                    </motion.h1>
                    <motion.p
                        initial={{ opacity: 0, y: 20 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ delay: 0.1 }}
                        className="hero-lead"
                    >
                        Temukan jawaban untuk pertanyaan yang sering diajukan seputar KostIn.
                    </motion.p>

                    <motion.div
                        initial={{ opacity: 0, y: 20 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ delay: 0.2 }}
                        className="relative max-w-lg mx-auto mt-8"
                    >
                        <FiSearch className="absolute left-4 top-1/2 -translate-y-1/2 text-text-secondary text-xl" />
                        <input
                            type="text"
                            placeholder="Cari pertanyaan..."
                            value={searchQuery}
                            onChange={(e) => setSearchQuery(e.target.value)}
                            className="w-full bg-surface border border-border rounded-full py-4 pl-12 pr-6 text-text focus:outline-none focus:border-primary transition-colors"
                        />
                    </motion.div>
                </div>

                <div className="space-y-4">
                    {filteredFaqs.length > 0 ? (
                        filteredFaqs.map((faq, index) => (
                            <motion.div
                                key={index}
                                initial={{ opacity: 0, y: 20 }}
                                animate={{ opacity: 1, y: 0 }}
                                transition={{ delay: index * 0.05 + 0.3 }}
                            >
                                <FAQItem
                                    item={faq}
                                    isOpen={openIndex === index}
                                    onClick={() => setOpenIndex(openIndex === index ? -1 : index)}
                                />
                            </motion.div>
                        ))
                    ) : (
                        <div className="text-center py-12 text-text-secondary">
                            Tidak ditemukan hasil untuk "{searchQuery}"
                        </div>
                    )}
                </div>

                <motion.div
                    initial={{ opacity: 0 }}
                    animate={{ opacity: 1 }}
                    transition={{ delay: 0.8 }}
                    className="text-center mt-20"
                >
                    <p className="text-text-secondary mb-4">Masih punya pertanyaan lain?</p>
                    <a href="mailto:support@kostin.com" className="btn ghost">Hubungi Support</a>
                </motion.div>
            </div>
        </div>
    );
};

export default FAQPage;
