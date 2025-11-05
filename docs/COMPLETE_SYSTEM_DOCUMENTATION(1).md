# 📚 DOKUMENTASI LENGKAP SISTEM KOSTIN

**Platform Manajemen Kos Terpadu dengan 3 Role Utama**

---

## 📋 DAFTAR ISI

1. [Overview Sistem](#overview-sistem)
2. [Arsitektur Sistem](#arsitektur-sistem)
3. [Role & Access Control](#role--access-control)
4. [Dokumentasi Halaman](#dokumentasi-halaman)
5. [Fitur-Fitur Utama](#fitur-fitur-utama)
6. [Integrasi Backend](#integrasi-backend)
7. [Komponen UI](#komponen-ui)
8. [Alur Proses Bisnis](#alur-proses-bisnis)

---

## 🎯 OVERVIEW SISTEM

### Nama Aplikasi
**KostIn** - Platform Manajemen Kos Berbasis Web

### Deskripsi
Sistem manajemen kos yang komprehensif dengan tiga role utama (Tenant/Penyewa, Owner/Pemilik, Admin), dilengkapi dengan fitur pencarian properti, manajemen kontrak, sistem pembayaran terintegrasi (QRIS + Manual), live chat, ticketing/moderasi, dan fitur enterprise-grade lainnya.

### Tech Stack
- **Frontend**: React, TypeScript, Tailwind CSS v4.0
- **UI Components**: shadcn/ui
- **Backend**: Supabase (Database, Auth, Edge Functions)
- **Payment Gateway**: Midtrans Core API (QRIS + Virtual Account)
- **Real-time**: Polling-based (3 detik untuk chat)
- **State Management**: React useState/useEffect

### Demo Users
```
Tenant:  tenant@demo.com  | Password: demo123
Owner:   owner@demo.com   | Password: demo123
Admin:   admin@demo.com   | Password: demo123
```

---

## 🏗️ ARSITEKTUR SISTEM

### Frontend Architecture
```
/App.tsx (Main Router)
├── Navbar (Role-based navigation)
├── Public Pages
│   ├── HomePage
│   ├── BrowseKostPage
│   ├── PropertyDetail
│   ├── ApplyRentalPage
│   ├── LoginPage
│   └── RegisterPage
├── Tenant Pages
│   ├── TenantDashboard
│   ├── TenantInvoicesPage
│   └── TenantContractsPage
├── Owner Pages
│   ├── OwnerDashboard
│   ├── OwnerInvoicesPage
│   ├── OwnerContractsPage
│   ├── PropertyManagementPage
│   └── ManualPaymentVerification
├── Admin Pages
│   ├── AdminDashboard
│   ├── AdminInvoicesPage
│   └── ManualPaymentVerification
└── Shared Pages
    ├── ProfilePage
    ├── WishlistPage
    ├── SavedSearchesPage
    ├── ChatPage
    └── TicketingPage
```

### Backend Architecture (Supabase Edge Functions)
```
/supabase/functions/server/
├── index.tsx (Hono server + routing)
├── payment.tsx (Midtrans integration)
└── kv_store.tsx (Key-Value database utility)
```

### Database
- **Main Table**: `kv_store_dbd6b95a` (Key-Value Store untuk semua data)
- **Auth**: Supabase Auth dengan Google OAuth support
- **Storage**: Private buckets untuk upload bukti pembayaran

---

## 👥 ROLE & ACCESS CONTROL

### 1️⃣ GUEST (Belum Login)
**Akses:**
- ✅ HomePage - Browsing kos terbaru
- ✅ BrowseKostPage - Pencarian dengan filter lengkap
- ✅ PropertyDetail - Melihat detail properti
- ✅ ApplyRentalPage - Mengisi form pengajuan (redirect ke login setelah submit)
- ✅ LoginPage & RegisterPage
- ✅ FAQ Page

**Tidak Bisa:**
- ❌ Dashboard
- ❌ Payment/Invoice
- ❌ Chat
- ❌ Wishlist
- ❌ Profile

---

### 2️⃣ TENANT (Penyewa Kos)
**Akses Penuh:**
- ✅ Dashboard dengan payment CTA & stats
- ✅ Invoice Management
- ✅ Contract Management
- ✅ Browse & Search Kos
- ✅ Apply Rental
- ✅ Payment (QRIS + Manual Upload)
- ✅ Wishlist
- ✅ Saved Searches & Alerts
- ✅ Live Chat dengan owner
- ✅ Ticketing/Support
- ✅ Profile Management

**Fitur Khusus:**
- 4 jenis Payment CTA:
  1. **Payment Banner** (Sticky top)
  2. **Quick Pay Card** (Dashboard)
  3. **Floating Button** (Bottom right saat scroll)
  4. **Inline Buttons** (Di list invoice)

---

### 3️⃣ OWNER (Pemilik Kos)
**Akses Penuh:**
- ✅ Dashboard dengan property overview & stats
- ✅ Property Management (CRUD)
- ✅ Invoice Management (semua tenant)
- ✅ Contract Management
- ✅ Manual Payment Verification
- ✅ Task/Facility Management
- ✅ Live Chat dengan tenant
- ✅ Ticketing/Support
- ✅ Profile Management

**Fitur Khusus:**
- Property CRUD dengan form lengkap
- Upload & verify manual payment
- Monitoring okupansi & revenue
- Quick actions untuk kontrak & properti baru

---

### 4️⃣ ADMIN (Platform Administrator)
**Akses Penuh:**
- ✅ Dashboard dengan system-wide stats
- ✅ Property Moderation (Approve/Reject)
- ✅ User Management
- ✅ All Invoices Overview
- ✅ Manual Payment Verification
- ✅ Ticketing System (Kanban Board)
- ✅ System Settings
- ✅ Activity Log

**Fitur Khusus:**
- Kanban board untuk ticketing
- Moderation queue untuk properti baru
- User analytics & statistics
- System-wide monitoring

---

## 📄 DOKUMENTASI HALAMAN

### 🏠 PUBLIC PAGES

#### 1. HomePage (`/`)
**Layout:**
```
┌─────────────────────────────────────┐
│ Navbar (Guest Mode)                 │
├─────────────────────────────────────┤
│ HERO SECTION                        │
│ ┌─────────────────────────────────┐ │
│ │ "Temukan Kos Impian Anda"       │ │
│ │ Search Bar (Lokasi, Harga)      │ │
│ └─────────────────────────────────┘ │
├─────────────────────────────────────┤
│ PROPERTY LISTINGS (Grid 4 kolom)   │
│ ┌────┐ ┌────┐ ┌────┐ ┌────┐       │
│ │Kos1│ │Kos2│ │Kos3│ │Kos4│       │
│ └────┘ └────┘ └────┘ └────┘       │
├─────────────────────────────────────┤
│ FEATURES SECTION                    │
│ [Pencarian Mudah] [Lokasi] [Aman]  │
└─────────────────────────────────────┘
```

**Komponen:**
- **Hero Section**: Gradient blue background dengan search bar
  - Input lokasi dengan icon MapPin
  - Input harga maksimal
  - Button "Cari Kos" → navigate ke `/browse-kost`
  
- **Property Cards** (Mock data 4 properti):
  - Gambar properti (dari Unsplash)
  - Nama & lokasi dengan icon MapPin
  - Badge "X tersedia" (hijau)
  - Fasilitas (max 3, sisanya tersembunyi)
  - Harga per bulan
  - Button "Lihat Detail" → navigate ke `/property/{id}`

- **Why Section**: 3 kolom feature highlights
  - Pencarian Mudah
  - Lokasi Strategis
  - Pembayaran Aman

**Fitur:**
- ✅ Responsive grid (4 col desktop, 2 tablet, 1 mobile)
- ✅ Hover effects pada cards
- ✅ Click anywhere pada card untuk detail
- ✅ Dynamic price formatting (Rp X.XXX.XXX)

---

#### 2. BrowseKostPage (`/browse-kost`)
**Layout:**
```
┌─────────────────────────────────────┐
│ Navbar                              │
├─────────────────────────────────────┤
│ HEADER (Sticky)                     │
│ ┌─────────────────────────────────┐ │
│ │ [Search] [City] [Filter Badge]  │ │
│ │ Active Filters: [X] [X]         │ │
│ │ Sort by: [Dropdown]             │ │
│ └─────────────────────────────────┘ │
├─────────────────────────────────────┤
│ RESULTS (Grid 3 kolom)             │
│ Menampilkan 6 kos                   │
│ ┌────┐ ┌────┐ ┌────┐              │
│ │Kos1│ │Kos2│ │Kos3│              │
│ └────┘ └────┘ └────┘              │
│ ┌────┐ ┌────┐ ┌────┐              │
│ │Kos4│ │Kos5│ │Kos6│              │
│ └────┘ └────┘ └────┘              │
└─────────────────────────────────────┘
```

**Filter Sheet (Side Panel):**
```
┌─────────────────────────┐
│ Filter Pencarian    [X] │
├─────────────────────────┤
│ Range Harga             │
│ [━━━━●━━━━━━━━]        │
│ Rp 0 - Rp 5.000.000    │
├─────────────────────────┤
│ Tipe Kos                │
│ ☐ Putra                 │
│ ☐ Putri                 │
│ ☐ Campur                │
├─────────────────────────┤
│ Fasilitas               │
│ ☐ AC                    │
│ ☐ Wi-Fi                 │
│ ☐ Kamar Mandi Dalam     │
│ ☐ Parkir                │
│ ☐ Dapur                 │
│ ☐ Kulkas                │
├─────────────────────────┤
│ [Hapus Filter]          │
└─────────────────────────┘
```

**Komponen:**
- **Search Bar**: 
  - Input text untuk nama/lokasi
  - Select untuk kota (Bogor, Jakarta, Depok, Bandung)
  - Button filter dengan badge counter

- **Filter System**:
  - Slider untuk range harga (Rp 0 - 5jt)
  - Checkbox untuk tipe (Putra/Putri/Campur)
  - Checkbox untuk fasilitas (AC, WiFi, dll)
  - Button clear all filters

- **Active Filters Display**:
  - Badge untuk setiap filter aktif
  - Icon X untuk remove individual filter
  - Counter total filter aktif

- **Sort Options**:
  - Paling Relevan
  - Harga Terendah
  - Harga Tertinggi
  - Rating Tertinggi
  - Paling Banyak Tersedia

- **Property Cards**:
  - Image dengan hover scale effect
  - Badge tipe (Putra/Putri/Campur) di kiri atas
  - Badge "X tersedia" di kanan atas
  - Rating dengan icon star (gold)
  - Fasilitas (4 badge + counter)
  - Harga per bulan

**Fitur:**
- ✅ Real-time filtering & sorting
- ✅ Responsive grid (3 col desktop, 2 tablet, 1 mobile)
- ✅ Sticky header saat scroll
- ✅ Empty state jika tidak ada hasil
- ✅ Filter persistence dalam URL params (future)

---

#### 3. PropertyDetail (`/property/{id}`)
**Layout:**
```
┌─────────────────────────────────────┐
│ Navbar                              │
├─────────────────────────────────────┤
│ [← Kembali]                         │
├─────────────────────────────────────┤
│ IMAGE GALLERY                       │
│ ┌──────────────┐ ┌────┐            │
│ │              │ │Img2│            │
│ │  Main Image  │ ├────┤            │
│ │              │ │Img3│            │
│ └──────────────┘ └────┘            │
├─────────────────────────────────────┤
│ CONTENT (2 kolom)                   │
│ ┌────────────┐ ┌──────────────┐   │
│ │ Info       │ │ SIDEBAR      │   │
│ │ Fasilitas  │ │ Rp 1.200.000 │   │
│ │ Peraturan  │ │ [Ajukan Sewa]│   │
│ │ Tipe Kamar │ │ [Hub Pemilik]│   │
│ └────────────┘ └──────────────┘   │
└─────────────────────────────────────┘
```

**Sections:**

1. **Property Info Card**:
   - Nama properti (H1)
   - Alamat lengkap dengan icon MapPin
   - Deskripsi properti

2. **Fasilitas Card**:
   - Grid 4 kolom
   - Icon + label untuk setiap fasilitas
   - Icons: Wifi, Wind (AC), Droplet (Kamar Mandi), Car (Parkir)

3. **Peraturan Card**:
   - List dengan icon X (red)
   - Contoh:
     - Dilarang hewan peliharaan
     - Tamu wajib lapor
     - Jam malam 22.00
     - Dilarang merokok

4. **Tipe Kamar Card**:
   - List 3 tipe kamar
   - Setiap tipe menampilkan:
     - Nama (H3)
     - Ukuran (3x4 m)
     - Badge "X tersedia" atau "Penuh"
     - Harga + Deposit
     - Fasilitas kamar (badges dengan icon Check)
     - Separator antar tipe

5. **Sidebar (Sticky)**:
   - "Mulai dari" text
   - Harga terkecil (text-3xl, blue)
   - "per bulan" caption
   - **Button "Ajukan Sewa"** dengan icon Calendar → navigate ke `/apply-rental/{id}`
   - Button "Hubungi Pemilik" (outline)
   - Separator
   - Info kontrak:
     - Lama sewa minimum: 1 bulan
     - Pembayaran: Bulanan
     - Deposit: 1x sewa

**Fitur:**
- ✅ Image gallery dengan layout responsive
- ✅ Sticky sidebar saat scroll
- ✅ Room type comparison
- ✅ Direct apply rental button
- ✅ Price formatting Indonesian locale

---

#### 4. ApplyRentalPage (`/apply-rental/{id}`)
**Layout:**
```
┌─────────────────────────────────────┐
│ Navbar                              │
├─────────────────────────────────────┤
│ [← Kembali] Ajukan Sewa             │
│ Kos Melati - Jl. Dramaga            │
├─────────────────────────────────────┤
│ PROGRESS BAR                        │
│ ●━━━○━━━○━━━○                      │
│ 1     2   3   4                     │
│ Pilih Data Konfir Selesai          │
│ Kamar Diri masi                     │
├─────────────────────────────────────┤
│ [ALERT] Guest Warning (jika guest)  │
├─────────────────────────────────────┤
│ STEP CONTENT                        │
│ (Dynamic based on current step)     │
└─────────────────────────────────────┘
```

**4-Step Wizard:**

**STEP 1: Pilih Kamar**
```
┌─────────────────────────────────┐
│ Pilih Tipe Kamar                │
├─────────────────────────────────┤
│ ○ Single AC - Kamar Mandi Dalam │
│   Ukuran: 3x4 m                 │
│   [5 tersedia]                  │
│   Rp 1.200.000 + Deposit        │
├─────────────────────────────────┤
│ ○ Single AC - Kamar Mandi Luar  │
│   Ukuran: 3x3 m                 │
│   [3 tersedia]                  │
│   Rp 1.000.000 + Deposit        │
├─────────────────────────────────┤
│ ○ Single Non-AC [PENUH]         │
│   (Disabled)                    │
├─────────────────────────────────┤
│            [Lanjutkan] →        │
└─────────────────────────────────┘
```
- RadioGroup untuk memilih tipe kamar
- Disabled jika kamar penuh
- Show price + deposit
- Validation: harus pilih 1 tipe

**STEP 2: Data Diri**
```
┌─────────────────────────────────┐
│ Data Diri Penyewa               │
├─────────────────────────────────┤
│ Informasi Pribadi:              │
│ [Nama Lengkap *]  [No KTP *]   │
│ [Email *]         [No Telp *]   │
│ [Pekerjaan/Status *]            │
├─────────────────────────────────┤
│ Kontak Darurat:                 │
│ [Nama Kontak *]   [No Telp *]   │
├─────────────────────────────────┤
│ Detail Sewa:                    │
│ [Tanggal Masuk *] [Durasi *]   │
├─────────────────────────────────┤
│ Catatan Tambahan (Optional)     │
│ [Textarea...]                   │
├─────────────────────────────────┤
│ [← Kembali]    [Lanjutkan] →   │
└─────────────────────────────────┘
```
- Form 2 kolom (responsive)
- Sections: Pribadi, Kontak Darurat, Detail Sewa
- Date picker dengan min date = today
- Select durasi: 1, 3, 6, 12 bulan
- Form validation sebelum lanjut

**STEP 3: Konfirmasi**
```
┌─────────────────────────────────┐
│ Konfirmasi Pengajuan            │
├─────────────────────────────────┤
│ Tipe Kamar:                     │
│ ┌─────────────────────────────┐ │
│ │ Single AC - Kamar Mandi     │ │
│ │ Ukuran: 3x4 m               │ │
│ │ Rp 1.200.000/bulan          │ │
│ └─────────────────────────────┘ │
├─────────────────────────────────┤
│ Data Penyewa:                   │
│ Nama: Ahmad Fauzi               │
│ KTP: 320123...                  │
│ Email: ahmad@...                │
│ ...                             │
├─────────────────────────────────┤
│ Rincian Pembayaran:             │
│ Sewa Bulan Pertama: 1.200.000   │
│ Deposit (1x sewa): 1.200.000    │
│ ───────────────────────────     │
│ Total: Rp 2.400.000             │
│ * Deposit dikembalikan          │
├─────────────────────────────────┤
│ ☑ Saya setujui syarat...        │
├─────────────────────────────────┤
│ [← Kembali]  [Kirim Pengajuan] │
└─────────────────────────────────┘
```
- Summary semua data
- Highlight payment calculation
- Terms checkbox (required)
- Disabled submit jika tidak agree

**STEP 4: Success**
```
┌─────────────────────────────────┐
│          ✅ SUCCESS             │
│                                 │
│ Pengajuan Berhasil Dikirim!     │
│                                 │
│ Pemilik akan menghubungi dalam  │
│ 1x24 jam                        │
│                                 │
│ ┌─────────────────────────────┐ │
│ │ Detail Pengajuan:           │ │
│ │ Properti: Kos Melati        │ │
│ │ Kamar: Single AC            │ │
│ │ Email: ahmad@...            │ │
│ └─────────────────────────────┘ │
│                                 │
│ Redirect in 3 seconds...        │
└─────────────────────────────────┘
```
- Success icon (green circle with check)
- Auto redirect:
  - Guest → `/login`
  - Tenant → `/tenant/contracts`

**Fitur:**
- ✅ Progress indicator dengan 4 steps
- ✅ Guest warning alert di semua steps
- ✅ Form validation per step
- ✅ Data persistence saat back/next
- ✅ Role-aware redirect setelah submit
- ✅ Disabled rooms handling

---

### 🔐 TENANT PAGES

#### 5. TenantDashboard (`/tenant`)
**Layout:**
```
┌─────────────────────────────────────┐
│ Navbar (Tenant)                     │
├─────────────────────────────────────┤
│ PAYMENT BANNER (Sticky, Jika ada)   │
│ ⚠ Tagihan Rp 1.200.000 jatuh tempo  │
│ 5 Nov (5 hari lagi) [Bayar Sekarang]│
├─────────────────────────────────────┤
│ Dashboard Penyewa                   │
│ Selamat datang, Ahmad Fauzi!        │
├─────────────────────────────────────┤
│ QUICK PAY CTA (Jika ada pending)    │
│ ⏰ Pembayaran Berikutnya            │
│ Rp 1.200.000 - Jatuh tempo 5 Nov   │
│ [Bayar Sekarang]                    │
├─────────────────────────────────────┤
│ STATS (3 cards)                     │
│ ┌──────┐ ┌──────┐ ┌──────┐        │
│ │  1   │ │ 12M  │ │  1   │        │
│ │Tagihan Dibayar Kontrak│        │
│ └──────┘ └──────┘ └──────┘        │
├─────────────────────────────────────┤
│ MAIN CONTENT (2 kolom)              │
│ ┌────────────┐ ┌──────────────┐   │
│ │ Tagihan    │ │ Info Kontrak │   │
│ │ Terbaru    │ │ Pembayaran   │   │
│ │ [List]     │ │ Berikutnya   │   │
│ └────────────┘ └──────────────┘   │
└─────────────────────────────────────┘
│ FLOATING PAY BUTTON (Bottom right)  │
│ [Rp 1.2M] Bayar → (Jika scroll down)│
└─────────────────────────────────────┘
```

**4 Jenis Payment CTA:**

1. **Payment Banner** (Top, Sticky):
   - Warna warning (yellow/orange)
   - Menampilkan: Amount, Due Date, Days Until Due
   - Button "Bayar Sekarang"
   - Muncul jika ada pending invoice

2. **Quick Pay Card**:
   - Card dengan gradient background
   - Icon calendar
   - Invoice month
   - Amount dengan format besar
   - Due date & days countdown
   - Button "Bayar Sekarang"

3. **Floating Pay Button**:
   - Fixed bottom-right
   - Rounded pill shape
   - Show amount
   - Icon wallet
   - Hanya muncul saat scroll > 100px

4. **Inline Buttons**:
   - Di setiap pending invoice di list
   - Small button dengan icon CreditCard
   - Langsung open payment dialog

**Stats Cards:**
- Tagihan Aktif (orange) - Count pending invoices
- Total Dibayar (green) - Sum of paid invoices
- Kontrak Aktif (blue) - Active contracts count

**Recent Invoices Section:**
- List 3 invoice terbaru
- Setiap item:
  - Icon status (CheckCircle green / Clock orange)
  - Month & Invoice ID
  - Amount
  - Badge status (Lunas/Menunggu)
  - Inline "Bayar" button jika pending
  - Click → navigate ke detail

**Contract Info Sidebar:**
- Nama properti & kamar
- Periode kontrak
- Biaya bulanan
- Button "Lihat Detail Kontrak"

**Next Payment Sidebar:**
- Due date dengan icon Calendar
- Amount dalam box biru
- Button "Bayar Sekarang"

**Payment Dialog (Midtrans):**
```
┌─────────────────────────────────┐
│ Pembayaran Tagihan          [X] │
├─────────────────────────────────┤
│ Selesaikan pembayaran tagihan   │
│ Anda melalui Midtrans Snap      │
├─────────────────────────────────┤
│ [MIDTRANS SNAP EMBED]           │
│ - QRIS                          │
│ - Virtual Account               │
│ - Gopay                         │
│ - dll                           │
└─────────────────────────────────┘
```

**Fitur:**
- ✅ 4 jenis payment CTA strategis
- ✅ Auto-calculate days until due
- ✅ Midtrans Snap integration
- ✅ Real-time payment status
- ✅ Responsive layout

---

### 🏢 OWNER PAGES

#### 6. OwnerDashboard (`/owner`)
**Layout:**
```
┌─────────────────────────────────────┐
│ Navbar (Owner)                      │
├─────────────────────────────────────┤
│ Dashboard Pemilik                   │
│ [+ Tambah Properti]                 │
├─────────────────────────────────────┤
│ STATS (4 cards)                     │
│ ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐  │
│ │  3  │ │ 24  │ │28.8M│ │ 82% │  │
│ │Props│ │Tenant Revenue Okupsi│  │
│ └─────┘ └─────┘ └─────┘ └─────┘  │
├─────────────────────────────────────┤
│ MAIN CONTENT (2 kolom)              │
│ ┌────────────┐ ┌──────────────┐   │
│ │ Properti   │ │ Quick Actions│   │
│ │ [List]     │ │ Tasks        │   │
│ │ Tagihan    │ │              │   │
│ │ Akan Datang│ │              │   │
│ └────────────┘ └──────────────┘   │
└─────────────────────────────────────┘
```

**Stats dengan Change Indicators:**
- Total Properti: 3 (+1 bulan ini)
- Total Penyewa: 24 (+3 dari bulan lalu)
- Pendapatan: Rp 28.8M (+12%)
- Okupansi: 82% (24/30 kamar terisi)

**Properties Overview:**
- List 3 properti
- Setiap item:
  - Nama & lokasi
  - Revenue per bulan
  - Occupancy bar (X/Y kamar, percentage)
  - Progress bar visual
  - Click → detail properti

**Upcoming Payments:**
- List tagihan akan jatuh tempo
- Setiap item:
  - Icon status (AlertTriangle red jika overdue)
  - Nama tenant & kamar
  - Amount
  - Badge due date / "Terlambat"

**Quick Actions Sidebar:**
- Button "Buat Kontrak Baru"
- Button "Tambah Properti"
- Button "Buat Tugas Baru"

**Tasks Sidebar:**
- List 3 tugas fasilitas
- Setiap task:
  - Judul task
  - Badge priority (High/Medium/Low)
  - Properti
  - Due date

**Fitur:**
- ✅ Revenue monitoring
- ✅ Occupancy tracking dengan visual
- ✅ Payment alerts
- ✅ Quick property creation
- ✅ Task management preview

---

#### 7. PropertyManagementPage (`/owner/properties`)
**Layout:**
```
┌─────────────────────────────────────┐
│ Navbar                              │
├─────────────────────────────────────┤
│ Kelola Properti                     │
│ Tambah dan kelola properti kos Anda │
├─────────────────────────────────────┤
│ [🔍 Search] [+ Tambah Properti]    │
├─────────────────────────────────────┤
│ PROPERTIES GRID (3 kolom)           │
│ ┌────┐ ┌────┐ ┌────┐              │
│ │Prop│ │Prop│ │Prop│              │
│ │  1 │ │  2 │ │  3 │              │
│ └────┘ └────┘ └────┘              │
└─────────────────────────────────────┘
```

**Property Card:**
```
┌─────────────────────────────────┐
│ Kos Melati Residence            │
│ [Aktif]                         │
│ 📍 Bogor                        │
├─────────────────────────────────┤
│ Harga: Rp 1.200.000/bulan       │
│ Kamar: 10/12 tersedia           │
│ Tipe: [Putra]                   │
├─────────────────────────────────┤
│ [👁 Lihat] [✏ Edit] [🗑 Hapus] │
└─────────────────────────────────┘
```

**Status Badges:**
- **Aktif** (hijau): Properti sudah disetujui & live
- **Menunggu Verifikasi** (kuning): Pending approval
- **Ditolak** (merah): Rejected by admin

**Add/Edit Dialog:**
```
┌─────────────────────────────────┐
│ Tambah Properti Baru        [X] │
├─────────────────────────────────┤
│ [Nama Properti *]               │
│ [Deskripsi]                     │
│ [Kota *]        [Tipe *]       │
│ [Alamat Lengkap *]              │
│ [Harga] [Tersedia] [Total]     │
├─────────────────────────────────┤
│ [Batal] [Tambah Properti]       │
└─────────────────────────────────┘
```

**Form Fields:**
- Nama Properti (required)
- Deskripsi (textarea)
- Kota (required)
- Tipe (select: Putra/Putri/Campur)
- Alamat lengkap (textarea, required)
- Harga per bulan (number, required)
- Kamar tersedia (number, required)
- Total kamar (number, required)

**Actions:**
- **Lihat** → Navigate ke PropertyDetail
- **Edit** → Open edit dialog dengan data
- **Hapus** → Confirm dialog → DELETE request

**Empty State:**
```
┌─────────────────────────────────┐
│         🏢                      │
│   Belum ada properti            │
│                                 │
│ Mulai tambahkan properti kos    │
│ Anda untuk ditampilkan          │
│                                 │
│ [+ Tambah Properti Pertama]     │
└─────────────────────────────────┘
```

**Fitur:**
- ✅ Search by name/city
- ✅ CRUD operations
- ✅ Status indicators
- ✅ Form validation
- ✅ Responsive grid
- ✅ Delete confirmation

---

### 👔 ADMIN PAGES

#### 8. AdminDashboard (`/admin`)
**Layout:**
```
┌─────────────────────────────────────┐
│ Navbar (Admin)                      │
├─────────────────────────────────────┤
│ Dashboard Admin                     │
│ Kelola dan moderasi platform KostIn │
├─────────────────────────────────────┤
│ STATS (4 cards)                     │
│ ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐  │
│ │ 156 │ │1234 │ │ 12  │ │450M │  │
│ │Props│ │Users│ │Pend │ │Trans│  │
│ └─────┘ └─────┘ └─────┘ └─────┘  │
├─────────────────────────────────────┤
│ MAIN CONTENT (2 kolom)              │
│ ┌────────────┐ ┌──────────────┐   │
│ │ Pending    │ │ Quick Actions│   │
│ │ Moderation │ │ System       │   │
│ │ Recent     │ │ Activity     │   │
│ │ Users      │ │              │   │
│ └────────────┘ └──────────────┘   │
└─────────────────────────────────────┘
```

**System-wide Stats:**
- Total Properti: 156 (+8 bulan ini)
- Total Pengguna: 1,234 (+42 bulan ini)
- Menunggu Moderasi: 12 (butuh review)
- Total Transaksi: Rp 450jt (bulan ini)

**Pending Moderation Section:**
- List properti pending approval
- Badge counter di header
- Setiap item:
  - Nama properti & lokasi
  - Nama owner
  - Jumlah kamar
  - Tanggal submit
  - Badge "Pending" (orange)
  - Actions:
    - Button "Tolak" (red, outline)
    - Button "Setujui" (green) dengan icon CheckCircle

**Recent Users Section:**
- List user baru
- Setiap item:
  - Avatar (icon User)
  - Nama & email
  - Badge role (Owner/Tenant)
  - Join date

**Quick Actions Sidebar:**
- Button "Review Moderasi"
- Button "Kelola Pengguna"
- Button "Pengaturan Sistem"

**System Activity Sidebar:**
- Timeline events
- Setiap event:
  - Icon (CheckCircle green / AlertCircle red / Clock blue)
  - Action description
  - Detail
  - Timestamp

**Fitur:**
- ✅ Platform-wide analytics
- ✅ Moderation queue
- ✅ User monitoring
- ✅ Activity logging
- ✅ Quick approve/reject

---

### 🤝 SHARED PAGES

#### 9. WishlistPage (`/wishlist`)
**Layout:**
```
┌─────────────────────────────────────┐
│ Navbar                              │
├─────────────────────────────────────┤
│ Wishlist Saya                       │
│ Properti kos yang Anda simpan       │
├─────────────────────────────────────┤
│ 3 properti dalam wishlist           │
├─────────────────────────────────────┤
│ WISHLIST GRID (3 kolom)             │
│ ┌────┐ ┌────┐ ┌────┐              │
│ │ ❤  │ │ ❤  │ │ ❤  │              │
│ └────┘ └────┘ └────┘              │
└─────────────────────────────────────┘
```

**Wishlist Card:**
```
┌─────────────────────────────────┐
│ Kos Melati Residence        ❤  │
│ 📍 Bogor                        │
├─────────────────────────────────┤
│ Harga: Rp 1.200.000/bulan       │
│ Kamar: 5/12 tersedia            │
│ Tipe: [Putra]                   │
├─────────────────────────────────┤
│ [👁 Lihat Detail] [🗑]         │
└─────────────────────────────────┘
```

**Features:**
- Heart icon (filled, red) untuk remove
- Click → remove with confirmation
- "Lihat Detail" button
- Trash button untuk delete

**Empty State:**
```
┌─────────────────────────────────┐
│         ❤                       │
│   Wishlist Anda kosong          │
│                                 │
│ Mulai simpan properti favorit   │
│                                 │
│ [🔍 Jelajahi Kos]               │
└─────────────────────────────────┘
```

**Fitur:**
- ✅ Add to wishlist from BrowseKost
- ✅ Remove from wishlist
- ✅ View property detail
- ✅ Backend integration
- ✅ Loading states

---

#### 10. SavedSearchesPage (`/saved-searches`)
**Layout:**
```
┌─────────────────────────────────────┐
│ Navbar                              │
├─────────────────────────────────────┤
│ Pencarian Tersimpan                 │
│ Simpan filter & dapatkan notifikasi │
├─────────────────────────────────────┤
│ SAVED SEARCHES LIST                 │
│ ┌─────────────────────────────────┐ │
│ │ "Kos Dekat IPB"       [🔔 Aktif]│ │
│ │ 🔧 Kota: Bogor • Harga: < 2M    │ │
│ │ Dibuat 5 Nov 2024               │ │
│ │ [🔍 Terapkan] [🗑]              │ │
│ └─────────────────────────────────┘ │
└─────────────────────────────────────┘
```

**Saved Search Item:**
- Nama custom untuk search
- Badge "Notifikasi Aktif" (hijau) jika enabled
- Filter summary (icon Filter)
- Created date
- Actions:
  - Button "Terapkan" → Navigate to browse with filters
  - Button Delete (trash icon)

**Filter Summary Format:**
- Kota: {city}
- Tipe: {type}
- Harga: Rp {min} - Rp {max}
- Fasilitas: {count} dipilih

**Empty State:**
```
┌─────────────────────────────────┐
│         💾                      │
│ Belum ada pencarian tersimpan   │
│                                 │
│ Simpan filter pencarian Anda    │
│                                 │
│ [🔍 Mulai Cari Kos]             │
└─────────────────────────────────┘
```

**Fitur:**
- ✅ Save search filters from BrowseKost
- ✅ Apply saved search (restore filters)
- ✅ Delete saved search
- ✅ Optional notification toggle
- ✅ Backend integration

---

#### 11. ChatPage (`/chat`)
**Layout:**
```
┌─────────────────────────────────────┐
│ Navbar                              │
├─────────────────────────────────────┤
│ Chat - Komunikasi dengan pemilik    │
├─────────────────────────────────────┤
│ ┌────────────┬──────────────────┐  │
│ │ CONV LIST  │ MESSAGES         │  │
│ │            │                  │  │
│ │ [👤 Owner] │ [Header]         │  │
│ │ Last msg.. │                  │  │
│ │            │ [Messages]       │  │
│ │ [👤 Owner] │                  │  │
│ │ Last msg.. │                  │  │
│ │            │ [Input + Send]   │  │
│ └────────────┴──────────────────┘  │
└─────────────────────────────────────┘
```

**Left Panel - Conversations (4 col):**
- List semua percakapan
- Setiap item:
  - Avatar
  - Nama lawan bicara
  - Last message preview
  - Highlight jika selected (blue background)
  
**Right Panel - Messages (8 col):**

**Header:**
- Avatar lawan bicara
- Nama & status online
- Button back (mobile)
- Button more options (...)

**Messages Area:**
- Scroll area dengan messages
- Setiap message:
  - Align right (sent) atau left (received)
  - Bubble dengan bg biru (sent) / gray (received)
  - Sender name (jika received)
  - Message content
  - Timestamp (HH:mm)
  - Auto scroll to bottom

**Input Area:**
- Button attach (Paperclip icon)
- Button image (Image icon)
- Text input
- Button send (disabled jika empty)
- Enter to send (Shift+Enter untuk newline)

**Empty State (No Conversation Selected):**
```
┌─────────────────────────────────┐
│         👤                      │
│ Pilih percakapan untuk          │
│ mulai chat                      │
└─────────────────────────────────┘
```

**Fitur:**
- ✅ Real-time chat (polling 3 detik)
- ✅ Conversation list
- ✅ Message bubbles (sent/received)
- ✅ Auto scroll to latest
- ✅ Timestamp formatting
- ✅ Backend integration
- ✅ Responsive (mobile shows one panel)

---

#### 12. TicketingPage (`/tickets`)

**Admin View - Kanban Board:**
```
┌─────────────────────────────────────┐
│ Navbar                              │
├─────────────────────────────────────┤
│ Ticketing & Moderasi                │
│ [+ Buat Tiket]                      │
├─────────────────────────────────────┤
│ KANBAN BOARD (5 kolom)              │
│ ┌────┬────┬────┬────┬────┐         │
│ │Open│Rev │Esc │Res │Rej │         │
│ │[3] │[2] │[1] │[5] │[1] │         │
│ ├────┼────┼────┼────┼────┤         │
│ │Card│Card│Card│Card│Card│         │
│ │Card│Card│    │Card│    │         │
│ │Card│    │    │Card│    │         │
│ └────┴────┴────┴────┴────┘         │
└─────────────────────────────────────┘
```

**Tenant/Owner View - List:**
```
┌─────────────────────────────────────┐
│ Navbar                              │
├─────────────────────────────────────┤
│ Ticketing & Moderasi                │
│ [+ Buat Tiket]                      │
├─────────────────────────────────────┤
│ TICKET LIST                         │
│ ┌─────────────────────────────────┐ │
│ │ Pembayaran tidak masuk [Open]   │ │
│ │ Saya sudah bayar tapi...        │ │
│ │ [Pembayaran] [Medium] #ABC123   │ │
│ │ 5 Nov 2024                      │ │
│ └─────────────────────────────────┘ │
└─────────────────────────────────────┘
```

**Kanban Columns:**
1. **Open** (Blue) - Tiket baru
2. **In Review** (Yellow) - Sedang ditinjau
3. **Escalated** (Red) - Di-eskalasi
4. **Resolved** (Green) - Selesai
5. **Rejected** (Gray) - Ditolak

**Ticket Card:**
```
┌─────────────────────────────────┐
│ Subject text...         [High]  │
│ [Teknis] #ABC123                │
│ Reporter Name                   │
└─────────────────────────────────┘
```

**Create Ticket Dialog:**
```
┌─────────────────────────────────┐
│ Buat Tiket Baru             [X] │
├─────────────────────────────────┤
│ [Kategori *]    [Prioritas *]   │
│ [Subjek *]                      │
│ [Deskripsi *]                   │
│                                 │
│ [Batal] [Buat Tiket]            │
└─────────────────────────────────┘
```

**Categories:**
- Teknis (Technical issues)
- Pembayaran (Payment issues)
- Konten (Content moderation)
- Abuse (Abuse report)

**Priorities:**
- Low (Gray)
- Medium (Blue)
- High (Orange)
- Urgent (Red)

**Ticket Detail Dialog:**
```
┌─────────────────────────────────┐
│ Subject Title          [Status] │
│ #ABC123 • Teknis • Reporter     │
├─────────────────────────────────┤
│ Deskripsi:                      │
│ Long description text...        │
├─────────────────────────────────┤
│ [Admin Actions] (if admin)      │
│ [Tinjau] [Eskalasi]             │
│ [Selesaikan] [Tolak]            │
├─────────────────────────────────┤
│ Timeline:                       │
│ • User created ticket           │
│ • Admin changed status...       │
│ • User commented...             │
├─────────────────────────────────┤
│ Tambah Komentar:                │
│ [Textarea] [Send]               │
└─────────────────────────────────┘
```

**Admin Actions:**
- **Tinjau** → Status: In Review
- **Eskalasi** → Status: Escalated
- **Selesaikan** → Status: Resolved (green)
- **Tolak** → Status: Rejected (red)

**Fitur:**
- ✅ Kanban board (admin)
- ✅ List view (tenant/owner)
- ✅ Create ticket
- ✅ Update status (admin)
- ✅ Comment system
- ✅ Timeline/events
- ✅ Category & priority badges
- ✅ Backend integration

---

## 🎨 FITUR-FITUR UTAMA

### 1. 🔐 Authentication System

**Google OAuth Integration:**
- Provider: Google
- Setup required: https://supabase.com/docs/guides/auth/social-login/auth-google
- Flow: signInWithOAuth → redirectTo
- Error handling: "Provider is not enabled"

**Email/Password Auth:**
- Signup: Server endpoint `/auth/signup`
- Login: Supabase `signInWithPassword`
- Logout: Supabase `signOut`
- Session: Auto-detect dengan `getSession()`
- Auth state listener: `onAuthStateChange`

**Role Assignment:**
- Set saat registration
- Stored di `user_metadata.role`
- Validated di server
- Persistent across sessions

**Protected Routes:**
```typescript
if (!user) {
  handleNavigate('/login');
  toast.error('Silakan login terlebih dahulu');
  return null;
}
```

---

### 2. 💳 Payment System

**Metode Pembayaran:**

**A. QRIS & E-Wallet (Midtrans Snap)**
- Integration: Midtrans Core API
- Flow:
  1. User click "Bayar"
  2. Frontend → Server `/payment/create-transaction`
  3. Server → Midtrans API
  4. Get `snap_token`
  5. Load Midtrans Snap embed
  6. User scan QRIS / pilih metode
  7. Callback: success/pending/error
  8. Update invoice status

**Snap Options:**
```javascript
{
  QRIS,
  GoPay,
  ShopeePay,
  BCA Virtual Account,
  BNI Virtual Account,
  BRI Virtual Account,
  Mandiri Bill,
  Permata VA,
  Alfamart,
  Indomaret
}
```

**B. Manual Transfer**
- User upload bukti transfer
- Format: JPG/PNG/PDF max 5MB
- Storage: Supabase private bucket
- Flow:
  1. Upload file
  2. Create manual payment record
  3. Owner/Admin verify
  4. Approve/Reject
  5. Update invoice status

**Payment CTA Strategies:**
1. **Banner** - Urgent, visible immediately
2. **Card** - Clear call to action
3. **Floating** - Always accessible
4. **Inline** - Contextual, di tempat invoice

**Invoice Statuses:**
- `pending` - Belum dibayar
- `paid` - Sudah lunas
- `verifying` - Manual payment, sedang diverifikasi
- `overdue` - Terlambat

---

### 3. 🏢 Property Management (Owner)

**CRUD Operations:**

**Create:**
```
POST /properties
Body: {
  name, description, address, city,
  type, pricePerMonth, availableRooms,
  totalRooms, facilities, images
}
```

**Read:**
```
GET /properties
Filter: ownerId === currentUserId
```

**Update:**
```
PUT /properties/{id}
Body: Same as create
```

**Delete:**
```
DELETE /properties/{id}
Confirmation required
```

**Property Statuses:**
- `pending_approval` - Menunggu moderasi admin
- `active` - Live & visible to tenants
- `rejected` - Ditolak admin
- `inactive` - Disabled by owner

**Form Validation:**
- Name: Required
- City: Required
- Type: Required (select)
- Address: Required
- Price: Required, numeric
- Available rooms: Required, numeric
- Total rooms: Required, numeric
- Images: Optional (future)
- Facilities: Optional (future)

---

### 4. ❤️ Wishlist & Saved Searches

**Wishlist:**
- Add/remove properti
- Stored per user
- Backend: `/wishlist` endpoints
- Show: name, location, price, availability
- Actions: View detail, Remove

**Saved Searches:**
- Save filter criteria
- Name the search
- Optional notification toggle
- Apply saved filters
- Backend: `/saved-searches` endpoints

**Filter Criteria Saved:**
```javascript
{
  name: "Kos Dekat IPB",
  filters: {
    search: "...",
    city: "Bogor",
    type: "putra",
    minPrice: 0,
    maxPrice: 2000000,
    facilities: ["ac", "wifi"]
  },
  notificationEnabled: true
}
```

---

### 5. 💬 Live Chat System

**Architecture:**
- Polling-based (interval 3 detik)
- No WebSocket (simplified)
- Supabase Edge Functions

**Endpoints:**
```
GET    /chat/conversations
GET    /chat/conversations/{id}/messages
POST   /chat/conversations/{id}/messages
```

**Features:**
- Conversation list
- Message bubbles (sent/received)
- Auto-scroll to latest
- Timestamp formatting
- Read/unread status (future)
- File attachments (future)

**Message Structure:**
```javascript
{
  id: "...",
  conversationId: "...",
  senderId: "...",
  senderName: "...",
  content: "...",
  type: "text",
  timestamp: "2024-11-04T..."
}
```

---

### 6. 🎫 Ticketing & Moderation

**Ticket Structure:**
```javascript
{
  id: "...",
  category: "technical|payment|content|abuse",
  subject: "...",
  description: "...",
  priority: "low|medium|high|urgent",
  status: "open|in_review|escalated|resolved|rejected",
  reporterId: "...",
  reporterName: "...",
  createdAt: "...",
  events: [...]
}
```

**Workflows:**

**User Flow:**
1. Create ticket
2. View ticket status
3. Add comments
4. Get notifications (future)

**Admin Flow:**
1. View kanban board
2. Move tickets between columns
3. Review ticket details
4. Change status
5. Add comments
6. Resolve/Reject

**Event Timeline:**
- Created
- Status changed
- Comment added
- Assigned (future)
- Resolved/Rejected

---

### 7. 📊 Dashboard Analytics

**Tenant Stats:**
- Tagihan aktif (count)
- Total dibayar (sum)
- Kontrak aktif (count)

**Owner Stats:**
- Total properti
- Total penyewa aktif
- Pendapatan bulan ini (sum)
- Tingkat okupansi (percentage)

**Admin Stats:**
- Total properti platform
- Total pengguna
- Pending moderasi
- Total transaksi

**Charts (Future Enhancement):**
- Revenue trend
- Occupancy trend
- User growth
- Payment success rate

---

## 🔌 INTEGRASI BACKEND

### Server Architecture

**Hono Web Server:**
```typescript
const app = new Hono();

app.use('*', cors({...}));
app.use('*', logger(console.log));

// Routes
app.post('/auth/signup', ...);
app.get('/properties', ...);
app.post('/payment/create-transaction', ...);
app.get('/wishlist', ...);
app.get('/chat/conversations', ...);
app.post('/tickets', ...);
...

Deno.serve(app.fetch);
```

**Base URL:**
```
https://{projectId}.supabase.co/functions/v1/make-server-dbd6b95a
```

**All routes prefixed:** `/make-server-dbd6b95a`

### Authentication in Requests

**Protected Endpoints:**
```typescript
const accessToken = request.headers.get('Authorization')?.split(' ')[1];
const { data: { user }, error } = await supabase.auth.getUser(accessToken);

if (!user?.id) {
  return new Response('Unauthorized', { status: 401 });
}
```

**Frontend Call:**
```typescript
const { data: { session } } = await supabase.auth.getSession();

fetch(url, {
  headers: {
    'Authorization': `Bearer ${session.access_token}`,
    'Content-Type': 'application/json'
  }
});
```

### Key-Value Store

**Utility:** `/supabase/functions/server/kv_store.tsx`

**Functions:**
- `get(key)` - Get single value
- `set(key, value)` - Set value
- `del(key)` - Delete key
- `mget(keys)` - Get multiple values
- `mset(entries)` - Set multiple
- `mdel(keys)` - Delete multiple
- `getByPrefix(prefix)` - Get all matching prefix

**Usage Pattern:**
```typescript
import * as kv from './kv_store.tsx';

// Save user preferences
await kv.set(`user:${userId}:preferences`, preferences);

// Get all user wishlists
const wishlists = await kv.getByPrefix(`wishlist:${userId}:`);

// Delete property
await kv.del(`property:${propertyId}`);
```

**Data Structure Examples:**
```
property:{propertyId} → {property object}
contract:{contractId} → {contract object}
invoice:{invoiceId} → {invoice object}
wishlist:{userId}:{propertyId} → {timestamp}
saved-search:{userId}:{searchId} → {search object}
chat:conversation:{conversationId} → {conversation object}
chat:message:{messageId} → {message object}
ticket:{ticketId} → {ticket object}
```

### Midtrans Core API Integration

KostIn menggunakan **Midtrans Core API** untuk pembayaran QRIS dengan auto-detection. Berbeda dengan Snap (popup), Core API memberikan kontrol penuh atas UI dan flow pembayaran.

---

#### 🔌 **1. Koneksi ke Midtrans Core API**

**Base URLs:**
```typescript
// Sandbox (Testing)
const SANDBOX_URL = "https://api.sandbox.midtrans.com/v2";

// Production
const PRODUCTION_URL = "https://api.midtrans.com/v2";
```

**Authentication:**
```typescript
// Server Key harus di-encode dengan Base64
const serverKey = Deno.env.get('MIDTRANS_SERVER_KEY');
const authString = btoa(serverKey + ':'); // Tambahkan ':' di akhir

// Header Authorization
headers: {
  'Authorization': `Basic ${authString}`,
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}
```

---

#### 🎯 **2. Create QRIS Transaction (Core API)**

**Endpoint:** `POST /v2/charge`

**Server Implementation** (`/supabase/functions/server/payment.tsx`):
```typescript
export async function createMidtransTransaction(params: CreateTransactionParams) {
  const serverKey = Deno.env.get('MIDTRANS_SERVER_KEY');
  
  if (!serverKey) {
    throw new Error('MIDTRANS_SERVER_KEY is not configured');
  }

  // Base64 encode server key dengan ':'
  const authString = btoa(serverKey + ':');

  // Pilih environment
  const midtransUrl = Deno.env.get('MIDTRANS_ENV') === 'production'
    ? 'https://api.midtrans.com/v2/charge'
    : 'https://api.sandbox.midtrans.com/v2/charge';

  // Payload untuk QRIS
  const payload = {
    payment_type: "qris",  // Spesifik untuk QRIS
    transaction_details: {
      order_id: params.orderId,      // Unique order ID
      gross_amount: params.amount,   // Total amount
    },
    customer_details: params.customerDetails,
    item_details: params.itemDetails,
  };

  // Call Midtrans API
  const response = await fetch(midtransUrl, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'Authorization': `Basic ${authString}`,
    },
    body: JSON.stringify(payload),
  });

  if (!response.ok) {
    const errorData = await response.json();
    throw new Error(errorData.error_messages?.join(', ') || 'Unknown error');
  }

  const data = await response.json();
  
  // Extract QRIS string dari response
  return {
    transaction_id: data.transaction_id,
    order_id: data.order_id,
    qris_string: data.actions?.find((action: any) => 
      action.name === 'generate-qr-code'
    )?.url || '',
    transaction_status: data.transaction_status,
    acquirer: data.acquirer,
  };
}
```

**Request Payload Structure:**
```json
{
  "payment_type": "qris",
  "transaction_details": {
    "order_id": "INV-2024-11-001",
    "gross_amount": 1200000
  },
  "customer_details": {
    "first_name": "Ahmad Fauzi",
    "email": "ahmad@email.com",
    "phone": "081234567890"
  },
  "item_details": [
    {
      "id": "ITEM1",
      "price": 1200000,
      "quantity": 1,
      "name": "Sewa Kos November 2024"
    }
  ]
}
```

**Response Structure:**
```json
{
  "status_code": "201",
  "status_message": "QRIS transaction is created",
  "transaction_id": "d4d6576e-26c0-4730-b45b-...",
  "order_id": "INV-2024-11-001",
  "merchant_id": "G812220370",
  "gross_amount": "1200000.00",
  "currency": "IDR",
  "payment_type": "qris",
  "transaction_time": "2024-11-04 10:30:00",
  "transaction_status": "pending",
  "fraud_status": "accept",
  "acquirer": "gopay",
  "actions": [
    {
      "name": "generate-qr-code",
      "method": "GET",
      "url": "https://api.sandbox.midtrans.com/v2/qris/d4d6576e-26c0-4730-b45b-.../qr-code"
    }
  ]
}
```

**Important Fields:**
- `qris_string`: URL untuk generate QR code
- `transaction_id`: ID transaksi dari Midtrans
- `order_id`: Order ID yang kita kirim
- `transaction_status`: Status transaksi (`pending`, `settlement`, dll)

---

#### 🔍 **3. Verify Transaction Status**

**Endpoint:** `GET /v2/{order_id}/status`

**Server Implementation:**
```typescript
export async function verifyMidtransTransaction(orderId: string) {
  const serverKey = Deno.env.get('MIDTRANS_SERVER_KEY');
  const authString = btoa(serverKey + ':');

  const midtransUrl = Deno.env.get('MIDTRANS_ENV') === 'production'
    ? `https://api.midtrans.com/v2/${orderId}/status`
    : `https://api.sandbox.midtrans.com/v2/${orderId}/status`;

  const response = await fetch(midtransUrl, {
    method: 'GET',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'Authorization': `Basic ${authString}`,
    },
  });

  if (!response.ok) {
    const errorData = await response.json();
    throw new Error(errorData.error_messages?.join(', '));
  }

  const data = await response.json();
  return {
    transaction_status: data.transaction_status,
    payment_type: data.payment_type,
    transaction_time: data.transaction_time,
    settlement_time: data.settlement_time,
    gross_amount: data.gross_amount,
  };
}
```

**Status Response:**
```json
{
  "status_code": "200",
  "status_message": "Success, transaction found",
  "transaction_id": "d4d6576e-26c0-4730-b45b-...",
  "order_id": "INV-2024-11-001",
  "gross_amount": "1200000.00",
  "payment_type": "qris",
  "transaction_time": "2024-11-04 10:30:00",
  "transaction_status": "settlement",
  "settlement_time": "2024-11-04 10:32:15",
  "merchant_id": "G812220370"
}
```

---

#### 📱 **4. Frontend Integration**

**API Routes** (`/supabase/functions/server/index.tsx`):
```typescript
// Create QRIS payment
app.post("/make-server-dbd6b95a/payment/create", async (c) => {
  const accessToken = c.req.header('Authorization')?.split(' ')[1];
  const { data: { user } } = await supabase.auth.getUser(accessToken);
  
  if (!user) {
    return c.json({ error: "Unauthorized" }, 401);
  }

  const { invoiceId, amount, description } = await c.req.json();
  
  // Generate unique order ID
  const orderId = `ORDER-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;
  
  // Create Midtrans transaction
  const result = await createMidtransTransaction({
    orderId,
    amount,
    customerDetails: {
      first_name: user.user_metadata?.name || 'Customer',
      email: user.email || '',
      phone: user.user_metadata?.phone,
    },
    itemDetails: [{
      id: invoiceId,
      price: amount,
      quantity: 1,
      name: description,
    }],
  });
  
  return c.json({
    success: true,
    orderId: result.order_id,
    transactionId: result.transaction_id,
    qrisString: result.qris_string,
    transactionStatus: result.transaction_status,
  });
});

// Verify payment status
app.get("/make-server-dbd6b95a/payment/verify/:orderId", async (c) => {
  const accessToken = c.req.header('Authorization')?.split(' ')[1];
  const { data: { user } } = await supabase.auth.getUser(accessToken);
  
  if (!user) {
    return c.json({ error: "Unauthorized" }, 401);
  }

  const orderId = c.req.param('orderId');
  const status = await verifyMidtransTransaction(orderId);
  
  return c.json({
    success: true,
    midtransStatus: status,
  });
});
```

**Frontend Component** (`/components/MidtransPayment.tsx`):
```typescript
// 1. Generate QRIS
const handleGenerateQris = async () => {
  const { data: { session } } = await supabase.auth.getSession();
  
  const response = await fetch(
    `https://${projectId}.supabase.co/functions/v1/make-server-dbd6b95a/payment/create`,
    {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "Authorization": `Bearer ${session.access_token}`,
      },
      body: JSON.stringify({
        invoiceId,
        amount,
        description,
      }),
    }
  );

  const result = await response.json();
  
  // Display QR code
  setQrisString(result.qrisString);
  setOrderId(result.orderId);
  
  // Start polling for payment status
  startPolling(result.orderId);
};

// 2. Auto-polling untuk check status (3 detik)
const checkPaymentStatus = async (orderId: string) => {
  const { data: { session } } = await supabase.auth.getSession();
  
  const response = await fetch(
    `https://${projectId}.supabase.co/functions/v1/make-server-dbd6b95a/payment/verify/${orderId}`,
    {
      headers: {
        "Authorization": `Bearer ${session.access_token}`,
      },
    }
  );

  const result = await response.json();
  const status = result.midtransStatus.transaction_status;
  
  if (status === "settlement" || status === "capture") {
    // Payment success!
    onSuccess();
    stopPolling();
  }
};

// 3. Display QR Code menggunakan qrcode.react
import { QRCodeSVG } from "qrcode.react";

<QRCodeSVG 
  value={qrisString} 
  size={240}
  level="H"
  includeMargin={true}
/>
```

---

#### 🔄 **5. Transaction Status Flow**

```
┌─────────────────────────────────────┐
│ CREATE TRANSACTION                  │
│ Status: pending                     │
└──────────────┬──────────────────────┘
               │
               ▼
┌─────────────────────────────────────┐
│ DISPLAY QR CODE                     │
│ - Show QRIS QR                      │
│ - Start countdown (5 min)           │
│ - Start polling (3 sec interval)    │
└──────────────┬──────────────────────┘
               │
        ┌──────┴──────┐
        │             │
        ▼             ▼
┌──────────┐   ┌────────────┐
│ USER     │   │ POLLING    │
│ SCANS QR │   │ STATUS     │
│ & PAYS   │   │ Every 3s   │
└────┬─────┘   └──────┬─────┘
     │                │
     └────────┬───────┘
              │
              ▼
┌─────────────────────────────────────┐
│ MIDTRANS PROCESSES PAYMENT          │
└──────────────┬──────────────────────┘
               │
        ┌──────┴──────────┐
        │                 │
        ▼                 ▼
┌──────────────┐   ┌────────────┐
│ SUCCESS      │   │ FAILED     │
│ settlement   │   │ deny/      │
│ capture      │   │ cancel/    │
│              │   │ expire     │
└──────┬───────┘   └──────┬─────┘
       │                  │
       ▼                  ▼
┌─────────────┐    ┌────────────┐
│ UPDATE      │    │ SHOW ERROR │
│ INVOICE     │    │ MESSAGE    │
│ Status: paid│    │            │
└─────────────┘    └────────────┘
```

---

#### 📊 **6. Transaction Statuses**

| Status | Meaning | Action |
|--------|---------|--------|
| `pending` | Waiting for payment | Continue polling |
| `settlement` | Payment successful (bank transfer) | Update to paid |
| `capture` | Payment successful (card) | Update to paid |
| `deny` | Payment denied | Show error |
| `cancel` | Payment cancelled by user | Show error |
| `expire` | Transaction expired (timeout) | Create new transaction |
| `refund` | Payment refunded | Handle refund |
| `partial_refund` | Partial refund | Handle refund |

---

#### ⚙️ **7. Environment Configuration**

**Required Environment Variables:**
```bash
# Midtrans API Keys
MIDTRANS_SERVER_KEY=SB-Mid-server-xxx  # Sandbox/Production
MIDTRANS_CLIENT_KEY=SB-Mid-client-xxx  # Not used in Core API

# Environment Mode
MIDTRANS_ENV=sandbox  # atau 'production'
```

**Cara Mendapatkan Keys:**
1. Login ke [Midtrans Dashboard](https://dashboard.midtrans.com)
2. Pilih environment (Sandbox/Production)
3. Go to **Settings → Access Keys**
4. Copy **Server Key**

---

#### 🔒 **8. Security Best Practices**

**✅ DO:**
- Store Server Key di environment variables (server-side only)
- Encode Server Key dengan Base64 + ':'
- Gunakan HTTPS untuk semua requests
- Validate amount & order_id di server
- Verify transaction status sebelum update database
- Implement idempotency untuk prevent double payment

**❌ DON'T:**
- Expose Server Key ke frontend/client
- Hardcode API keys di code
- Trust client-side amount (always validate server-side)
- Skip signature verification untuk webhooks
- Store sensitive payment data di frontend

---

#### 🧪 **9. Testing (Sandbox)**

**Sandbox URLs:**
```
API: https://api.sandbox.midtrans.com
Dashboard: https://dashboard.sandbox.midtrans.com
```

**Test QRIS Payment:**
1. Generate QR code via API
2. Di Midtrans Simulator, akan muncul tombol "Success" / "Failure"
3. Click "Success" untuk simulate payment berhasil
4. Status akan berubah ke `settlement`
5. Polling akan detect perubahan status

**Manual Testing dengan cURL:**
```bash
# Create QRIS transaction
curl -X POST https://api.sandbox.midtrans.com/v2/charge \
  -H "Content-Type: application/json" \
  -H "Authorization: Basic $(echo -n 'YOUR_SERVER_KEY:' | base64)" \
  -d '{
    "payment_type": "qris",
    "transaction_details": {
      "order_id": "TEST-001",
      "gross_amount": 10000
    }
  }'

# Check status
curl -X GET https://api.sandbox.midtrans.com/v2/TEST-001/status \
  -H "Authorization: Basic $(echo -n 'YOUR_SERVER_KEY:' | base64)"
```

---

#### 📚 **10. Error Handling**

**Common Errors:**

```typescript
// 1. Invalid Server Key
{
  "status_code": "401",
  "status_message": "Access denied due to unauthorized transaction",
  "error_messages": ["Access denied"]
}
// Solution: Check Server Key dan Base64 encoding

// 2. Duplicate Order ID
{
  "status_code": "400",
  "status_message": "Duplicate order id",
  "error_messages": ["Duplicate order id"]
}
// Solution: Generate unique order ID setiap transaksi

// 3. Invalid Amount
{
  "status_code": "400",
  "status_message": "Invalid gross_amount",
  "error_messages": ["Gross amount must be greater than 0"]
}
// Solution: Validate amount > 0

// 4. Network Timeout
// Solution: Implement retry mechanism dengan exponential backoff
```

---

#### 🎯 **11. Production Checklist**

**Before going to production:**

- [ ] Dapatkan Production Server Key dari Midtrans
- [ ] Update `MIDTRANS_SERVER_KEY` di production environment
- [ ] Set `MIDTRANS_ENV=production`
- [ ] Configure webhook notification URL
- [ ] Test dengan real payment amounts
- [ ] Implement proper error logging
- [ ] Setup monitoring untuk failed transactions
- [ ] Verify signature pada webhook callbacks
- [ ] Implement transaction reconciliation
- [ ] Setup automated refund process

---

#### 📖 **12. Official Documentation**

**Midtrans Core API Docs:**
- [Core API Overview](https://docs.midtrans.com/en/core-api/overview)
- [QRIS Payment](https://docs.midtrans.com/en/core-api/qris)
- [Transaction Status](https://docs.midtrans.com/en/after-payment/get-status)
- [HTTP Notification](https://docs.midtrans.com/en/after-payment/http-notification)
- [Sandbox Testing](https://docs.midtrans.com/en/technical-reference/sandbox-test)

---

#### 💡 **13. Advantages of Core API vs Snap**

**Core API:**
- ✅ Full control over UI/UX
- ✅ Custom QR code display
- ✅ Better mobile integration
- ✅ Auto-detection dengan polling
- ✅ Seamless dalam aplikasi
- ✅ Tidak perlu redirect/popup
- ✅ Better untuk kustomisasi

**Snap (Popup/Redirect):**
- ✅ Easier integration (less code)
- ✅ Midtrans handles UI
- ✅ Support banyak payment methods out-of-box
- ✅ Automatic 3DS handling
- ❌ Less control over UX
- ❌ Popup dapat di-block browser
- ❌ Sulit customize appearance

### Midtrans Webhook Notification (Production)

**Webhook untuk auto-update payment status tanpa polling.**

#### Setup Webhook:
```typescript
// Endpoint: POST /payment/notification
app.post("/make-server-dbd6b95a/payment/notification", async (c) => {
  try {
    const notification = await c.req.json();
    
    // Verify signature (IMPORTANT!)
    const serverKey = Deno.env.get('MIDTRANS_SERVER_KEY');
    const orderId = notification.order_id;
    const statusCode = notification.status_code;
    const grossAmount = notification.gross_amount;
    
    const signatureKey = `${orderId}${statusCode}${grossAmount}${serverKey}`;
    const expectedSignature = crypto
      .createHash('sha512')
      .update(signatureKey)
      .digest('hex');
    
    if (notification.signature_key !== expectedSignature) {
      return c.json({ error: "Invalid signature" }, 401);
    }
    
    // Update payment status based on transaction_status
    const transactionStatus = notification.transaction_status;
    const fraudStatus = notification.fraud_status;
    
    if (transactionStatus === 'capture' || transactionStatus === 'settlement') {
      // Payment successful - update invoice
      await updateInvoiceStatus(orderId, 'paid');
    } else if (transactionStatus === 'deny' || transactionStatus === 'cancel' || transactionStatus === 'expire') {
      // Payment failed
      await updateInvoiceStatus(orderId, 'failed');
    }
    
    return c.json({ status: "ok" });
  } catch (error: any) {
    console.error("Webhook error:", error);
    return c.json({ error: error.message }, 500);
  }
});
```

**Configure di Midtrans Dashboard:**
```
Settings → Configuration → Notification URL
URL: https://[PROJECT_ID].supabase.co/functions/v1/make-server-dbd6b95a/payment/notification
```

---

### File Upload (Manual Payment)

**Supabase Storage:**
```typescript
// Create bucket (idempotent)
const bucketName = 'make-dbd6b95a-payment-proofs';
const { data: buckets } = await supabase.storage.listBuckets();
const exists = buckets?.some(b => b.name === bucketName);
if (!exists) {
  await supabase.storage.createBucket(bucketName, { public: false });
}

// Upload file
const { data, error } = await supabase.storage
  .from(bucketName)
  .upload(`${userId}/${timestamp}_${filename}`, file);

// Get signed URL (private)
const { data: signedUrl } = await supabase.storage
  .from(bucketName)
  .createSignedUrl(path, 3600); // 1 hour
```

---

## 🎨 KOMPONEN UI

### Shadcn/ui Components Used

**Form Components:**
- `Input` - Text input
- `Textarea` - Multi-line text
- `Select` - Dropdown select
- `Checkbox` - Checkboxes
- `RadioGroup` - Radio buttons
- `Slider` - Range slider
- `Switch` - Toggle switch
- `Calendar` - Date picker
- `Label` - Form labels

**Layout Components:**
- `Card` - Content containers
- `Separator` - Dividers
- `Tabs` - Tab navigation
- `Sheet` - Side panel
- `Dialog` - Modal dialogs
- `ScrollArea` - Scrollable areas
- `AspectRatio` - Image ratios
- `Resizable` - Resizable panels

**Feedback Components:**
- `Badge` - Status indicators
- `Alert` - Alert messages
- `Toast` (Sonner) - Notifications
- `Progress` - Progress bars
- `Skeleton` - Loading states
- `Avatar` - User avatars

**Navigation:**
- `Button` - All buttons
- `Dropdown Menu` - Dropdowns
- `Navigation Menu` - Nav menus
- `Breadcrumb` - Breadcrumbs
- `Pagination` - Pagination

### Custom Components

**Payment Components:**
```
PaymentBanner         - Sticky top banner
QuickPayCTA          - Dashboard card CTA
FloatingPayButton    - Floating bottom-right
PendingPaymentsCard  - Inline payment card
MidtransPayment      - Midtrans Snap embed
ManualPayment        - Upload bukti transfer
```

**Profile Components:**
```
ProfilePage          - User profile management
ProfileSidebar       - Settings sidebar
```

**Property Components:**
```
PropertyDetail       - Property detail page
PropertyManagementPage - CRUD properties
WishlistButton       - Add/remove wishlist
SaveSearchDialog     - Save search filters
```

**Chat Components:**
```
ChatPage             - Full chat interface
```

**Ticketing Components:**
```
TicketingPage        - Ticket management
```

### Icon Library - Lucide React

**Common Icons:**
```typescript
import {
  Home, Search, Building2, User, Heart,
  MessageSquare, Bell, Settings, LogIn,
  Calendar, DollarSign, FileText, Check,
  X, AlertCircle, CheckCircle, Clock,
  MapPin, Wifi, Wind, Droplet, Car,
  Plus, Edit, Trash2, Eye, Filter,
  ChevronDown, ChevronRight, ArrowLeft,
  Send, Paperclip, Image, MoreVertical
} from "lucide-react";
```

---

## 🔄 ALUR PROSES BISNIS

### 1. Rental Application Flow

```
┌─────────────────────────────────────┐
│ USER BROWSES PROPERTIES             │
│ (Guest/Tenant)                      │
└──────────────┬──────────────────────┘
               │
               ▼
┌─────────────────────────────────────┐
│ VIEWS PROPERTY DETAIL               │
│ - See room types                    │
│ - Check prices                      │
│ - Review facilities                 │
└──────────────┬──────────────────────┘
               │
               ▼
┌─────────────────────────────────────┐
│ CLICKS "AJUKAN SEWA"                │
│ → Navigate to ApplyRentalPage       │
└──────────────┬──────────────────────┘
               │
               ▼
┌─────────────────────────────────────┐
│ STEP 1: SELECT ROOM TYPE            │
│ - Choose from available types       │
│ - See price + deposit               │
└──────────────┬──────────────────────┘
               │
               ▼
┌─────────────────────────────────────┐
│ STEP 2: FILL PERSONAL DATA          │
│ - Personal info                     │
│ - Emergency contact                 │
│ - Move-in date & duration           │
└──────────────┬──────────────────────┘
               │
               ▼
┌─────────────────────────────────────┐
│ STEP 3: REVIEW & CONFIRM            │
│ - Review all data                   │
│ - See total payment                 │
│ - Agree to terms                    │
└──────────────┬──────────────────────┘
               │
               ▼
┌─────────────────────────────────────┐
│ SUBMIT APPLICATION                  │
└──────────────┬──────────────────────┘
               │
      ┌────────┴────────┐
      │                 │
      ▼                 ▼
┌──────────┐    ┌────────────┐
│  GUEST   │    │  TENANT    │
│ →Login   │    │ →Contracts │
└──────────┘    └────────────┘
               │
               ▼
┌─────────────────────────────────────┐
│ OWNER RECEIVES NOTIFICATION         │
│ - Reviews application               │
│ - Contacts tenant                   │
│ - Creates contract (if approved)    │
└──────────────┬──────────────────────┘
               │
               ▼
┌─────────────────────────────────────┐
│ CONTRACT CREATED                    │
│ - Generate invoice for 1st month   │
│ - Set payment due date              │
└─────────────────────────────────────┘
```

---

### 2. Payment Flow

```
┌─────────────────────────────────────┐
│ INVOICE GENERATED                   │
│ Status: pending                     │
│ Due date: 5th of month              │
└──────────────┬──────────────────────┘
               │
               ▼
┌─────────────────────────────────────┐
│ TENANT SEES PAYMENT CTAS            │
│ - Banner (sticky top)               │
│ - Quick Pay Card (dashboard)        │
│ - Floating Button (scroll)          │
│ - Inline Button (invoice list)      │
└──────────────┬──────────────────────┘
               │
               ▼
┌───────���─────────────────────────────┐
│ TENANT CLICKS "BAYAR"               │
└──────────────┬──────────────────────┘
               │
       ┌───────┴────────┐
       │                │
       ▼                ▼
┌────────────┐   ┌──────────────┐
│ QRIS/VA    │   │ MANUAL       │
│ (Midtrans) │   │ TRANSFER     │
└──────┬─────┘   └──────┬───────┘
       │                │
       ▼                ▼
┌────────────┐   ┌──────────────┐
│ Snap Dialog│   │ Upload Bukti │
│ - Scan QR  │   │ - JPG/PNG    │
│ - VA info  │   │ - Max 5MB    │
│ - E-wallet │   │              │
└──────┬─────┘   └──────┬───────┘
       │                │
       ▼                ▼
┌────────────┐   ┌──────────────┐
│ Pay via    │   │ Submit proof │
│ chosen     │   │ Status:      │
│ method     │   │ verifying    │
└──────┬─────┘   └──────┬───────┘
       │                │
       │                ▼
       │         ┌──────────────┐
       │         │ Owner/Admin  │
       │         │ VERIFIES     │
       │         │ - View proof │
       │         │ - Approve or │
       │         │   Reject     │
       │         └──────┬───────┘
       │                │
       └────────┬───────┘
                │
                ▼
┌─────────────────────────────────────┐
│ PAYMENT SUCCESS                     │
│ - Update invoice status: paid       │
│ - Send notification                 │
│ - Remove from pending list          │
└─────────────────────────────────────┘
```

---

### 3. Property Lifecycle

```
┌─────────────────────────────────────┐
│ OWNER CREATES PROPERTY              │
│ - Fill form                         │
│ - Submit                            │
│ Status: pending_approval            │
└──────────────┬──────────────────────┘
               │
               ▼
┌─────────────────────────────────────┐
│ ADMIN MODERATION QUEUE              │
│ - Property appears in kanban        │
│ - Admin reviews details             │
└──────────────┬──────────────────────┘
               │
        ┌──────┴──────┐
        │             │
        ▼             ▼
┌──────────┐   ┌────────────┐
│ APPROVE  │   │ REJECT     │
│ Status:  │   │ Status:    │
│ active   │   │ rejected   │
└────┬─────┘   └──────┬─────┘
     │                │
     ▼                ▼
┌─────────┐    ┌──────────────┐
│ VISIBLE │    │ OWNER NOTIF  │
│ TO      │    │ - Can edit   │
│ TENANTS │    │ - Re-submit  │
└─────────┘    └──────────────┘
     │
     ▼
┌─────────────────────────────────────┐
│ TENANT BROWSING                     │
│ - Search & filter                   │
│ - View details                      │
│ - Apply rental                      │
└─────────────────────────────────────┘
```

---

### 4. Ticketing Flow

```
┌─────────────────────────────────────┐
│ USER HAS ISSUE                      │
│ (Tenant/Owner)                      │
└──────────────┬──────────────────────┘
               │
               ▼
┌─────────────────────────────────────┐
│ CREATE TICKET                       │
│ - Select category                   │
│ - Set priority                      │
│ - Write description                 │
│ Status: open                        │
└──────────────┬──────────────────────┘
               │
               ▼
┌─────────────────────────────────────┐
│ ADMIN KANBAN BOARD                  │
│ - Ticket in "Open" column           │
└──────────────┬──────────────────────┘
               │
               ▼
┌─────────────────────────────────────┐
│ ADMIN REVIEWS                       │
│ - Read description                  │
│ - Check priority                    │
│ - Add comments                      │
└──────────────┬──────────────────────┘
               │
     ┌─────────┼──────────┐
     │         │          │
     ▼         ▼          ▼
┌────────┐ ┌─────┐  ┌─────────┐
│IN      │ │ESCA │  │RESOLVED │
│REVIEW  │ │LATED│  │         │
└───┬────┘ └──┬──┘  └────┬────┘
    │         │          │
    └─────────┼──────────┘
              │
              ▼
┌─────────────────────────────────────┐
│ USER RECEIVES NOTIFICATION          │
│ - Status change                     │
│ - Admin comment                     │
└──────────────┬──────────────────────┘
               │
               ▼
┌─────────────────────────────────────┐
│ TICKET CLOSED                       │
│ Status: resolved/rejected           │
└─────────────────────────────────────┘
```

---

## 📱 RESPONSIVE DESIGN

### Breakpoints
```css
mobile:    < 768px
tablet:    768px - 1024px
desktop:   > 1024px
```

### Grid Adaption

**HomePage Property Grid:**
```
Desktop: 4 columns
Tablet:  2 columns
Mobile:  1 column
```

**BrowseKost Grid:**
```
Desktop: 3 columns
Tablet:  2 columns
Mobile:  1 column
```

**Dashboard Stats:**
```
Desktop: 4 columns
Tablet:  2 columns
Mobile:  1 column
```

### Mobile Navigation
- Hamburger menu
- Collapsible sections
- Bottom navigation (future)
- Swipe gestures (future)

### Mobile Optimizations
- Touch-friendly buttons (min 44x44px)
- Simplified forms
- Stack layouts
- Hide secondary info
- Sticky headers
- Full-width CTAs

---

## 🔒 SECURITY

### Authentication
- ✅ Supabase Auth (secure by default)
- ✅ Google OAuth
- ✅ Session management
- ✅ Role-based access control

### API Security
- ✅ Bearer token authentication
- ✅ Server-side validation
- ✅ CORS configuration
- ✅ Rate limiting (Supabase default)

### Data Protection
- ✅ Private storage buckets
- ✅ Signed URLs (1 hour expiry)
- ✅ User data isolation
- ✅ No sensitive data in frontend

### Best Practices
- ✅ Environment variables for keys
- ✅ HTTPS only
- ✅ Input validation
- ✅ SQL injection prevention (KV store)
- ✅ XSS protection (React default)

---

## 🚀 DEPLOYMENT

### Environment Variables Required
```
SUPABASE_URL=https://xxx.supabase.co
SUPABASE_ANON_KEY=eyJ...
SUPABASE_SERVICE_ROLE_KEY=eyJ...
MIDTRANS_SERVER_KEY=SB-Mid-server-xxx
MIDTRANS_CLIENT_KEY=SB-Mid-client-xxx
```

### Supabase Setup
1. Create project
2. Setup Google OAuth
3. Deploy edge functions
4. Create storage buckets
5. Configure CORS

### Midtrans Setup
1. Create account (sandbox)
2. Get server & client keys
3. Configure webhooks (future)
4. Test transactions

---

## 📊 FUTURE ENHANCEMENTS

### Phase 2
- [ ] Real-time chat (WebSocket)
- [ ] Push notifications
- [ ] Email notifications
- [ ] Advanced search (geo-location)
- [ ] Reviews & ratings
- [ ] Virtual tour (360°)

### Phase 3
- [ ] Mobile app (React Native)
- [ ] Payment analytics
- [ ] Revenue reports
- [ ] AI recommendations
- [ ] Multi-language support
- [ ] Dark mode

### Phase 4
- [ ] Marketplace features
- [ ] Loyalty program
- [ ] Referral system
- [ ] Integration with other platforms
- [ ] White-label solution

---

## 📞 SUPPORT

### Demo Accounts
```
Tenant:  tenant@demo.com  | demo123
Owner:   owner@demo.com   | demo123
Admin:   admin@demo.com   | demo123
```

### Documentation Files
- `README.md` - Quick start
- `QUICK_START_DEVELOPER.md` - Developer guide
- `PAYMENT_CTA_README.md` - Payment CTA guide
- `QRIS_AUTO_DETECTION.md` - QRIS integration
- `MIDTRANS_SETUP.md` - Midtrans setup
- `BROWSE_KOST_GUIDE.md` - Browse feature guide

---

**Last Updated:** November 4, 2024  
**Version:** 1.0  
**Platform:** KostIn - Sistem Manajemen Kos Terpadu
