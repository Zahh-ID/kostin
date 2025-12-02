import React, { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { currentUser } from '../api/client.js';

const ProtectedAdminRoute = ({ children }) => {
  const [loading, setLoading] = useState(true);
  const navigate = useNavigate();

  useEffect(() => {
    currentUser()
      .then((user) => {
        if (!user || user.role !== 'admin') {
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
      <div className="auth">
        <div className="card placeholder" style={{ textAlign: 'center' }}>
          <div className="card-title">Memuat admin dashboard...</div>
          <div className="muted">Harap tunggu</div>
        </div>
      </div>
    );
  }

  return children;
};

export default ProtectedAdminRoute;
