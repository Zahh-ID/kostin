import { useState, useEffect } from "react";
import { Navbar } from "./components/Navbar";
import { HomePage } from "./components/HomePage";
import { PropertyDetail } from "./components/PropertyDetail";
import { LoginPage } from "./components/LoginPage";
import { RegisterPage } from "./components/RegisterPage";
import { SetupInstructions } from "./components/SetupInstructions";
import { MidtransSetupInstructions } from "./components/MidtransSetupInstructions";
import { TenantDashboard } from "./components/TenantDashboard";
import { OwnerDashboard } from "./components/OwnerDashboard";
import { AdminDashboard } from "./components/AdminDashboard";
import { TenantInvoicesPage } from "./components/TenantInvoicesPage";
import { OwnerInvoicesPage } from "./components/OwnerInvoicesPage";
import { AdminInvoicesPage } from "./components/AdminInvoicesPage";
import { TenantContractsPage } from "./components/TenantContractsPage";
import { OwnerContractsPage } from "./components/OwnerContractsPage";
import { PaymentCTAShowcase } from "./components/PaymentCTAShowcase";
import { ProfilePage } from "./components/ProfilePage";
import { ManualPaymentVerification } from "./components/ManualPaymentVerification";
import { ProfileSidebarShowcase } from "./components/ProfileSidebarShowcase";
import { BrowseKostPage } from "./components/BrowseKostPage";
import { PropertyManagementPage } from "./components/PropertyManagementPage";
import { WishlistPage } from "./components/WishlistPage";
import { SavedSearchesPage } from "./components/SavedSearchesPage";
import { ChatPage } from "./components/ChatPage";
import { TicketingPage } from "./components/TicketingPage";
import { ApplyRentalPage } from "./components/ApplyRentalPage";
import { Toaster } from "./components/ui/sonner";
import { toast } from "sonner@2.0.3";
import { createClient } from "./utils/supabase/client";
import { projectId } from "./utils/supabase/info";

type UserRole = 'guest' | 'tenant' | 'owner' | 'admin';

interface User {
  id: string;
  name: string;
  email: string;
  role: UserRole;
}

