# UAT / Local Stability Checklist - NACHO Vehicle Inspection

The local version is considered stable (ready for Dockerization) only when all items below pass.

## 1. Public site

- [ ] Home page renders all 10 sections correctly
- [ ] Center status shows 3 operational + 2 under construction (opening before Oct 2026)
- [ ] About page complete (mission, vision, values, road safety, expansion, CTA)
- [ ] Centers index + each center detail page work (map, hours, services, gallery)
- [ ] Services index + all 5 service detail pages render full content
- [ ] Tariffs page shows correct 7 rows, search works, mobile cards work, notice present
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
- [ ] Center / service / tariff CRUD works (tariff change logs audit)
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

- [ ] Stakeholder review of placeholder content (centers, legal, contact details) completed
- [ ] Real center data, logo, and legal text supplied or flagged as pending
