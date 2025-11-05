# 🗄️ DATABASE SCHEMA, ERD & DFD - SISTEM KOSTIN

**Dokumentasi Lengkap Database Architecture, Entity Relationship Diagram, dan Data Flow Diagram**

---

## 📋 DAFTAR ISI

1. [Database Architecture Overview](#database-architecture-overview)
2. [Database Schema (KV Store)](#database-schema-kv-store)
3. [Entity Relationship Diagram (ERD)](#entity-relationship-diagram-erd)
4. [Data Flow Diagram (DFD)](#data-flow-diagram-dfd)
5. [Key Patterns Reference](#key-patterns-reference)
6. [Data Structures](#data-structures)
7. [Database Operations](#database-operations)

---

## 🏗️ DATABASE ARCHITECTURE OVERVIEW

### **Database Type: Key-Value Store (KV Store)**

KostIn menggunakan **Supabase PostgreSQL** dengan **Key-Value Store pattern** untuk flexibility dan simplicity.

**Alasan KV Store:**
- ✅ **Flexible Schema** - Mudah add fields tanpa migration
- ✅ **Rapid Development** - Cocok untuk prototyping
- ✅ **Simple Queries** - Get/Set operations
- ✅ **NoSQL-like** - JSON storage dalam RDBMS
- ✅ **Scalable** - Horizontal scaling ready

**Trade-offs:**
- ❌ Complex joins sulit (mitigated dengan prefix queries)
- ❌ No foreign key constraints (handled di application layer)
- ❌ Indexing terbatas pada key (mitigated dengan composite keys)

---

## 📊 DATABASE SCHEMA (KV STORE)

### **Physical Schema**

**Table: `kv_store_dbd6b95a`**

```sql
CREATE TABLE kv_store_dbd6b95a (
  key TEXT NOT NULL PRIMARY KEY,
  value JSONB NOT NULL
);

-- Index untuk prefix queries
CREATE INDEX idx_kv_key_prefix ON kv_store_dbd6b95a 
  USING btree (key text_pattern_ops);
```

**Structure:**
- **key**: String identifier (TEXT) - Primary Key
- **value**: JSON object (JSONB) - Flexible schema

---

### **Logical Schema - Key Patterns**

Semua data disimpan dengan **hierarchical key pattern** untuk organization dan efficient querying.

**Key Pattern Format:**
```
{entity_type}:{primary_id}[:{relation}:{secondary_id}]
```

**Example:**
```
user:abc123                          // User entity
property:prop001                     // Property entity
contract:contract001                 // Contract entity
invoice:inv001                       // Invoice entity
wishlist:user123:prop456            // Wishlist relation
chat:conversation:conv001           // Chat conversation
chat:message:msg001                 // Chat message
ticket:ticket001                    // Support ticket
```

---

### **Entity Types**

#### 1️⃣ **USER** (Stored in Supabase Auth)
```
Table: auth.users
Primary Key: id (UUID)
```

**User Metadata (in auth):**
```json
{
  "name": "Ahmad Fauzi",
  "role": "tenant|owner|admin"
}
```

**Extended Profile (in KV):**
```
Key: profile:{userId}
Value: {
  "phone": "081234567890",
  "address": "Jl. Example No. 123",
  "name": "Ahmad Fauzi",
  "role": "tenant",
  "updatedAt": "2024-11-04T10:00:00Z"
}
```

---

#### 2️⃣ **PROPERTY**
```
Key: property:{propertyId}
```

**Value Structure:**
```json
{
  "id": "prop_001",
  "ownerId": "user_owner_123",
  "ownerName": "Ibu Susi",
  "name": "Kos Melati Residence",
  "description": "Kos nyaman dan strategis...",
  "address": "Jl. Raya Dramaga No. 45",
  "city": "Bogor",
  "district": "Dramaga",
  "type": "putra|putri|campur",
  "pricePerMonth": 1200000,
  "availableRooms": 5,
  "totalRooms": 12,
  "facilities": ["AC", "Wi-Fi", "Kamar Mandi Dalam", "Parkir"],
  "images": [
    "https://images.unsplash.com/...",
    "https://images.unsplash.com/..."
  ],
  "status": "pending_approval|active|rejected|inactive",
  "rating": 4.5,
  "reviewCount": 24,
  "createdAt": "2024-10-01T10:00:00Z",
  "updatedAt": "2024-11-04T10:00:00Z"
}
```

**Related Keys:**
- `property:{ownerId}:list` - List all properties by owner
- `property:active:list` - List all active properties
- `property:{city}:list` - List properties by city

---

#### 3️⃣ **CONTRACT**
```
Key: contract:{contractId}
```

**Value Structure:**
```json
{
  "id": "contract_001",
  "tenantId": "user_tenant_123",
  "tenantName": "Ahmad Fauzi",
  "tenantEmail": "ahmad@email.com",
  "tenantPhone": "081234567890",
  "ownerId": "user_owner_456",
  "propertyId": "prop_001",
  "propertyName": "Kos Melati Residence",
  "roomType": "Single AC - Kamar Mandi Dalam",
  "roomNumber": "101",
  "startDate": "2024-01-01",
  "endDate": "2024-12-31",
  "monthlyRent": 1200000,
  "deposit": 1200000,
  "duration": 12,
  "status": "active|expired|terminated",
  "paymentSchedule": "monthly",
  "paymentDueDay": 5,
  "terms": [
    "Dilarang membawa hewan peliharaan",
    "Tamu wajib lapor",
    "Jam malam 22.00 WIB"
  ],
  "emergencyContact": {
    "name": "Budi Santoso",
    "phone": "081234567890",
    "relation": "Ayah"
  },
  "createdAt": "2024-01-01T10:00:00Z",
  "updatedAt": "2024-11-04T10:00:00Z"
}
```

**Related Keys:**
- `contract:tenant:{tenantId}` - List contracts by tenant
- `contract:owner:{ownerId}` - List contracts by owner
- `contract:property:{propertyId}` - List contracts by property

---

#### 4️⃣ **INVOICE**
```
Key: invoice:{invoiceId}
```

**Value Structure:**
```json
{
  "id": "INV-2024-11-001",
  "contractId": "contract_001",
  "tenantId": "user_tenant_123",
  "tenantName": "Ahmad Fauzi",
  "ownerId": "user_owner_456",
  "propertyId": "prop_001",
  "propertyName": "Kos Melati Residence",
  "month": "November 2024",
  "year": 2024,
  "monthNumber": 11,
  "amount": 1200000,
  "dueDate": "2024-11-05",
  "status": "pending|paid|overdue|cancelled",
  "paymentMethod": null,
  "paidDate": null,
  "paidAmount": 0,
  "lateFee": 0,
  "description": "Sewa bulan November 2024",
  "notes": "",
  "createdAt": "2024-10-25T10:00:00Z",
  "updatedAt": "2024-11-04T10:00:00Z"
}
```

**Related Keys:**
- `invoice:tenant:{tenantId}` - List invoices by tenant
- `invoice:owner:{ownerId}` - List invoices by owner
- `invoice:contract:{contractId}` - List invoices by contract
- `invoice:status:pending` - List pending invoices

---

#### 5️⃣ **PAYMENT**
```
Key: payment:{paymentId}
```

**Value Structure:**
```json
{
  "id": "payment_001",
  "invoiceId": "INV-2024-11-001",
  "tenantId": "user_tenant_123",
  "ownerId": "user_owner_456",
  "amount": 1200000,
  "method": "qris|manual|bank_transfer|gopay|shopeepay",
  "status": "pending|success|failed|cancelled",
  "type": "automatic|manual",
  
  // For QRIS/Midtrans
  "midtransOrderId": "ORDER-1730700000-abc123",
  "midtransTransactionId": "d4d6576e-26c0-4730...",
  "qrisString": "https://api.sandbox.midtrans.com/v2/qris/...",
  "midtransStatus": "pending|settlement|capture|deny|cancel|expire",
  
  // For Manual Transfer
  "proofUrl": "https://...supabase.co/storage/.../proof.jpg",
  "proofUploadedAt": "2024-11-04T10:00:00Z",
  "verifiedBy": "user_owner_456",
  "verifiedAt": "2024-11-04T10:30:00Z",
  "verificationStatus": "pending|approved|rejected",
  "rejectionReason": "",
  
  "paidAt": "2024-11-04T10:30:00Z",
  "createdAt": "2024-11-04T10:00:00Z",
  "updatedAt": "2024-11-04T10:30:00Z"
}
```

**Related Keys:**
- `payment:invoice:{invoiceId}` - Payment for invoice
- `payment:tenant:{tenantId}` - List payments by tenant
- `payment:status:pending` - Pending payments

---

#### 6️⃣ **WISHLIST**
```
Key: wishlist:{userId}:{propertyId}
```

**Value Structure:**
```json
{
  "userId": "user_tenant_123",
  "propertyId": "prop_001",
  "propertyName": "Kos Melati Residence",
  "propertyCity": "Bogor",
  "propertyPrice": 1200000,
  "propertyImage": "https://images.unsplash.com/...",
  "addedAt": "2024-11-04T10:00:00Z"
}
```

**Related Keys:**
- `wishlist:{userId}:*` - All wishlists for user (prefix query)

---

#### 7️⃣ **SAVED SEARCH**
```
Key: saved-search:{userId}:{searchId}
```

**Value Structure:**
```json
{
  "id": "search_001",
  "userId": "user_tenant_123",
  "name": "Kos Dekat IPB",
  "filters": {
    "search": "",
    "city": "Bogor",
    "type": "putra",
    "minPrice": 0,
    "maxPrice": 2000000,
    "facilities": ["ac", "wifi"]
  },
  "notificationEnabled": true,
  "createdAt": "2024-11-04T10:00:00Z",
  "lastUsed": "2024-11-04T10:00:00Z"
}
```

**Related Keys:**
- `saved-search:{userId}:*` - All saved searches for user

---

#### 8️⃣ **CHAT - CONVERSATION**
```
Key: chat:conversation:{conversationId}
```

**Value Structure:**
```json
{
  "id": "conv_001",
  "participants": ["user_tenant_123", "user_owner_456"],
  "participantNames": {
    "user_tenant_123": "Ahmad Fauzi",
    "user_owner_456": "Ibu Susi"
  },
  "participantRoles": {
    "user_tenant_123": "tenant",
    "user_owner_456": "owner"
  },
  "propertyId": "prop_001",
  "propertyName": "Kos Melati Residence",
  "lastMessage": "Kamar masih tersedia kah?",
  "lastMessageAt": "2024-11-04T10:00:00Z",
  "createdAt": "2024-11-01T10:00:00Z",
  "updatedAt": "2024-11-04T10:00:00Z"
}
```

**Related Keys:**
- `chat:user:{userId}:conversations` - List conversations by user

---

#### 9️⃣ **CHAT - MESSAGE**
```
Key: chat:message:{messageId}
```

**Value Structure:**
```json
{
  "id": "msg_001",
  "conversationId": "conv_001",
  "senderId": "user_tenant_123",
  "senderName": "Ahmad Fauzi",
  "content": "Kamar masih tersedia kah?",
  "type": "text|image|file",
  "fileUrl": null,
  "fileName": null,
  "readBy": ["user_tenant_123"],
  "timestamp": "2024-11-04T10:00:00Z"
}
```

**Related Keys:**
- `chat:conversation:{conversationId}:messages` - List messages by conversation

---

#### 🔟 **TICKET**
```
Key: ticket:{ticketId}
```

**Value Structure:**
```json
{
  "id": "ticket_001",
  "reporterId": "user_tenant_123",
  "reporterName": "Ahmad Fauzi",
  "reporterEmail": "ahmad@email.com",
  "reporterRole": "tenant",
  "category": "technical|payment|content|abuse",
  "subject": "Pembayaran tidak masuk",
  "description": "Saya sudah bayar tapi status masih pending...",
  "priority": "low|medium|high|urgent",
  "status": "open|in_review|escalated|resolved|rejected",
  "assignedTo": "user_admin_789",
  "events": [
    {
      "type": "created|status_changed|comment|assigned",
      "userId": "user_tenant_123",
      "userName": "Ahmad Fauzi",
      "timestamp": "2024-11-04T10:00:00Z",
      "data": {
        "oldStatus": null,
        "newStatus": "open",
        "comment": "Tiket dibuat"
      }
    }
  ],
  "createdAt": "2024-11-04T10:00:00Z",
  "updatedAt": "2024-11-04T10:00:00Z",
  "resolvedAt": null
}
```

**Related Keys:**
- `ticket:reporter:{reporterId}` - List tickets by reporter
- `ticket:status:{status}` - List tickets by status
- `ticket:category:{category}` - List tickets by category

---

## 🔗 ENTITY RELATIONSHIP DIAGRAM (ERD)

### **Conceptual ERD**

```
┌──────────────────────────────────────────────────────────────────────┐
│                         KOSTIN ERD                                   │
│                    (Logical Relationships)                           │
└──────────────────────────────────────────────────────────────────────┘

┌─────────────────┐
│      USER       │
│  (Auth Table)   │
├─────────────────┤
│ PK: id          │
│    email        │
│    password     │
│    metadata     │
│    - name       │
│    - role       │─────────┐
│    created_at   │         │
└────────┬────────┘         │
         │                  │
         │ 1                │
         │                  │
         │ owns             │ extends
         │                  │
         │ N                │ 1
         │                  │
         ▼                  ▼
┌─────────────────┐  ┌─────────────────┐
│    PROPERTY     │  │     PROFILE     │
├─────────────────┤  │   (KV Store)    │
│ PK: id          │  ├─────────────────┤
│ FK: ownerId     │  │ PK: userId      │
│    name         │  │    phone        │
│    address      │  │    address      │
│    city         │  │    updated_at   │
│    type         │  └─────────────────┘
│    price        │
│    rooms        │
│    status       │
│    created_at   │
└────────┬────────┘
         │
         │ 1
         │
         │ has
         │
         │ N
         │
         ▼
┌─────────────────┐
│    CONTRACT     │
├─────────────────┤
│ PK: id          │
│ FK: tenantId    │───────────┐
│ FK: ownerId     │           │
│ FK: propertyId  │           │
│    startDate    │           │ 1
│    endDate      │           │
│    monthlyRent  │           │ for
│    status       │           │
│    created_at   │           │ N
└────────┬────────┘           │
         │                    │
         │ 1                  ▼
         │              ┌─────────────────┐
         │ generates    │     INVOICE     │
         │              ├─────────────────┤
         │ N            │ PK: id          │
         │              │ FK: contractId  │
         └──────────────│ FK: tenantId    │
                        │ FK: ownerId     │
                        │    amount       │
                        │    dueDate      │
                        │    status       │
                        │    created_at   │
                        └────────┬────────┘
                                 │
                                 │ 1
                                 │
                                 │ paid by
                                 │
                                 │ 0..1
                                 │
                                 ▼
                        ┌─────────────────┐
                        │     PAYMENT     │
                        ├─────────────────┤
                        │ PK: id          │
                        │ FK: invoiceId   │
                        │    method       │
                        │    amount       │
                        │    status       │
                        │    proofUrl     │
                        │    paidAt       │
                        └─────────────────┘


┌─────────────────┐         ┌─────────────────┐
│      USER       │─────────│    WISHLIST     │
│                 │    N    │  (Many-to-Many) │
│                 │─────────│                 │
└────────┬────────┘         └────────┬────────┘
         │                           │
         │                           │ N
         │ N                         │
         │                           │
         │ saves                     │
         │                           │
         │                           │
         │                           │
         │                           │
         └───────────┬───────────────┘
                     │
                     │ M
                     │
                     ▼
            ┌─────────────────┐
            │    PROPERTY     │
            │                 │
            └─────────────────┘


┌─────────────────┐         ┌─────────────────┐
│      USER       │─────────│  SAVED SEARCH   │
│                 │    1    │                 │
│                 │─────────│                 │
└─────────────────┘    N    └─────────────────┘


┌─────────────────┐         ┌─────────────────┐
│      USER       │─────────│  CONVERSATION   │
│   (Tenant)      │    N    │                 │
│                 │─────────│                 │───────┐
└─────────────────┘         └────────┬────────┘       │
                                     │                 │
                                     │ 1               │ N
         ┌───────────────────────────┘                 │
         │                                             │
         │ has                                         │
         │                                             │
         │ N                                           │
         │                                             │
         ▼                                             │
┌─────────────────┐                                   │
│     MESSAGE     │                                   │
│                 │                                   │
└─────────────────┘                                   │
                                                      │
┌─────────────────┐                                  │
│      USER       │──────────────────────────────────┘
│   (Owner)       │    N
│                 │
└─────────────────┘


┌─────────────────┐         ┌─────────────────┐
│      USER       │─────────│     TICKET      │
│                 │    1    │                 │
│                 │─────────│                 │───────┐
└─────────────────┘    N    └─────────────────┘       │
                                                       │
                                                       │ handled by
┌─────────────────┐                                   │
│      USER       │───────────────────────────────────┘
│    (Admin)      │    N
│                 │
└─────────────────┘
```

---

### **Physical ERD (KV Store Implementation)**

```
┌──────────────────────────────────────────────────────────────────────┐
│                    PHYSICAL KV STORE SCHEMA                          │
│             (Key Patterns & Value Structures)                        │
└──────────────────────────────────────────────────────────────────────┘

┌────────────────────────────────────────────────────────────────────┐
│                    TABLE: kv_store_dbd6b95a                        │
├────────────────────────────────────────────────────────────────────┤
│  key (TEXT, PRIMARY KEY)     │  value (JSONB)                      │
├──────────────────────────────┼─────────────────────────────────────┤
│ profile:{userId}             │ { phone, address, ... }             │
│ property:{propertyId}        │ { name, owner, price, ... }         │
│ contract:{contractId}        │ { tenant, property, dates, ... }    │
│ invoice:{invoiceId}          │ { amount, dueDate, status, ... }    │
│ payment:{paymentId}          │ { method, amount, proof, ... }      │
│ wishlist:{userId}:{propId}   │ { timestamp, propertyData, ... }    │
│ saved-search:{userId}:{id}   │ { name, filters, notif, ... }       │
│ chat:conversation:{convId}   │ { participants, lastMsg, ... }      │
│ chat:message:{messageId}     │ { sender, content, timestamp }      │
│ ticket:{ticketId}            │ { reporter, subject, events, ... }  │
└──────────────────────────────┴─────────────────────────────────────┘

KEY PATTERNS:
─────────────
Single Entity:        {type}:{id}
User Relation:        {type}:{userId}:{relatedId}
Nested Relation:      {type}:{parentType}:{parentId}:{childId}
Status Filter:        {type}:status:{statusValue}
List/Index:           {type}:{category}:list
```

---

## 📊 DATA FLOW DIAGRAM (DFD)

### **DFD LEVEL 0 - Context Diagram**

```
┌──────────────────────────────────────────────────────────────────────┐
│                        CONTEXT DIAGRAM                               │
│                  (System Boundary & External Entities)               │
└──────────────────────────────────────────────────────────────────────┘


                    ┌─────────────────┐
                    │     GUEST       │
                    │   (Visitor)     │
                    └────────┬────────┘
                             │
                    Browse   │ Register
                    Search   │ Apply
                             │
                             ▼
    ┌──────────────────────────────────────────────────────┐
    │                                                      │
    │                  KOSTIN SYSTEM                       │
    │         (Property Management Platform)               │
    │                                                      │
    │  • User Management                                   │
    │  • Property Listing                                  │
    │  • Contract Management                               │
    │  • Payment Processing                                │
    │  • Communication                                     │
    │  • Support & Moderation                              │
    │                                                      │
    └──┬────────┬────────────┬────────────┬─────────┬────┘
       │        │            │            │         │
       │        │            │            │         │
       ▼        ▼            ▼            ▼         ▼
  ┌─────────┐ ┌────────┐ ┌────────┐ ┌────────┐ ┌────────┐
  │ TENANT  │ │ OWNER  │ │ ADMIN  │ │MIDTRANS│ │SUPABASE│
  │(Penyewa)│ │(Pemilik)│ │ (Sys)  │ │Payment │ │ Auth & │
  └─────────┘ └────────┘ └────────┘ │Gateway │ │Storage │
                                     └────────┘ └────────┘
      │            │           │
      │ Pay Rent   │ Add Prop  │ Moderate
      │ View Inv   │ Verify Pay│ Manage
      │ Chat       │ Chat      │ Users
      │ Report     │ Report    │ Reports
      │            │           │
      └────────────┴───────────┘
```

---

### **DFD LEVEL 1 - Major Processes**

```
┌──────────────────────────────────────────────────────────────────────┐
│                          DFD LEVEL 1                                 │
│                    (Major System Processes)                          │
└──────────────────────────────────────────────────────────────────────┘

LEGEND:
━━━━━  Data Flow
[  ]   External Entity
( )    Process
═════  Data Store


    [GUEST/USER]
         │
         │ Login/Register
         │
         ▼
    ┌─────────────────┐
    │  1.0            │      User Data
    │  USER           │─────────────▶ ═══════════════
    │  MANAGEMENT     │                 D1: Users
    │                 │◀─────────────  (Supabase Auth)
    └─────────────────┘                ═══════════════
         │
         │ Auth Token
         │
         ├────────────────────┬────────────────────┬────────────────────┐
         │                    │                    │                    │
         ▼                    ▼                    ▼                    ▼
    ┌─────────────┐     ┌─────────────┐     ┌─────────────┐     ┌─────────────┐
    │  2.0        │     │  3.0        │     │  4.0        │     │  5.0        │
    │  PROPERTY   │     │  CONTRACT   │     │  PAYMENT    │     │  CHAT       │
    │  MANAGEMENT │     │  MANAGEMENT │     │  PROCESSING │     │  SYSTEM     │
    │             │     │             │     │             │     │             │
    └──────┬──────┘     └──────┬──────┘     └──────┬──────┘     └──────┬──────┘
           │                   │                    │                   │
           │                   │                    │                   │
           ▼                   ▼                    ▼                   ▼
    ═══════════════     ═══════════════     ═══════════════     ═══════════════
      D2: Property        D3: Contract        D4: Invoice         D5: Message
      D6: Wishlist                            D7: Payment         D8: Convers.
    ═══════════════     ═══════════════     ═══════════════     ═══════════════
           │                   │                    │
           │                   │                    │ Payment Status
           │                   │                    │
           │                   │                    ▼
           │                   │              ┌─────────────┐
           │                   │              │ [MIDTRANS]  │
           │                   │              │  Payment    │
           │                   │              │  Gateway    │
           │                   │              └─────────────┘
           │                   │
           └───────────┬───────┴────────────────────┐
                       │                            │
                       ▼                            ▼
                 ┌─────────────┐            ┌─────────────┐
                 │  6.0        │            │  7.0        │
                 │  WISHLIST & │            │  TICKETING  │
                 │  SEARCH     │            │  SYSTEM     │
                 │             │            │             │
                 └──────┬──────┘            └──────┬──────┘
                        │                          │
                        ▼                          ▼
                 ═══════════════            ═══════════════
                   D9: Wishlist               D10: Ticket
                   D10: Saved                 D11: Event
                   Search
                 ═══════════════            ═══════════════
```

---

### **DFD LEVEL 2 - Detailed Processes**

#### **2.1 - Property Management Process**

```
┌──────────────────────────────────────────────────────────────────────┐
│                    DFD LEVEL 2: PROPERTY MANAGEMENT                  │
└──────────────────────────────────────────────────────────────────────┘

[OWNER]
   │
   │ Property Data
   │
   ▼
┌─────────────────┐
│  2.1            │      Property
│  CREATE         │─────────────▶ ═══════════════
│  PROPERTY       │                 D2: Property
│                 │                ═══════════════
└─────────────────┘                      │
   │                                     │
   │ PropertyId                          │
   │                                     ▼
   ▼                              ┌─────────────────┐
┌─────────────────┐               │  2.2            │
│ [ADMIN]         │◀──────────────│  MODERATE       │
│ Moderator       │   Review Req  │  PROPERTY       │
└─────────────────┘               │                 │
   │                              └─────────────────┘
   │ Approval/Rejection                  │
   │                                     │
   └─────────────────────────────────────┤
                                         │ Status Update
                                         │
                                         ▼
                                  ═══════════════
                                    D2: Property
                                  ═══════════════
                                         │
                                         │ Active Properties
                                         │
                                         ▼
                                  ┌─────────────────┐
                                  │  2.3            │
[TENANT]                          │  BROWSE         │
   │                              │  PROPERTIES     │
   │ Search/Filter                │                 │
   └─────────────────────────────▶└─────────────────┘
                                         │
                                         │ Property List
                                         │
                                         ▼
                                  ┌─────────────────┐
                                  │  2.4            │
                                  │  ADD TO         │
                                  │  WISHLIST       │
                                  │                 │
                                  └────────┬────────┘
                                           │
                                           │ Wishlist Data
                                           │
                                           ▼
                                    ═══════════════
                                      D6: Wishlist
                                    ═══════════════
```

---

#### **2.2 - Contract Management Process**

```
┌──────────────────────────────────────────────────────────────────────┐
│                   DFD LEVEL 2: CONTRACT MANAGEMENT                   │
└──────────────────────────────────────────────────────────────────────┘

[TENANT]
   │
   │ Application Data
   │ (Property, Room, Duration)
   │
   ▼
┌─────────────────┐
│  3.1            │      Application
│  APPLY          │─────────────▶ ═══════════════
│  RENTAL         │                 Temp Storage
│                 │                ═══════════════
└─────────────────┘                      │
   │                                     │
   │ ApplicationId                       │ Notify Owner
   │                                     │
   │                                     ▼
   │                              ┌─────────────────┐
   │                              │ [OWNER]         │
   │                              │ Reviews App     │
   │                              └────────┬────────┘
   │                                       │
   │                                       │ Approval
   │                                       │
   │                                       ▼
   │                              ┌─────────────────┐
   │                              │  3.2            │
   │                              │  CREATE         │
   │                              │  CONTRACT       │
   │                              │                 │
   │                              └────────┬────────┘
   │                                       │
   │                                       │ Contract Data
   │                                       │
   │                                       ▼
   │                                ═══════════════
   │                                  D3: Contract
   │                                ═══════════════
   │                                       │
   │                                       │ ContractId
   │                                       │
   │                                       ▼
   │                              ┌─────────────────┐
   │                              │  3.3            │
   │                              │  GENERATE       │
   └──────────────────────────────│  INVOICES       │
                                  │  (Monthly)      │
                                  └────────┬────────┘
                                           │
                                           │ Invoice Data
                                           │
                                           ▼
                                    ═══════════════
                                      D4: Invoice
                                    ═══════════════
```

---

#### **2.3 - Payment Processing**

```
┌──────────────────────────────────────────────────────────────────────┐
│                   DFD LEVEL 2: PAYMENT PROCESSING                    │
└──────────────────────────────────────────────────────────────────────┘

[TENANT]
   │
   │ Pay Invoice
   │
   ├──────────────────────────┬────────────────────────┐
   │                          │                        │
   │ QRIS                     │ Manual                 │ Manual Upload
   │                          │                        │
   ▼                          ▼                        ▼
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│  4.1            │    │  4.2            │    │  4.3            │
│  CREATE QRIS    │    │  UPLOAD         │    │  UPLOAD TO      │
│  TRANSACTION    │    │  PROOF          │    │  STORAGE        │
│                 │    │                 │    │  (Supabase)     │
└────────┬────────┘    └────────┬────────┘    └────────┬────────┘
         │                      │                       │
         │ Order Data           │ Proof File            │ File URL
         │                      │                       │
         ▼                      ▼                       ▼
  ┌─────────────┐      ┌─────────────────┐     ═══════════════
  │ [MIDTRANS]  │      │  4.4            │       D12: Storage
  │  Core API   │      │  SAVE PAYMENT   │     ═══════════════
  │             │      │  RECORD         │
  └──────┬──────┘      └────────┬────────┘
         │                      │
         │ QRIS String          │ Payment Data
         │ Transaction ID       │
         │                      ▼
         ▼               ═══════════════
┌─────────────────┐       D7: Payment
│  4.5            │     ═══════════════
│  DISPLAY QR     │            │
│  CODE           │            │ Notify
└─────────────────┘            │
         │                     ▼
         │              ┌─────────────────┐
         │ User Scans   │ [OWNER/ADMIN]   │
         │              │ Verifies        │
         ▼              └────────┬────────┘
┌─────────────────┐             │
│  4.6            │             │ Approve/Reject
│  AUTO-DETECT    │             │
│  PAYMENT        │             ▼
│  (Polling 3s)   │      ┌─────────────────┐
└────────┬────────┘      │  4.7            │
         │               │  UPDATE         │
         │ Status        │  INVOICE        │
         │ Settlement    │  STATUS         │
         │               │                 │
         └───────────────▶└────────┬────────┘
                                   │
                                   │ Update Status
                                   │
                                   ▼
                            ═══════════════
                              D4: Invoice
                            ═══════════════
```

---

#### **2.4 - Chat System**

```
┌──────────────────────────────────────────────────────────────────────┐
│                      DFD LEVEL 2: CHAT SYSTEM                        │
└──────────────────────────────────────────────────────────────────────┘

[TENANT]                                                    [OWNER]
   │                                                           │
   │ Start Chat                                                │
   │ About Property                                            │
   │                                                           │
   ├───────────────────────────┬───────────────────────────────┤
   │                           │                               │
   ▼                           ▼                               ▼
┌─────────────────┐     ┌─────────────────┐          ┌─────────────────┐
│  5.1            │     │  5.2            │          │  5.3            │
│  CREATE/FIND    │────▶│  LOAD           │◀─────────│  JOIN           │
│  CONVERSATION   │     │  CONVERSATION   │          │  CONVERSATION   │
│                 │     │                 │          │                 │
└─────────────────┘     └────────┬────────┘          └─────────────────┘
         │                       │
         │                       │ Conversation Data
         │                       │
         ▼                       ▼
  ═══════════════         ═══════════════
    D8: Convers.            D5: Message
  ═══════════════         ═══════════════
         │                       │
         │                       │
         └───────────┬───────────┘
                     │
                     │ Messages
                     │
                     ▼
            ┌─────────────────┐
            │  5.4            │
            │  DISPLAY        │
            │  MESSAGES       │
            │                 │
            └────────┬────────┘
                     │
[TENANT/OWNER]       │ Type Message
   │                 │
   │ Send Message    │
   │                 │
   └────────────────▶▼
            ┌─────────────────┐
            │  5.5            │      Message Data
            │  SEND           │────────────────▶ ═══════════════
            │  MESSAGE        │                   D5: Message
            │                 │                 ═══════════════
            └────────┬────────┘                        │
                     │                                 │
                     │                                 │ Update Last Msg
                     │                                 │
                     └─────────────────────────────────┤
                                                       │
                                                       ▼
                                                ═══════════════
                                                  D8: Convers.
                                                ═══════════════
                                                       │
                                                       │ Notify
                                                       │
                                                       ▼
                                                [OTHER USER]
```

---

#### **2.5 - Ticketing System**

```
┌──────────────────────────────────────────────────────────────────────┐
│                    DFD LEVEL 2: TICKETING SYSTEM                     │
└──────────────────────────────────────────────────────────────────────┘

[TENANT/OWNER]
   │
   │ Report Issue
   │
   ▼
┌─────────────────┐
│  7.1            │      Ticket Data
│  CREATE         │─────────────────▶ ═══════════════
│  TICKET         │                     D10: Ticket
│                 │                   ═══════════════
└─────────────────┘                          │
   │                                         │
   │ TicketId                                │ Notify Admin
   │                                         │
   │                                         ▼
   │                                  ┌─────────────────┐
   │                                  │ [ADMIN]         │
   │                                  │ Review Queue    │
   │                                  └────────┬────────┘
   │                                           │
   │                                           │ Select Ticket
   │                                           │
   │                                           ▼
   │                                  ┌─────────────────┐
   │                                  │  7.2            │
   │                                  │  REVIEW         │
   │                                  │  TICKET         │
   │                                  │                 │
   │                                  └────────┬────────┘
   │                                           │
   │                                           │ Action
   │                                           │
   ├───────────────────────────────────────────┤
   │                                           │
   │ Add Comment                               │ Change Status
   │                                           │ Add Comment
   ▼                                           ▼
┌─────────────────┐                    ┌─────────────────┐
│  7.3            │      Event         │  7.4            │     Event
│  ADD COMMENT    │─────────────────▶  │  UPDATE         │──────────▶
│                 │                    │  STATUS         │
│                 │                    │                 │
└─────────────────┘                    └─────────────────┘
         │                                     │
         │                                     │
         └─────────────────┬───────────────────┘
                           │
                           │ Event Data
                           │
                           ▼
                    ═══════════════
                      D10: Ticket
                      D11: Event
                    ═══════════════
                           │
                           │ Notification
                           │
                           ▼
                    [TICKET OWNER]
```

---

## 🔑 KEY PATTERNS REFERENCE

### **Complete Key Pattern Catalog**

```typescript
// ==========================================
// USER & PROFILE
// ==========================================
profile:{userId}                              // User profile data
profile:tenant:{tenantId}                     // Tenant-specific data
profile:owner:{ownerId}                       // Owner-specific data

// ==========================================
// PROPERTY
// ==========================================
property:{propertyId}                         // Property detail
property:owner:{ownerId}                      // List by owner
property:city:{city}                          // List by city
property:status:active                        // Active properties
property:status:pending_approval              // Pending moderation
property:type:{type}                          // Filter by type

// ==========================================
// CONTRACT
// ==========================================
contract:{contractId}                         // Contract detail
contract:tenant:{tenantId}                    // Contracts by tenant
contract:owner:{ownerId}                      // Contracts by owner
contract:property:{propertyId}                // Contracts by property
contract:status:active                        // Active contracts
contract:status:expired                       // Expired contracts

// ==========================================
// INVOICE
// ==========================================
invoice:{invoiceId}                           // Invoice detail
invoice:tenant:{tenantId}                     // Invoices by tenant
invoice:owner:{ownerId}                       // Invoices by owner
invoice:contract:{contractId}                 // Invoices by contract
invoice:status:pending                        // Pending invoices
invoice:status:paid                           // Paid invoices
invoice:status:overdue                        // Overdue invoices
invoice:month:{year}-{month}                  // Invoices by month

// ==========================================
// PAYMENT
// ==========================================
payment:{paymentId}                           // Payment detail
payment:invoice:{invoiceId}                   // Payment for invoice
payment:tenant:{tenantId}                     // Payments by tenant
payment:type:qris                             // QRIS payments
payment:type:manual                           // Manual payments
payment:status:pending                        // Pending payments
payment:status:success                        // Successful payments
payment:midtrans:{orderId}                    // Payment by Midtrans order

// ==========================================
// WISHLIST
// ==========================================
wishlist:{userId}:{propertyId}                // User wishlist item
wishlist:{userId}:*                           // All wishlists (prefix)

// ==========================================
// SAVED SEARCH
// ==========================================
saved-search:{userId}:{searchId}              // Saved search
saved-search:{userId}:*                       // All saved searches

// ==========================================
// CHAT
// ==========================================
chat:conversation:{conversationId}            // Conversation detail
chat:user:{userId}:conversations              // User conversations list
chat:property:{propertyId}:conversations      // Property chats
chat:message:{messageId}                      // Message detail
chat:conversation:{convId}:messages           // Messages in conversation

// ==========================================
// TICKET
// ==========================================
ticket:{ticketId}                             // Ticket detail
ticket:reporter:{reporterId}                  // Tickets by reporter
ticket:status:{status}                        // Tickets by status
ticket:category:{category}                    // Tickets by category
ticket:priority:{priority}                    // Tickets by priority
ticket:assigned:{adminId}                     // Tickets assigned to admin

// ==========================================
// ADMIN & SYSTEM
// ==========================================
owner-upgrade:{userId}                        // Owner upgrade record
demo-user:{email}                             // Demo user flag
system:config                                 // System configuration
system:stats                                  // System statistics
```

---

## 📦 DATA STRUCTURES

### **Complete JSON Schemas**

Sudah dijelaskan lengkap di section **Database Schema (KV Store)** di atas untuk:
- ✅ User/Profile
- ✅ Property
- ✅ Contract
- ✅ Invoice
- ✅ Payment
- ✅ Wishlist
- ✅ Saved Search
- ✅ Chat (Conversation & Message)
- ✅ Ticket

---

## 🔧 DATABASE OPERATIONS

### **KV Store Operations**

**Available Functions:**
```typescript
// Single operations
await kv.get(key)           // Get single value
await kv.set(key, value)    // Set/Update value
await kv.del(key)           // Delete key

// Multiple operations
await kv.mget([key1, key2])         // Get multiple
await kv.mset([key1, key2], [v1, v2]) // Set multiple
await kv.mdel([key1, key2])         // Delete multiple

// Prefix operations
await kv.getByPrefix('wishlist:user123:')  // Get all matching prefix
```

---

### **Common Query Patterns**

#### **1. Get User Profile**
```typescript
const profile = await kv.get(`profile:${userId}`);
```

#### **2. List All Properties by Owner**
```typescript
const properties = await kv.getByPrefix(`property:owner:${ownerId}:`);
```

#### **3. List Pending Invoices for Tenant**
```typescript
// Get all tenant invoices
const allInvoices = await kv.getByPrefix(`invoice:tenant:${tenantId}:`);

// Filter for pending
const pending = allInvoices.filter(inv => inv.status === 'pending');
```

#### **4. Get Active Contract**
```typescript
const contracts = await kv.getByPrefix(`contract:tenant:${tenantId}:`);
const active = contracts.find(c => c.status === 'active');
```

#### **5. Get Conversation Messages**
```typescript
const messages = await kv.getByPrefix(`chat:conversation:${convId}:messages:`);
// Sort by timestamp
messages.sort((a, b) => new Date(a.timestamp) - new Date(b.timestamp));
```

#### **6. Get User Wishlist**
```typescript
const wishlists = await kv.getByPrefix(`wishlist:${userId}:`);
```

#### **7. List Tickets by Status**
```typescript
const tickets = await kv.getByPrefix(`ticket:status:${status}:`);
```

---

### **Indexing Strategy**

**Primary Index:** Key (TEXT) with B-tree
**Secondary Index:** Key prefix for pattern matching

**Optimization Tips:**
- ✅ Use composite keys untuk common queries
- ✅ Prefix-based listing instead of full table scan
- ✅ Denormalize data untuk reduce lookups
- ✅ Cache frequently accessed data di frontend
- ✅ Batch operations dengan mget/mset

---

## 🔐 DATA INTEGRITY

### **Application-Level Constraints**

Karena KV Store tidak enforce foreign keys, validation dilakukan di application layer:

**1. Referential Integrity:**
```typescript
// Before deleting property, check contracts
const contracts = await kv.getByPrefix(`contract:property:${propertyId}:`);
if (contracts.length > 0) {
  throw new Error("Cannot delete property with active contracts");
}
```

**2. Unique Constraints:**
```typescript
// Check duplicate before insert
const existing = await kv.get(`property:${propertyId}`);
if (existing) {
  throw new Error("Property already exists");
}
```

**3. Required Fields:**
```typescript
// Validate required fields
if (!property.name || !property.ownerId || !property.price) {
  throw new Error("Missing required fields");
}
```

---

## 📈 SCALABILITY CONSIDERATIONS

### **Current Setup:**
- Single KV Store table
- Prefix-based partitioning
- JSON flexible schema

### **Future Scaling Options:**

**1. Add Indexes:**
```sql
CREATE INDEX idx_kv_value_status ON kv_store_dbd6b95a 
  ((value->>'status'));

CREATE INDEX idx_kv_value_owner ON kv_store_dbd6b95a 
  ((value->>'ownerId'));
```

**2. Table Partitioning:**
```sql
-- Partition by key prefix
CREATE TABLE kv_property PARTITION OF kv_store_dbd6b95a
  FOR VALUES FROM ('property:') TO ('property;');
```

**3. Caching Layer:**
- Redis for hot data
- In-memory cache for sessions
- CDN for static assets

**4. Read Replicas:**
- Separate read/write connections
- Load balancing across replicas

---

## 📊 DATABASE STATISTICS

**Estimated Storage per Entity:**
- User Profile: ~500 bytes
- Property: ~2 KB
- Contract: ~1.5 KB
- Invoice: ~800 bytes
- Payment: ~1 KB
- Message: ~500 bytes
- Ticket: ~2 KB (with events)

**Example Capacity (1GB Database):**
- ~2M user profiles
- ~500K properties
- ~1M invoices
- ~2M messages

---

## 🔄 MIGRATION STRATEGY

### **From KV to Relational (Future)**

If scaling requires relational DB:

**1. Create normalized tables**
**2. Migrate data with ETL script**
**3. Dual-write during transition**
**4. Cutover when verified**

**Migration Script Example:**
```typescript
// Read from KV
const properties = await kv.getByPrefix('property:');

// Write to relational
for (const prop of properties) {
  await db.property.create({
    data: {
      id: prop.id,
      ownerId: prop.ownerId,
      name: prop.name,
      // ... other fields
    }
  });
}
```

---

## 📚 DOCUMENTATION REFERENCES

**Related Docs:**
- [Complete System Documentation](./COMPLETE_SYSTEM_DOCUMENTATION.md)
- [Midtrans Setup](./MIDTRANS_SETUP.md)
- [Quick Start Guide](./QUICK_START_DEVELOPER.md)

**External Resources:**
- [Supabase Docs](https://supabase.com/docs)
- [PostgreSQL JSONB](https://www.postgresql.org/docs/current/datatype-json.html)
- [Key-Value Design Patterns](https://redis.io/docs/manual/patterns/)

---

**Last Updated:** November 4, 2024  
**Version:** 1.0  
**Database:** Supabase PostgreSQL + KV Store Pattern
