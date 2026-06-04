# Changelog

All notable changes to this project are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).

## [Unreleased]

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
