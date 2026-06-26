# NACHO Vehicle Inspection — Implementation Plan

Chronological build plan for the NACHO Vehicle Inspection website. **Step 0 (documentation) is complete.** Application code has not started.

## How to use this plan

- Work proceeds **one step at a time**, in order.
- Say **"do Phase 7"** or **"do Step 7"** to approve and start that step only.
- Each step ends with: smoke test → [CHANGELOG.md](CHANGELOG.md) entry → [docs/ROADMAP.md](docs/ROADMAP.md) status update.
- Steps **45–50** (deployment) are deferred until Step 44 UAT sign-off.

## Current state

| Item | Status |
|------|--------|
| Documentation (`docs/` + ADRs) | Done |
| Laravel application | **Done** (Laravel 13.8, MySQL connected) |
| Local environment (Step 1) | **Done** |
| Step 3 — Visual identity | **Done** |
| Step 5 — Reusable Blade components | **Done** |
| Step 6 — Static homepage (14 sections, DESIGN.md) | **Done** |
| Step 19 — Database migrations | **Done** |
| Step 20 — Seed data | **Done** |
| Step 21 — Models, enums, factories | **Done** |
| Step 22 — Public controllers backed by database data | **Done** |
| Step 23 — Booking form backend | **Done** |
| Step 24 — Contact form backend | **Done** |
| Step 26 — Admin auth + custom roles | **Done** |
| Step 27 — Admin layout + dashboard cards | **Done** |
| Next step | **Step 28** — admin: centers |

## Timeline overview

| Block | Steps | What |
|-------|:-----:|------|
| Setup | 1–3 | Environment, Laravel scaffold, brand tokens |
| Frontend base | 4–6 | Layout, components, homepage |
| Static pages | 7–18 | One public page per step (before database) |
| Database | 19–21 | Migrations, seeders, models |
| Public backend | 22–25 | Wire DB + form backends |
| Admin shell | 26–27 | Auth, roles, dashboard |
| Admin CRUD | 28–38 | One admin module per step |
| Polish & UAT | 39–44 | i18n, SEO, security, tests, sign-off |
| Deploy | 45–50 | Docker, VPS, SSL, launch (**deferred**) |

## All steps at a glance

| Step | Deliverable |
|:----:|-------------|
| 0 | Documentation (**done**) |
| 1 | Local environment (PHP, Composer, Node, MySQL `nacho_vehicle_inspection`) |
| 2 | Laravel project created; docs preserved; git initialized |
| 3 | Tailwind `nacho-*` tokens, Breeze, base CSS |
| 4 | Public layout (nav, footer, `FR \| EN` switcher) |
| 5 | Reusable Blade components |
| 6 | Homepage — 14 sections per [docs/DESIGN.md](docs/DESIGN.md) |
| 7 | About page |
| 8 | Centers page — Dynamic Center Finder (4 blocks) |
| 9 | Services index |
| 10 | Five service detail pages |
| 11 | Tariffs page — Master Pricing Console (4 blocks) |
| 12 | Inspection process page (5-step timeline) |
| 13 | Booking form UI only |
| 14 | Contact page |
| 15 | Blog / road-safety education |
| 16 | Careers page — 4-block email apply |
| 17 | Compliance & quality page |
| 18 | Legal pages (×4) |
| 19 | Migrations (15 tables + tariff audit log) |
| 20 | Seed data |
| 21 | Eloquent models, enums, factories |
| 22 | Public controllers — dynamic content |
| 23 | Booking backend + reference service |
| 24 | Contact form backend |
| 25 | *(cancelled — email-only careers)* |
| 26 | Admin auth + roles |
| 27 | Admin dashboard |
| 28 | Admin: centers |
| 29 | Admin: services |
| 30 | Admin: tariffs |
| 31 | Admin: bookings |
| 32 | Admin: contact messages |
| 33 | Admin: blog |
| 34 | Admin: careers (vacancies) |
| 35 | Admin: pages |
| 36 | Admin: media |
| 37 | Admin: users + roles |
| 38 | Admin: site settings |
| 39 | Multilingual completion |
| 40 | SEO |
| 41 | Security hardening |
| 42 | Frontend tests |
| 43 | Backend tests |
| 44 | UAT sign-off |
| 45–50 | Deploy (**deferred**) |

## Why frontend before database?

The unified proposal specifies designing the frontend structure before backend logic:

1. **Steps 4–18** — static Blade pages; review UI page-by-page.
2. **Steps 19–21** — database layer.
3. **Steps 22–25** — swap placeholders for live data; enable forms.

## Locked technical decisions

| Topic | Decision |
|-------|----------|
| Stack | Laravel, Blade, Tailwind, Vite, Breeze |
| Database | MySQL `nacho_vehicle_inspection` |
| i18n | Session locale, French default, single URLs |
| URL paths | English (`/centers` index-only, English service slugs) |
| Roles | 6 roles — Super Admin through Content Manager |
| Booking statuses | pending → confirmed → arrived → in_inspection → completed; plus cancelled, no_show, rescheduled |
| Excluded | SMS/WhatsApp reminders, expiry tracking, customer portal, fleet/corporate |

ADRs: [docs/adr/](docs/adr/)

## Critical content rules

- **3 operational + 2 under construction** centers (opening before November 2026). Never present 5 as operational.
- Slogans: *Drive Safe. Stay Compliant. Trust NACHO.* / *Roulez en sécurité. Restez conforme. Faites confiance à NACHO.*
- Booking forms: **no** reminder or expiry fields.
- Compliance: safe wording unless certifications are verified.

## Documentation index

| Doc | Purpose |
|-----|---------|
| [docs/PROJECT_BRIEF.md](docs/PROJECT_BRIEF.md) | Identity, scope, audience |
| [docs/BRAND.md](docs/BRAND.md) | Colors, typography, tone |
| [docs/CONTENT_GUIDELINES.md](docs/CONTENT_GUIDELINES.md) | Messaging rules |
| [docs/DESIGN.md](docs/DESIGN.md) | UX direction (homepage, nav, footer) |
| [docs/FRONTEND.md](docs/FRONTEND.md) | Layout, components, pages |
| [docs/DATABASE.md](docs/DATABASE.md) | Schema and ERD |
| [docs/SEEDING.md](docs/SEEDING.md) | Default data |
| [docs/CENTERS_DATA.md](docs/CENTERS_DATA.md) | Verified centers + HQ (from CCTs docx) |
| [docs/ROADMAP.md](docs/ROADMAP.md) | Step status tracker |
| [docs/DEPLOYMENT.md](docs/DEPLOYMENT.md) | Docker/VPS (later) |

## Next action

Say **"do Step 7"** to build the static About page.
