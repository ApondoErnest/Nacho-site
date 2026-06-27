# Security - NACHO Vehicle Inspection

Security spans public forms, the admin area, file uploads, and production configuration.

## Implemented in Step 41

- `App\Http\Middleware\SecurityHeaders` is applied globally and adds CSP, `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`, `Permissions-Policy`, and `Cross-Origin-Opener-Policy` headers.
- HSTS is emitted only on secure requests: `Strict-Transport-Security: max-age=31536000; includeSubDomains; preload`.
- Auth/admin surfaces send `Cache-Control`, `Pragma`, and `Expires` headers that prevent browser caching.
- Booking and contact submissions use the named `public-form` rate limiter at 6 requests/minute per route/IP.
- Production sessions default to encrypted payloads and secure cookies when `APP_ENV=production`.
- The cookie banner accessible label is localized in `lang/en/footer.php` and `lang/fr/footer.php`.
- `tests/Feature/SecurityHardeningTest.php` covers the headers, HSTS behavior, auth no-cache behavior, localized cookie label, and public booking/contact throttles.

## 1. Public form protection

Applies to booking and contact forms:

- server-side validation via FormRequests ([ARCHITECTURE.md](ARCHITECTURE.md))
- CSRF protection (Laravel default on all POST forms)
- rate limiting (`throttle:public-form`) on submissions: 6 requests/min/IP per public POST route
- spam protection: honeypot field (hidden input that must stay empty); optional reCAPTCHA toggled via `site_settings`
- file upload restriction (type + size) where uploads are accepted
- consent checkbox required on the booking form

## 2. Admin area protection

- authentication required for all `/admin` routes
- role/ability-based authorization ([ROLES.md](ROLES.md))
- inactive users (`status = inactive`) cannot log in
- passwords hashed with bcrypt
- login rate-limiting; logout available
- admin routes excluded from sitemap and disallowed in robots.txt
- no admin link exposed on the public site

## 3. File upload protection

- allow only safe types: images (`jpg`, `jpeg`, `png`, `webp`) and documents (`pdf`, `doc`/`docx` for optional booking attachments)
- enforce max size (e.g. 10 MB) per upload
- store in controlled folders under `storage/app/public` (booking docs, media)
- never allow executable/script uploads
- validate images as real images; validate document mime types for booking uploads
- generated/stored file names avoid path traversal; original name kept only as metadata

## 4. Data protection

- collect only the fields defined in [FRONTEND.md](FRONTEND.md) / [DATABASE.md](DATABASE.md)
- booking forms never collect reminder/expiry data
- clear data-processing consent where required
- **Career applications:** submitted via applicant's email client only (ADR 009) — no server-side CV storage
- uploaded booking documents are not publicly listed; access via admin only

## 5. Production hardening (pre-deployment)

- `APP_DEBUG=false`, `APP_ENV=production`
- strong, unique `APP_KEY` and admin passwords
- HTTPS enforced (Let's Encrypt; optional Cloudflare)
- secure session/cookie settings; HTTPS-only cookies
- protect sensitive files (`.env`, storage) from web access
- configure error logging (not displayed to users)
- database and file backups configured
- dependency updates reviewed
Details in [DEPLOYMENT.md](DEPLOYMENT.md).

Default session hardening now follows production mode automatically:

- `SESSION_ENCRYPT=true` is implicit when `APP_ENV=production` unless explicitly overridden.
- `SESSION_SECURE_COOKIE=true` is implicit when `APP_ENV=production` unless explicitly overridden.

## 6. Security testing checklist

- unauthorized admin access is blocked
- wrong credentials rejected; inactive user blocked
- form abuse (rapid/bulk submissions) is throttled
- disallowed file types/oversized files rejected
- invalid/guessed URLs handled (404, no info leak)
- hidden admin routes not reachable without auth
- production debug disabled
- public responses include the Step 41 security headers
- auth/admin pages prevent browser caching
- cookie banner label is translated for FR/EN accessibility

See [TESTING.md](TESTING.md) for how these are exercised.
