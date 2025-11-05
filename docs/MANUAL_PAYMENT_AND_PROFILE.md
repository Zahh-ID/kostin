# Manual Payment & Profile Management Documentation

## Penjelasan Tentang Midtrans Snap Design

### Mengapa Tidak Bisa Copy Design Midtrans Snap?

Midtrans Snap adalah **hosted payment page** yang UI-nya di-host dan di-render oleh server Midtrans, bukan oleh aplikasi Anda. Beberapa alasan teknis:

1. **Iframe/Modal Hosted**: Snap ditampilkan sebagai iframe atau popup yang content-nya dimuat dari server Midtrans
2. **Security**: Untuk keamanan PCI-DSS compliance, UI pembayaran harus berada di environment Midtrans
3. **Customization Terbatas**: Anda hanya bisa customize warna dan logo melalui dashboard Midtrans

### Alternatif Solusi

#### 1. Gunakan Midtrans Snap (Current Implementation)
✅ **Kelebihan:**
- Mudah diimplementasi
- PCI-DSS compliant
- Sudah ada UI yang bagus
- Support banyak metode pembayaran

❌ **Kekurangan:**
- Tidak bisa customize UI secara penuh
- Design tidak sesuai dengan brand aplikasi

#### 2. Gunakan Midtrans Core API (Custom UI)
Jika Anda ingin full control atas UI pembayaran:

```typescript
// Contoh implementasi custom payment UI dengan Core API
// NOTE: Lebih kompleks dan memerlukan PCI-DSS compliance

const response = await fetch('https://api.midtrans.com/v2/charge', {
  method: 'POST',
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
    'Authorization': 'Basic ' + btoa(serverKey + ':')
  },
  body: JSON.stringify({
    payment_type: 'gopay',
    transaction_details: { /* ... */ },
    gopay: {
      enable_callback: true,
      callback_url: 'your-callback-url'
    }
  })
});
```

✅ **Kelebihan:**
- Full control UI
- Bisa customize sepenuhnya

❌ **Kekurangan:**
- Implementasi lebih kompleks
- Perlu handle setiap payment method secara terpisah
- Tanggung jawab security lebih besar

#### 3. Manual Payment System (NEW - Implemented)
Solusi hybrid yang kami buat: **Manual payment dengan upload bukti transfer**

---

## Fitur Manual Payment System

### 1. Overview
Sistem pembayaran manual memungkinkan tenant untuk:
- Transfer ke rekening bank yang tersedia
- Bayar tunai di kantor kos
- Upload bukti pembayaran (foto/screenshot)
- Menunggu verifikasi dari admin/owner

### 2. Flow Pembayaran Manual

#### Untuk Tenant:
1. Buka halaman Tagihan (`/tenant/invoices`)
2. Klik tombol "Bayar" pada invoice yang pending
3. Pilih tab **"Transfer Manual"**
4. Pilih metode pembayaran (BCA, Mandiri, BNI, atau Cash)
5. Salin detail rekening tujuan
6. Lakukan transfer
7. Upload foto bukti pembayaran (max 5MB)
8. Tambahkan catatan (opsional)
9. Submit dan tunggu verifikasi

#### Untuk Owner/Admin:
1. Buka menu **"Verifikasi"** di navbar
2. Lihat daftar pembayaran yang menunggu verifikasi
3. Klik tombol "mata" untuk melihat detail
4. Review bukti pembayaran dan informasi
5. **Setujui** atau **Tolak** pembayaran
6. Jika tolak, berikan alasan penolakan
7. Status invoice akan otomatis diupdate

### 3. Data yang Disimpan

Backend menyimpan data pembayaran manual di KV Store:

```typescript
{
  paymentId: "MAN-INV-2024-001-1699234567890",
  invoiceId: "INV-2024-001",
  userId: "user-uuid",
  userName: "Nama Tenant",
  amount: 1200000,
  description: "Pembayaran November 2024",
  paymentMethod: "BCA",
  proofFile: "base64-image-data",
  fileName: "bukti-transfer.jpg",
  notes: "Transfer dari rekening pribadi",
  status: "pending_verification", // atau "approved", "rejected"
  submittedAt: "2024-11-01T10:30:00Z",
  verifiedBy: "admin-user-id",
  verifiedByName: "Admin Name",
  verifiedAt: "2024-11-01T11:00:00Z",
  rejectionReason: "Bukti transfer tidak jelas" // hanya jika rejected
}
```

