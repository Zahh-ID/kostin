# 🚀 Quick Start: Midtrans Payment Integration

## ⚡ TL;DR

Midtrans Snap sudah **terintegrasi penuh** dan **popup berfungsi dengan sempurna**!

## 🎯 How to Test (3 Steps)

1. **Login as Tenant**
   ```
   URL: http://localhost:5173 (atau domain Anda)
   Email: tenant@demo.com
   Password: demo123
   ```

2. **Go to Dashboard**
   ```
   Click "Dashboard" di navbar
   ```

3. **Click "Bayar Sekarang"**
   ```
   ✅ Popup Midtrans akan muncul
   ✅ Pilih QRIS / GoPay / ShopeePay
   ✅ Di sandbox: klik "Success"
   ✅ Done!
   ```

## 🔧 Configuration Status

| Component | Status | Notes |
|-----------|--------|-------|
| `MIDTRANS_SERVER_KEY` | ✅ Configured | Backend only |
| `MIDTRANS_CLIENT_KEY` | ✅ Configured | Sent from backend |
| Backend API | ✅ Ready | `/payment/create` endpoint |
| Frontend Component | ✅ Ready | `MidtransPayment.tsx` |
| Popup Mode | ✅ Working | No redirect needed |
| Sandbox Mode | ✅ Active | Testing environment |

## 🎨 Where to Find Payment Buttons

### 1. **PaymentBanner** (Top of Dashboard)
```tsx
// Location: Top of TenantDashboard
// Appearance: Sticky banner, red/orange/blue based on urgency
// Action: Click "Bayar Sekarang" → Opens popup
```

### 2. **QuickPayCTA** (Center Card)
```tsx
// Location: Center of TenantDashboard
// Appearance: Large card with payment info
// Action: Click "Bayar dengan Snap" → Opens popup
```

### 3. **Invoice Table Button**
```tsx
// Location: TenantInvoicesPage table
// Appearance: "Bayar" button on pending invoices
// Action: Click "Bayar" → Opens popup
```

### 4. **FloatingPayButton** (Bottom-Right)
```tsx
// Location: Fixed bottom-right corner
// Appearance: Appears when scrolling down
// Action: Click to expand, then "Bayar Sekarang" → Opens popup
```

## 💻 Usage in Your Code

### Basic Implementation

```tsx
import { MidtransPayment } from "./components/MidtransPayment";

function YourComponent() {
  const [showPayment, setShowPayment] = useState(false);
  
  return (
    <>
      <Button onClick={() => setShowPayment(true)}>
        Bayar Sekarang
      </Button>
      
      <Dialog open={showPayment} onOpenChange={setShowPayment}>
        <DialogContent>
          <MidtransPayment
            invoiceId="INV-001"
            amount={1200000}
            description="Sewa bulan November"
            onSuccess={() => {
              setShowPayment(false);
              toast.success("Pembayaran berhasil!");
            }}
            onPending={() => {
              setShowPayment(false);
              toast.info("Pembayaran sedang diproses");
            }}
            onError={(error) => {
              console.error(error);
              toast.error("Pembayaran gagal");
            }}
            onClose={() => setShowPayment(false)}
          />
        </DialogContent>
      </Dialog>
    </>
  );
}
```

## 🧪 Testing Scenarios

### Scenario 1: Successful Payment
```
1. Click "Bayar Sekarang"
2. Wait for popup to load (1-2 seconds)
3. Select payment method (e.g., QRIS)
4. In sandbox: Click "Success" button
5. ✅ Toast notification appears
6. ✅ Dialog closes
7. ✅ Invoice updated
```

### Scenario 2: User Closes Popup
```
1. Click "Bayar Sekarang"
2. Popup opens
3. Click X to close popup (don't pay)
4. ✅ onClose callback called
5. ✅ Dialog remains open (user can try again)
```

### Scenario 3: Payment Error
```
1. Click "Bayar Sekarang"
2. Popup opens
3. In sandbox: Click "Failure" button
4. ✅ onError callback called
5. ✅ Error message shown
```

## 📱 Payment Methods Available

| Method | Sandbox Testing | Production |
|--------|----------------|------------|
| **QRIS** | Click "Success" | Real QR scan |
| **GoPay** | Use `081234567890` | Real GoPay account |
| **ShopeePay** | Use `081234567890` | Real ShopeePay account |
| **Credit Card** | `4811 1111 1111 1114` | Real cards |

## 🔐 Environment Variables

**Already Configured** ✅:
- `MIDTRANS_SERVER_KEY` - For backend API calls
- `MIDTRANS_CLIENT_KEY` - For frontend Snap.js

**Optional**:
- `MIDTRANS_ENV` - Set to `production` when going live (default: `sandbox`)

## 🎯 Key Features

✅ **Popup Mode** - Tidak redirect, tetap di app
✅ **Dynamic Loading** - Client key dari backend
✅ **Multiple CTAs** - 4 variasi payment buttons
✅ **Real-time Callbacks** - Instant feedback
✅ **Responsive** - Works on mobile & desktop
✅ **Secure** - Server key tetap di backend
✅ **Toast Notifications** - User-friendly feedback

## 🐛 Quick Troubleshooting

**Popup tidak muncul?**
```bash
# Check browser console
# Should see: "Midtrans Snap script loaded successfully"
# If not, check Network tab for snap.js
```

**Error "Unauthorized"?**
```bash
# Make sure user is logged in
# Check: await supabase.auth.getSession()
# Access token should be valid
```

**Script loading failed?**
```bash
# Disable ad-blocker
# Clear browser cache
# Refresh page
# Check internet connection
```

## 📞 API Endpoints

### Create Payment
```
POST /make-server-dbd6b95a/payment/create
Authorization: Bearer <access_token>
Body: { invoiceId, amount, description }
Response: { snapToken, clientKey, orderId }
```

### Verify Payment
```
GET /make-server-dbd6b95a/payment/verify/:orderId
Authorization: Bearer <access_token>
Response: { payment, midtransStatus }
```

### Webhook (Midtrans → Your Server)
```
POST /make-server-dbd6b95a/payment/notification
Body: Midtrans notification object
Response: { success: true }
```

## 🎓 Learn More

- Full setup guide: [MIDTRANS_SETUP.md](./MIDTRANS_SETUP.md)
- Popup fix details: [MIDTRANS_POPUP_FIX.md](./MIDTRANS_POPUP_FIX.md)
- Payment CTAs guide: [PAYMENT_CTA_README.md](./PAYMENT_CTA_README.md)
- Main README: [README.md](./README.md)

## ✨ Ready to Go!

Your Midtrans integration is **complete** and **production-ready** (in sandbox mode).

**Next Steps**:
1. ✅ Test all payment scenarios
2. ✅ Verify callbacks work correctly
3. 🔄 Switch to production keys when ready
4. 🚀 Deploy and go live!

---

**Status**: ✅ Fully Working
**Last Updated**: Based on popup fix implementation
**Environment**: Sandbox (ready for production)
