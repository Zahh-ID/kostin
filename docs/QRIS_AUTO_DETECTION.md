# QRIS Auto-Detection System

## Overview
Sistem pembayaran telah diupgrade dari Midtrans Snap (popup) menjadi Midtrans Core API dengan QRIS auto-detection. Sistem ini lebih modern, user-friendly, dan fully automated.

## Key Features

### 1. **Automatic QRIS Generation**
- Menggunakan Midtrans Core API `/v2/charge` endpoint
- Langsung generate QRIS code tanpa popup
- Support semua e-wallet dan mobile banking yang support QRIS

### 2. **Real-time Payment Detection**
- Auto-polling setiap 3 detik untuk check status pembayaran
- Tidak perlu manual refresh atau klik tombol
- Instant notification ketika pembayaran berhasil

### 3. **Enhanced UI/UX**
- QR Code ditampilkan langsung di halaman (menggunakan `qrcode.react`)
- Countdown timer 5 menit untuk validitas QR
- Real-time status updates dengan visual indicators
- Status badges: Pending, Success, Failed

### 4. **Automatic Status Management**
- Status otomatis update: pending → settlement/capture
- Handle berbagai status: settlement, capture, deny, cancel, expire
- Cleanup intervals on unmount untuk prevent memory leaks

## Technical Implementation

### Backend Changes (`payment.tsx`)

**Before (Snap API):**
```typescript
// Used Snap API
POST https://app.sandbox.midtrans.com/snap/v1/transactions
Response: { token, redirect_url }
```

**After (Core API):**
```typescript
// Using Core API with QRIS
POST https://api.sandbox.midtrans.com/v2/charge
Body: { payment_type: "qris", ... }
Response: { 
  transaction_id, 
  order_id,
  actions: [{ name: "generate-qr-code", url: "QRIS_STRING" }]
}
```

### Frontend Changes (`MidtransPayment.tsx`)

**New Features:**
- QR Code display using `qrcode.react` library
- Countdown timer (5 minutes default)
- Auto-polling mechanism with 3-second intervals
- Status tracking and visual feedback
- Manual status check button

**Component States:**
```typescript
- loading: Generate QR loading state
- qrisString: The QRIS data string
- orderId: Midtrans order ID
- transactionStatus: Current payment status
- countdown: Remaining time in seconds
- checking: Status check loading state
```

**Auto-polling Logic:**
1. Start after QR code generated
2. Check status every 3 seconds
3. Stop when: payment success, failed, or timeout
4. Cleanup on component unmount

## Payment Flow

### User Journey:
1. **Click "Generate QRIS"** → System calls backend to create QRIS transaction
2. **QR Code displayed** → User sees QR code with countdown timer
3. **Auto-detection starts** → System polls Midtrans every 3 seconds
4. **User scans & pays** → Using any e-wallet/mobile banking app
5. **Auto-detected** → System detects payment and shows success message
6. **Invoice updated** → Backend updates invoice status automatically

### Status States:
- `pending` → Waiting for payment
- `settlement` / `capture` → Payment successful
- `deny` / `cancel` / `expire` → Payment failed
- `expired` → Timeout (5 minutes)

## API Endpoints

### 1. Create QRIS Payment
```
POST /make-server-dbd6b95a/payment/create
Authorization: Bearer {access_token}

Request:
{
  "invoiceId": "INV-001",
  "amount": 500000,
  "description": "Pembayaran sewa bulan Januari 2025"
}

Response:
{
  "success": true,
  "orderId": "INV-001-1234567890",
  "transactionId": "xxxx-xxxx-xxxx",
  "qrisString": "00020101021126...",
  "transactionStatus": "pending"
}
```

### 2. Check Payment Status
```
GET /make-server-dbd6b95a/payment/verify/{orderId}
Authorization: Bearer {access_token}

Response:
{
  "success": true,
  "payment": {
    "orderId": "INV-001-1234567890",
    "status": "settlement",
    ...
  },
  "midtransStatus": {
    "transaction_status": "settlement",
    "payment_type": "qris",
    ...
  }
}
```

### 3. Webhook/Notification (Midtrans → Server)
```
POST /make-server-dbd6b95a/payment/notification

Body: {
  "order_id": "INV-001-1234567890",
  "transaction_status": "settlement",
  "payment_type": "qris",
  ...
}
```

## Benefits Over Snap

### Snap (Old):
❌ Popup window (can be blocked by browser)
❌ Redirect required
❌ Multiple payment methods shown (confusing)
❌ Manual status check
❌ Poor mobile experience

### Core API with QRIS (New):
✅ No popup, clean inline UI
✅ No redirect needed
✅ Single payment method (QRIS only)
✅ Automatic status detection
✅ Mobile-optimized
✅ Better user experience

## Configuration

### Environment Variables (Already Set):
- `MIDTRANS_SERVER_KEY` → For API authentication
- `MIDTRANS_CLIENT_KEY` → Not needed for Core API
- `MIDTRANS_ENV` → "sandbox" or "production"

### Polling Configuration:
```typescript
// In MidtransPayment.tsx
const POLLING_INTERVAL = 3000; // 3 seconds
const QR_TIMEOUT = 300; // 5 minutes
```

## Testing

### Test Flow:
1. Login sebagai tenant
2. Buka invoice yang belum dibayar
3. Click "Bayar dengan QRIS"
4. Generate QRIS code
5. Scan menggunakan e-wallet test (di sandbox)
6. Sistem akan auto-detect pembayaran dalam 3-6 detik
7. Status invoice otomatis berubah menjadi "paid"

### Midtrans Sandbox Test:
- Gunakan simulator QRIS di Midtrans dashboard
- Atau gunakan test credentials dari Midtrans
- Status akan berubah otomatis setelah simulate payment

## Troubleshooting

### QR Code tidak muncul:
- Check MIDTRANS_SERVER_KEY environment variable
- Check network console for API errors
- Verify Midtrans account active

### Auto-detection tidak bekerja:
- Check browser console untuk errors
- Verify polling interval running (should see logs every 3s)
- Check Midtrans webhook configuration

### Timeout terlalu cepat:
- Adjust `countdown` initial state (default 300s = 5 min)
- Bisa diperpanjang sesuai kebutuhan

## Future Enhancements

Possible improvements:
- [ ] Add support for specific e-wallet (GoPay, OVO, Dana, ShopeePay)
- [ ] Customize QR expiry time per transaction
- [ ] Add payment analytics and tracking
- [ ] Email/SMS notification on payment success
- [ ] Add payment receipt PDF generation
- [ ] Support installment payments

## Security Notes

⚠️ **Important:**
- Server key MUST NOT be exposed to frontend
- All payment creation goes through backend
- Webhook verification should be implemented for production
- Use HTTPS in production
- Validate signature from Midtrans webhook

## Migration from Snap

If you had previous Snap integration:
1. Old Snap tokens will still work until expiry
2. New payments will use QRIS Core API
3. No breaking changes to invoice system
4. Old payment records remain intact
5. Webhook endpoint remains backward compatible

---

**Updated:** October 31, 2025
**Version:** 2.0.0 - QRIS Core API with Auto-Detection
