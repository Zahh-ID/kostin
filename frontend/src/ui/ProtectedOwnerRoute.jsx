import React, { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { currentUser } from '../api/client.js';

const ProtectedOwnerRoute = ({ children }) => {
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

        if (user.role !== 'owner') {
          navigate('/dashboard', { replace: true });
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
          <div className="card-title">Memuat dashboard owner...</div>
          <div className="muted">Harap tunggu</div>
        </div>
      </div>
    );
  }

  return children;
};

export default ProtectedOwnerRoute;
