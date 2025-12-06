import React from 'react';
import { motion } from 'framer-motion';
import { FiMail, FiPhone, FiMapPin, FiSend } from 'react-icons/fi';

const ContactPage = () => {
    return (
        <div className="page pt-32 pb-20">
            <div className="container">
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-24">
                    {/* Contact Info */}
                    <motion.div
                        initial={{ opacity: 0, x: -20 }}
                        animate={{ opacity: 1, x: 0 }}
                    >
                        <h1 className="hero-title mb-6 text-4xl">
                            Hubungi <span>Kami</span>
                        </h1>
                        <p className="text-text-secondary text-lg mb-12 leading-relaxed">
                            Punya pertanyaan atau butuh bantuan? Tim kami siap membantu Anda. Jangan ragu untuk menghubungi kami melalui formulir atau kontak di bawah ini.
                        </p>

                        <div className="space-y-8">
                            <div className="flex items-start gap-4">
                                <div className="w-12 h-12 rounded-xl bg-surface-highlight border border-border flex items-center justify-center flex-shrink-0 text-primary">
                                    <FiMail className="text-xl" />
                                </div>
                                <div>
                                    <h3 className="font-bold text-lg mb-1">Email</h3>
                                    <p className="text-text-secondary mb-1">Pertanyaan umum & support</p>
                                    <a href="mailto:hello@kostin.id" className="text-primary hover:underline">hello@kostin.id</a>
                                </div>
                            </div>

                            <div className="flex items-start gap-4">
                                <div className="w-12 h-12 rounded-xl bg-surface-highlight border border-border flex items-center justify-center flex-shrink-0 text-primary">
                                    <FiPhone className="text-xl" />
                                </div>
                                <div>
                                    <h3 className="font-bold text-lg mb-1">Telepon</h3>
                                    <p className="text-text-secondary mb-1">Senin - Jumat, 09:00 - 17:00</p>
                                    <a href="tel:+622112345678" className="text-primary hover:underline">+62 21 1234 5678</a>
                                </div>
                            </div>

                            <div className="flex items-start gap-4">
                                <div className="w-12 h-12 rounded-xl bg-surface-highlight border border-border flex items-center justify-center flex-shrink-0 text-primary">
                                    <FiMapPin className="text-xl" />
                                </div>
                                <div>
                                    <h3 className="font-bold text-lg mb-1">Kantor</h3>
                                    <p className="text-text-secondary">
                                        Jl. Teknologi No. 10<br />
                                        Jakarta Selatan, 12345<br />
                                        Indonesia
                                    </p>
                                </div>
                            </div>
                        </div>
                    </motion.div>

                    {/* Contact Form */}
                    <motion.div
                        initial={{ opacity: 0, x: 20 }}
                        animate={{ opacity: 1, x: 0 }}
                        transition={{ delay: 0.2 }}
                        className="card p-8"
                    >
                        <h2 className="text-2xl font-bold font-display mb-6">Kirim Pesan</h2>
                        <form className="space-y-6" onSubmit={(e) => e.preventDefault()}>
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div className="space-y-2">
                                    <label className="text-sm font-medium text-text-secondary">Nama Depan</label>
                                    <input type="text" className="input w-full" placeholder="John" required minLength={2} />
                                </div>
                                <div className="space-y-2">
                                    <label className="text-sm font-medium text-text-secondary">Nama Belakang</label>
                                    <input type="text" className="input w-full" placeholder="Doe" required minLength={2} />
                                </div>
                            </div>

                            <div className="space-y-2">
                                <label className="text-sm font-medium text-text-secondary">Email</label>
                                <input type="email" className="input w-full" placeholder="john@example.com" required />
                            </div>

                            <div className="space-y-2">
                                <label className="text-sm font-medium text-text-secondary">Subjek</label>
                                <select className="input w-full" required>
                                    <option value="">Pilih Subjek</option>
                                    <option>Pertanyaan Umum</option>
                                    <option>Support Teknis</option>
                                    <option>Kerjasama</option>
                                    <option>Lainnya</option>
                                </select>
                            </div>

                            <div className="space-y-2">
                                <label className="text-sm font-medium text-text-secondary">Pesan</label>
                                <textarea className="input w-full h-32 resize-none" placeholder="Tulis pesan Anda di sini..." required minLength={10} maxLength={1000}></textarea>
                            </div>

                            <button type="submit" className="btn primary w-full justify-center">
                                <FiSend /> Kirim Pesan
                            </button>
                        </form>
                    </motion.div>
                </div>
            </div>
        </div>
    );
};

export default ContactPage;
