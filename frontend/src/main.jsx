import React from 'react';
import { createRoot } from 'react-dom/client';
import { BrowserRouter, Routes, Route } from 'react-router-dom';
import { HelmetProvider } from 'react-helmet-async';
import AppShell from './ui/AppShell.jsx';
import AuthShell from './ui/AuthShell.jsx';
import HomePage from './pages/HomePage.jsx';
import FeaturesPage from './pages/FeaturesPage.jsx';
import FAQPage from './pages/FAQPage.jsx';
import LoginPage from './pages/LoginPage.jsx';
import RegisterPage from './pages/RegisterPage.jsx';
import ForgotPasswordPage from './pages/ForgotPasswordPage.jsx';
import ResetPasswordPage from './pages/ResetPasswordPage.jsx';
import TenantDashboard from './pages/tenant/Dashboard.jsx';
import TenantContracts from './pages/tenant/Contracts.jsx';
import TenantWishlist from './pages/tenant/Wishlist.jsx';
import TenantSearch from './pages/tenant/Search.jsx';
import TenantTickets from './pages/tenant/Tickets.jsx';
import TenantNotifications from './pages/tenant/Notifications.jsx';
import TenantInvoices from './pages/tenant/Invoices.jsx';
import TenantApply from './pages/tenant/Apply.jsx';
import ProtectedTenantRoute from './ui/ProtectedTenantRoute.jsx';
import ProtectedAdminRoute from './ui/ProtectedAdminRoute.jsx';
import ProtectedOwnerRoute from './ui/ProtectedOwnerRoute.jsx';
import AdminDashboard from './pages/admin/Dashboard.jsx';
import AdminModerations from './pages/admin/Moderations.jsx';
import AdminTickets from './pages/admin/Tickets.jsx';
import AdminUsers from './pages/admin/Users.jsx';
import OwnerDashboard from './pages/owner/Dashboard.jsx';
import OwnerProperties from './pages/owner/Properties.jsx';
import OwnerPropertyDetail from './pages/owner/PropertyDetail.jsx';
import OwnerAddProperty from './pages/owner/AddProperty.jsx';
import WebhookSimulator from './pages/admin/WebhookSimulator.jsx';

import OwnerContracts from './pages/owner/Contracts.jsx';
import OwnerContractTerminations from './pages/owner/ContractTerminations.jsx';
import OwnerPayments from './pages/owner/Payments.jsx';
import OwnerTickets from './pages/owner/Tickets.jsx';
import OwnerApplications from './pages/owner/Applications.jsx';
import './styles.css';

const root = document.getElementById('root');

createRoot(root).render(
  <React.StrictMode>
    <HelmetProvider>
      <BrowserRouter>
        <Routes>
          <Route element={<AppShell />}>
            <Route index element={<HomePage />} />
            <Route path="/features" element={<FeaturesPage />} />
            <Route path="/faq" element={<FAQPage />} />
            <Route
              path="/dashboard"
              element={
                <ProtectedTenantRoute>
                  <TenantDashboard />
                </ProtectedTenantRoute>
              }
            />
            <Route
              path="/tenant/contracts"
              element={
                <ProtectedTenantRoute>
                  <TenantContracts />
                </ProtectedTenantRoute>
              }
            />
            <Route
              path="/tenant/wishlist"
              element={
                <ProtectedTenantRoute>
                  <TenantWishlist />
                </ProtectedTenantRoute>
              }
            />
            <Route
              path="/tenant/invoices"
              element={
                <ProtectedTenantRoute>
                  <TenantInvoices />
                </ProtectedTenantRoute>
              }
            />
            <Route path="/search" element={<TenantSearch />} />
            <Route
              path="/tenant/apply/:propertyId"
              element={
                <ProtectedTenantRoute>
                  <TenantApply />
                </ProtectedTenantRoute>
              }
            />
            <Route
              path="/tenant/properties/:propertyId/apply"
              element={
                <ProtectedTenantRoute>
                  <TenantApply />
                </ProtectedTenantRoute>
              }
            />
            <Route
              path="/tenant/tickets"
              element={
                <ProtectedTenantRoute>
                  <TenantTickets />
                </ProtectedTenantRoute>
              }
            />
            <Route
              path="/notifications"
              element={
                <ProtectedTenantRoute>
                  <TenantNotifications />
                </ProtectedTenantRoute>
              }
            />
            <Route
              path="/owner"
              element={
                <ProtectedOwnerRoute>
                  <OwnerDashboard />
                </ProtectedOwnerRoute>
              }
            />
            <Route
              path="/owner/properties"
              element={
                <ProtectedOwnerRoute>
                  <OwnerProperties />
                </ProtectedOwnerRoute>
              }
            />
            <Route
              path="/owner/properties/create"
              element={
                <ProtectedOwnerRoute>
                  <OwnerAddProperty />
                </ProtectedOwnerRoute>
              }
            />
            <Route
              path="/owner/properties/:id"
              element={
                <ProtectedOwnerRoute>
                  <OwnerPropertyDetail />
                </ProtectedOwnerRoute>
              }
            />

            <Route
              path="/owner/contracts"
              element={
                <ProtectedOwnerRoute>
                  <OwnerContracts />
                </ProtectedOwnerRoute>
              }
            />
            <Route
              path="/owner/contract-terminations"
              element={
                <ProtectedOwnerRoute>
                  <OwnerContractTerminations />
                </ProtectedOwnerRoute>
              }
            />
            <Route
              path="/owner/payments"
              element={
                <ProtectedOwnerRoute>
                  <OwnerPayments />
                </ProtectedOwnerRoute>
              }
            />
            <Route
              path="/owner/tickets"
              element={
                <ProtectedOwnerRoute>
                  <OwnerTickets />
                </ProtectedOwnerRoute>
              }
            />
            <Route
              path="/owner/applications"
              element={
                <ProtectedOwnerRoute>
                  <OwnerApplications />
                </ProtectedOwnerRoute>
              }
            />
            <Route
              path="/admin"
              element={
                <ProtectedAdminRoute>
                  <AdminDashboard />
                </ProtectedAdminRoute>
              }
            />
            <Route
              path="/admin/moderations"
              element={
                <ProtectedAdminRoute>
                  <AdminModerations />
                </ProtectedAdminRoute>
              }
            />
            <Route
              path="/admin/tickets"
              element={
                <ProtectedAdminRoute>
                  <AdminTickets />
                </ProtectedAdminRoute>
              }
            />
            <Route
              path="/admin/users"
              element={
                <ProtectedAdminRoute>
                  <AdminUsers />
                </ProtectedAdminRoute>
              }
            />
            <Route
              path="/admin/simulator/webhook"
              element={
                <ProtectedAdminRoute>
                  <WebhookSimulator />
                </ProtectedAdminRoute>
              }
            />
          </Route>
          <Route element={<AuthShell />}>
            <Route path="/login" element={<LoginPage />} />
            <Route path="/register" element={<RegisterPage />} />
            <Route path="/forgot-password" element={<ForgotPasswordPage />} />
            <Route path="/reset-password" element={<ResetPasswordPage />} />
            <Route path="/auth/google/callback" element={<GoogleCallbackPage />} />
          </Route>
        </Routes>
      </BrowserRouter>
    </HelmetProvider>
  </React.StrictMode>,
);
