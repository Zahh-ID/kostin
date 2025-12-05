# Fitur & Detail Implementasi

Dokumen ini menjelaskan secara rinci fitur-fitur yang tersedia untuk setiap tipe pengguna (Role) beserta Controller yang menanganinya di backend.

---

## 1. Fitur Penyewa (Tenant)
**Role**: `tenant`
**Base Route**: `/api/v1/tenant`

Fitur-fitur ini ditujukan untuk pengguna umum yang mencari dan menyewa kamar kost.

| Fitur | Deskripsi | Controller | Akses |
|-------|-----------|------------|-------|
| **Pencarian (Search)** | Mencari kost berdasarkan lokasi, harga, dan fasilitas. | `Tenant\SearchController` | **Publik** |
| **Wishlist** | Menyimpan kost favorit untuk dilihat nanti. | `Tenant\WishlistController` | `auth` |
| **Pengajuan Sewa** | Mengajukan permohonan sewa kamar ke pemilik kost. | `Tenant\ApplicationController` | `auth` |
| **Kontrak Saya** | Melihat daftar kontrak sewa yang sedang aktif. | `Tenant\ContractController` | `auth` |
| **Unduh Kontrak** | Mengunduh surat perjanjian sewa dalam format PDF. | `Tenant\ContractPdfController` | `auth` |
| **Tagihan (Invoice)** | Melihat daftar tagihan sewa yang harus dibayar. | `Api\V1\InvoiceController` | `auth` |
| **Pembayaran Otomatis** | Membayar tagihan via QRIS/VA (Midtrans). | `Web\Tenant\InvoicePaymentController` | `auth` |
| **Cek Status Bayar** | Mengecek apakah pembayaran berhasil. | `Web\Tenant\InvoicePaymentStatusController` | `auth` |
| **Pembayaran Manual** | Mengupload bukti transfer bank manual (jika perlu). | `Web\Tenant\ManualPaymentController` | `auth` |
| **Tiket Komplain** | Melaporkan kerusakan/masalah ke pemilik kost. | `Tenant\TicketController` | `auth` |
| **Dashboard** | Ringkasan status sewa dan tagihan aktif. | `Tenant\OverviewController` | `auth` |

### Logika Penting
-   **Sistem Pembayaran**: Menggunakan **Midtrans Snap**. Ketika user klik bayar, `InvoicePaymentController` akan meminta "Snap Token" ke Midtrans, lalu Frontend menampilkan popup pembayaran.
-   **PDF Generator**: `ContractPdfController` menggunakan library `dompdf` untuk mengubah tampilan HTML surat perjanjian menjadi file PDF siap cetak.

---

## 2. Fitur Pemilik Kost (Owner)
**Role**: `owner`
**Base Route**: `/api/v1/owner`

Fitur-fitur ini memberikan kontrol penuh kepada pemilik kost untuk mengelola properti dan penyewa mereka.

| Fitur | Deskripsi | Controller | Middleware |
|-------|-----------|------------|------------|
| **Manajemen Properti** | Tambah, Edit, Hapus data kost (Nama, Alamat, Deskripsi). | `Owner\PropertyController` | `role:owner` |
| **Foto Properti** | Upload dan kelola galeri foto kost. | `Owner\PropertyPhotoController` | `role:owner` |
| **Manajemen Kamar** | Mengelola unit kamar (Nomor kamar, status). | `Owner\RoomController` | `role:owner` |
| **Tipe Kamar** | Mengatur tipe kamar (Ukuran, Harga, Fasilitas). | `Owner\RoomTypeController` | `role:owner` |
| **Buat Kamar Massal** | Membuat banyak kamar sekaligus (Bulk Create). | `Owner\PropertyRoomController` | `role:owner` |
| **Aplikasi Masuk** | Menyetujui atau Menolak calon penyewa baru. | `Owner\OwnerApplicationController` | `role:owner` |
| **Manajemen Kontrak** | Memantau masa berlaku sewa penghuni. | `Owner\ContractIndexController` | `role:owner` |
| **Pemutusan Sewa** | Mengakhiri kontrak sewa secara sepihak/kesepakatan. | `Owner\OwnerContractController` | `role:owner` |
| **Verifikasi Pembayaran** | Mengecek bukti transfer manual dari penyewa. | `Owner\OwnerManualPaymentController` | `role:owner` |
| **Dompet (Wallet)** | Melihat total pendapatan sewa. | `Owner\WalletController` | `role:owner` |
| **Tarik Dana** | Mengajukan pencairan dana ke rekening bank. | `Owner\WalletWithdrawController` | `role:owner` |
| **Manajemen Tiket** | Membalas dan menyelesaikan komplain penghuni. | `Owner\TicketUpdateController` | `role:owner` |
| **Dashboard Owner** | Statistik okupansi (keterisian) dan pendapatan. | `Owner\DashboardController` | `role:owner` |

### Logika Penting
-   **Validasi Manual**: Untuk pembayaran manual, Owner harus memverifikasi bukti transfer secara visual sebelum sistem menandai tagihan sebagai "Lunas".
-   **Bulk Action**: Fitur "Buat Kamar Massal" memudahkan owner yang memiliki puluhan kamar agar tidak perlu input satu per satu.

---

## 3. Fitur Administrator (Admin)
**Role**: `admin`
**Base Route**: `/api/v1/admin`

Fitur untuk pengelola aplikasi Kostin guna menjaga kualitas dan keamanan platform.

| Fitur | Deskripsi | Controller | Middleware |
|-------|-----------|------------|------------|
| **Dashboard Admin** | Statistik global sistem (Total User, Transaksi). | `Admin\DashboardController` | `role:admin` |
| **Moderasi Kost** | Meninjau kost baru sebelum tayang (Approve/Reject). | `Admin\ModerationActionController` | `role:admin` |
| **Manajemen User** | Melihat daftar semua pengguna terdaftar. | `Admin\UserIndexController` | `role:admin` |
| **Suspend User** | Memblokir pengguna yang melanggar aturan. | `Admin\UserActionController` | `role:admin` |
| **Pantau Tiket** | Mengawasi penyelesaian masalah antara Owner & Tenant. | `Admin\TicketIndexController` | `role:admin` |
| **Simulator Webhook** | Alat testing untuk simulasi pembayaran (Dev Only). | `Admin\WebhookSimulatorController` | `role:admin` |

---

## 4. Fitur Sistem & Umum

Fitur-fitur ini bekerja di belakang layar atau digunakan bersama oleh semua role.

| Fitur | Deskripsi | Controller |
|-------|-----------|------------|
| **Core Payment** | Logika inti pemrosesan pembayaran (QRIS/VA). | `Api\V1\PaymentController` |
| **Webhooks** | Menerima notifikasi otomatis dari Midtrans (Real-time). | `WebhookController` |
| **Cron Jobs** | Skrip otomatis untuk mengirim email pengingat tagihan. | `Console\Commands\SendInvoiceReminders` |
| **Statistik Publik** | Data untuk ditampilkan di Landing Page. | `Api\V1\StatsController` |
