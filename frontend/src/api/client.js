import axios from 'axios';

export const api = axios.create({
  baseURL: (import.meta.env.VITE_API_BASE_URL || '') + '/api',
  headers: {
    Accept: 'application/json',
  },
});

// Add a request interceptor to inject the token
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('auth_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

export const login = async (payload) => {
  const response = await api.post('/v1/auth/login', payload);
  const { token, user } = response.data;

  localStorage.setItem('auth_token', token);
  return { user }; // Return user to match previous API contract
};

export const register = async (payload) => {
  const response = await api.post('/v1/auth/register', payload);
  const { token, user } = response.data;

  if (token) {
    localStorage.setItem('auth_token', token);
  }

  return { user };
};

export const forgotPassword = async (email) => {
  const response = await api.post('/v1/auth/forgot-password', { email });
  return response.data;
};

export const resetPassword = async (payload) => {
  const response = await api.post('/v1/auth/reset-password', payload);
  return response.data;
};

export const logout = async () => {
  try {
    await api.post('/v1/auth/logout');
  } finally {
    localStorage.removeItem('auth_token');
  }
};

export const currentUser = async () => {
  try {
    const response = await api.get('/v1/auth/me');
    const user = response.data;

    // Validate that we got a real user object, not HTML or empty object
    if (user && typeof user === 'object' && user.id) {
      return user;
    }
    return null;
  } catch (error) {
    return null;
  }
  return null;
}


export const updateUserRole = async (role) => {
  const response = await api.post('/v1/auth/role', { role });
  return response.data;
};

export const fetchProperties = async () => {
  const response = await api.get('/v1/properties');

  return response.data?.data ?? [];
};

export const fetchStats = async () => {
  const response = await api.get('/v1/stats');

  return response.data;
};

export const fetchTenantOverview = async () => {
  const response = await api.get('/v1/tenant/overview');

  return response.data;
};

export const fetchTenantTickets = async (limit = 10) => {
  const response = await api.get('/v1/tenant/tickets', { params: { limit } });

  return response.data?.data ?? [];
};

export const fetchTenantTicket = async (id) => {
  const response = await api.get(`/v1/tenant/tickets/${id}`);

  return response.data?.data ?? response.data;
};

export const createTenantTicket = async (payload) => {

  const response = await api.post('/v1/tenant/tickets', payload);

  return response.data?.data ?? response.data;
};

export const fetchTenantWishlist = async () => {
  const response = await api.get('/v1/tenant/wishlist');

  return response.data?.data ?? [];
};

export const addToWishlist = async (propertyId) => {
  const response = await api.post('/v1/tenant/wishlist', { property_id: propertyId });
  return response.data;
};

export const removeFromWishlist = async (propertyId) => {
  const response = await api.delete(`/v1/tenant/wishlist/${propertyId}`);
  return response.data;
};

export const searchTenantProperties = async (search) => {
  const response = await api.get('/v1/tenant/search', { params: { search } });

  return response.data?.data ?? [];
};

export const fetchTenantInvoices = async (page = 1) => {
  const response = await api.get('/v1/invoices', { params: { page } });

  return response.data?.data ?? [];
};

export const fetchTenantInvoice = async (id) => {
  const response = await api.get(`/v1/invoices/${id}`);

  return response.data?.data ?? response.data;
};

export const fetchTenantContracts = async ({ page = 1, per_page = 50 } = {}) => {
  const response = await api.get('/v1/tenant/contracts', { params: { page, per_page } });

  return response.data?.data ?? [];
};

export const fetchTenantContract = async (id) => {
  const response = await api.get(`/v1/tenant/contracts/${id}`);

  return response.data?.data ?? response.data;
};

export const fetchOwnerProperties = async () => {
  const response = await api.get('/v1/owner/properties');

  return response.data;
};

export const createOwnerProperty = async (payload) => {

  const response = await api.post('/v1/owner/properties', payload);

  return response.data;
};

export const updateOwnerProperty = async (id, payload) => {

  const response = await api.put(`/v1/owner/properties/${id}`, payload);

  return response.data;
};

export const submitOwnerProperty = async (id) => {

  const response = await api.post(`/v1/owner/properties/${id}/submit`);

  return response.data;
};

export const withdrawOwnerProperty = async (id) => {

  const response = await api.post(`/v1/owner/properties/${id}/withdraw`);

  return response.data;
};

export const deleteOwnerProperty = async (id) => {

  const response = await api.delete(`/v1/owner/properties/${id}`);

  return response.data;
};

export const uploadOwnerPropertyPhoto = async (id, file) => {

  const formData = new FormData();
  formData.append('photo', file);

  const response = await api.post(`/v1/owner/properties/${id}/photos`, formData, {
    headers: {
      'Content-Type': 'multipart/form-data',
    },
  });

  return response.data;
};

export const fetchOwnerDashboard = async () => {
  const response = await api.get('/v1/owner/dashboard');

  return response.data;
};

export const fetchOwnerRooms = async () => {
  const response = await api.get('/v1/owner/rooms');

  return response.data;
};

export const fetchOwnerTickets = async (params = {}) => {
  const response = await api.get('/v1/owner/tickets', { params });
  return response.data;
};

export const createOwnerTicket = async (data) => {
  const response = await api.post('/v1/owner/tickets', data);
  return response.data;
};

export const updateOwnerTicket = async (id, data) => {
  const response = await api.patch(`/v1/owner/tickets/${id}`, data);
  return response.data?.data ?? response.data;
};

export const createOwnerRoom = async (payload) => {

  const response = await api.post('/v1/owner/rooms', payload);

  return response.data;
};

export const fetchPropertyRooms = async (propertyId) => {
  const response = await api.get(`/v1/owner/properties/${propertyId}/rooms`);
  return response.data?.data ?? [];
};

export const createPropertyRoomsBulk = async (propertyId, payload) => {

  const response = await api.post(`/v1/owner/properties/${propertyId}/rooms/bulk`, payload);
  return response.data;
};

export const updateOwnerRoom = async (id, payload) => {

  const response = await api.put(`/v1/owner/rooms/${id}`, payload);
  return response.data;
};

export const deleteOwnerRoom = async (id) => {

  const response = await api.delete(`/v1/owner/rooms/${id}`);
  return response.data;
};

export const createOwnerRoomType = async (payload) => {

  const response = await api.post('/v1/owner/room-types', payload);
  return response.data;
};

export const updateOwnerRoomType = async (id, payload) => {

  const response = await api.put(`/v1/owner/room-types/${id}`, payload);
  return response.data;
};

export const deleteOwnerRoomType = async (id) => {

  const response = await api.delete(`/v1/owner/room-types/${id}`);
  return response.data;
};

export const fetchOwnerProperty = async (id) => {
  const response = await api.get(`/v1/owner/properties/${id}`);
  return response.data;
};

export const fetchAdminDashboard = async () => {
  const response = await api.get('/v1/admin/dashboard');

  return response.data;
};

export const fetchAdminModerations = async () => {
  const response = await api.get('/v1/admin/moderations');

  return response.data;
};

export const fetchAdminTickets = async () => {
  const response = await api.get('/v1/admin/tickets');

  return response.data;
};

export const fetchAdminUsers = async () => {
  const response = await api.get('/v1/admin/users');

  return response.data;
};

export const approveAdminModeration = async (propertyId, payload = {}) => {

  const response = await api.post(`/v1/admin/moderations/${propertyId}/approve`, payload);

  return response.data;
};

export const rejectAdminModeration = async (propertyId, payload) => {

  const response = await api.post(`/v1/admin/moderations/${propertyId}/reject`, payload);

  return response.data;
};

export const updateAdminTicket = async (ticketId, payload) => {

  const response = await api.put(`/v1/admin/tickets/${ticketId}`, payload);

  return response.data;
};

export const downloadTenantContractPdf = async (id) => {
  const response = await api.get(`/v1/tenant/contracts/${id}/pdf`, {
    responseType: 'blob',
  });

  return response.data;
};

export const searchTenantPropertiesApi = async ({ search = '', min_price, max_price, facilities = [] }) => {
  const response = await api.get('/v1/tenant/search', {
    params: {
      search,
      min_price,
      max_price,
      facilities: facilities.length ? facilities.join(',') : undefined,
    },
  });

  return response.data?.data ?? [];
};

export const fetchTenantProperty = async (id) => {
  const response = await api.get(`/v1/tenant/properties/${id}`);

  return response.data?.data ?? response.data;
};

export const submitRentalApplication = async (payload) => {

  const response = await api.post('/v1/tenant/applications', payload);

  return response.data;
};

export const initiateInvoicePayment = async (invoiceId) => {

  const response = await api.post(`/v1/tenant/invoices/${invoiceId}/pay`);

  return response.data;
};

export const checkInvoicePaymentStatus = async (invoiceId) => {

  const response = await api.post(`/v1/tenant/invoices/${invoiceId}/status`);

  return response.data;
};

export const submitManualPayment = async (invoiceId, formData) => {

  const response = await api.post(`/v1/tenant/invoices/${invoiceId}/manual-payment`, formData, {
    headers: {
      'Content-Type': 'multipart/form-data',
    },
  });

  return response.data;
};

// Owner Additional APIs
export const fetchOwnerContracts = async () => {
  const response = await api.get('/v1/owner/contracts');
  return response.data?.contracts ?? [];
};

export const fetchOwnerPayments = async () => {
  const response = await api.get('/v1/owner/manual-payments');
  return response.data?.data ?? [];
};

export const fetchOwnerApplications = async () => {
  const response = await api.get('/v1/owner/applications');
  return response.data?.data ?? [];
};

export const fetchOwnerWallet = async () => {
  const response = await api.get('/v1/owner/wallet');
  return response.data;
};

export const withdrawOwnerWallet = async (payload) => {

  const response = await api.post('/v1/owner/wallet/withdraw', payload);
  return response.data;
};

export const approveOwnerApplication = async (id, data = {}) => {

  const response = await api.post(`/v1/owner/applications/${id}/approve`, data);
  return response.data;
};

export const rejectOwnerApplication = async (id) => {

  const response = await api.post(`/v1/owner/applications/${id}/reject`);
  return response.data;
};

export const approveOwnerPayment = async (id) => {

  const response = await api.post(`/v1/owner/manual-payments/${id}/approve`);
  return response.data;
};

export const rejectOwnerPayment = async (id) => {

  const response = await api.post(`/v1/owner/manual-payments/${id}/reject`);
  return response.data;
};

export const terminateOwnerContract = async (id, reason) => {

  const response = await api.post(`/v1/owner/contracts/${id}/terminate`, { reason });
  return response.data;
};

export const requestContractTermination = async (id, payload) => {

  const response = await api.post(`/v1/tenant/contracts/${id}/terminate`, payload);
  return response.data;
};

export const suspendAdminUser = async (userId) => {

  const response = await api.post(`/v1/admin/users/${userId}/suspend`);
  return response.data;
};

export const activateAdminUser = async (userId) => {

  const response = await api.post(`/v1/admin/users/${userId}/activate`);
  return response.data;
};

export const simulateWebhook = async (payload) => {
  const response = await api.post('/v1/admin/webhook/midtrans', payload);
  return response.data;
};

export const fetchPendingPayments = async () => {
  const response = await api.get('/v1/admin/simulator/orders');
  return response.data?.data ?? [];
};

export const fetchGoogleAuthUrl = async () => {
  const response = await api.get('/v1/auth/google/redirect');
  return response.data.url;
};

export const handleGoogleCallback = async (searchParams) => {
  const response = await api.get(`/v1/auth/google/callback${searchParams}`);
  return response.data;
};
