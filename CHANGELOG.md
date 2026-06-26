# Changelog

All notable changes to this project are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).

## [Unreleased]

### Added

- **Step 26 — Admin auth + custom roles.** Added the custom `AdminAccess` ability matrix, `admin.active`, `admin.ability`, and `role` middleware, `@adminCan` Blade checks, inactive-user login blocking, staff login redirects to `/admin`, and a protected `/admin` access foundation page. Added feature tests for guest redirects, inactive users, ability/role gates, Blade authorization, and admin login behavior.

- **Step 24 — Contact form backend.** Added public contact message validation, a POST-backed `/contact` flow, a hidden honeypot field, and persistence into `contact_messages` with `new` status. The contact page now preserves failed submissions and shows bilingual success/error feedback. Added feature tests for successful storage, invalid centers/reasons, honeypot no-store behavior, and form route wiring.

- **Step 23 — Booking form backend.** Added `BookingReferenceService` for unique `NACHO-YYYYMMDD-XXXX` references, a public booking request validator that enforces bookable centers/services/tariffs and consent, and a POST-backed booking flow that stores pending bookings from the public form. The booking page now submits stable tariff slugs, preserves failed submissions, and displays the generated reference on success. Added feature tests for successful booking creation, invalid center/service combinations, non-bookable centers, and stable form payloads.

- **Step 22 — Public controllers backed by database data.** Replaced static public route views with controller actions, added a `PublicSiteData` mapper for DB-backed centers, services, tariffs, headquarters settings, careers, and legal pages, and updated public views/components to prefer controller/Eloquent data with config fallbacks. Added tests proving centers, booking/tariff options, careers, and legal pages render from database rows.

- **Step 21 — Models, enums, factories.** Added backed enums for user, center, booking, content, contact, career, tariff revision, and setting statuses; Eloquent models for all NACHO domain tables with relationships, casts, scopes, and bilingual fallback helpers; factories for domain models; and focused model tests covering relationships, scopes, casts, localized fields, JSON payloads, and typed settings.

- **Step 20 — Seed data.** Added idempotent seeders for roles, first super admin, services, tariffs, verified centers with contacts/hours/service assignments, career departments, blog categories, editable legal pages, and site settings. Seeded local MySQL successfully with 5 centers, 7 tariffs, 6 roles, and starter content/settings.

- **Step 19 — Database migrations.** Added the NACHO domain schema as additive Laravel migrations: custom roles, staff user fields, normalized centers/contacts/hours/service pivot/progress updates, services, tariffs, tariff revisions and audit logs, bookings, contact messages, blog categories/posts, career departments/posts, pages, media, and site settings. Applied successfully to local MySQL `nacho_vehicle_inspection`; test suite remains green.

- **Step 6 — Static homepage (10 sections).** `/` answers who/what/where/why/next: hero (4 CTAs), trust strip, about preview, centers grid (`config/centers.php`), 5 services, why-choose list, 5-step process, tariff preview, 3 blog placeholders, final CTA. Copy in `lang/{fr,en}/home.php`; tariffs/services structure in `config/home.php`. `<x-public.section-heading>` component.

- **Verified center data in the UI.** `config/centers.php` drives header/footer HQ contact, `/centers` index, `/contact` page (HQ + centers grid + form), home page center preview, booking form center select (operational only), and design-system center cards. `<x-public.centers-grid>` component; center cards show phones and hours.

- **Verified center documentation from CCTs of NACHO.** [docs/CENTERS_DATA.md](docs/CENTERS_DATA.md) (5 inspection centers + Main HQ); [docs/sources/README.md](docs/sources/README.md) tracks `CCTs of NACHO.docx` as source.

### Added

- **[docs/DESIGN.md](docs/DESIGN.md)** — Premium UX spec: nav (top bar, sticky, mobile slide-in), homepage 14 sections, 6-step process, six technical checks, footer CTA band, floating booking, microcopy, anti-patterns.