### 4. API Endpoints

#### Submit Manual Payment
```
POST /make-server-dbd6b95a/payment/manual
Authorization: Bearer {access_token}

Body:
{
  "invoiceId": "INV-2024-001",
  "amount": 1200000,
  "description": "Pembayaran November 2024",
  "paymentMethod": "BCA",
  "proofFile": "data:image/jpeg;base64,...",
  "fileName": "bukti.jpg",
  "notes": "Optional notes"
}
```

#### Get Pending Payments (Admin/Owner only)
```
GET /make-server-dbd6b95a/payment/manual/pending
Authorization: Bearer {access_token}
```

#### Verify Payment (Admin/Owner only)
```
POST /make-server-dbd6b95a/payment/manual/verify
Authorization: Bearer {access_token}

Body:
{
  "paymentId": "MAN-INV-2024-001-1699234567890",
  "action": "approve" | "reject",
  "rejectionReason": "Required if action is reject"
}
```

---

## Fitur Profile Management

### 1. Overview
Sistem manajemen profil memungkinkan semua user untuk:
- Melihat dan edit informasi pribadi
- Mengubah password
- Melihat role dan email account

### 2. Fitur Profile Page

#### Tab 1: Informasi Profil
- **Avatar**: Tampilan foto profil (placeholder)
- **Email**: Tidak bisa diubah (read-only)
- **Nama Lengkap**: Bisa diedit
- **Nomor Telepon**: Bisa diedit
- **Alamat**: Bisa diedit
- **Role Badge**: Menampilkan role user (Tenant/Owner/Admin)

#### Tab 2: Keamanan
- **Password Saat Ini**: Required untuk validasi
- **Password Baru**: Minimal 6 karakter
- **Konfirmasi Password**: Harus sama dengan password baru
- **Toggle Show/Hide Password**: Untuk semua field password

### 3. Data yang Disimpan

Profile data disimpan di 2 tempat:

#### Supabase Auth (user_metadata):
```typescript
{
  name: "User Name",
  role: "tenant" | "owner" | "admin"
}
```

#### KV Store (additional profile data):
```typescript
{
  name: "User Name",
  phone: "08123456789",
  address: "Jl. Example No. 123",
  updatedAt: "2024-11-01T10:30:00Z"
}
```

### 4. API Endpoints

#### Get Profile
```
GET /make-server-dbd6b95a/profile
Authorization: Bearer {access_token}

Response:
{
  "profile": {
    "id": "user-uuid",
    "email": "user@example.com",
    "name": "User Name",
    "role": "tenant",
    "phone": "08123456789",
    "address": "Jl. Example No. 123"
  }
}
```

#### Update Profile
```
PUT /make-server-dbd6b95a/profile
Authorization: Bearer {access_token}

Body:
{
  "name": "Updated Name",
  "phone": "08123456789",
  "address": "New Address"
}
```

#### Change Password
```
POST /make-server-dbd6b95a/profile/change-password
Authorization: Bearer {access_token}

Body:
{
  "currentPassword": "old-password",
  "newPassword": "new-password-min-6-chars"
}
```

---

## Cara Menggunakan

### 1. Akses Profile Page
- Klik avatar/nama user di navbar kanan atas
- Pilih **"Profil"** dari dropdown menu
- Atau navigate langsung ke `/profile`

### 2. Edit Profile
1. Masuk ke tab **"Informasi Profil"**
2. Edit field yang ingin diubah
3. Klik **"Simpan Perubahan"**
4. Success message akan muncul

### 3. Change Password
1. Masuk ke tab **"Keamanan"**
2. Isi password saat ini
3. Isi password baru (min 6 karakter)
4. Konfirmasi password baru
5. Klik **"Ubah Password"**
6. Success message akan muncul

### 4. Bayar dengan Manual Payment
1. Tenant: Buka halaman Tagihan
2. Klik "Bayar" pada invoice pending
3. Pilih tab "Transfer Manual"
4. Ikuti instruksi pembayaran
5. Upload bukti
6. Submit dan tunggu verifikasi

### 5. Verifikasi Manual Payment (Owner/Admin)
1. Klik menu **"Verifikasi"** di navbar
2. Lihat daftar pembayaran pending
3. Klik icon "mata" untuk detail
4. Review bukti dan informasi
5. Klik "Setujui" atau "Tolak"
6. Jika tolak, isi alasan penolakan
7. Konfirmasi action

---

## Keamanan

