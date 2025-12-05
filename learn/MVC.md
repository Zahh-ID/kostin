# Arsitektur MVC (Model-View-Controller)

Aplikasi Kostin menerapkan pola desain **MVC** yang dimodifikasi untuk arsitektur modern (Headless/Decoupled).

## 1. Model (M)
Model adalah representasi struktur data di database. Di Laravel, kita menggunakan **Eloquent ORM** untuk berinteraksi dengan database tanpa menulis SQL manual.

### Daftar Model (`app/Models/`)
Berikut adalah entitas data utama dalam aplikasi:

| Model | Deskripsi |
|-------|-----------|
| `User` | Pengguna aplikasi (Tenant, Owner, Admin). |
| `Property` | Data kost (Nama, Alamat, Deskripsi). |
| `Room` | Unit kamar spesifik yang disewakan. |
| `RoomType` | Tipe kamar (Ukuran, Fasilitas, Harga Dasar). |
| `RentalApplication` | Pengajuan sewa dari Tenant ke Owner. |
| `Contract` | Perjanjian sewa aktif antara Tenant dan Owner. |
| `Invoice` | Tagihan sewa yang harus dibayar. |
| `Payment` | Catatan pembayaran (Transaksi Midtrans). |
| `Ticket` | Tiket komplain/masalah dari Tenant. |
| `WishlistItem` | Kost yang disimpan oleh Tenant. |
| `AuditLog` | Catatan aktivitas sistem untuk keamanan. |

---

## 2. View (V)
Dalam arsitektur "Decoupled", bagian **View** terbagi menjadi dua:

### A. Frontend (React JS) - *Utama*
Tampilan utama aplikasi yang dilihat pengguna.
-   **Lokasi**: `frontend/src/pages/`
-   **Fungsi**: Menerima data JSON dari Controller dan merendernya menjadi antarmuka pengguna (UI).
-   **Contoh**: Halaman Dashboard, Pencarian Kost, Login.

### B. Blade Templates (Laravel) - *Pendukung*
Laravel tetap menggunakan View tradisional (Blade) untuk keperluan khusus yang tidak bisa ditangani React.
-   **Lokasi**: `resources/views/`
-   **Penggunaan**:
    1.  **Email**: Template email notifikasi (`resources/views/emails/`).
    2.  **PDF**: Template surat perjanjian sewa (`resources/views/pdf/`).

---

## 3. Controller (C)
Controller adalah "otak" yang menghubungkan Model dan View. Ia menerima request dari user, memproses data menggunakan Model, dan mengembalikan respons.

### Struktur Controller (`app/Http/Controllers/`)
Kami memisahkan controller berdasarkan **Role** dan **Domain** untuk kerapian kode.

-   **Auth**: Menangani Login, Register, Reset Password.
-   **Api/V1/Tenant**: Logika khusus penyewa (Cari kost, Bayar tagihan).
-   **Api/V1/Owner**: Logika khusus pemilik (Kelola kost, Cek pendapatan).
-   **Api/V1/Admin**: Logika administrator (Moderasi, Blokir user).

### Contoh Alur MVC
1.  **User** (di Frontend) mengklik tombol "Sewa Kamar".
2.  **Controller** (`Tenant\ApplicationController`) menerima request.
3.  **Controller** memvalidasi input.
4.  **Controller** menyuruh **Model** (`RentalApplication`) untuk menyimpan data baru ke database.
5.  **Controller** mengembalikan respons JSON "Sukses".
6.  **View** (Frontend) menampilkan pesan "Pengajuan Berhasil".