- **Master Pricing Console documentation.** [DESIGN.md](docs/DESIGN.md) §10, [FRONTEND.md](docs/FRONTEND.md), [CONTENT_GUIDELINES.md](docs/CONTENT_GUIDELINES.md) §3 — 4-block tariffs page (split-screen console, regulatory info, logistics strip, FAQ), safe regulatory copy, mobile UX. [DATABASE.md](docs/DATABASE.md) expanded `tariffs` schema + `tariff_revisions`; ADR 007; [SEEDING.md](docs/SEEDING.md) 7 slugs; [ADMIN_MODULES.md](docs/ADMIN_MODULES.md) revision workflow; [ARCHITECTURE.md](docs/ARCHITECTURE.md) `TariffService`.

- **Dynamic Center Finder documentation.** [DESIGN.md](docs/DESIGN.md) §11, [FRONTEND.md](docs/FRONTEND.md) — 4-block centers page (intro/filters, list+map finder, expansion network, visit CTA), index-only UX, lazy map, HQ progressive disclosure. [DATABASE.md](docs/DATABASE.md) `center_contacts`, `center_hours`, `center_progress_updates`, expanded `center_service` pivot; ADR 008; [CENTERS_DATA.md](docs/CENTERS_DATA.md), [SEEDING.md](docs/SEEDING.md) §6; [ARCHITECTURE.md](docs/ARCHITECTURE.md) `CenterFinderService`.

- **Email-based Careers documentation.** [DESIGN.md](docs/DESIGN.md) §13, [FRONTEND.md](docs/FRONTEND.md) — 4-block careers page, mailto apply, index-only UX. [DATABASE.md](docs/DATABASE.md) `career_departments`, expanded `career_posts`; **removed** `job_applications`; ADR 009; [CONTENT_GUIDELINES.md](docs/CONTENT_GUIDELINES.md) §4; [ARCHITECTURE.md](docs/ARCHITECTURE.md) `CareerVacancyService`. Step 25 cancelled.

### Changed

- **Steps 4–6 aligned with [docs/DESIGN.md](docs/DESIGN.md).** Nav: Book last, Compliance footer-only, utility tagline + hours; mobile slide-in panel; floating Book button; footer columns (services, 5 centers, HQ). Homepage rebuilt to **14 sections** (hero split + status card, availability strip, about 3 cards, 6-step process, six technical checks, tariff category chips). Process timeline **6 steps** (was 5). New components: `hero-split`, `center-availability-strip`, `about-preview-cards`, `technical-checks-grid`, `tariff-category-selector`, `floating-booking-button`.

- **Frontend docs aligned with design vision.** [FRONTEND.md](docs/FRONTEND.md), [BRAND.md](docs/BRAND.md), [CONTENT_GUIDELINES.md](docs/CONTENT_GUIDELINES.md), [plan.md](plan.md), [ROADMAP.md](docs/ROADMAP.md) — homepage 14 sections (was 10); inspection process **6 steps** (was 5); Compliance moved to footer nav; nav order matches design spec.

- **Tariff docs:** removed unverified Ministry/June 2022 homologation as required copy; logistics strip configurable via `site_settings`; Step 11 scope is Master Pricing Console (not table-only).

- **Centers docs:** replaced card-grid + detail route with 4-block Dynamic Center Finder; normalized contacts/hours schema; index-only `/centers`; booking preselect via `?center={slug}`.

- **Careers docs:** replaced online apply form + `job_applications` with email-only recruitment; 4-block index-only `/careers`; Step 25 cancelled.

- **Center names and phones corrected per NACHO source.** Official name is **NACHO Yaounde** (not “Yaounde 1”); slug `nacho-yaounde`; phones stored as `(+237) 675117327` format; labels A–E; Douala/Kumba “Coming soon” + October 2026 notice.

- **Center data docs aligned with CCTs docx.** Replaced incorrect placeholder cities (Douala×2 operational, Bafoussam/Garoua) with Yaounde + Bamenda (operational) and Douala/Kumba (under construction). Updated [docs/SEEDING.md](docs/SEEDING.md), [CONTENT_GUIDELINES.md](docs/CONTENT_GUIDELINES.md), [FRONTEND.md](docs/FRONTEND.md), [DATABASE.md](docs/DATABASE.md), [SEO.md](docs/SEO.md), [ROADMAP.md](docs/ROADMAP.md), [PROJECT_BRIEF.md](docs/PROJECT_BRIEF.md), [UAT_CHECKLIST.md](docs/UAT_CHECKLIST.md), [MAINTENANCE.md](docs/MAINTENANCE.md), [README.md](README.md), [plan.md](plan.md).

