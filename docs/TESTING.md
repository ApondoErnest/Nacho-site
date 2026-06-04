# Testing - NACHO Vehicle Inspection

Testing happens incrementally after each major module. Automated tests use PHPUnit with model factories; manual testing follows [UAT_CHECKLIST.md](UAT_CHECKLIST.md).

## 1. Automated tests

- **Feature tests** for HTTP behavior: public pages load (200), forms validate and persist, auth and role gates enforce access, language switching works.
- **Unit tests** for focused logic: booking reference generation, ability matrix (`AdminAccess`), bilingual accessors/fallback.
- **Factories** for all core models to arrange test data.
- Run with `php artisan test`; tests use a separate test database (or SQLite in-memory) so local MySQL data is untouched.

Priority coverage:
- booking store: valid submission creates booking + reference; invalid/missing fields rejected; consent required; reminder fields absent.
- contact store and career application store (incl. file upload validation).
- admin auth: guest redirected; inactive user blocked; each role sees only permitted modules.
- tariff update writes an audit log.

## 2. Frontend testing (manual)

homepage layout, mobile responsiveness, navigation, language switcher, center cards, service cards, tariff table/cards, booking page, contact page, blog listing, careers page, legal pages.

## 3. Backend testing (manual)

admin login, dashboard access, role permissions, and each management module (centers, services, tariffs, bookings, contact, blog, careers, pages, media, users).

## 4. Database testing

table creation, default data insertion, foreign-key relationships, booking/contact/blog/application storage, update and delete operations, soft deletes.

## 5. Form testing

For each public and admin form, test with: valid data, missing required fields, invalid email, invalid phone, invalid file type, oversized file, and repeated submissions.

## 6. Security testing

Per [SECURITY.md](SECURITY.md) section 6: unauthorized admin access, wrong credentials, inactive login, form abuse, upload restrictions, invalid URLs, hidden admin routes, production debug settings.

## 7. Definition of done (per module)

A module is done when its automated tests pass, its manual checks pass, and no major errors appear in logs during exercise.
