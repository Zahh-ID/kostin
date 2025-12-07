# DashboardController

**Namespace**: `App\Http\Controllers\Api\V1\Admin`

## Description
Provides the data for the Admin Dashboard, including key metrics, trends, and status counts.

## Methods

### `__invoke`
- **Method**: GET
- **Path**: `/api/v1/admin/dashboard`
- **Description**: Returns a summary of the platform's performance.
- **Response Data**:
    - `revenue_this_month`: Total revenue for the current month.
    - `registrations_this_month`: New user registrations.
    - `pending_moderations`: Count of properties waiting for approval.
    - `tickets_open`: Count of open support tickets.
    - `invoices`: Breakdown of invoice statuses (paid, unpaid, etc.).
    - `users`: Count of users by role (admin, owner, tenant).
    - `revenue_trend`: 6-month revenue trend data.
    - `registrations_trend`: 6-month registration trend data.
    - `approved_properties`: Total approved properties.
    - `rejected_properties`: Total rejected properties.
