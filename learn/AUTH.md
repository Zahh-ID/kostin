# Sistem Otentikasi (Authentication)

## Ringkasan
Sistem otentikasi Kostin dibangun di atas **Laravel Sanctum**. Kami menggunakan konfigurasi **Stateful** untuk SPA (Single Page Application). Ini adalah metode yang paling aman untuk aplikasi web karena menggunakan cookie `HttpOnly` yang tidak bisa dibaca oleh JavaScript, sehingga kebal terhadap pencurian token (XSS).

---

## Alur Otentikasi (Authentication Flow)

Berikut adalah langkah-langkah bagaimana seorang pengguna masuk ke dalam sistem:

### 1. Inisialisasi CSRF (CSRF Protection)
Sebelum melakukan request login, Frontend **wajib** meminta "cookie pembuka" terlebih dahulu.
-   **Request**: `GET /sanctum/csrf-cookie`
-   **Tujuan**: Server Laravel akan memberikan cookie `XSRF-TOKEN`.
-   **Otomatisasi**: Library Axios di frontend akan otomatis membaca cookie ini dan menyertakannya di *header* setiap request selanjutnya (`X-XSRF-TOKEN`). Ini memastikan request benar-benar datang dari aplikasi kita, bukan dari situs jahat.

### 2. Proses Login
-   **Endpoint**: `POST /api/v1/auth/login`
-   **Controller**: `AuthController@login`
-   **Proses**:
    1.  Validasi email dan password.
    2.  Jika valid, Laravel akan membuat ulang ID sesi (session regeneration) untuk keamanan.
    3.  Mengembalikan respons JSON berisi data user dan role-nya.
    4.  **Penting**: Tidak ada "token" yang dikembalikan di body JSON. Token sesi disimpan aman di dalam cookie browser.

### 3. Proses Register
-   **Endpoint**: `POST /api/v1/auth/register`
-   **Controller**: `AuthController@register`
-   **Proses**:
    1.  Validasi input (Password minimal 8 karakter, email unik, dll).
    2.  Membuat user baru di database.
    3.  Memberikan role default (misal: `tenant` atau `owner`).
    4.  Otomatis meloginkan user tersebut setelah registrasi sukses.

### 4. Lupa Password (Password Reset)
Fitur ini menggunakan email untuk verifikasi kepemilikan akun.
1.  **Request Link**: User memasukkan email di halaman "Lupa Password".
    -   Endpoint: `POST /api/v1/auth/forgot-password`
    -   Backend mengirim email berisi link unik ke user (menggunakan `resend-php`).
2.  **Reset Password**: User mengklik link di email, lalu diarahkan ke halaman frontend untuk membuat password baru.
    -   Endpoint: `POST /api/v1/auth/reset-password`
    -   Backend memverifikasi token dan mengubah password user.

---

## Middleware (Penjaga Akses)

Middleware berfungsi sebagai "satpam" yang mengecek apakah user boleh mengakses halaman tertentu.

### 1. `auth:sanctum`
Middleware bawaan Laravel Sanctum.
-   **Fungsi**: Memastikan user **sudah login**. Jika belum, server akan menolak dengan error `401 Unauthorized`.
-   **Digunakan di**: Hampir semua route API kecuali Login, Register, dan halaman publik (Landing Page).

### 2. `role:{nama_role}`
Middleware kustom (`App\Http\Middleware\RoleMiddleware`).
-   **Fungsi**: Memastikan user memiliki **jabatan/role** tertentu.
-   **Daftar Role**:
    -   `tenant`: Pencari kost (User biasa).
    -   `owner`: Pemilik kost (Mitra).
    -   `admin`: Administrator sistem (Super user).
-   **Contoh Penggunaan**:
    ```php
    // Hanya Owner yang boleh mengakses route ini
    Route::middleware('role:owner')->group(function () { ... });
    ```

---

## Daftar Controller Otentikasi

| Fitur | Controller | Lokasi File |
|-------|------------|-------------|
| **Login & Register** | `AuthController` | `app/Http/Controllers/Api/V1/AuthController.php` |
| **Kirim Link Reset** | `ForgotPasswordController` | `app/Http/Controllers/Api/V1/Auth/ForgotPasswordController.php` |
| **Ubah Password** | `ResetPasswordController` | `app/Http/Controllers/Api/V1/Auth/ResetPasswordController.php` |
