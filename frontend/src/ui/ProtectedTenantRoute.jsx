import React, { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { currentUser } from '../api/client.js';

const ProtectedTenantRoute = ({ children }) => {
  const [loading, setLoading] = useState(true);
  const navigate = useNavigate();

  useEffect(() => {
    currentUser()
      .then((user) => {
        if (!user) {
          navigate('/login', { replace: true });
          return;
        }

        if (user.role === 'admin') {
          navigate('/admin', { replace: true });
          return;
        }

        if (user.role === 'owner') {
          navigate('/owner', { replace: true });
          return;
        }

        if (user.role !== 'tenant') {
          navigate('/login', { replace: true });
          return;
        }

        setLoading(false);
      })
      .catch(() => {
        navigate('/login', { replace: true });
      });
  }, [navigate]);

  if (loading) {
    return (
      <div className="fixed inset-0 bg-bg flex items-center justify-center z-50">
        <div className="text-center">
          <div className="w-12 h-12 border-4 border-primary/30 border-t-primary rounded-full animate-spin mx-auto mb-4" />
          <h2 className="text-xl font-display font-bold text-white mb-1">Memuat...</h2>
          <p className="text-text-secondary text-sm">Harap tunggu sebentar</p>
        </div>
      </div>
    );
  }

  return children;
};

export default ProtectedTenantRoute;
