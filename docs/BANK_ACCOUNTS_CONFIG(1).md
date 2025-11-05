# Bank Accounts Configuration

## Cara Mengubah Rekening Bank untuk Manual Payment

File yang perlu diubah: `/components/ManualPayment.tsx`

### Default Configuration

```typescript
const bankAccounts = [
  {
    method: "BCA",
    accountNumber: "1234567890",
    accountName: "PT Kos Kita Indonesia",
  },
  {
    method: "Mandiri",
    accountNumber: "0987654321",
    accountName: "PT Kos Kita Indonesia",
  },
  {
    method: "BNI",
    accountNumber: "5555666677",
    accountName: "PT Kos Kita Indonesia",
  },
];
```

### Cara Mengubah:

1. **Edit Existing Banks**
   ```typescript
   const bankAccounts = [
     {
       method: "BCA",
       accountNumber: "YOUR_REAL_BCA_ACCOUNT",
       accountName: "Your Company Name",
     },
     // ... others
   ];
   ```

2. **Tambah Bank Baru**
   ```typescript
   const bankAccounts = [
     // ... existing banks
     {
       method: "BRI",
       accountNumber: "1111222233",
       accountName: "PT Kos Kita Indonesia",
     },
     {
       method: "CIMB Niaga",
       accountNumber: "4444555566",
       accountName: "PT Kos Kita Indonesia",
     },
   ];
   ```

3. **Hapus Option "Bayar Tunai"**
   Hapus baris ini dari Select options:
   ```typescript
   // DELETE THIS LINE:
   <SelectItem value="Cash">Bayar Tunai</SelectItem>
   ```

4. **Ubah Jumlah Transfer**
   Saat ini jumlah transfer otomatis sama dengan amount invoice.
   Jika ingin tambahkan biaya admin:
   ```typescript
   // Di bagian display amount:
   <span className="text-lg font-bold text-blue-700">
     Rp {(amount + 2500).toLocaleString("id-ID")}
   </span>
   <p className="text-xs text-gray-500">*Termasuk biaya admin Rp 2.500</p>
   ```

### Production Tips:

1. **Store in Environment Variables** (Recommended)
   ```typescript
   const bankAccounts = [
     {
       method: "BCA",
       accountNumber: import.meta.env.VITE_BCA_ACCOUNT,
       accountName: import.meta.env.VITE_BANK_ACCOUNT_NAME,
     },
   ];
   ```

2. **Store in Backend/Database** (Best Practice)
   - Store bank accounts di KV Store atau database
   - Fetch dari API endpoint
   - Bisa diubah tanpa deploy ulang

3. **Add Bank Logos**
   ```typescript
   import bcaLogo from './assets/bca-logo.png';
   
   const bankAccounts = [
     {
       method: "BCA",
       logo: bcaLogo,
       accountNumber: "1234567890",
       accountName: "PT Kos Kita Indonesia",
     },
   ];
   
   // Display in UI:
   <div className="flex items-center gap-2">
     <img src={bank.logo} alt={bank.method} className="h-6" />
     <span>{bank.method}</span>
   </div>
   ```

---

## Contact & Support Info

Untuk menambahkan info kontak di halaman pembayaran tunai:

Edit file `/components/ManualPayment.tsx`, bagian Cash payment:

```typescript
{paymentMethod === "Cash" && (
  <Card className="bg-orange-50 border-orange-200">
    <CardContent className="p-4">
      <div className="flex items-start gap-2 text-orange-700">
        <AlertCircle className="h-4 w-4 mt-0.5" />
        <div className="text-sm">
          <p className="font-medium mb-1">Pembayaran Tunai</p>
          <p className="text-orange-600 mb-2">
            Silakan datang ke kantor manajemen kos untuk melakukan pembayaran tunai.
          </p>
          <div className="space-y-1 text-xs">
            <p>📍 Alamat: Jl. Kebon Jeruk No. 123, Jakarta</p>
            <p>⏰ Jam Operasional: Senin-Jumat 09:00-17:00</p>
            <p>📞 Kontak: (021) 1234-5678</p>
            <p>💬 WhatsApp: +62 812-3456-7890</p>
          </div>
        </div>
      </div>
    </CardContent>
  </Card>
)}
```

---

## File Size & Type Limits

Current configuration in `/components/ManualPayment.tsx`:

```typescript
// File type validation
if (!file.type.startsWith("image/")) {
  setError("Hanya file gambar yang diperbolehkan");
  return;
}

// File size validation (max 5MB)
if (file.size > 5 * 1024 * 1024) {
  setError("Ukuran file maksimal 5MB");
  return;
}
```

### Untuk mengubah limits:

```typescript
// Max 10MB
if (file.size > 10 * 1024 * 1024) {
  setError("Ukuran file maksimal 10MB");
  return;
}

// Allow PDF too
if (!file.type.startsWith("image/") && file.type !== "application/pdf") {
  setError("Hanya file gambar atau PDF yang diperbolehkan");
  return;
}
```

---

## Multi-language Support

Untuk menambahkan bahasa Indonesia/Inggris:

```typescript
const translations = {
  id: {
    selectBank: "Pilih Metode Pembayaran",
    uploadProof: "Upload Bukti Pembayaran",
    submit: "Kirim Bukti Pembayaran",
  },
  en: {
    selectBank: "Select Payment Method",
    uploadProof: "Upload Payment Proof",
    submit: "Submit Payment Proof",
  }
};

const lang = "id"; // or get from context/state

<Label>{translations[lang].selectBank}</Label>
```