### File Upload Security
1. **File Type Validation**: Hanya accept file image (image/*)
2. **File Size Limit**: Maximum 5MB
3. **Base64 Encoding**: File dikonversi ke base64 untuk storage
4. **Authorization**: Semua endpoints memerlukan valid access token

### Password Security
1. **Minimum Length**: 6 karakter (bisa ditingkatkan)
2. **Current Password Validation**: Diperlukan untuk change password
3. **Supabase Auth**: Password di-hash oleh Supabase Auth service
4. **No Password Storage**: Password tidak disimpan di KV store

### Role-Based Access
1. **Verification Endpoints**: Hanya admin dan owner yang bisa akses
2. **Profile Endpoints**: User hanya bisa edit profile sendiri
3. **Token Validation**: Setiap request divalidasi dengan Supabase Auth

---

## Production Considerations

### Storage Optimization
Untuk production, pertimbangkan menggunakan **Supabase Storage** untuk bukti pembayaran:

```typescript
// Upload ke Supabase Storage (contoh)
const { data, error } = await supabase.storage
  .from('payment-proofs')
  .upload(`${userId}/${paymentId}.jpg`, file);

// Simpan hanya file path, bukan base64
manualPayment.proofFilePath = data.path;
```

### Email Notifications
Tambahkan email notification untuk:
- Tenant: Ketika payment verified/rejected
- Admin/Owner: Ketika ada payment baru yang perlu diverifikasi

### Image Compression
Compress image sebelum upload untuk menghemat storage:

```typescript
// Contoh dengan browser-image-compression
import imageCompression from 'browser-image-compression';

const options = {
  maxSizeMB: 1,
  maxWidthOrHeight: 1920
};
const compressedFile = await imageCompression(file, options);
```

### Audit Log
Simpan log untuk semua action verifikasi:

```typescript
{
  action: 'approve_payment',
  paymentId: 'MAN-xxx',
  performedBy: 'admin-id',
  performedAt: '2024-11-01T10:30:00Z',
  notes: 'Additional context'
}
```

---

## Troubleshooting

### Issue: Profile tidak ter-update
**Solution:**
1. Pastikan access token valid
2. Check console untuk error messages
3. Verify backend endpoint berjalan
4. Clear browser cache dan reload

### Issue: File upload gagal
**Solution:**
1. Check file size (max 5MB)
2. Pastikan file type adalah image
3. Check network tab untuk error response
4. Verify backend dapat menerima base64 data

### Issue: Password change gagal
**Solution:**
1. Pastikan password baru minimal 6 karakter
2. Pastikan konfirmasi password match
3. Check jika current password benar
4. Logout dan login kembali jika perlu

### Issue: Verification tidak muncul untuk admin/owner
**Solution:**
1. Pastikan role user adalah 'admin' atau 'owner'
2. Check access token valid
3. Verify backend endpoint role validation
4. Reload page untuk refresh data

---

## Next Steps & Improvements

### Short Term:
- [ ] Add image preview/zoom untuk bukti pembayaran
- [ ] Add filtering dan sorting di verification page
- [ ] Add notification counter untuk pending payments
- [ ] Add email notification system
- [ ] Add upload progress indicator

### Medium Term:
- [ ] Migrate file storage ke Supabase Storage
- [ ] Add image compression untuk upload
- [ ] Add payment history untuk tenant
- [ ] Add audit log untuk verification actions
- [ ] Add bulk approval untuk multiple payments

### Long Term:
- [ ] Add OCR untuk auto-detect payment amount dari bukti
- [ ] Add integration dengan bank API untuk auto-verification
- [ ] Add photo avatar upload untuk profile
- [ ] Add 2FA untuk security enhancement
- [ ] Add webhook notification untuk third-party integration

---

## Kesimpulan

Dengan sistem manual payment dan profile management ini, aplikasi KosKita sekarang memiliki:

✅ **Dual Payment System**: Snap QRIS + Manual Transfer
✅ **Complete Profile Management**: Edit info + change password
✅ **Role-Based Verification**: Admin/Owner dapat approve/reject
✅ **Better UX**: User punya pilihan metode pembayaran
✅ **Security**: Proper authorization dan validation
✅ **Scalable**: Ready untuk production dengan minor improvements

**Note**: Untuk Midtrans Snap customization penuh, Anda perlu upgrade ke Midtrans Core API, namun manual payment system ini sudah menyediakan alternatif yang baik dengan UI yang bisa dikontrol sepenuhnya.
