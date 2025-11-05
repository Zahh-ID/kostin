# 💳 Payment CTA Components

## Components Created

### 1. PaymentBanner (`/components/PaymentBanner.tsx`)
Sticky banner notification at the top of the dashboard.

**Props:**
- `amount: number` - Payment amount
- `dueDate: string` - Due date text
- `daysUntilDue: number` - Days until due (negative if overdue)
- `onPayClick: () => void` - Callback when pay button clicked

**Features:**
- Color-coded by urgency (red=overdue, orange=urgent, blue=normal)
- Dismissible
- Animated background
- Shows payment amount and due date

### 2. QuickPayCTA (`/components/QuickPayCTA.tsx`)
Prominent card in the dashboard center.

**Props:**
- `amount: number` - Payment amount
- `dueDate: string` - Due date text
- `invoiceMonth: string` - Invoice month/period
- `onPayClick: () => void` - Callback when pay button clicked

**Features:**
- Large, eye-catching design
- Shows payment methods (QRIS, GoPay, ShopeePay)
- Status-based styling
- Lightning bolt animation for urgency

### 3. FloatingPayButton (`/components/FloatingPayButton.tsx`)
Fixed button that appears when scrolling.

**Props:**
- `amount: number` - Payment amount
- `onPayClick: () => void` - Callback when pay button clicked

**Features:**
- Appears after scrolling 300px
- Fixed at bottom-right
- Expandable on click to show details
- Pulse and bounce animations
- Badge showing number of pending invoices

### 4. PaymentCTAShowcase (`/components/PaymentCTAShowcase.tsx`)
Demo page showing all CTA variations.

**Route:** `/showcase-payment-cta`

**Features:**
- Tabs for each CTA type
- Different status variations (overdue, urgent, normal)
- Interactive testing
- Inline button examples

## Integration

All components are integrated into `/components/TenantDashboard.tsx`:

```tsx
// At the top
<PaymentBanner
  amount={tenant.nextPaymentAmount}
  dueDate={tenant.nextPaymentDate}
  daysUntilDue={daysUntilDue}
  onPayClick={() => handlePayNow(pendingInvoice)}
/>

// In the center
<QuickPayCTA
  amount={tenant.nextPaymentAmount}
  dueDate={tenant.nextPaymentDate}
  invoiceMonth="November 2024"
  onPayClick={() => handlePayNow(pendingInvoice)}
/>

// Inline buttons in invoice list
<Button size="sm" onClick={() => handlePayNow(invoice)}>
  Bayar
</Button>

// Floating button (shows when scrolling)
<FloatingPayButton
  amount={tenant.nextPaymentAmount}
  onPayClick={() => handlePayNow(pendingInvoice)}
/>
```

## Testing

1. Visit `/showcase-payment-cta` to see all CTAs
2. Login as tenant: `tenant@demo.com` / `demo123`
3. Visit `/tenant` to see integrated CTAs
4. Click any "Bayar" button to open payment dialog

## Status Colors

- **Red (Overdue)**: `daysUntilDue < 0`
- **Orange (Urgent)**: `daysUntilDue <= 3`
- **Blue (Normal)**: `daysUntilDue > 3`

## Payment Flow ✅ UPDATED

1. User clicks CTA button (PaymentBanner/QuickPayCTA/FloatingPayButton)
2. Opens Dialog/Sheet with MidtransPayment component
3. MidtransPayment requests backend to create payment transaction
4. Backend returns Snap Token + Client Key
5. Frontend dynamically loads Midtrans Snap.js script
6. **Midtrans Snap popup opens** (not redirect, stays in app!)
7. User completes payment in popup (QRIS/GoPay/ShopeePay)
8. Popup closes with success/pending/error callback
9. Dialog closes and invoice status updates

### Key Features:
- ✅ **Popup mode** - User stays in the app (no redirect)
- ✅ **Dynamic script loading** - Client Key loaded from backend
- ✅ **Seamless UX** - Payment in modal overlay
- ✅ **Real-time callbacks** - Instant feedback on payment status

## Dependencies

All components use only:
- React hooks (useState, useEffect)
- shadcn/ui components (Button, Card, Dialog)
- lucide-react icons
- Native CSS animations (no external animation libraries)

## Files Modified

- `/components/TenantDashboard.tsx` - Added all 4 CTAs
- `/App.tsx` - Added `/showcase-payment-cta` route
- `/README.md` - Updated with new route

## Notes

- No external animation libraries required
- All animations use CSS (animate-ping, animate-bounce, transitions)
- Responsive design
- TypeScript typed
- No build dependencies beyond existing setup
