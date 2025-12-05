# Arsitektur Frontend

## Ringkasan
Frontend Kostin adalah **Single Page Application (SPA)** yang modern dan responsif. Dibangun menggunakan **React 18** dan **Vite**, aplikasi ini menawarkan pengalaman pengguna yang cepat layaknya aplikasi native. Desain antarmuka dibuat menggunakan **Tailwind CSS** untuk fleksibilitas maksimal.

---

## Struktur Proyek (Project Structure)

Berikut adalah bedah struktur folder `frontend/src/` agar Anda mudah menavigasi kode:

```
frontend/src/
├── api/
│   └── client.js       # JANTUNG KOMUNIKASI API. Semua request ke backend diatur di sini.
├── components/         # Komponen UI yang bisa dipakai ulang (Reusable).
│   ├── SEO.jsx         # Komponen untuk mengatur Meta Tags & Judul Halaman dinamis.
│   ├── Navbar.jsx      # Menu navigasi atas.
│   └── Footer.jsx      # Bagian kaki halaman.
├── pages/              # Halaman-halaman utama (Views).
│   ├── admin/          # Halaman khusus Dashboard Admin.
│   ├── owner/          # Halaman khusus Dashboard Owner.
│   ├── tenant/         # Halaman khusus Dashboard Penyewa.
│   └── ...             # Halaman publik (Home, Login, Register, Search).
├── ui/                 # Komponen UI atomik (Tombol, Input, Card, Modal).
├── main.jsx            # Titik masuk aplikasi (Entry Point) & Konfigurasi Routing.
└── styles.css          # File CSS global & konfigurasi Tailwind.
```

---

## Integrasi API (`api/client.js`)

Kami tidak melakukan `fetch` atau `axios` sembarangan di setiap komponen. Semua komunikasi ke backend dipusatkan di `api/client.js`.

### Konfigurasi Utama
-   **Base URL**: Diambil dari `.env` (`VITE_API_BASE_URL`). Defaultnya `http://localhost:8000`.
-   **Otentikasi Otomatis**:
    -   `withCredentials: true`: Memastikan cookie sesi dikirim di setiap request.
    -   `Interceptor`: Menangani error secara global. Jika backend membalas `401 Unauthorized`, user otomatis di-logout dan diarahkan ke halaman login.

### Metode Penting
-   `api.login(email, password)`: Melakukan handshake CSRF lalu mengirim kredensial.
-   `api.register(data)`: Mengirim data pendaftaran user baru.
-   `api.logout()`: Menghapus sesi di backend dan frontend.
-   `api.getProfile()`: Mengambil data user yang sedang login (`/api/v1/auth/me`).

---

## Routing & Navigasi

Navigasi diatur oleh **React Router DOM (v7)** di file `main.jsx`.

### Konsep "Protected Routes" (Rute Terlindungi)
Kami menggunakan komponen pembungkus (Wrapper) untuk melindungi halaman tertentu agar tidak bisa diakses sembarang orang.

1.  **GuestRoute**: Hanya untuk user yang **BELUM** login.
    -   Contoh: Halaman Login, Register.
    -   Jika user sudah login mencoba akses ini, mereka dilempar ke Dashboard.

2.  **PrivateRoute**: Hanya untuk user yang **SUDAH** login.
    -   Contoh: Dashboard, Edit Profil.
    -   Jika tamu mencoba akses ini, mereka dilempar ke Halaman Login.

3.  **Role-Based Route**: Hanya untuk user dengan **JABATAN** tertentu.
    -   Contoh: `/owner/*` hanya bisa dibuka oleh user dengan role `owner`.
    -   Jika `tenant` mencoba buka halaman `owner`, mereka akan melihat halaman "403 Forbidden" atau dialihkan.

---

## Styling & Desain

-   **Tailwind CSS**: Kami jarang menulis CSS manual (`.css`). Sebaliknya, kami menggunakan kelas utilitas langsung di JSX.
    -   Contoh: `<div className="bg-blue-500 text-white p-4 rounded-lg">`
-   **Animasi**:
    -   **Framer Motion**: Digunakan untuk animasi transisi antar halaman dan elemen interaktif (seperti Modal yang muncul perlahan).
    -   **GSAP**: Digunakan di Landing Page untuk animasi scrolling yang kompleks dan performa tinggi.

---

## Pustaka Kunci (Key Libraries)

-   **react-helmet-async**: Mengubah judul tab browser (`<title>`) sesuai halaman yang dibuka. Penting untuk SEO.
-   **react-icons**: Menyediakan ribuan ikon vektor. Kami menggunakan paket `Fa` (FontAwesome) dan `Md` (Material Design).
-   **react-hot-toast**: Menampilkan notifikasi "Toast" (popup kecil) yang cantik untuk sukses/gagal operasi.
