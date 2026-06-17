# UAT / Local Stability Checklist - NACHO Vehicle Inspection

The local version is considered stable (ready for Dockerization) only when all items below pass.

## 1. Public site

- [ ] Home page renders all 14 sections per [DESIGN.md](DESIGN.md) (hero split, center strip, 6-step timeline, technical checks, result cards, footer CTA, floating book button)
- [ ] Center status shows 3 operational + 2 under construction (opening before Nov 2026)
- [ ] About page complete (mission, vision, values, road safety, expansion, CTA)
- [ ] Centers page — Dynamic Center Finder: 4 blocks; region/service filters; desktop 42/58 split; category select → expandable card → Book preselect (`?center=slug`); List/Map toggle; lazy map + failure fallback; HQ progressive disclosure on Nacho-Bamenda
- [ ] Expansion section separate from active finder; no booking/notify on Douala/Kumba; verified phase copy only
- [ ] Services index + all 5 service detail pages render full content
- [ ] Tariffs page — Master Pricing Console: category select → result card → Book preselect; Show All Tariffs matrix toggle; mobile sticky bar + accordion/modal
- [ ] Tariffs page shows safe regulatory notice (no unverified homologation claims); FAQ wording per [CONTENT_GUIDELINES.md](CONTENT_GUIDELINES.md) §3.3
- [ ] Tariffs page logistics strip uses configurable copy (not unconfirmed payment/document facts)
- [ ] Inspection process page shows steps + Accepted/Suspended/Refused
- [ ] Blog index + detail pages work; categories shown
- [ ] Compliance & quality page uses safe wording
- [ ] Careers index + detail + application flow work
- [ ] Contact page (map, center links, form) works
- [ ] Legal pages render from the pages table

## 2. Forms

- [ ] Booking submits, shows confirmation + reference; no reminder/expiry fields
- [ ] Contact form submits and shows confirmation
- [ ] Career application submits with CV upload
- [ ] Validation errors display correctly (required, email, phone, file type/size)
- [ ] Honeypot/rate limiting block obvious abuse

## 3. Admin

- [ ] Admin login works; logout works
- [ ] Inactive user cannot log in
- [ ] Dashboard summary cards show correct counts
- [ ] Each role sees only its permitted modules
- [ ] Center / service CRUD works
- [ ] Admin center: contacts, hours, service pivot flags, expansion phase, activate construction center workflow
- [ ] Admin tariff: create future revision, preview, auto-activation by effective date; revision + audit history visible; no hard delete of historical tariffs
- [ ] Booking status workflow works; admin notes save
- [ ] Contact message statuses work
- [ ] Blog category/post CRUD + publish/draft/archive work
- [ ] Careers post CRUD + application status updates + CV download work
- [ ] Page (legal) editing works
- [ ] Media upload/list/delete + alt text work
- [ ] User & role management (Super Admin) works
- [ ] Site settings save and take effect

## 4. i18n

- [ ] Language switcher toggles FR/EN and persists in session
- [ ] No page mixes both languages
- [ ] Dynamic content shows correct language (FR fallback when EN missing)

## 5. SEO

- [ ] Each page has title + meta description
- [ ] Clean URLs match the URL map
- [ ] sitemap.xml and robots.txt generate correctly
- [ ] Homepage JSON-LD present

## 6. Quality

- [ ] Mobile responsiveness acceptable across key pages
- [ ] File uploads work and are restricted correctly
- [ ] Role-based access verified
- [ ] No major errors during testing (logs clean)
- [ ] Automated test suite passes (`php artisan test`)

## 7. Sign-off

- [ ] Center cards and detail pages match [CENTERS_DATA.md](CENTERS_DATA.md) (not deprecated Douala/Bafoussam/Garoua placeholders)
- [ ] HQ contact on footer/contact matches Main Headquarter in CENTERS_DATA.md
- [ ] Stakeholder review of remaining placeholder content (legal, vehicle categories per center, photos) completed
- [ ] Logo and legal text supplied or flagged as pending
