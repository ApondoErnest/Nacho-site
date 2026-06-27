# Testing - NACHO Vehicle Inspection

Testing happens incrementally after each major module. Automated tests use PHPUnit with model factories; manual testing follows [UAT_CHECKLIST.md](UAT_CHECKLIST.md).

## 1. Automated tests

- **Feature tests** for HTTP behavior: public pages load (200), forms validate and persist, auth and role gates enforce access, language switching works.
- **Frontend smoke tests** for public chrome, compiled Vite assets, local image/build references, interactive Blade/Alpine markup hooks, booking/contact form accessibility, and excluded v1 fields. Run with `npm run test:frontend` or `php artisan test --filter=FrontendSmokeTest` after assets are built.
- **Backend stability tests** for schema guardrails, admin route protection/rendering, non-staff access denial, booking query preselects, and email-only careers backend payloads. Run with `php artisan test --filter=BackendStabilityTest`.
- **Unit tests** for focused logic: booking reference generation, ability matrix (`AdminAccess`), bilingual accessors/fallback.
- **Factories** for all core models to arrange test data.
- Run with `php artisan test`; tests use a separate test database (or SQLite in-memory) so local MySQL data is untouched.

Priority coverage:
- booking store: valid submission creates booking + reference; invalid/missing fields rejected; consent required; reminder fields absent; preselected `tariff_id` from console preserved.
- contact store (incl. validation).
- admin auth: guest redirected; inactive user blocked; each role sees only permitted modules.
- tariff admin update writes an audit log (`tariff_audit_logs`).
- `TariffService::resolveActiveTariffs()` returns correct price for current date given scheduled revisions (ADR 007).
- tariff revision: future revision not public before `effective_date`; becomes active on/after effective date.

- `CenterFinderService::resolveForFinder()` filters by region, service, search; groups active vs construction centers.
- `CenterFinderService::resolveBySlug()` for booking `?center=` preselect.
- expansion centers excluded when `booking_enabled` is false.
- center_service pivot: service filter respects `is_available`.

- `CareerVacancyService::resolveForCareersPage()` filters by department, center, employment type, search.
- `CareerVacancyService::buildMailtoUrl()` encodes correct subject with `reference`.
- closed/filled vacancies disable Apply by Email; no `job_applications` table exists.

## 2. Frontend testing (manual)

homepage layout, mobile responsiveness, navigation, language switcher, **Dynamic Center Finder** (4 blocks, 42/58 desktop, filters, expandable cards, HQ disclosure, lazy map, map failure fallback, expansion separation), **Careers page** (4 blocks, 40/60 desktop, filters, Apply by Email mailto, closed disables apply, general application mailto, no form/CV upload), service cards, **Master Pricing Console** (desktop split-screen, mobile sticky bar), booking page (`?center=` preselect), contact page, blog listing, legal pages.

Automated Step 42 frontend pass:

- `npm run test:frontend` runs `npm run build` followed by `php artisan test --filter=FrontendSmokeTest`.
- The smoke suite intentionally fails when a stale `public/hot` file points Laravel at a stopped Vite dev server, because that renders pages without compiled CSS.
- The suite verifies built CSS/JS references, shared public header/footer/cookie/language/booking chrome, local asset existence, centers/tariffs/careers Alpine hooks, and public form accessibility fields.

## 3. Backend testing (manual)

admin login, dashboard access, role permissions, and each management module (centers, services, tariffs, bookings, contact, blog, careers, pages, media, users).

Automated Step 43 backend pass:

- `php artisan test --filter=BackendStabilityTest` verifies the core v1 schema, including `roles`, staff fields on `users`, center/service/tariff/booking/contact/blog/career/page/media/settings tables, and the continued absence of `job_applications`.
- Representative admin routes are checked twice: guests must redirect to login, and Super Admin must render dashboard, CRUD indexes, create screens, and show screens across backend modules without errors.
- Non-staff users are forbidden from admin routes.
- Public backend state checks verify `?center=` and tariff category preselection on booking, and email-only careers vacancy payloads with no file-upload or application-storage path.

## 4. Database testing

table creation, default data insertion, foreign-key relationships, booking/contact/blog/career vacancy storage, update and delete operations, soft deletes.

## 5. Form testing

For each public and admin form, test with: valid data, missing required fields, invalid email, invalid phone, invalid file type, oversized file, and repeated submissions.

## 6. Security testing

Per [SECURITY.md](SECURITY.md) section 6: unauthorized admin access, wrong credentials, inactive login, form abuse, upload restrictions, invalid URLs, hidden admin routes, production debug settings.

## 7. Definition of done (per module)

A module is done when its automated tests pass, its manual checks pass, and no major errors appear in logs during exercise.
