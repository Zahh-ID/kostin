# DashboardController

**Lokasi Kode**: `App\Http\Controllers\Api\V1\Admin`

## Penjelasan Singkat
Bagian ini adalah "pusat informasi" untuk Admin. Ia mengumpulkan semua data penting agar Admin bisa melihat kondisi aplikasi dalam sekali pandang.

## Daftar Fungsi

### `__invoke` (Fungsi Utama)
- **Kegunaan**: Menampilkan ringkasan statistik aplikasi.
- **Data yang ditampilkan**:
    - **Pendapatan Bulan Ini**: Total uang yang masuk dari pembayaran sewa.
    - **Pendaftaran Baru**: Berapa banyak orang yang baru mendaftar bulan ini.
    - **Properti Menunggu**: Jumlah kost baru yang perlu dicek dan disetujui Admin.
    - **Tiket Terbuka**: Jumlah keluhan atau pertanyaan pengguna yang belum selesai ditangani.
    - **Status Tagihan**: Ringkasan berapa tagihan yang sudah lunas, belum bayar, atau telat.
    - **Jumlah Pengguna**: Total admin, pemilik kost, dan pencari kost yang terdaftar.
    - **Grafik Tren**: Data untuk membuat grafik pendapatan dan pendaftaran selama 6 bulan terakhir.
