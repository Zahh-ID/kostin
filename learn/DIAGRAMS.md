# Diagram Sistem (System Diagrams)

Dokumen ini berisi visualisasi alur dan proses sistem Kostin menggunakan diagram standar.

---

## 1. Use Case Diagram

Menggambarkan interaksi antara pengguna (Aktor) dengan fitur-fitur sistem.

```mermaid
flowchart LR
    subgraph System [Sistem Kostin]
        direction TB
        UC1(Login / Register)
        UC2(Cari Kost)
        UC3(Ajukan Sewa)
        UC4(Bayar Tagihan)
        UC5(Lapor Komplain)
        UC6(Kelola Properti)
        UC7(Kelola Kamar)
        UC8(Validasi Sewa)
        UC9(Cek Pendapatan)
        UC10(Moderasi Kost)
        UC11(Blokir User)
    end

    %% Actors
    T{{Tenant}}
    O{{Owner}}
    A{{Admin}}

    %% Relationships
    T --> UC1
    T --> UC2
    T --> UC3
    T --> UC4
    T --> UC5

    O --> UC1
    O --> UC6
    O --> UC7
    O --> UC8
    O --> UC9

    A --> UC1
    A --> UC10
    A --> UC11
```

---

## 2. Data Flow Diagram (DFD)

### DFD Level 0 (Context Diagram)
Gambaran umum aliran data antara entitas luar dan sistem.

```mermaid
flowchart LR
    Tenant[Entitas: Tenant]
    Owner[Entitas: Owner]
    Admin[Entitas: Admin]
    System((Sistem Informasi Kostin))
    Bank[Layanan Payment Gateway]

    Tenant -- Data Diri, Pengajuan Sewa, Pembayaran --> System
    System -- Info Kost, Tagihan, Kontrak --> Tenant

    Owner -- Data Kost, Kamar, Persetujuan --> System
    System -- Notifikasi Sewa, Laporan Pendapatan --> Owner

    Admin -- Moderasi, Blokir --> System
    System -- Laporan Statistik --> Admin

    System -- Request Pembayaran --> Bank
    Bank -- Status Pembayaran --> System
```

### DFD Level 1 (Proses Utama)
Pecahan proses yang lebih detail.

```mermaid
flowchart TD
    %% Entities
    T[Tenant]
    O[Owner]
    
    %% Processes
    P1((1.0 Pengelolaan Akun))
    P2((2.0 Pencarian & Sewa))
    P3((3.0 Pembayaran))
    P4((4.0 Manajemen Kost))

    %% Data Stores
    D1[(Data User)]
    D2[(Data Kost)]
    D3[(Data Transaksi)]

    %% Flows
    T -->|Regis/Login| P1
    O -->|Regis/Login| P1
    P1 -->|Simpan Profil| D1

    O -->|Input Data Kost| P4
    P4 -->|Simpan Properti| D2
    
    T -->|Cari Kost| P2
    D2 -->|Info Kost| P2
    P2 -->|Ajukan Sewa| O
    O -->|Approve| P2
    P2 -->|Buat Kontrak| D3

    T -->|Bayar Tagihan| P3
    P3 -->|Update Status| D3
    P3 -->|Notifikasi| O
```

---

## 3. Flowchart (Alur Proses)

### Alur Penyewaan Kamar (Rental Process)

```mermaid
flowchart TD
    Start([Mulai]) --> Search[Tenant Mencari Kost]
    Search --> View[Lihat Detail Kamar]
    View --> Apply{Tertarik?}
    Apply -- Tidak --> Search
    Apply -- Ya --> Form[Isi Form Pengajuan]
    Form --> Submit[Kirim Pengajuan]
    Submit --> Notify[Notifikasi ke Owner]
    Notify --> Review{Owner Setuju?}
    
    Review -- Tidak --> Reject[Status: Rejected]
    Reject --> End([Selesai])
    
    Review -- Ya --> Approve[Status: Approved]
    Approve --> CreateContract[Sistem Buat Kontrak]
    CreateContract --> CreateInvoice[Sistem Buat Tagihan]
    CreateInvoice --> NotifyTenant[Notifikasi ke Tenant]
    NotifyTenant --> End
```

### Alur Pembayaran (Payment Process)

```mermaid
flowchart TD
    Start([Mulai]) --> Invoice[Tenant Buka Tagihan]
    Invoice --> Method{Pilih Metode}
    
    Method -- Otomatis (QRIS/VA) --> Snap[Request ke Midtrans]
    Snap --> Pay[Tenant Bayar di Payment Gateway]
    Pay --> Webhook[Midtrans Kirim Webhook]
    Webhook --> Verify[Sistem Verifikasi Signature]
    Verify --> UpdateAuto[Update Status: Paid]
    
    Method -- Manual --> Transfer[Tenant Transfer Bank]
    Transfer --> Upload[Upload Bukti Transfer]
    Upload --> Pending[Status: Pending Verification]
    Pending --> Check[Owner Cek Mutasi]
    Check --> Valid{Valid?}
    
    Valid -- Tidak --> Reject[Tolak & Minta Upload Ulang]
    Reject --> Upload
    
    Valid -- Ya --> UpdateManual[Update Status: Paid]
    
    UpdateAuto --> Success([Transaksi Selesai])
    UpdateManual --> Success
```
