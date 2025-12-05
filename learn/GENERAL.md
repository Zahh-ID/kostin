# Arsitektur Sistem & Teknologi Umum

## Tumpukan Teknologi (Tech Stack)

Aplikasi Kostin dibangun menggunakan teknologi modern yang menjamin performa, keamanan, dan kemudahan pengembangan.

### Backend (Sisi Server)
-   **Bahasa**: PHP 8.2+
-   **Framework**: **Laravel 11** - Framework PHP terpopuler yang menawarkan sintaks ekspresif dan fitur keamanan bawaan yang kuat.
-   **Database**: MySQL / MariaDB - Relational Database Management System (RDBMS) untuk penyimpanan data terstruktur.
-   **Web Server**: Nginx - Server web berkinerja tinggi untuk melayani aplikasi PHP.

### Frontend (Sisi Klien)
-   **Framework**: **React 18** - Library JavaScript untuk membangun antarmuka pengguna yang interaktif.
-   **Build Tool**: **Vite** - Build tool generasi baru yang sangat cepat untuk pengembangan frontend.
-   **Bahasa**: JavaScript (ES6+)
-   **Styling**: **Tailwind CSS** - Framework CSS utility-first untuk desain yang cepat dan responsif.
-   **State Management**: React Context & Hooks - Pengelolaan state aplikasi tanpa library pihak ketiga yang berat.

---

## Pustaka & Paket Kunci (Key Libraries)

### Backend (Laravel)
| Paket | Fungsi & Kegunaan |
|-------|-------------------|
| `laravel/sanctum` | **Otentikasi SPA**: Menangani otentikasi berbasis cookie yang aman untuk Single Page Application, serta token API untuk penggunaan mobile/eksternal. |
| `laravel/socialite` | **Login Sosial**: Memudahkan integrasi login menggunakan akun Google (OAuth). |
| `laravel/reverb` | **Real-time**: Server WebSocket bawaan Laravel untuk fitur real-time seperti notifikasi instan. |
| `barryvdh/laravel-dompdf` | **Pembuatan PDF**: Digunakan untuk men-generate Surat Perjanjian Sewa (Kontrak) dalam format PDF secara otomatis. |
| `darkaonline/l5-swagger` | **Dokumentasi API**: Menghasilkan dokumentasi API interaktif (Swagger UI) agar mudah diuji oleh pengembang frontend. |
| `resend/resend-php` | **Email Transaksional**: Layanan pengiriman email modern untuk mengirim notifikasi penting (Lupa Password, Pengingat Tagihan). |
| `midtrans/midtrans-php` | **Payment Gateway**: Integrasi pembayaran digital (QRIS, Virtual Account, E-Wallet) yang populer di Indonesia. |

### Frontend (React)
| Paket | Fungsi & Kegunaan |
|-------|-------------------|
| `axios` | **HTTP Client**: Melakukan request ke API backend dengan konfigurasi interceptor otomatis (untuk token & error handling). |
| `react-router-dom` | **Routing**: Mengatur navigasi halaman di sisi klien tanpa reload browser (SPA experience). |
| `framer-motion` | **Animasi UI**: Membuat transisi halaman dan animasi elemen yang halus dan menarik. |
| `gsap` | **Animasi Kompleks**: Digunakan untuk animasi landing page yang membutuhkan performa tinggi. |
| `react-helmet-async` | **SEO Dinamis**: Mengubah Title dan Meta Tags halaman secara dinamis agar ramah mesin pencari (Google). |
| `react-icons` | **Ikon**: Kumpulan ikon vektor (SVG) dari berbagai library populer (FontAwesome, Material Design, dll). |

---

## Gambaran Arsitektur (Architecture Overview)

Aplikasi ini menggunakan **Decoupled Architecture** (Terpisah), di mana Backend dan Frontend adalah dua aplikasi yang berdiri sendiri namun saling berkomunikasi.

### 1. API Layer (Backend)
-   Berfungsi sebagai penyedia data (REST API).
-   Tidak merender HTML (kecuali untuk email), hanya mengirimkan data dalam format JSON.
-   Menangani logika bisnis yang kompleks, validasi data, dan keamanan database.
-   **Stateless**: Secara umum tidak menyimpan state sesi di server, namun menggunakan cookie terenkripsi dari Sanctum untuk mengenali pengguna.

### 2. Client Layer (Frontend)
-   Aplikasi Single Page Application (SPA) yang berjalan di browser pengguna.
-   Mengambil data dari API dan menampilkannya ke pengguna.
-   Menangani interaksi pengguna, validasi formulir di sisi klien, dan navigasi.

### 3. Komunikasi & Keamanan
-   **CORS (Cross-Origin Resource Sharing)**: Dikonfigurasi agar Backend hanya menerima permintaan dari domain Frontend yang diizinkan.
-   **Sanctum Auth**:
    -   Menggunakan cookie `laravel_session` dan `XSRF-TOKEN`.
    -   Cookie `HttpOnly` digunakan untuk keamanan maksimal guna mencegah serangan XSS (Cross-Site Scripting).
    -   Token CSRF dikirimkan otomatis oleh Axios untuk mencegah serangan CSRF (Cross-Site Request Forgery).
