import axios from 'axios';

export const api = axios.create({
  baseURL: (import.meta.env.VITE_API_BASE_URL || '') + '/api',
  withCredentials: true,
  headers: {
    Accept: 'application/json',
  },
  xsrfCookieName: 'XSRF-TOKEN',
  xsrfHeaderName: 'X-XSRF-TOKEN',
});

export const ensureCsrfCookie = () => axios.get(
  (import.meta.env.VITE_API_BASE_URL || '') + '/sanctum/csrf-cookie',
  { withCredentials: true }
);

export const login = async (payload) => {
  await ensureCsrfCookie();
  const response = await api.post('/v1/auth/login', payload);

  return response.data;
};

export const register = async (payload) => {
  await ensureCsrfCookie();
  const response = await api.post('/v1/auth/register', payload);

  return response.data;
};

export const logout = () => api.post('/v1/auth/logout');

export const currentUser = async () => {
  try {
    const response = await api.get('/v1/auth/me');
    return response.data;
  } catch (error) {
    return null;
  }
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
  await ensureCsrfCookie();
  const response = await api.post('/v1/tenant/tickets', payload);

  return response.data?.data ?? response.data;
};

export const fetchTenantWishlist = async () => {
  const response = await api.get('/v1/tenant/wishlist');

  return response.data?.data ?? [];
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
  await ensureCsrfCookie();
  const response = await api.post('/v1/owner/properties', payload);

  return response.data;
};

export const updateOwnerProperty = async (id, payload) => {
  await ensureCsrfCookie();
  const response = await api.put(`/v1/owner/properties/${id}`, payload);

  return response.data;
};

export const submitOwnerProperty = async (id) => {
  await ensureCsrfCookie();
  const response = await api.post(`/v1/owner/properties/${id}/submit`);

  return response.data;
};

export const withdrawOwnerProperty = async (id) => {
  await ensureCsrfCookie();
  const response = await api.post(`/v1/owner/properties/${id}/withdraw`);

  return response.data;
};

export const deleteOwnerProperty = async (id) => {
  await ensureCsrfCookie();
  const response = await api.delete(`/v1/owner/properties/${id}`);

  return response.data;
};

export const uploadOwnerPropertyPhoto = async (id, file) => {
  await ensureCsrfCookie();
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

export const fetchOwnerTickets = async () => {
  const response = await api.get('/v1/owner/tickets');

  return response.data?.data ?? [];
};

export const updateOwnerTicket = async (ticketId, payload) => {
  await ensureCsrfCookie();
  const response = await api.put(`/v1/owner/tickets/${ticketId}`, payload);

  return response.data?.data ?? response.data;
};

export const createOwnerRoom = async (payload) => {
  await ensureCsrfCookie();
  const response = await api.post('/v1/owner/rooms', payload);

  return response.data;
};

export const fetchPropertyRooms = async (propertyId) => {
  const response = await api.get(`/v1/owner/properties/${propertyId}/rooms`);
  return response.data;
};

export const createPropertyRoomsBulk = async (propertyId, payload) => {
  await ensureCsrfCookie();
  const response = await api.post(`/v1/owner/properties/${propertyId}/rooms/bulk`, payload);
  return response.data;
};

export const updateOwnerRoom = async (id, payload) => {
  await ensureCsrfCookie();
  const response = await api.put(`/v1/owner/rooms/${id}`, payload);
  return response.data;
};

export const deleteOwnerRoom = async (id) => {
  await ensureCsrfCookie();
  const response = await api.delete(`/v1/owner/rooms/${id}`);
  return response.data;
};

export const createOwnerRoomType = async (payload) => {
  await ensureCsrfCookie();
  const response = await api.post('/v1/owner/room-types', payload);
  return response.data;
};

export const updateOwnerRoomType = async (id, payload) => {
  await ensureCsrfCookie();
  const response = await api.put(`/v1/owner/room-types/${id}`, payload);
  return response.data;
};

export const deleteOwnerRoomType = async (id) => {
  await ensureCsrfCookie();
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
  await ensureCsrfCookie();
  const response = await api.post(`/v1/admin/moderations/${propertyId}/approve`, payload);

  return response.data;
};

export const rejectAdminModeration = async (propertyId, payload) => {
  await ensureCsrfCookie();
  const response = await api.post(`/v1/admin/moderations/${propertyId}/reject`, payload);

  return response.data;
};

export const updateAdminTicket = async (ticketId, payload) => {
  await ensureCsrfCookie();
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
  await ensureCsrfCookie();
  const response = await api.post('/v1/tenant/applications', payload);

  return response.data;
};

export const initiateInvoicePayment = async (invoiceId) => {
  await ensureCsrfCookie();
  const response = await api.post(`/v1/tenant/invoices/${invoiceId}/pay`);

  return response.data;
};

export const checkInvoicePaymentStatus = async (invoiceId) => {
  await ensureCsrfCookie();
  const response = await api.post(`/v1/tenant/invoices/${invoiceId}/status`);

  return response.data;
};

export const submitManualPayment = async (invoiceId, formData) => {
  await ensureCsrfCookie();
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
  await ensureCsrfCookie();
  const response = await api.post('/v1/owner/wallet/withdraw', payload);
  return response.data;
};

export const approveOwnerApplication = async (id) => {
  await ensureCsrfCookie();
  const response = await api.post(`/v1/owner/applications/${id}/approve`);
  return response.data;
};

export const rejectOwnerApplication = async (id) => {
  await ensureCsrfCookie();
  const response = await api.post(`/v1/owner/applications/${id}/reject`);
  return response.data;
};

export const approveOwnerPayment = async (id) => {
  await ensureCsrfCookie();
  const response = await api.post(`/v1/owner/manual-payments/${id}/approve`);
  return response.data;
};

export const rejectOwnerPayment = async (id) => {
  await ensureCsrfCookie();
  const response = await api.post(`/v1/owner/manual-payments/${id}/reject`);
  return response.data;
};

export const terminateOwnerContract = async (id, reason) => {
  await ensureCsrfCookie();
  const response = await api.post(`/v1/owner/contracts/${id}/terminate`, { reason });
  return response.data;
};

export const requestContractTermination = async (id, payload) => {
  await ensureCsrfCookie();
  const response = await api.post(`/v1/tenant/contracts/${id}/terminate`, payload);
  return response.data;
};

export const suspendAdminUser = async (userId) => {
  await ensureCsrfCookie();
  const response = await api.post(`/v1/admin/users/${userId}/suspend`);
  return response.data;
};

export const activateAdminUser = async (userId) => {
  await ensureCsrfCookie();
  const response = await api.post(`/v1/admin/users/${userId}/activate`);
  return response.data;
};
