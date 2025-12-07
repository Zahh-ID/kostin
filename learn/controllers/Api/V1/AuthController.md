# AuthController

**Lokasi Kode**: `App\Http\Controllers\Api\V1`

## Penjelasan Singkat
Bagian ini adalah "pintu gerbang" aplikasi. Ia mengatur siapa saja yang boleh masuk (login), mendaftar akun baru (register), dan keluar dari aplikasi (logout).

## Daftar Fungsi

### `register`
- **Kegunaan**: Mendaftarkan pengguna baru ke dalam sistem.
- **Data yang dibutuhkan**:
    - `name`: Nama lengkap.
    - `email`: Alamat email aktif.
    - `password`: Kata sandi (minimal 8 karakter).
    - `role`: Peran pengguna (apakah sebagai pencari kost, pemilik kost, atau admin).

### `login`
- **Kegunaan**: Masuk ke dalam aplikasi bagi pengguna yang sudah punya akun.
- **Data yang dibutuhkan**: Email dan Password.
- **Hasil**: Memberikan "kunci akses" (token) agar pengguna bisa menggunakan fitur-fitur lainnya.

### `logout`
- **Kegunaan**: Keluar dari aplikasi.
- **Hasil**: "Kunci akses" (token) akan dihancurkan sehingga tidak bisa dipakai lagi.

### `me`
- **Kegunaan**: Mengecek "Siapa saya?".
- **Hasil**: Menampilkan data profil pengguna yang sedang login saat ini.
