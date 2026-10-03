# UAT / Local Stability Checklist - NOVETESCO Vehicle Inspection

The local version is considered stable (ready for Dockerization) only when all items below pass.

## Step 44 automated verification — 2026-09-29

Run locally:

```bash
npm run test:frontend   # build + FrontendSmokeTest
php artisan test        # full suite incl. Step44LocalUatTest
```

Latest results: **172** PHPUnit tests, **1551** assertions — all passing (includes Step 45 gate).

**Manual browse (2026-09-29):** OK on local server — home, about, centers, services, tariffs, inspection process, booking, contact, careers, blog/compliance placeholders, legal pages, language switch, and admin access.

**Step 45 local stabilization gate — 2026-09-29:** §7 verified data/HQ alignment via `Step45LocalStabilizationTest`; remaining content items recorded as **v1 launch deferrals** in [UAT_REPORT.md](UAT_REPORT.md). Ready for **Step 46 (Dockerize)**.

## 1. Public site

- [x] Home page renders current DESIGN-aligned sections (hero showcase, technical checks, about, process, centers, fees, articles, testimonials, book CTAs) — *automated: `Step44LocalUatTest`*
- [x] Center status shows 3 active + 2 under construction in seeded data — *automated: `Step44LocalUatTest`*
- [x] About page — layout and navigation OK — *manual browse 2026-09-29*; final copy sign-off in §7
- [x] Centers page — Dynamic Center Finder markup (filters, map, list/map toggle) — *automated + manual browse*
- [x] Expansion section — under-construction centers behave as expected — *manual browse 2026-09-29*
- [x] Services **index** renders from database — *automated smoke*; **five service detail pages deferred in v1** (see UAT_REPORT)
- [x] Tariffs page — Master Pricing Console hooks — *automated: `FrontendSmokeTest`*
- [x] Tariffs regulatory notice and logistics copy — *safe seeded wording accepted for v1 launch (2026-09-29); replace via admin settings when legal approves final text*
- [x] Inspection process page renders journey sections — *automated: `Step44LocalUatTest`*
- [x] Blog index renders (placeholder or CMS when published) — *automated smoke*; **detail routes deferred in v1**
- [x] Compliance page renders safe placeholder — *automated smoke*; **final copy pending**
- [x] Careers page — email apply UI, no CV upload — *automated: `FrontendSmokeTest`*
- [x] Contact page form markup — *automated smoke*
- [x] Legal pages render from pages table — *automated: `PublicDatabaseContentTest` / smoke*

## 2. Forms

- [x] Booking form fields present; no reminder/expiry fields — *automated: `FrontendSmokeTest`*
- [x] Contact form fields + honeypot present — *automated*
- [x] Booking/contact POST flows — *automated: `PublicBookingTest`, `PublicContactMessageTest`*
- [x] Honeypot/rate limiting — *automated: `SecurityHardeningTest`*

## 3. Admin

- [x] Admin module routes, roles, CRUD — *automated: `Admin/*`, `AdminAccessTest`, `BackendStabilityTest`*

## 4. i18n

- [x] FR default, session switch, parity — *automated: `MultilingualCompletionTest`*

## 5. SEO

- [x] Meta, OG, JSON-LD, sitemap, robots — *automated: `SeoTest`, `Step44LocalUatTest`*

## 6. Quality

- [x] Mobile responsiveness acceptable on key pages — *manual browse 2026-09-29*
- [x] Media upload restrictions — *admin tests*
- [x] Role-based access — *admin tests*
- [x] Automated test suite passes — *168 tests, 2026-09-29*

## 7. Sign-off

- [x] Center data matches [CENTERS_DATA.md](CENTERS_DATA.md) — *automated: `Step45LocalStabilizationTest` (2026-09-29)*
- [x] HQ contact on footer/contact matches headquarters in CENTERS_DATA.md — *automated + contact page smoke*
- [x] Stakeholder review of placeholder content (legal, blog, compliance, photos) — *v1 deferrals documented in UAT_REPORT; safe placeholders accepted for soft launch*
- [x] Logo and legal text supplied or explicitly flagged for post-launch update — *seeded logo `images/novetesco-logo.png`; legal pages from CMS seed — final brand/legal review flagged post-launch*

**Step 45 complete (2026-09-29).** Proceed to Step 46 — see [DEPLOYMENT.md](DEPLOYMENT.md).
