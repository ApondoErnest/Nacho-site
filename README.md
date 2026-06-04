# NACHO Vehicle Inspection Website

Professional, bilingual (French default / English), mobile-responsive website and admin platform for **NACHO Vehicle Inspection**, a vehicle technical inspection center network in Cameroon.

Built with **Laravel** (full-stack, Blade + Tailwind CSS) on **MySQL**. Local-first development; Docker and VPS deployment come later.

> NACHO currently operates **3 vehicle technical inspection centers**, with **2 additional centers under construction**, expected to open **before October 2026**. The site must never present NACHO as having 5 fully operational centers until all 5 are functioning.

**Slogan:** *Drive Safe. Stay Compliant. Trust NACHO.* / *Roulez en sécurité. Restez conforme. Faites confiance à NACHO.*

---

## Project status

| Phase | Status |
|-------|--------|
| Step 0 — Documentation | **Done** |
| Step 1 — Local environment | **Done** |
| Step 2 — Laravel project + git | **Done** |
| Step 3 — Visual identity | **Done** |
| Step 4 — Public layout shell | **Done** |
| Step 5 — Reusable Blade components | **Done** |
| Steps 6–44 — Application build | Pending (approval-gated, one step at a time) |
| Steps 45–50 — Deployment | Deferred until local UAT passes |

Public layout and a reusable Blade component library are in place. Preview all components at `/design-system` (smoke-test page; remove or protect before production). See [plan.md](plan.md) for the full 50-step build plan and [docs/ROADMAP.md](docs/ROADMAP.md) for live step status.

To continue building: say **"do Step 6"**.

---

## What this project includes

- Public website: home, about, centers, services, tariffs, inspection process, booking request, blog, compliance, careers, contact, legal pages.
- Booking request system (no SMS/WhatsApp reminders, no expiry tracking).
- Admin dashboard: centers, services, tariffs, bookings, contact messages, blog, careers, pages, media, users/roles, settings.
- Bilingual content (FR/EN) with a session-based language switcher.
- SEO readiness (titles, meta, Open Graph, JSON-LD, sitemap, robots).

## What this project explicitly excludes

SMS reminders, WhatsApp reminders, vehicle expiry-date tracking, customer reminder forms, reminder dashboards/reports, customer portal, fleet services, corporate services/accounts, fleet dashboard, inspection-equipment integration, regulatory reporting dashboards, and advanced external API integrations. The existing SMS/WhatsApp reminder system remains a completely separate product.

---

## Technology stack

| Layer | Choice |
|-------|--------|
| Backend | Laravel |
| Frontend | Blade + Tailwind CSS |
| Database | MySQL (`nacho_vehicle_inspection`) |
| Assets | Vite |
| Auth | Laravel Breeze + custom roles |
| URLs | English paths (`/centers/`, English service slugs) |

Future deployment: Nginx + PHP-FPM + MySQL in Docker, Let's Encrypt SSL, optional Cloudflare, backups, monitoring. See [docs/DEPLOYMENT.md](docs/DEPLOYMENT.md).

---

## Local quick start

> Available after **Step 2** (Laravel scaffold). Prerequisites: PHP 8.2+, Composer, Node.js 18+, MySQL.

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate

# Set in .env:
# DB_CONNECTION=mysql
# DB_DATABASE=nacho_vehicle_inspection

php artisan migrate --seed
npm run dev          # asset bundling
php artisan serve    # http://127.0.0.1:8000
```

Full setup: [docs/ENVIRONMENT.md](docs/ENVIRONMENT.md).

---

## Documentation

| Document | Description |
|----------|-------------|
| [plan.md](plan.md) | **Implementation plan** — 50 steps, how to approve work |
| [docs/ROADMAP.md](docs/ROADMAP.md) | Step-by-step status tracker |
| [docs/PROJECT_BRIEF.md](docs/PROJECT_BRIEF.md) | Identity, scope, audience |
| [docs/BRAND.md](docs/BRAND.md) | Visual identity |
| [docs/CONTENT_GUIDELINES.md](docs/CONTENT_GUIDELINES.md) | Messaging rules |
| [docs/FRONTEND.md](docs/FRONTEND.md) | Layout, components, pages |
| [docs/DATABASE.md](docs/DATABASE.md) | Schema and ERD |
| [docs/ROLES.md](docs/ROLES.md) | Roles and permissions |
| [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md) | Backend structure |
| [docs/ADMIN_MODULES.md](docs/ADMIN_MODULES.md) | Admin dashboard modules |
| [docs/SEEDING.md](docs/SEEDING.md) | Default seed data |
| [docs/I18N.md](docs/I18N.md) | Bilingual implementation |
| [docs/SEO.md](docs/SEO.md) | SEO and URL map |
| [docs/SECURITY.md](docs/SECURITY.md) | Security measures |
| [docs/TESTING.md](docs/TESTING.md) | Test strategy |
| [docs/UAT_CHECKLIST.md](docs/UAT_CHECKLIST.md) | UAT sign-off checklist |
| [docs/ENVIRONMENT.md](docs/ENVIRONMENT.md) | Local environment |
| [docs/DEPLOYMENT.md](docs/DEPLOYMENT.md) | Docker + VPS (deferred) |
| [docs/MAINTENANCE.md](docs/MAINTENANCE.md) | Ongoing maintenance |
| [docs/adr/](docs/adr/) | Architecture decision records |

---

## Build approach

1. **Static frontend first** (Steps 4–18) — review each page before the database exists.
2. **Database** (Steps 19–21) — migrations, seeders, models.
3. **Wire backend** (Steps 22–25) — dynamic content and forms.
4. **Admin** (Steps 26–38) — one CRUD module per step.
5. **Polish & UAT** (Steps 39–44) — i18n, SEO, security, tests.
6. **Deploy** (Steps 45–50) — after local sign-off.

---

## License

Proprietary — NACHO Industries Cameroon. All rights reserved.
