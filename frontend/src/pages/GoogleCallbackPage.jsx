import React, { useEffect, useState } from 'react';
import { useNavigate, useLocation } from 'react-router-dom';
import { motion } from 'framer-motion';
import axios from 'axios';

const GoogleCallbackPage = () => {
    const navigate = useNavigate();
    const location = useLocation();
    const [error, setError] = useState('');

    useEffect(() => {
        const handleCallback = async () => {
            const searchParams = new URLSearchParams(location.search);
            const code = searchParams.get('code');
            const state = searchParams.get('state');

            if (!code) {
                setError('No authorization code found.');
                return;
            }

            try {
                // Exchange code for token via backend
                // Note: We use the full URL for the callback to the backend, passing the query params
                const response = await axios.get(`${import.meta.env.VITE_API_BASE_URL}/v1/auth/google/callback${location.search}`, {
                    withCredentials: true
                });

                const { token, user } = response.data;

                // Store token
                localStorage.setItem('token', token);
                localStorage.setItem('user', JSON.stringify(user));

                // Redirect based on role
                if (user.role === 'admin') {
                    window.location.href = '/admin';
                } else if (user.role === 'owner') {
                    window.location.href = '/owner';
                } else {
                    window.location.href = '/dashboard';
                }

            } catch (err) {
                console.error('Google Auth Error:', err);
                setError(err.response?.data?.message || 'Authentication failed.');
                setTimeout(() => {
                    navigate('/login');
                }, 3000);
            }
        };

        handleCallback();
    }, [location, navigate]);

    return (
        <div className="min-h-screen flex items-center justify-center bg-background">
            <div className="text-center">
                {error ? (
                    <motion.div
                        initial={{ opacity: 0, scale: 0.9 }}
                        animate={{ opacity: 1, scale: 1 }}
                        className="text-red-500"
                    >
                        <h2 className="text-2xl font-bold mb-2">Login Failed</h2>
                        <p>{error}</p>
                        <p className="text-sm mt-4 text-text-secondary">Redirecting to login...</p>
                    </motion.div>
                ) : (
                    <motion.div
                        initial={{ opacity: 0 }}
                        animate={{ opacity: 1 }}
                    >
                        <div className="w-16 h-16 border-4 border-primary border-t-transparent rounded-full animate-spin mx-auto mb-6"></div>
                        <h2 className="text-xl font-medium text-text-primary">Verifying Google Account...</h2>
                    </motion.div>
                )}
            </div>
        </div>
    );
};

export default GoogleCallbackPage;
