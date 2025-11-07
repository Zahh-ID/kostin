# Project Kanban – KostIn (Laravel + Blade)

## Backlog
- [ ] Reconcile platform documentation with current Laravel + MySQL stack (replace Supabase/React references)
- [ ] Produce Laravel-centric architecture guide (controllers, views, models, schema)
- [ ] Audit feature parity vs. Supabase spec (payments, chat, ticketing, dashboards) and capture gaps
- [ ] Refine admin/owner/tenant dashboards to ensure charts and analytics reflect available MySQL data
- [ ] Harden migrations for SQLite-friendly test runs (manual payment + Midtrans columns)
- [ ] QA tenant ↔ owner rental application flow end-to-end (validation, terms agreement, approvals)
- [ ] Formalize QA plan covering Midtrans QRIS + manual payments, moderation, chat, ticketing

## In Progress

## Done
- [x] Bootstrap owner room listing/detail UI and wire it to the shared layout
- [x] Implement owner room creation/editing with validation, facilities override, and room-type scoped routing
- [x] Enable owner property publishing workflow with admin moderation review UI
- [x] Add automated Pest coverage for payments, ticketing, and chat modules
- [x] Relationalise database schema, factories, and seeders (wishlist, tickets, manual payments, etc.)
- [x] Rebuild authentication (email + Google OAuth) and role-aware scaffolding
- [x] Convert shared layouts (public, guest, app) and dashboards to Bootstrap
- [x] Refactor public landing & property pages to match Bootstrap design system
- [x] Bootstrap tenant invoice list/detail screens for the new UI foundation
- [x] Implement manual payment upload + owner verification flow (tenant ↔ owner)
- [x] Integrate Midtrans QRIS generation with real API credentials and callbacks
- [x] Implement wishlist & saved-search experiences on the tenant side
- [x] Launch ticketing kanban board (admin) and list view (tenant/owner)
- [x] Wire up live chat polling experience with Bootstrap friendly styling
- [x] Enrich dashboards with real data visualisations (occupancy, revenue, CTA states)
- [x] Migrate remaining Livewire profile/settings screens to Bootstrap components