### Added

- **Step 5 — Reusable Blade components.** Public components: hero, page-title, trust-strip, alerts, CTA, process-steps, inspection-result, service/center/blog/career cards, tariff table + mobile card, form-field, booking/contact/career forms (UI only, no expiry/reminder fields), pagination (`vendor/pagination/nacho`). Shared styles: `.card-nacho`, badges, `.form-shell`. Bilingual `lang/*/components.php`. Design-system preview at `/design-system`.

- **Navbar & footer polish.** Logo contained inside white nav bar (no bleed into utility bar); footer columns (logo, quick links, contact, legal) on one row from `md` breakpoint.

- **Official NACHO logo integrated.** Asset at `public/images/nacho-logo.png`; `<x-nacho-logo>` component with responsive sizing; used in public header, footer, and Breeze login/register layouts; favicon; bilingual alt text.

- **Step 4 — Public layout shell.** `layouts/public.blade.php` with contact bar, full nav (Book CTA highlighted), mobile menu (Alpine), `FR | EN` language switcher, footer, cookie banner. `SetLocaleFromSession` middleware + `/language/{locale}` route. Bilingual `lang/fr` + `lang/en` navigation/footer strings. Placeholder routes for all public pages.

- **Step 3 — Visual identity.** Laravel Breeze (Blade) installed. Tailwind `nacho-*` color tokens in `tailwind.config.js`; base typography, focus states, and component classes in `resources/css/app.css`. NACHO wordmark component; brand preview on `/`; Breeze auth styled with NACHO palette.

- **Step 2 — Laravel project + git init.** Laravel 13.8 scaffold merged into repo root; existing `docs/`, `README.md`, `plan.md`, and `CHANGELOG.md` preserved. `.env.example` configured for NACHO (MySQL, FR locale, notification env keys). Default Laravel migrations run on `nacho_vehicle_inspection`. Git repository initialized. Smoke test: `php artisan serve` returns HTTP 200.

- **Step 1 — Local environment.** Verified PHP 8.5.6, Composer 2.9.8, Node.js 26, npm 11, MySQL 9.6.0 (Homebrew). Created MySQL database `nacho_vehicle_inspection` (`utf8mb4` / `utf8mb4_unicode_ci`). Updated [docs/ENVIRONMENT.md](docs/ENVIRONMENT.md) with verified toolchain table.

### Changed

- **Clean rebuild baseline.** The project directory was reset to empty (including prior application code and git history) and is being rebuilt from the Complete Technical Implementation Document. Work proceeds in approval-gated phases, starting with documentation.
- **50-step plan aligned with unified proposal.** [plan.md](plan.md) and [docs/ROADMAP.md](docs/ROADMAP.md) replace the old 11-phase roadmap. One step = one approval phase.
- **Docs updated for proposal alignment:** English URL paths; nav order; center landmark/vehicle categories; tariff required documents; 5-step inspection process; blog topic list; WhatsApp contact; SEO keywords; deployment Search Console/analytics.

### Added (documentation phase)

- Docs A - Foundation: `README.md`, `docs/PROJECT_BRIEF.md`, `docs/BRAND.md`, `docs/CONTENT_GUIDELINES.md`, this changelog.
- Docs B - Data & architecture: `docs/DATABASE.md` (ERD + 15 tables + audit log), `docs/ROLES.md` (permission matrix), `docs/ARCHITECTURE.md`, `docs/SEEDING.md`.
- Docs C - Frontend & admin: `docs/FRONTEND.md` (layout, components, page specs), `docs/ADMIN_MODULES.md`, `docs/I18N.md`, `docs/SEO.md`.
- Docs D - Ops & quality: `docs/SECURITY.md`, `docs/TESTING.md`, `docs/UAT_CHECKLIST.md`, `docs/ENVIRONMENT.md`, `docs/DEPLOYMENT.md`, `docs/MAINTENANCE.md`.
- Docs E - Roadmap & decisions: `docs/ROADMAP.md`, [plan.md](plan.md), and `docs/adr/` (001 Laravel full-stack, 002 session i18n + single URLs, 003 custom roles, 004 MySQL, 005 custom media table, 006 tariff audit log).

### Notes

- Documentation phase complete. Application build is gated behind explicit approval — say **"do Step N"** per [plan.md](plan.md).
