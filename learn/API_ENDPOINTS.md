# Dokumentasi Endpoint API

Berikut adalah daftar lengkap endpoint API yang tersedia di aplikasi Kostin, dikelompokkan berdasarkan hak akses pengguna.

**Base URL**: `https://kostin-api.syzzhd.web.id/api/v1` (Production) / `http://localhost:8000/api/v1` (Local)

---

## 1. Otentikasi (Authentication)
Endpoint ini terbuka untuk umum (Public) kecuali Logout dan Me.

| Method | Endpoint | Deskripsi | Middleware |
|:------:|----------|-----------|------------|
| `POST` | `/auth/login` | Masuk ke aplikasi. Mengembalikan cookie sesi. | - |
| `POST` | `/auth/register` | Mendaftar akun baru (Tenant/Owner). | - |
| `POST` | `/auth/forgot-password` | Mengirim email link reset password. | - |
| `POST` | `/auth/reset-password` | Mengubah password menggunakan token dari email. | - |
| `POST` | `/auth/logout` | Keluar dari aplikasi (Hapus sesi). | `auth:sanctum` |
| `GET` | `/auth/me` | Mengambil data user yang sedang login. | `auth:sanctum` |

---

## 2. Fitur Penyewa (Tenant)
Endpoint khusus untuk pencari kost.
**Prefix**: `/tenant`

| Method | Endpoint | Deskripsi |
|:------:|----------|-----------|
| `GET` | `/tenant/search` | Mencari kost dengan filter (lokasi, harga). |
| `GET` | `/tenant/search/{id}` | Melihat detail kost tertentu. |
| `GET` | `/tenant/wishlist` | Melihat daftar kost favorit. |
| `GET` | `/tenant/contracts` | Melihat daftar kontrak sewa aktif. |
| `GET` | `/tenant/contracts/{id}/pdf` | Download PDF surat perjanjian sewa. |
| `POST` | `/tenant/applications` | Mengajukan sewa kamar baru. |
| `POST` | `/tenant/tickets` | Membuat tiket komplain baru. |
| `GET` | `/tenant/tickets` | Melihat riwayat tiket komplain. |
| `POST` | `/tenant/invoices/{id}/pay` | Membayar tagihan (Dapat Snap Token). |
| `POST` | `/tenant/invoices/{id}/manual-payment` | Upload bukti transfer manual. |

---

## 3. Fitur Pemilik (Owner)
Endpoint khusus untuk pemilik kost. Memerlukan role `owner`.
**Prefix**: `/owner`

| Method | Endpoint | Deskripsi |
|:------:|----------|-----------|
| `GET` | `/owner/dashboard` | Statistik pendapatan dan okupansi. |
| `GET` | `/owner/properties` | Melihat daftar kost milik sendiri. |
| `POST` | `/owner/properties` | Menambah kost baru. |
| `PUT` | `/owner/properties/{id}` | Mengupdate data kost. |
| `POST` | `/owner/properties/{id}/rooms/bulk` | Membuat banyak kamar sekaligus. |
| `GET` | `/owner/applications` | Melihat pengajuan sewa masuk. |
| `POST` | `/owner/applications/{id}/approve` | Menyetujui pengajuan sewa. |
| `POST` | `/owner/applications/{id}/reject` | Menolak pengajuan sewa. |
| `GET` | `/owner/manual-payments` | Melihat daftar pembayaran manual yang perlu dicek. |
| `POST` | `/owner/manual-payments/{id}/approve` | Menyetujui pembayaran manual. |
| `GET` | `/owner/wallet` | Mengecek saldo pendapatan. |
| `POST` | `/owner/wallet/withdraw` | Menarik dana ke rekening bank. |

---

## 4. Fitur Admin
Endpoint khusus administrator sistem. Memerlukan role `admin`.
**Prefix**: `/admin`

| Method | Endpoint | Deskripsi |
|:------:|----------|-----------|
| `GET` | `/admin/dashboard` | Statistik global sistem. |
| `GET` | `/admin/moderations` | Daftar kost baru yang butuh persetujuan. |
| `POST` | `/admin/moderations/{id}/approve` | Menyetujui kost untuk tayang. |
| `GET` | `/admin/users` | Melihat daftar semua pengguna. |
| `POST` | `/admin/users/{id}/suspend` | Memblokir pengguna bermasalah. |
| `POST` | `/webhook/midtrans` | (Dev Only) Simulasi webhook pembayaran. |

---

## 5. Sistem & Umum
Endpoint pendukung yang digunakan oleh sistem.

| Method | Endpoint | Deskripsi |
|:------:|----------|-----------|
| `GET` | `/stats` | Data statistik untuk Landing Page publik. |
| `POST` | `/webhook/midtrans` | Menerima notifikasi status pembayaran dari Midtrans. |
| `GET` | `/sanctum/csrf-cookie` | Inisialisasi keamanan CSRF (Wajib dipanggil pertama). |
