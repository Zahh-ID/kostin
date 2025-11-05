## Dashboard
- Tenant: `/tenant` → charts (payment trend, invoice status), cards (active contracts, unpaid invoices), action alert for next due invoice.
- Owner: `/owner` → income trend, room status doughnut, upcoming tasks/contract expirations, moderation CTAs.
- Admin: `/admin` → revenue & registration charts, property/ticket breakdowns, latest tickets/moderations, user/invoice lists.
- Shared layout: Bootstrap sidebar with role-aware menu, badge counts for pending work, profile header cards.

## Tenant
- Invoices: QRIS + manual upload, status badges, history table.
- Contracts: list/detail with property hierarchy, call-to-action.
- Wishlist & Saved Searches: Bootstrap card grid/list, Livewire actions.
- Tickets: list/create/detail with status chips, message timeline.

## Owner
- Properties, contracts, room types/rooms, shared tasks, manual payments (approval), tickets (assigned workflow).
- Dashboard analytics (see above).

## Admin
- Moderation queue, ticketing Kanban & detail, user management, settings.

## Shared Modules
- Live chat (`/chat`): Livewire panel, polling, unread detection.
- Auth: Breeze + Google OAuth, profile update/password/delete screens (Bootstrap cards/modals).
- Payments: Midtrans QRIS integration, manual verification, webhooks.
- Navigation component: Volt sidebar with logout + live badge counts.

## Tests
- Pest feature suites for auth, tenant modules (invoices, wishlist, tickets, chat), owner manual payments/tickets, admin tickets, property API, profile.
- Midtrans webhook coverage (success, expiry, invalid signature) plus ticket closed_at regression and chat unread/access guard scenarios.
- Unit tests for contracts etc.

## Outstanding Backlog
- None – next items can be planned based on new requirements.
