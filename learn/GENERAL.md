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

## Daftar Lengkap Pustaka (Libraries)

Berikut adalah daftar lengkap pustaka yang digunakan dalam proyek ini, dikategorikan berdasarkan fungsinya.

### 1. Backend - Dependensi Utama (`composer.json`)
Pustaka ini wajib ada agar aplikasi dapat berjalan di server produksi.

| Paket | Versi | Fungsi & Kegunaan |
|-------|-------|-------------------|
| `laravel/framework` | ^11.0 | **Core Framework**: Jantung dari aplikasi Kostin. Menyediakan routing, ORM (Eloquent), dan fitur dasar lainnya. |
| `laravel/sanctum` | ^4.2 | **Otentikasi API**: Menangani otentikasi berbasis cookie (SPA) dan token API yang aman. |
| `laravel/socialite` | ^5.23 | **Login Sosial**: Memungkinkan pengguna login menggunakan akun Google (OAuth). |
| `laravel/reverb` | ^1.6 | **WebSocket Server**: Server real-time bawaan Laravel untuk fitur komunikasi instan (chat/notifikasi). |
| `barryvdh/laravel-dompdf` | ^3.1 | **PDF Generator**: Mengubah tampilan HTML (Blade) menjadi file PDF. Digunakan untuk mencetak Surat Perjanjian Sewa. |
| `darkaonline/l5-swagger` | ^9.0 | **Dokumentasi API**: Menghasilkan halaman dokumentasi API interaktif (OpenAPI/Swagger) secara otomatis. |
| `guzzlehttp/guzzle` | ^7.10 | **HTTP Client**: Pustaka standar PHP untuk mengirim request HTTP ke layanan eksternal (misal: API Midtrans). |
| `livewire/livewire` | ^3.6 | **Full-stack Framework**: (Opsional/Bawaan) Terinstall sebagai bagian dari ekosistem Laravel, namun frontend utama menggunakan React. |
| `livewire/volt` | ^1.7 | **Functional API**: Pelengkap Livewire untuk penulisan komponen yang lebih ringkas. |
| `laravel/ui` | ^4.4 | **Auth Scaffolding**: Paket legacy untuk setup otentikasi dasar (kemungkinan digunakan untuk preset awal). |

> **Catatan**: Pustaka `midtrans/midtrans-php` dan `resend/resend-php` mungkin diinstall secara manual atau merupakan dependensi tidak langsung, namun sangat krusial untuk fitur Pembayaran dan Email.

### 2. Backend - Dependensi Pengembangan (`require-dev`)
Pustaka ini hanya digunakan saat pengembangan (local) dan testing.

| Paket | Fungsi & Kegunaan |
|-------|-------------------|
| `fakerphp/faker` | **Data Palsu**: Men-generate data dummy (Nama, Alamat, Email) untuk keperluan testing database (Seeding). |
| `laravel/tinker` | **REPL**: Terminal interaktif untuk mencoba kode PHP/Laravel secara langsung tanpa membuat route. |
| `laravel/sail` | **Docker Environment**: Lingkungan pengembangan berbasis Docker yang ringan. |
| `laravel/pint` | **Code Style**: Memperbaiki format kode PHP secara otomatis agar rapi dan konsisten. |
| `pestphp/pest` | **Testing Framework**: Framework testing modern dengan sintaks yang lebih sederhana dibanding PHPUnit. |
| `mockery/mockery` | **Mocking**: Membuat objek tiruan (mock) untuk isolasi saat unit testing. |
| `nunomaduro/collision` | **Error Reporting**: Menampilkan pesan error yang indah dan informatif di terminal. |

---

### 3. Frontend - Dependensi Utama (`package.json`)
Pustaka yang dibundle bersama aplikasi React untuk pengguna.

| Paket | Versi | Fungsi & Kegunaan |
|-------|-------|-------------------|
| `react` | ^18.3 | **UI Library**: Pustaka inti untuk membangun antarmuka pengguna berbasis komponen. |
| `react-dom` | ^18.3 | **DOM Renderer**: Menghubungkan React dengan browser (DOM). |
| `react-router-dom` | ^7.0 | **Routing**: Mengatur navigasi halaman (pindah URL) tanpa reload halaman penuh. |
| `axios` | ^1.11 | **HTTP Client**: Melakukan request AJAX ke API backend. Dikonfigurasi dengan interceptor untuk token. |
| `framer-motion` | ^11.9 | **Animasi**: Pustaka animasi deklaratif untuk React. Digunakan untuk transisi halaman dan efek hover. |
| `gsap` | ^3.12 | **Animasi Pro**: GreenSock Animation Platform. Digunakan untuk animasi kompleks dengan performa tinggi (misal: Landing Page). |
| `react-helmet-async` | ^2.0 | **SEO**: Mengelola tag `<head>` (Title, Meta Description) secara dinamis di setiap halaman. |
| `react-icons` | ^5.5 | **Ikon**: Koleksi ikon vektor (SVG) dari berbagai set populer (FontAwesome, Material, dll). |

### 4. Frontend - Dependensi Pengembangan (`devDependencies`)
Alat bantu untuk memproses kode sebelum siap diproduksi.

| Paket | Fungsi & Kegunaan |
|-------|-------------------|
| `vite` | **Build Tool**: Server pengembangan super cepat dan bundler untuk produksi. |
| `tailwindcss` | **CSS Framework**: Framework CSS utility-first untuk styling cepat. |
| `postcss` | **CSS Processor**: Alat untuk memtransformasi CSS (dipakai oleh Tailwind). |
| `autoprefixer` | **Vendor Prefix**: Menambahkan prefix CSS otomatis (misal: `-webkit-`) agar kompatibel dengan browser lama. |
| `@vitejs/plugin-react` | **Vite Plugin**: Plugin resmi agar Vite bisa mengerti sintaks JSX/React. |

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
