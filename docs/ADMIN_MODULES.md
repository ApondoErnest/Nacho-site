# Admin Dashboard Modules - NACHO Vehicle Inspection

The admin dashboard lives under `/admin`, requires authentication, and is gated by role/ability ([ROLES.md](ROLES.md)).

## 1. Admin layout

- sidebar navigation (links shown per ability via `@adminCan`)
- top bar with logged-in user + logout
- main content area
- notification cues (pending bookings, unread messages, new applications)
- responsive admin design

## 2. Dashboard summary cards

- total centers
- operational centers
- centers under construction
- total services
- total tariffs
- total bookings
- pending bookings
- total contact messages
- unread contact messages
- published blog posts
- open career posts

## 3. Center management

Create, edit, view, delete (soft), activate/deactivate, upload image, set operational status, add location info, **nearby landmark**, **vehicle categories accepted** (FR/EN), add Google Map link, assign services to center.

## 4. Service management

Create, edit, view, delete (soft), activate/deactivate, FR/EN content, upload image, set icon, set display order, manage SEO fields.

## 5. Tariff management

Master Pricing Console data and revision workflow (ADR 006, ADR 007):

- View all tariff rows (`category_slug`, FR/EN names, price, validity, bookable flag)
- Create and edit tariff rows; maintain `description_*`, `vehicle_icon`, weight bounds, display order
- **Create future tariff revision** with `effective_date` and price snapshot
- **Preview** revision before publication
- **Activate/deactivate** categories (`is_active`, `is_bookable`)
- Set `regulatory_reference`, `last_verified_at`, `effective_date`, optional `expiry_date`
- View **revision history** and **audit log** (`tariff_audit_logs`)
- **No hard delete** of historical tariffs or revisions — deactivate or mark `superseded` only
- Public site auto-displays the revision whose `effective_date` is current (via `TariffService`) — no manual midnight price swap

Every admin field change writes a `tariff_audit_logs` record (who/when/before-after). Scheduled publishes use `tariff_revisions`.

## 6. Booking management

View, search, filter by status/center/service, view details, update status, add admin notes, internally cancel/reschedule.

Status workflow: pending -> confirmed -> arrived -> in_inspection -> completed; plus cancelled, no_show, rescheduled.

No reminder/expiry fields anywhere in this module.

## 7. Contact message management

View, mark as read, mark as replied, archive, add admin notes, search. Statuses: new, read, replied, archived.

## 8. Blog management

Create blog categories; create/edit posts; save drafts; publish; archive; upload featured images; manage FR/EN content; manage SEO title and meta description. Statuses: draft, published, archived.

## 9. Careers management

Create/edit/close job posts; view applications; download CVs; update application status; add admin notes. Application statuses: new, reviewed, shortlisted, rejected, accepted.

## 10. Page management

Edit legal/static pages: Privacy Policy, Terms and Conditions, Cookie Policy, Legal Notice (optionally About, Compliance & Quality). FR/EN content + SEO fields; status draft/published/archived.

## 11. Media management

Upload images and documents; view library; delete; add FR/EN alt text; reuse media in pages, blog posts, centers, and services. Upload restrictions per [SECURITY.md](SECURITY.md).

## 12. User & role management (Super Admin only)

Create/edit staff users; deactivate users; assign roles; reset access. Roles per [ROLES.md](ROLES.md).

## 13. Site settings management

Manage: site name, logo, contact email, contact phone, address, social links, footer text (FR/EN), default language, primary theme color, maintenance mode. Backed by the `site_settings` table.

## 14. Access summary

Which roles can use which module is defined by the permission matrix in [ROLES.md](ROLES.md). Sidebar links and action buttons are hidden when the current user's role lacks the ability.
