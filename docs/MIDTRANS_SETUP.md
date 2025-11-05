# Setup Midtrans Payment Gateway

Aplikasi ini menggunakan Midtrans Snap untuk pembayaran dengan **popup mode** yang seamless.

## 1. Dapatkan API Keys

1. Daftar atau login ke [Midtrans Dashboard](https://dashboard.midtrans.com)
2. Pilih environment **Sandbox** untuk testing
3. Buka **Settings → Access Keys**
4. Salin **Server Key** dan **Client Key**

## 2. Konfigurasi Environment Variables

### Server Key & Client Key ✅

Keys sudah dikonfigurasi di Supabase Secrets:
- `MIDTRANS_SERVER_KEY` ✅ Already configured
- `MIDTRANS_CLIENT_KEY` ✅ Already configured

### Environment Mode (Opsional)

Tambahkan `MIDTRANS_ENV` di Supabase Secrets untuk mengatur mode:
- `sandbox` (default) - untuk testing
- `production` - untuk live

## 3. Cara Kerja Popup Midtrans Snap ✅

### Flow Pembayaran
1. ✅ User klik tombol "Bayar Sekarang"
2. ✅ Frontend request ke backend `/payment/create`
3. ✅ Backend generate Snap Token + return Client Key
4. ✅ Frontend load Snap.js script dengan Client Key yang benar
5. ✅ Frontend panggil `window.snap.pay(token)` → **POPUP MUNCUL**
6. ✅ User selesaikan pembayaran di popup Midtrans
7. ✅ Callback onSuccess/onPending/onError dipanggil
8. ✅ Backend verify status pembayaran

### Keuntungan Popup Mode
- ✅ User tetap di aplikasi (tidak redirect)
- ✅ Seamless UX experience
- ✅ Callback langsung di-handle
- ✅ Tidak perlu redirect URL

## 4. Konfigurasi Notification URL (Webhook)

Untuk menerima notifikasi pembayaran otomatis:

1. Buka **Settings → Configuration** di Midtrans Dashboard
2. Tambahkan **Notification URL**:
   ```
   https://[PROJECT_ID].supabase.co/functions/v1/make-server-dbd6b95a/payment/notification
   ```
3. Ganti `[PROJECT_ID]` dengan Supabase Project ID Anda

## 5. Testing Pembayaran (Sandbox)

### Metode Pembayaran yang Tersedia:
- ✅ QRIS (QR Code Indonesia Standard)
- ✅ GoPay
- ✅ ShopeePay
- ✅ Dan metode QRIS lainnya

### Test Credentials (Sandbox):
Di popup Midtrans Sandbox, Anda bisa:
- **QRIS**: Klik tombol "Success" untuk simulasi pembayaran berhasil
- **GoPay**: Gunakan nomor `081234567890` atau klik "Success"
- **ShopeePay**: Gunakan nomor `081234567890` atau klik "Success"
- **Credit Card**:
  - Card Number: `4811 1111 1111 1114`
  - CVV: `123`
  - Exp: any future date (contoh: 01/26)

## 6. Production Mode

Untuk production:

1. Dapatkan Production Keys dari [Midtrans Production Dashboard](https://dashboard.midtrans.com)
2. Update Supabase Secrets dengan Production Keys
3. Set `MIDTRANS_ENV=production` di Supabase Secrets
4. Konfigurasi webhook notification URL
5. Lengkapi verifikasi bisnis di Midtrans Dashboard

## Recent Fixes ✅

### ✅ FIXED: Popup Midtrans Tidak Muncul

**Problem**: Snap popup tidak muncul saat klik "Bayar Sekarang"

**Root Cause**: 
- Client Key tidak tersedia di frontend
- Frontend mencoba akses `Deno.env.get()` yang hanya tersedia di server
- Snap script di-load dengan Client Key kosong

**Solution**:
1. Backend sekarang return Client Key di response `/payment/create`
2. Frontend load Snap script secara dinamis dengan Client Key yang benar
3. Popup sekarang muncul dengan sempurna menggunakan `window.snap.pay()`

### Updated Files:
- ✅ `/supabase/functions/server/index.tsx` - Added clientKey to response
- ✅ `/components/MidtransPayment.tsx` - Rewritten with dynamic script loading

## Testing Checklist

Untuk memastikan popup berfungsi dengan baik:

- [ ] Login sebagai tenant
- [ ] Buka halaman "Tagihan" atau Dashboard
- [ ] Klik tombol "Bayar Sekarang" pada invoice pending
- [ ] **Popup Midtrans harus muncul** (bukan redirect)
- [ ] Pilih metode pembayaran (contoh: QRIS)
- [ ] Di sandbox, klik "Success" untuk simulasi pembayaran berhasil
- [ ] Verify notifikasi "Pembayaran berhasil!" muncul
- [ ] Verify popup tertutup otomatis
- [ ] Test close popup tanpa bayar (onClose callback)

## Troubleshooting

### Popup Tidak Muncul ✅ FIXED
**Solution**: Sudah diperbaiki dengan implementasi baru. Client Key sekarang didapat dari backend.

### Payment Gagal
1. Verifikasi Server Key dan Client Key sudah benar di Supabase Secrets
2. Check environment mode (sandbox/production) sesuai dengan keys
3. Lihat error message di browser console
4. Check Network tab untuk response dari API

### Script Loading Issues
1. Buka browser console, cari error terkait Midtrans script
2. Pastikan tidak ada ad-blocker yang memblok script Midtrans
3. Coba refresh halaman atau clear browser cache
4. Check Network tab untuk memastikan script `snap.js` berhasil di-load

### Webhook Tidak Berfungsi
1. Pastikan notification URL sudah dikonfigurasi di Midtrans Dashboard
2. Check logs di Supabase Edge Functions untuk melihat incoming webhooks
3. Pastikan endpoint accessible dari internet (tidak blocked)
4. Verify order_id di webhook matches dengan payment records

## Dokumentasi Resmi

- [Midtrans Documentation](https://docs.midtrans.com/)
- [Snap Integration Guide](https://docs.midtrans.com/en/snap/overview)
- [Snap.js Documentation](https://docs.midtrans.com/en/snap/integration-guide)
- [Testing Payment](https://docs.midtrans.com/en/technical-reference/sandbox-test)
- [Notification/Webhook](https://docs.midtrans.com/en/after-payment/http-notification)

## Security Notes

⚠️ **IMPORTANT**:
- ✅ Server Key hanya digunakan di backend (aman)
- ✅ Client Key di-expose ke frontend (aman, designed untuk frontend)
- ✅ Client Key dikirim via HTTPS dari backend
- ⚠️ JANGAN commit atau hardcode Server Key di frontend
- ✅ Gunakan HTTPS untuk semua request

## Support

Jika mengalami masalah:
1. Check browser console untuk error messages
2. Check Supabase Edge Functions logs
3. Lihat [Midtrans Documentation](https://docs.midtrans.com/)
4. Contact Midtrans support untuk masalah API
