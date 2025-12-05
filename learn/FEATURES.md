# Fitur & Alur Bisnis (Business Logic Flows)

Dokumen ini menjelaskan secara rinci bagaimana setiap fitur bekerja dari awal hingga akhir, melibatkan interaksi antara User, Frontend, Backend, dan Database.

---

## 1. Alur Penyewaan Kamar (Rental Application Flow)

Bagaimana seorang pencari kost (Tenant) menjadi penghuni resmi.

1.  **Pencarian & Detail**
    *   Tenant mencari kost di halaman Search.
    *   Melihat detail fasilitas, harga, dan foto kamar.
    *   **API**: `GET /api/v1/tenant/properties/{id}`.

2.  **Pengajuan Sewa (Apply)**
    *   Tenant mengklik tombol "Ajukan Sewa" pada kamar tertentu.
    *   Sistem membuat data di tabel `rental_applications` dengan status `pending`.
    *   **API**: `POST /api/v1/tenant/applications`.

3.  **Persetujuan Pemilik (Owner Approval)**
    *   Owner menerima notifikasi ada pengajuan baru.
    *   Owner melihat profil calon penyewa.
    *   Owner mengklik "Setuju" (Approve).
    *   **Sistem**:
        *   Mengubah status aplikasi menjadi `approved`.
        *   Otomatis membuat **Kontrak** (`contracts`) baru dengan status `active` (atau `draft` tergantung konfigurasi).
        *   Mengubah status kamar (`rooms`) menjadi `occupied` (terisi).
    *   **API**: `POST /api/v1/owner/applications/{id}/approve`.

4.  **Mulai Menghuni**
    *   Tenant kini bisa melihat kontrak aktif di menu "Kontrak Saya".
    *   Tagihan bulan pertama (`invoices`) otomatis dibuat.

---

## 2. Alur Pembayaran Tagihan (Payment Flow)

Bagaimana sistem menangani pembayaran sewa bulanan menggunakan Midtrans.

1.  **Tagihan Terbit**
    *   Setiap bulan (atau saat kontrak dibuat), sistem membuat record di tabel `invoices` dengan status `unpaid`.

2.  **Inisiasi Pembayaran**
    *   Tenant membuka detail tagihan dan klik "Bayar Sekarang".
    *   **Backend**:
        *   Menghubungi API Midtrans untuk meminta **Snap Token**.
        *   Menyimpan `external_order_id` unik ke invoice.
    *   **Frontend**: Membuka popup pembayaran Midtrans (Snap) menggunakan token tersebut.

3.  **Proses Bayar**
    *   Tenant memilih metode (QRIS, GoPay, Transfer Bank) dan menyelesaikan pembayaran di antarmuka Midtrans.

4.  **Verifikasi Otomatis (Webhook)**
    *   Midtrans mengirim sinyal (Webhook) ke server kita (`POST /api/v1/webhook/midtrans`).
    *   **Backend**:
        *   Memverifikasi tanda tangan keamanan (Signature Key).
        *   Mencari invoice berdasarkan Order ID.
        *   Mengupdate status invoice menjadi `paid`.
        *   Membuat record di tabel `payments`.
        *   Mengirim notifikasi "Pembayaran Berhasil" ke Tenant.

---

## 3. Alur Pembayaran Manual (Manual Payment Flow)

Alternatif jika Tenant tidak bisa menggunakan pembayaran digital otomatis.

1.  **Upload Bukti**
    *   Tenant mentransfer uang ke rekening Owner/Admin secara manual.
    *   Tenant memfoto bukti transfer.
    *   Tenant mengupload foto tersebut di menu tagihan.
    *   **Sistem**: Menyimpan foto, membuat record `payments` dengan status `pending`, dan mengubah status invoice menjadi `pending_verification`.

2.  **Verifikasi Owner**
    *   Owner melihat ada pembayaran manual yang perlu dicek.
    *   Owner mencocokkan mutasi rekening bank dengan foto bukti.
    *   **Aksi**:
        *   **Terima**: Status invoice jadi `paid`, pembayaran jadi `success`.
        *   **Tolak**: Status invoice kembali `unpaid`, tenant diminta upload ulang.

---

## 4. Alur Komplain (Ticket System Flow)

Penanganan masalah fasilitas atau kerusakan.

1.  **Pelaporan**
    *   Tenant membuat tiket baru: Judul "AC Bocor", Prioritas "High", Lampiran Foto.
    *   **API**: `POST /api/v1/tenant/tickets`.

2.  **Respon Owner**
    *   Owner melihat tiket masuk.
    *   Owner membalas komentar: "Teknisi akan datang besok".
    *   Owner mengubah status tiket menjadi `in_progress`.

3.  **Penyelesaian**
    *   Setelah diperbaiki, Owner atau Tenant mengubah status tiket menjadi `resolved`.
    *   Tiket ditutup (`closed`).

---

## 5. Alur Pemutusan Kontrak (Termination Flow)

Ketika penyewa ingin pindah sebelum masa sewa habis.

1.  **Pengajuan Berhenti**
    *   Tenant mengajukan "Stop Sewa" di detail kontrak.
    *   Memilih tanggal keluar (misal: akhir bulan ini).
    *   **API**: `POST /api/v1/tenant/contracts/{id}/terminate`.

2.  **Persetujuan**
    *   Owner meninjau alasan dan tanggal.
    *   Jika disetujui, sistem menjadwalkan status kontrak berubah menjadi `terminated` pada tanggal tersebut.
    *   Kamar (`rooms`) akan otomatis berubah menjadi `available` setelah tanggal tersebut lewat.

---

## 6. Fitur & Controller Mapping

Berikut adalah pemetaan fitur ke kode program (Controller) yang menanganinya.

### Tenant (Penyewa)
| Fitur | Controller | Middleware |
|-------|------------|------------|
| **Cari Kost** | `Tenant\SearchController` | Public |
| **Wishlist** | `Tenant\WishlistController` | `auth` |
| **Ajukan Sewa** | `Tenant\ApplicationController` | `auth` |
| **Kontrak & PDF** | `Tenant\ContractController`, `ContractPdfController` | `auth` |
| **Bayar (Snap)** | `Web\Tenant\InvoicePaymentController` | `auth` |
| **Bayar (Manual)** | `Web\Tenant\ManualPaymentController` | `auth` |
| **Tiket** | `Tenant\TicketController` | `auth` |

### Owner (Pemilik)
| Fitur | Controller | Middleware |
|-------|------------|------------|
| **Kelola Kost** | `Owner\PropertyController` | `role:owner` |
| **Kelola Kamar** | `Owner\RoomController` | `role:owner` |
| **Cek Aplikasi** | `Owner\ApplicationIndexController` | `role:owner` |
| **Cek Kontrak** | `Owner\ContractIndexController` | `role:owner` |
| **Cek Pembayaran** | `Owner\OwnerManualPaymentController` | `role:owner` |
| **Dompet & Tarik** | `Owner\WalletController` | `role:owner` |

### Admin (Administrator)
| Fitur | Controller | Middleware |
|-------|------------|------------|
| **Moderasi Kost** | `Admin\ModerationActionController` | `role:admin` |
| **Blokir User** | `Admin\UserActionController` | `role:admin` |
| **Dashboard** | `Admin\DashboardController` | `role:admin` |
