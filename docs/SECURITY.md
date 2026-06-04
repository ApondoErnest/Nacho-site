# Security - NACHO Vehicle Inspection

Security spans public forms, the admin area, file uploads, and production configuration.

## 1. Public form protection

Applies to booking, contact, and career application forms:

- server-side validation via FormRequests ([ARCHITECTURE.md](ARCHITECTURE.md))
- CSRF protection (Laravel default on all POST forms)
- rate limiting (`throttle`) on submissions, e.g. 5-10 requests/min/IP
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

- allow only safe types: images (`jpg`, `jpeg`, `png`, `webp`) and documents (`pdf`, and `doc`/`docx` for CVs)
- enforce max size (e.g. 10 MB) per upload
- store in controlled folders under `storage/app/public` (booking docs, CVs, media)
- never allow executable/script uploads
- validate images as real images; validate CV mime types
- generated/stored file names avoid path traversal; original name kept only as metadata

## 4. Data protection

- collect only the fields defined in [FRONTEND.md](FRONTEND.md) / [DATABASE.md](DATABASE.md)
- booking forms never collect reminder/expiry data
- clear data-processing consent where required
- uploaded CVs and booking documents are not publicly listed; access via admin only

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

## 6. Security testing checklist

- unauthorized admin access is blocked
- wrong credentials rejected; inactive user blocked
- form abuse (rapid/bulk submissions) is throttled
- disallowed file types/oversized files rejected
- invalid/guessed URLs handled (404, no info leak)
- hidden admin routes not reachable without auth
- production debug disabled

See [TESTING.md](TESTING.md) for how these are exercised.
