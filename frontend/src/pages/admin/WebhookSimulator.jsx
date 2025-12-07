import React, { useState } from 'react';
import { motion } from 'framer-motion';
import { FiActivity, FiCheck, FiAlertCircle, FiPlay } from 'react-icons/fi';
import { simulateWebhook, fetchPendingPayments } from '../../api/client';

const WebhookSimulator = () => {
    const [formData, setFormData] = useState({
        order_id: '',
        transaction_status: 'settlement',
    });
    const [loading, setLoading] = useState(false);
    const [result, setResult] = useState(null);
    const [error, setError] = useState(null);
    const [pendingOrders, setPendingOrders] = useState([]);

    React.useEffect(() => {
        const loadOrders = async () => {
            try {
                const orders = await fetchPendingPayments();
                setPendingOrders(orders);
            } catch (err) {
                console.error('Failed to load pending orders:', err);
            }
        };
        loadOrders();
    }, []);

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        setResult(null);
        setError(null);

        try {
            const response = await simulateWebhook(formData);
            setResult(response);
        } catch (err) {
            setError(err.response?.data?.message || 'Failed to simulate webhook');
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="page min-h-screen bg-bg relative overflow-x-hidden pt-28 pb-20">
            <div className="fixed inset-0 pointer-events-none z-0">
                <div className="absolute top-[-20%] right-[-10%] w-[800px] h-[800px] bg-primary/5 rounded-full blur-[120px] opacity-50" />
                <div className="absolute inset-0 bg-[url('/noise.svg')] opacity-[0.02] mix-blend-overlay" />
            </div>

            <div className="container relative z-10 max-w-2xl mx-auto px-6">
                <motion.div
                    initial={{ opacity: 0, y: 20 }}
                    animate={{ opacity: 1, y: 0 }}
                    className="bg-surface/50 backdrop-blur-xl border border-white/5 rounded-[2rem] p-8"
                >
                    <div className="flex items-center gap-4 mb-8">
                        <div className="p-3 rounded-2xl bg-primary/20 text-primary">
                            <FiActivity className="text-2xl" />
                        </div>
                        <div>
                            <h1 className="text-2xl font-display font-bold text-white">Webhook Simulator</h1>
                            <p className="text-text-secondary">Manually trigger payment status updates</p>
                        </div>
                    </div>

                    <form onSubmit={handleSubmit} className="space-y-6">
                        <div>
                            <label className="block text-sm font-medium text-text-secondary mb-2">Order ID</label>
                            <select
                                value={formData.order_id}
                                onChange={(e) => setFormData({ ...formData, order_id: e.target.value })}
                                className="w-full bg-bg/50 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all"
                                required
                            >
                                <option value="">Select a pending order...</option>
                                {pendingOrders.map(order => (
                                    <option key={order.id} value={order.order_id}>
                                        {order.order_id} - {new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(order.amount)}
                                    </option>
                                ))}
                            </select>
                            <p className="text-xs text-text-tertiary mt-2">Select a pending payment to simulate.</p>
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-text-secondary mb-2">Transaction Status</label>
                            <select
                                value={formData.transaction_status}
                                onChange={(e) => setFormData({ ...formData, transaction_status: e.target.value })}
                                className="w-full bg-bg/50 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all"
                            >
                                <option value="settlement">Settlement (Success)</option>
                                <option value="capture">Capture (Success)</option>
                                <option value="pending">Pending</option>
                                <option value="deny">Deny (Failed)</option>
                                <option value="cancel">Cancel (Failed)</option>
                                <option value="expire">Expire (Failed)</option>
                            </select>
                        </div>

                        <button
                            type="submit"
                            disabled={loading}
                            className="w-full btn primary py-4 rounded-xl font-bold flex items-center justify-center gap-2"
                        >
                            {loading ? (
                                <span className="animate-spin w-5 h-5 border-2 border-black border-t-transparent rounded-full" />
                            ) : (
                                <>
                                    <FiPlay /> Trigger Webhook
                                </>
                            )}
                        </button>
                    </form>

                    {result && (
                        <motion.div
                            initial={{ opacity: 0, height: 0 }}
                            animate={{ opacity: 1, height: 'auto' }}
                            className="mt-8 p-4 rounded-xl bg-green-500/10 border border-green-500/20 text-green-400 flex items-start gap-3"
                        >
                            <FiCheck className="mt-1 shrink-0" />
                            <div>
                                <p className="font-bold">Success!</p>
                                <p className="text-sm opacity-90">{result.message}</p>
                            </div>
                        </motion.div>
                    )}

                    {error && (
                        <motion.div
                            initial={{ opacity: 0, height: 0 }}
                            animate={{ opacity: 1, height: 'auto' }}
                            className="mt-8 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 flex items-start gap-3"
                        >
                            <FiAlertCircle className="mt-1 shrink-0" />
                            <div>
                                <p className="font-bold">Error</p>
                                <p className="text-sm opacity-90">{error}</p>
                            </div>
                        </motion.div>
                    )}
                </motion.div>
            </div>
        </div>
    );
};

export default WebhookSimulator;
