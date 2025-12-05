# Skema Database (Database Schema)

Dokumen ini menjelaskan struktur database aplikasi Kostin secara rinci, termasuk tabel, kolom, tipe data, dan relasi antar tabel.

---

## 1. Otentikasi & Pengguna (Auth & Users)

### `users`
Tabel utama untuk menyimpan data pengguna aplikasi.
| Kolom | Tipe Data | Deskripsi |
|-------|-----------|-----------|
| `id` | BIGINT (PK) | Primary Key. |
| `name` | STRING | Nama lengkap pengguna. |
| `email` | STRING | Alamat email (Unik). |
| `phone` | STRING | Nomor telepon (Opsional). |
| `role` | ENUM | Peran pengguna: `'admin'`, `'owner'`, `'tenant'`. |
| `password` | STRING | Password terenkripsi (Bcrypt). |
| `google_id` | STRING | ID akun Google (jika login via Google). |
| `suspended_at` | TIMESTAMP | Waktu pengguna diblokir (jika ada). |

### `personal_access_tokens`
Menyimpan token API untuk otentikasi (Sanctum).
| Kolom | Tipe Data | Deskripsi |
|-------|-----------|-----------|
| `tokenable_id` | BIGINT | ID User pemilik token. |
| `name` | STRING | Nama token (misal: "auth_token"). |
| `token` | STRING | Hash token SHA-256. |
| `abilities` | JSON | Hak akses token (default: `["*"]`). |

---

## 2. Manajemen Properti (Property Management)

### `properties`
Menyimpan data rumah kost.
| Kolom | Tipe Data | Relasi | Deskripsi |
|-------|-----------|--------|-----------|
| `id` | BIGINT (PK) | - | Primary Key. |
| `owner_id` | BIGINT | `users.id` | Pemilik kost. |
| `name` | STRING | - | Nama kost. |
| `address` | STRING | - | Alamat lengkap. |
| `lat`, `lng` | DECIMAL | - | Koordinat lokasi (Latitude, Longitude). |
| `rules_text` | TEXT | - | Peraturan kost. |
| `photos` | JSON | - | Array URL foto-foto kost. |
| `status` | ENUM | - | Status moderasi: `'draft'`, `'pending'`, `'approved'`, `'rejected'`. |

### `room_types`
Kategori atau tipe kamar dalam sebuah properti.
| Kolom | Tipe Data | Relasi | Deskripsi |
|-------|-----------|--------|-----------|
| `id` | BIGINT (PK) | - | Primary Key. |
| `property_id` | BIGINT | `properties.id` | Kost induk. |
| `name` | STRING | - | Nama tipe (misal: "Tipe A - AC"). |
| `price` | INTEGER | - | Harga dasar per bulan. |
| `facilities` | JSON | - | Daftar fasilitas (misal: `["AC", "WiFi"]`). |

### `rooms`
Unit kamar fisik yang disewakan.
| Kolom | Tipe Data | Relasi | Deskripsi |
|-------|-----------|--------|-----------|
| `id` | BIGINT (PK) | - | Primary Key. |
| `property_id` | BIGINT | `properties.id` | Kost induk. |
| `room_type_id` | BIGINT | `room_types.id` | Jenis kamar. |
| `name` | STRING | - | Nomor/Nama kamar (misal: "A-101"). |
| `status` | ENUM | - | `'available'`, `'occupied'`, `'maintenance'`. |

---

## 3. Penyewaan (Tenancy)

### `rental_applications`
Permohonan sewa dari calon penyewa.
| Kolom | Tipe Data | Relasi | Deskripsi |
|-------|-----------|--------|-----------|
| `id` | BIGINT (PK) | - | Primary Key. |
| `tenant_id` | BIGINT | `users.id` | Pemohon sewa. |
| `room_id` | BIGINT | `rooms.id` | Kamar yang diinginkan. |
| `status` | ENUM | - | `'pending'`, `'approved'`, `'rejected'`, `'canceled'`. |

