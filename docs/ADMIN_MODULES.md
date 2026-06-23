# Admin Dashboard Modules - NACHO Vehicle Inspection

The admin dashboard lives under `/admin`, requires authentication, and is gated by role/ability ([ROLES.md](ROLES.md)).

## 1. Admin layout

- sidebar navigation (links shown per ability via `@adminCan`)
- top bar with logged-in user + logout
- main content area
- notification cues (pending bookings, unread messages)
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

Create, edit, view, delete (soft), activate/deactivate, upload image, set status (`planned`, `construction`, `active`, `inactive`), bilingual location fields, **nearby landmark**, **search keywords**, **vehicle categories** (FR/EN), **google_maps_url**, coordinates, **is_headquarters**, **booking_enabled**, **display_order**.

**Contacts (`center_contacts`):** add/edit/remove multiple phones, WhatsApp lines, emails; set primary and public visibility; order for display.

**Hours (`center_hours`):** structured weekly schedule editor (replaces raw JSON).

**Services (`center_service` pivot):** assign services with `is_available`, `booking_enabled`, `effective_date`, optional bilingual notes — drives public service filter.

**Expansion centers:** set `target_opening_date`, `target_date_text_*`, `expansion_phase`, `expansion_updated_at`; publish optional `center_progress_updates` history.

**Activate expansion workflow:** when Douala/Kumba opens — change status to `active`, add contacts/hours, assign services, enable `booking_enabled`, publish map URL; frontend moves center from expansion section to finder automatically.

**No** Notify Me, SMS, or WhatsApp subscription features on expansion cards.

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

Vacancy CRUD (ADR 009) — **no** online application inbox:

- Create/edit vacancies with all bilingual fields (`title_*`, `summary_*`, `description_*`, `responsibilities_*`, `requirements_*`, `preferred_requirements_*`, `skills_*`, `application_documents_*`)
- Assign `department_id`, `center_id`; set `reference`, `employment_type`, `vacancies_count`, `closes_at`
- Configure `application_email`, `application_subject`, `application_instructions_*`; **preview mailto link** before publish
- Status workflow: `draft` → `published` → `closing_soon` → `closed` / `filled` → `archived`
- `allow_email_application` toggle; verify recruitment email before publishing
- Manage `career_departments` (names, icons, order)
- General application + recruitment safety notice via `site_settings` ([SEEDING.md](SEEDING.md) §10)

**Removed:** view applications, CV download, application status workflow.

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