export default function App() {
  const [currentPath, setCurrentPath] = useState('/');
  const [user, setUser] = useState<User | null>(null);
  const [loading, setLoading] = useState(true);
  const [supabase] = useState(() => createClient());

  // Check for existing session on mount
  useEffect(() => {
    const checkSession = async () => {
      try {
        const { data: { session } } = await supabase.auth.getSession();
        if (session?.user) {
          setUser({
            id: session.user.id,
            name: session.user.user_metadata?.name || session.user.email?.split('@')[0] || 'User',
            email: session.user.email || '',
            role: (session.user.user_metadata?.role as UserRole) || 'tenant',
          });
        }
      } catch (error) {
        console.error('Error checking session:', error);
      } finally {
        setLoading(false);
      }
    };

    checkSession();
    
    // Listen for auth changes
    const { data: { subscription } } = supabase.auth.onAuthStateChange((_event, session) => {
      if (session?.user) {
        setUser({
          id: session.user.id,
          name: session.user.user_metadata?.name || session.user.email?.split('@')[0] || 'User',
          email: session.user.email || '',
          role: (session.user.user_metadata?.role as UserRole) || 'tenant',
        });
      } else {
        setUser(null);
      }
    });

    return () => subscription.unsubscribe();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  const handleNavigate = (path: string) => {
    setCurrentPath(path);
    window.scrollTo(0, 0);
  };

  const handleLogin = async (email: string, password: string, role: string) => {
    try {
      const { data, error } = await supabase.auth.signInWithPassword({
        email,
        password,
      });

      if (error) throw error;

      if (data.user) {
        const userRole = (data.user.user_metadata?.role as UserRole) || role as UserRole;
        setUser({
          id: data.user.id,
          name: data.user.user_metadata?.name || email.split('@')[0],
          email: data.user.email || email,
          role: userRole,
        });

        toast.success(`Selamat datang, ${data.user.user_metadata?.name || email}!`);
        
        // Navigate to role-specific dashboard
        if (userRole === 'tenant') handleNavigate('/tenant');
        else if (userRole === 'owner') handleNavigate('/owner');
        else if (userRole === 'admin') handleNavigate('/admin');
      }
    } catch (error: any) {
      console.error('Login error:', error);
      toast.error(error.message || 'Gagal masuk. Periksa email dan password Anda.');
    }
  };

  const handleRegister = async (name: string, email: string, password: string, role: string) => {
    try {
      // Use server endpoint to create user with proper role assignment
      const response = await fetch(
        `https://${projectId}.supabase.co/functions/v1/make-server-dbd6b95a/auth/signup`,
        {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({ name, email, password, role }),
        }
      );

      const result = await response.json();

      if (!response.ok) {
        throw new Error(result.error || 'Gagal mendaftar');
      }

      toast.success('Registrasi berhasil! Silakan login.');
      handleNavigate('/login');
    } catch (error: any) {
      console.error('Registration error:', error);
      toast.error(error.message || 'Gagal mendaftar. Silakan coba lagi.');
    }
  };

  const handleGoogleAuth = async () => {
    try {
      const { error } = await supabase.auth.signInWithOAuth({
        provider: 'google',
        options: {
          redirectTo: window.location.origin,
        },
      });

      if (error) throw error;
    } catch (error: any) {
      console.error('Google auth error:', error);
      toast.error(error.message || 'Gagal masuk dengan Google. Pastikan Google OAuth telah dikonfigurasi di Supabase.');
    }
  };

  const handleLogout = async () => {
    try {
      await supabase.auth.signOut();
      setUser(null);
      toast.success('Anda telah keluar');
      handleNavigate('/');
    } catch (error: any) {
      console.error('Logout error:', error);
      toast.error('Gagal keluar');
    }
  };

  const renderPage = () => {
    // Public routes
    if (currentPath === '/') {
      return <HomePage onNavigate={handleNavigate} />;
    }
    
    if (currentPath.startsWith('/property/')) {
      const propertyId = currentPath.split('/')[2];
      return <PropertyDetail propertyId={propertyId} onNavigate={handleNavigate} />;
    }

    if (currentPath.startsWith('/apply-rental/')) {
      const propertyId = currentPath.split('/')[2];
      return <ApplyRentalPage propertyId={propertyId} onNavigate={handleNavigate} userRole={user?.role || 'guest'} />;
    }

    if (currentPath === '/login') {
      return <LoginPage onLogin={handleLogin} onGoogleAuth={handleGoogleAuth} onNavigate={handleNavigate} />;
    }

    if (currentPath === '/register') {
      return (
        <RegisterPage
          onRegister={handleRegister}
          onGoogleAuth={handleGoogleAuth}
          onNavigate={handleNavigate}
        />
      );
    }

    if (currentPath === '/faq') {
      return (
        <div className="min-h-screen bg-gray-50">
          <div className="container mx-auto px-4 py-12">
            <h1 className="mb-8">Frequently Asked Questions</h1>
            <div className="max-w-3xl space-y-6">
              <div className="bg-white p-6 rounded-lg shadow">
                <h3 className="mb-2">Bagaimana cara menyewa kos?</h3>
                <p className="text-gray-600">
                  Anda dapat mencari kos melalui halaman beranda, memilih kos yang sesuai, dan mengajukan sewa setelah login.
                </p>
              </div>
              <div className="bg-white p-6 rounded-lg shadow">
                <h3 className="mb-2">Apa saja metode pembayaran yang tersedia?</h3>
                <p className="text-gray-600">
                  Kami mendukung pembayaran melalui QRIS untuk kemudahan dan keamanan transaksi.
                </p>
              </div>
              <div className="bg-white p-6 rounded-lg shadow">
                <h3 className="mb-2">Bagaimana jika pembayaran terlambat?</h3>
                <p className="text-gray-600">
                  Setiap properti memiliki kebijakan grace period dan denda keterlambatan yang berbeda. Silakan cek detail kontrak Anda.
                </p>
              </div>
            </div>
          </div>
        </div>
      );
    }

    if (currentPath === '/setup-oauth') {
      return <SetupInstructions />;
    }

    if (currentPath === '/setup-midtrans') {
      return <MidtransSetupInstructions />;
    }

    if (currentPath === '/showcase-payment-cta') {
      return <PaymentCTAShowcase onNavigate={handleNavigate} />;
    }

    if (currentPath === '/showcase-profile-sidebar') {
      return <ProfileSidebarShowcase onNavigate={handleNavigate} />;
    }

    if (currentPath === '/browse-kost') {
      return <BrowseKostPage onNavigate={handleNavigate} />;
    }

    // Protected routes - require login
    if (!user) {
      handleNavigate('/login');
      toast.error('Silakan login terlebih dahulu');
      return null;
    }

    // Tenant routes
    if (currentPath === '/tenant' && user.role === 'tenant') {
      return <TenantDashboard onNavigate={handleNavigate} />;
    }

    if (currentPath === '/tenant/contracts' && user.role === 'tenant') {
      return <TenantContractsPage onNavigate={handleNavigate} />;
    }

    if (currentPath === '/tenant/invoices' && user.role === 'tenant') {
      return <TenantInvoicesPage onNavigate={handleNavigate} />;
    }

    // Owner routes
    if (currentPath === '/owner' && user.role === 'owner') {
      return <OwnerDashboard onNavigate={handleNavigate} />;
    }

    if (currentPath === '/owner/invoices' && user.role === 'owner') {
      return <OwnerInvoicesPage onNavigate={handleNavigate} />;
    }

    if (currentPath === '/owner/properties' && user.role === 'owner') {
      return <PropertyManagementPage onNavigate={handleNavigate} />;
    }

    if (currentPath === '/owner/contracts' && user.role === 'owner') {
      return <OwnerContractsPage onNavigate={handleNavigate} />;
    }

    if (currentPath === '/owner/manual-payments' && user.role === 'owner') {
      return <ManualPaymentVerification onNavigate={handleNavigate} />;
    }

    if (currentPath === '/owner/tasks' && user.role === 'owner') {
      return (
        <div className="min-h-screen bg-gray-50">
          <div className="container mx-auto px-4 py-8">
            <h1 className="mb-6">Tugas Fasilitas</h1>
            <div className="bg-white p-8 rounded-lg shadow text-center">
              <p className="text-gray-600">Halaman tugas fasilitas dalam pengembangan</p>
            </div>
          </div>
        </div>
      );
    }

    // Admin routes
    if (currentPath === '/admin' && user.role === 'admin') {
      return <AdminDashboard onNavigate={handleNavigate} />;
    }

    if (currentPath === '/admin/invoices' && user.role === 'admin') {
      return <AdminInvoicesPage onNavigate={handleNavigate} />;
    }

    if (currentPath === '/admin/manual-payments' && user.role === 'admin') {
      return <ManualPaymentVerification onNavigate={handleNavigate} />;
    }

    if (currentPath === '/admin/moderations' && user.role === 'admin') {
      return (
        <div className="min-h-screen bg-gray-50">
          <div className="container mx-auto px-4 py-8">
            <h1 className="mb-6">Moderasi Listing</h1>
            <div className="bg-white p-8 rounded-lg shadow text-center">
              <p className="text-gray-600">Halaman moderasi dalam pengembangan</p>
            </div>
          </div>
        </div>
      );
    }

    if (currentPath === '/admin/users' && user.role === 'admin') {
      return (
        <div className="min-h-screen bg-gray-50">
          <div className="container mx-auto px-4 py-8">
            <h1 className="mb-6">Kelola Pengguna</h1>
            <div className="bg-white p-8 rounded-lg shadow text-center">
              <p className="text-gray-600">Halaman kelola pengguna dalam pengembangan</p>
            </div>
          </div>
        </div>
      );
    }

    if (currentPath === '/admin/settings' && user.role === 'admin') {
      return (
        <div className="min-h-screen bg-gray-50">
          <div className="container mx-auto px-4 py-8">
            <h1 className="mb-6">Pengaturan Sistem</h1>
            <div className="bg-white p-8 rounded-lg shadow text-center">
              <p className="text-gray-600">Halaman pengaturan sistem dalam pengembangan</p>
            </div>
          </div>
        </div>
      );
    }

    // Shared routes
    if (currentPath === '/profile') {
      return <ProfilePage onNavigate={handleNavigate} userRole={user.role} />;
    }

    if (currentPath === '/wishlist') {
      return <WishlistPage onNavigate={handleNavigate} />;
    }

    if (currentPath === '/saved-searches') {
      return <SavedSearchesPage onNavigate={handleNavigate} />;
    }

    if (currentPath === '/chat') {
      return <ChatPage onNavigate={handleNavigate} />;
    }

    if (currentPath === '/tickets') {
      return <TicketingPage onNavigate={handleNavigate} userRole={user.role} />;
    }

    if (currentPath === '/settings/notifications') {
      return (
        <div className="min-h-screen bg-gray-50">
          <div className="container mx-auto px-4 py-8">
            <h1 className="mb-6">Pengaturan Notifikasi</h1>
            <div className="bg-white p-8 rounded-lg shadow text-center">
              <p className="text-gray-600">Halaman pengaturan notifikasi dalam pengembangan</p>
            </div>
          </div>
        </div>
      );
    }

    // 404
    return (
      <div className="min-h-screen bg-gray-50 flex items-center justify-center">
        <div className="text-center">
          <h1 className="mb-4">404 - Halaman Tidak Ditemukan</h1>
          <button
            onClick={() => handleNavigate('/')}
            className="text-blue-600 hover:underline"
          >
            Kembali ke Beranda
          </button>
        </div>
      </div>
    );
  };

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-gray-50">
        <div className="text-center">
          <div className="w-16 h-16 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
          <p className="text-gray-600">Memuat...</p>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen">
      <Navbar
        role={user?.role || 'guest'}
        userName={user?.name}
        onNavigate={handleNavigate}
        onLogout={handleLogout}
      />
      {renderPage()}
      <Toaster />
    </div>
  );
}
