# Dokumentasi Endpoint API Detail

Berikut adalah spesifikasi teknis untuk setiap endpoint API.

**Base URL**: `https://kostin-api.syzzhd.web.id/api/v1`

---

## 1. Otentikasi (Authentication)

### Login
*   **Endpoint**: `POST /auth/login`
*   **Body**:
    *   `email` (string, required): Email pengguna.
    *   `password` (string, required): Password akun.
*   **Respons Sukses (200)**:
    ```json
    {
        "message": "Login successful",
        "user": { "id": 1, "name": "Budi", "role": "tenant", ... }
    }
    ```

### Register
*   **Endpoint**: `POST /auth/register`
*   **Body**:
    *   `name` (string, required): Nama lengkap.
    *   `email` (string, required): Email unik.
    *   `password` (string, required): Min 8 karakter.
    *   `role` (string, required): `tenant` atau `owner`.
*   **Respons Sukses (201)**: User dibuat dan otomatis login.

### Forgot Password
*   **Endpoint**: `POST /auth/forgot-password`
*   **Body**:
    *   `email` (string, required): Email terdaftar.
*   **Respons**: Mengirim email berisi link reset.

---

## 2. Fitur Penyewa (Tenant)

### Cari Kost (Search)
*   **Endpoint**: `GET /tenant/search`
*   **Query Params**:
    *   `q` (string): Kata kunci nama/alamat.
    *   `min_price` (int): Harga minimum.
    *   `max_price` (int): Harga maksimum.
    *   `facilities` (array): Filter fasilitas (misal: `?facilities[]=AC&facilities[]=WiFi`).
*   **Respons**: Array objek properti.

### Detail Kost
*   **Endpoint**: `GET /tenant/properties/{id}`
*   **Respons**: Detail lengkap kost termasuk tipe kamar (`room_types`) dan ketersediaan kamar (`rooms`).

### Ajukan Sewa (Apply)
*   **Endpoint**: `POST /tenant/applications`
*   **Body**:
    *   `room_id` (int, required): ID Kamar yang dipilih.
    *   `start_date` (date, required): Tanggal rencana masuk (YYYY-MM-DD).
    *   `duration_months` (int, optional): Rencana lama sewa.

### Bayar Tagihan (Get Snap Token)
*   **Endpoint**: `POST /tenant/invoices/{id}/pay`
*   **Respons**:
    ```json
    {
        "token": "snap_token_xyz123",
        "redirect_url": "https://app.sandbox.midtrans.com/snap/..."
    }
    ```
    Gunakan token ini di Frontend untuk memunculkan popup pembayaran.

### Upload Bukti Bayar (Manual)
*   **Endpoint**: `POST /tenant/invoices/{id}/manual-payment`
*   **Body**:
    *   `proof_file` (file, required): Gambar bukti transfer (JPG/PNG).
    *   `bank_name` (string): Nama bank pengirim.
    *   `account_holder` (string): Nama pemilik rekening pengirim.

---

## 3. Fitur Pemilik (Owner)

### Tambah Properti
*   **Endpoint**: `POST /owner/properties`
*   **Body**:
    *   `name` (string): Nama kost.
    *   `address` (string): Alamat lengkap.
    *   `rules_text` (string): Peraturan kost.
    *   `photos[]` (array of files): Foto-foto kost.

### Tambah Kamar Massal (Bulk Create)
*   **Endpoint**: `POST /owner/properties/{id}/rooms/bulk`
*   **Body**:
    *   `room_type_id` (int): Tipe kamar.
    *   `prefix` (string): Awalan nomor (misal: "A-").
    *   `start_number` (int): Nomor mulai (misal: 1).
    *   `count` (int): Jumlah kamar yang dibuat (misal: 10).
    *   **Hasil**: Membuat kamar A-1, A-2, ..., A-10.

### Setujui Penyewa
*   **Endpoint**: `POST /owner/applications/{id}/approve`
*   **Efek**: Mengubah status aplikasi jadi `approved`, membuat kontrak aktif, dan mengubah status kamar jadi `occupied`.

### Tarik Dana (Withdraw)
*   **Endpoint**: `POST /owner/wallet/withdraw`
*   **Body**:
    *   `amount` (int): Jumlah penarikan.
    *   `bank_details` (string): Info rekening tujuan.

---

## 4. Fitur Admin

### Moderasi Kost
*   **Endpoint**: `POST /admin/moderations/{id}/approve`
*   **Efek**: Mengubah status properti dari `pending` menjadi `approved`. Properti kini muncul di pencarian publik.

### Blokir User
*   **Endpoint**: `POST /admin/users/{id}/suspend`
*   **Body**:
    *   `reason` (string): Alasan pemblokiran.
*   **Efek**: User tidak bisa login lagi.

---

## 5. Webhook (System)

### Midtrans Notification
*   **Endpoint**: `POST /webhook/midtrans`
*   **Body**: JSON payload dari Midtrans.
*   **Logika**:
    1.  Cek `signature_key` untuk keamanan.
    2.  Cek `transaction_status` (`settlement`, `capture` -> Sukses).
    3.  Update status invoice di database.