### `contracts`
Perjanjian sewa aktif antara Tenant dan Owner.
| Kolom | Tipe Data | Relasi | Deskripsi |
|-------|-----------|--------|-----------|
| `id` | BIGINT (PK) | - | Primary Key. |
| `tenant_id` | BIGINT | `users.id` | Penyewa. |
| `room_id` | BIGINT | `rooms.id` | Kamar yang disewa. |
| `start_date` | DATE | - | Tanggal mulai sewa. |
| `end_date` | DATE | - | Tanggal berakhir (bisa NULL jika perpanjang otomatis). |
| `price_per_month` | INTEGER | - | Harga sewa yang disepakati. |
| `billing_day` | INTEGER | - | Tanggal jatuh tempo tagihan bulanan (1-28). |
| `status` | ENUM | - | `'active'`, `'pending_renewal'`, `'terminated'`, `'canceled'`. |

---

## 4. Keuangan (Finance)

### `invoices`
Tagihan sewa bulanan.
| Kolom | Tipe Data | Relasi | Deskripsi |
|-------|-----------|--------|-----------|
| `id` | BIGINT (PK) | - | Primary Key. |
| `contract_id` | BIGINT | `contracts.id` | Kontrak terkait. |
| `period_month` | INTEGER | - | Bulan tagihan (1-12). |
| `period_year` | INTEGER | - | Tahun tagihan. |
| `amount` | INTEGER | - | Jumlah tagihan pokok. |
| `late_fee` | INTEGER | - | Denda keterlambatan. |
| `total` | INTEGER | - | Total yang harus dibayar (`amount` + `late_fee`). |
| `status` | ENUM | - | `'unpaid'`, `'paid'`, `'overdue'`, `'pending_verification'`. |
| `external_order_id` | STRING | - | ID Order Midtrans (untuk pembayaran otomatis). |

### `payments`
Catatan transaksi pembayaran.
| Kolom | Tipe Data | Relasi | Deskripsi |
|-------|-----------|--------|-----------|
| `id` | BIGINT (PK) | - | Primary Key. |
| `invoice_id` | BIGINT | `invoices.id` | Tagihan yang dibayar. |
| `amount` | INTEGER | - | Jumlah yang dibayarkan. |
| `method` | ENUM | - | `'midtrans'`, `'manual'`. |
| `status` | ENUM | - | `'pending'`, `'success'`, `'failed'`. |
| `proof_url` | STRING | - | URL foto bukti transfer (jika manual). |
| `midtrans_data` | JSON | - | Data respons lengkap dari Midtrans. |

### `owner_wallets`
Saldo pendapatan pemilik kost.
| Kolom | Tipe Data | Relasi | Deskripsi |
|-------|-----------|--------|-----------|
| `id` | BIGINT (PK) | - | Primary Key. |
| `owner_id` | BIGINT | `users.id` | Pemilik dompet. |
| `balance` | INTEGER | - | Saldo saat ini. |

---

## 5. Dukungan (Support)

### `tickets`
Tiket komplain atau laporan masalah.
| Kolom | Tipe Data | Relasi | Deskripsi |
|-------|-----------|--------|-----------|
| `id` | BIGINT (PK) | - | Primary Key. |
| `user_id` | BIGINT | `users.id` | Pelapor (Tenant). |
| `property_id` | BIGINT | `properties.id` | Lokasi masalah. |
| `subject` | STRING | - | Judul masalah. |
| `status` | ENUM | - | `'open'`, `'in_progress'`, `'resolved'`, `'closed'`. |
| `priority` | ENUM | - | `'low'`, `'medium'`, `'high'`. |

### `ticket_comments`
Balasan atau komentar pada tiket.
| Kolom | Tipe Data | Relasi | Deskripsi |
|-------|-----------|--------|-----------|
| `id` | BIGINT (PK) | - | Primary Key. |
| `ticket_id` | BIGINT | `tickets.id` | Tiket induk. |
| `user_id` | BIGINT | `users.id` | Pengirim pesan. |
| `content` | TEXT | - | Isi pesan. |
