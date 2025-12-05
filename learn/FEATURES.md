# Features & Implementation Details

## 1. Tenant Features
**Role**: `tenant`
**Base Route**: `/api/v1/tenant`

| Feature | Description | Controller | Middleware |
|---------|-------------|------------|------------|
| **Search** | Find properties with filters | `Tenant\SearchController` | Public |
| **Wishlist** | Save properties for later | `Tenant\WishlistController` | `auth:sanctum` |
| **Applications** | Apply for a room | `Tenant\ApplicationController` | `auth:sanctum` |
| **Contracts** | View active lease agreements | `Tenant\ContractController` | `auth:sanctum` |
| **Contract PDF** | Download lease agreement PDF | `Tenant\ContractPdfController` | `auth:sanctum` |
| **Invoices** | View rent invoices | `Api\V1\InvoiceController` | `auth:sanctum` |
| **Payments** | Pay invoices (Midtrans Snap) | `Web\Tenant\InvoicePaymentController` | `auth:sanctum` |
| **Payment Status** | Check payment status | `Web\Tenant\InvoicePaymentStatusController` | `auth:sanctum` |
| **Manual Payment** | Upload transfer proof | `Web\Tenant\ManualPaymentController` | `auth:sanctum` |
| **Tickets** | Report issues to owner | `Tenant\TicketController` | `auth:sanctum` |
| **Overview** | Dashboard stats | `Tenant\OverviewController` | `auth:sanctum` |

### Key Logic
-   **Payments**: Uses `Midtrans` gateway. `InvoicePaymentController` handles the Snap Token generation.
-   **PDFs**: `ContractPdfController` generates lease agreements using `barryvdh/laravel-dompdf`.
-   **Manual Payments**: Allows tenants to upload proof of transfer for manual verification.

---

## 2. Owner Features
**Role**: `owner`
**Base Route**: `/api/v1/owner`

| Feature | Description | Controller | Middleware |
|---------|-------------|------------|------------|
| **Properties** | CRUD for boarding houses | `Owner\PropertyController` | `role:owner` |
| **Property Photos** | Manage property images | `Owner\PropertyPhotoController` | `role:owner` |
| **Rooms** | Manage room units | `Owner\RoomController` | `role:owner` |
| **Room Types** | Manage room categories | `Owner\RoomTypeController` | `role:owner` |
| **Room Bulk** | Create multiple rooms | `Owner\PropertyRoomController` | `role:owner` |
| **Applications** | Approve/Reject tenants | `Owner\ApplicationIndexController` | `role:owner` |
| **Approve/Reject** | Application actions | `Owner\OwnerApplicationController` | `role:owner` |
| **Contracts** | Manage tenant leases | `Owner\ContractIndexController` | `role:owner` |
| **Terminate** | End lease early | `Owner\OwnerContractController` | `role:owner` |
| **Manual Payments** | Verify transfer proofs | `Owner\ManualPaymentIndexController` | `role:owner` |
| **Verify Payment** | Approve/Reject payment | `Owner\OwnerManualPaymentController` | `role:owner` |
| **Wallet** | View earnings | `Owner\WalletController` | `role:owner` |
| **Withdraw** | Request payout | `Owner\WalletWithdrawController` | `role:owner` |
| **Tickets** | Manage tenant issues | `Owner\TicketIndexController` | `role:owner` |
| **Update Ticket** | Resolve/Reply to tickets | `Owner\TicketUpdateController` | `role:owner` |
| **Dashboard** | Statistics & Overview | `Owner\DashboardController` | `role:owner` |

### Key Logic
-   **Withdrawals**: `WalletWithdrawController` handles payout requests.
-   **Bulk Creation**: `PropertyRoomController` allows creating multiple rooms at once.
-   **Verification**: Owners must manually approve "Manual Payment" proofs.

---

## 3. Admin Features
**Role**: `admin`
**Base Route**: `/api/v1/admin`

| Feature | Description | Controller | Middleware |
|---------|-------------|------------|------------|
| **Dashboard** | System-wide stats | `Admin\DashboardController` | `role:admin` |
| **Moderation** | List pending properties | `Admin\ModerationIndexController` | `role:admin` |
| **Approve/Reject** | Moderate properties | `Admin\ModerationActionController` | `role:admin` |
| **Users** | List all users | `Admin\UserIndexController` | `role:admin` |
| **User Actions** | Suspend/Activate users | `Admin\UserActionController` | `role:admin` |
| **Tickets** | Oversee disputes | `Admin\TicketIndexController` | `role:admin` |
| **Webhook Sim** | Simulate Midtrans events | `Admin\WebhookSimulatorController` | `role:admin` |

---

## 4. Shared / System Features

| Feature | Description | Controller |
|---------|-------------|------------|
| **Payment API** | Core payment logic (QRIS/Bank) | `Api\V1\PaymentController` |
| **Webhooks** | Handle Midtrans payment notifications | `WebhookController` |
| **Cron Jobs** | Daily invoice reminders | `Console\Commands\SendInvoiceReminders` |
| **Stats** | Public landing page statistics | `Api\V1\StatsController` |
| **Wishlist** | Save properties for later | `Tenant\WishlistController` | `auth:sanctum` |
| **Applications** | Apply for a room | `Tenant\ApplicationController` | `auth:sanctum` |
| **Contracts** | View active lease agreements | `Tenant\ContractController` | `auth:sanctum` |
| **Invoices** | View and pay rent invoices | `InvoicePaymentController` | `auth:sanctum` |
| **Tickets** | Report issues to owner | `Tenant\TicketController` | `auth:sanctum` |

### Key Logic
-   **Payments**: Uses `Midtrans` gateway. `InvoicePaymentController` handles the Snap Token generation.
-   **PDFs**: `ContractPdfController` generates lease agreements using `barryvdh/laravel-dompdf`.

---

## 2. Owner Features
**Role**: `owner`
**Base Route**: `/api/v1/owner`

| Feature | Description | Controller | Middleware |
|---------|-------------|------------|------------|
| **Properties** | CRUD for boarding houses | `Owner\PropertyController` | `role:owner` |
| **Rooms** | Manage room types & units | `Owner\RoomController` | `role:owner` |
| **Applications** | Approve/Reject tenants | `Owner\ApplicationIndexController` | `role:owner` |
| **Contracts** | Manage tenant leases | `Owner\ContractIndexController` | `role:owner` |
| **Wallet** | View earnings & withdraw | `Owner\WalletController` | `role:owner` |
| **Dashboard** | Statistics & Overview | `Owner\DashboardController` | `role:owner` |

### Key Logic
-   **Withdrawals**: `WalletWithdrawController` handles payout requests (manual or automated depending on implementation).
-   **Bulk Creation**: `PropertyRoomController` allows creating multiple rooms at once.

---

## 3. Admin Features
**Role**: `admin`
**Base Route**: `/api/v1/admin`

| Feature | Description | Controller | Middleware |
|---------|-------------|------------|------------|
| **Moderation** | Approve/Reject new properties | `Admin\ModerationIndexController` | `role:admin` |
| **Users** | Suspend/Ban users | `Admin\UserIndexController` | `role:admin` |
| **Tickets** | Oversee disputes | `Admin\TicketIndexController` | `role:admin` |

---

## 4. Shared / System Features

| Feature | Description | Controller |
|---------|-------------|------------|
| **Webhooks** | Handle Midtrans payment notifications | `WebhookController` |
| **Cron Jobs** | Daily invoice reminders | `Console\Commands\SendInvoiceReminders` |
| **Stats** | Public landing page statistics | `StatsController` |
