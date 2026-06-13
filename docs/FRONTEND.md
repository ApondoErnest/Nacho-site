# Frontend Design - NACHO Vehicle Inspection

Blade + Tailwind CSS, mobile-first, premium inspection-platform UX. **Canonical UX spec:** [DESIGN.md](DESIGN.md). **Brand tokens:** [BRAND.md](BRAND.md).

## 1. Public layout

Single layout (`layouts/public.blade.php`) on every public page:

1. Top contact bar (dark) — see [DESIGN.md](DESIGN.md) §3.1  
2. Main navbar (sticky, shrink on scroll) — §3.2–3.3  
3. Main content  
4. Optional floating Book Inspection (desktop right / mobile bottom) — §3.4  
5. Footer CTA band + main footer + footer bottom — [DESIGN.md](DESIGN.md) §6  
6. Cookie consent banner  

**Compliance & Quality** remains at `/compliance-quality` but is linked from footer/About, not main nav.

## 2. Navigation menu

Main nav order (Book Inspection = CTA button at end):

1. Home  
2. About  
3. Centers  
4. Services  
5. Tariffs  
6. Inspection Process  
7. Blog  
8. Careers  
9. Contact  
10. **Book Inspection** (primary CTA — burnt orange button)  

`FR | EN` in the top contact bar. Mobile: slide-in panel per [DESIGN.md](DESIGN.md) §4.

## 3. Reusable components

Blade components (`resources/views/components/public/`):

**Layout & chrome:** header (top bar + nav), footer (CTA band + columns + bottom), language switcher, mobile-menu (slide-in), floating-booking-button  

**Marketing blocks:** hero-split (with status overlay card), center-availability-strip, trust-strip, about-preview-cards, cta-section (final + footer band)  

**Content cards:** service-card, center-card (status badges), tariff-card, tariff-table, tariff-category-selector, pricing-console, tariff-matrix, tariff-result-card, tariff-regulatory-section, tariff-logistics-strip, tariff-faq, tariff-mobile-action-bar, blog-card, career-card  

**Process & trust:** process-steps (6-step timeline), technical-checks-grid (6 checks), inspection-result (Accepted/Suspended/Refused)  

**Forms:** form-field, booking-form, contact-form, career-application-form  

**Utility:** page-title, breadcrumb, alert, pagination  

**Admin:** admin table/form components (admin side)

Design-system preview: `/design-system` (Step 5).

## 4. Home page (14 sections + layout chrome)

Full spec: [DESIGN.md](DESIGN.md) §5. Summary:

| # | Section |
|---|---------|
| 1 | Hero (split + floating 3+2 status card + trust icons + CTAs) |
| 2 | Center availability strip |
| 3 | About preview (3 cards) |
| 4 | Services preview (5 cards) |
| 5 | Inspection process timeline (**6 steps**) |
| 6 | Six technical checks |
| 7 | Tariffs preview (+ category selector) |
| 8 | Centers (verified data — [CENTERS_DATA.md](CENTERS_DATA.md)) |
| 9 | Why choose NACHO (6 benefits) |
| 10 | Inspection result explanation (3 cards) |
| 11 | Blog preview (3 articles) |
| 12 | Final CTA |
| 13 | Footer CTA band |
| 14 | Main footer + footer bottom |

**Note:** Step 6 build may require enhancing Steps 4–5 layout/components to match this spec if the first homepage pass used the older 10-section structure.

## 5. Public pages

### About
Company intro, mission, vision, values, road-safety commitment, professional inspection approach, center-expansion statement, CTA.

### Centers (index + detail)

**Data source:** [CENTERS_DATA.md](CENTERS_DATA.md).

Index: center cards — name, city, status badge (Operational green / Opening before November 2026 amber), hours, phone, address, landmark, services, vehicle categories, photo, Get Directions, Book at this Center (operational only).

Routes: `/centers`, `/centers/{slug}`.

### Services (index + 5 detail pages)

Index: 5 service cards (icon, description, Learn more / Book now). Detail pages per existing spec (periodic, counter-visit, heavy, pre-purchase, road safety) with full inspection-point copy.

**Tariffs page (`/tariffs`) — Master Pricing Console (4 blocks):**

See [CONTENT_GUIDELINES.md](CONTENT_GUIDELINES.md) §3 for safe copy.

**Block 1 — Master Pricing Console (split-screen)**

| Panel | Desktop behaviour |
|-------|-------------------|
| Left | 7 category cards from [SEEDING.md](SEEDING.md); selected state: orange border, subtle tint, checkmark, keyboard focus |
| Right (default) | Compact matrix — Category \| Vehicle \| Price \| Validity (not a dense spreadsheet) |
| Right (on select) | Detail result card: vehicle icon, category code, classification, price, validity, applicability note (`description_*`); actions: **Book This Category**, **Change Category**, **Show All Tariffs** |

Booking route receives preselected `tariff_id` or `category_slug`.

**Block 2 — Tariff and Regulatory Information**

Two columns: inclusions/exclusions + regulatory metadata. Display when available: effective date, last verified date, regulatory reference, view/download notice link. Safe notice text — not unverified “Ministry homologated” claims.

**Block 3 — Logistics strip (4 items, configurable)**

Payment methods and required documents from `site_settings` (generic defaults until NACHO confirms specifics). See CONTENT_GUIDELINES §3.2.

**Block 4 — FAQ**

Rates consistency and failed-inspection/counter-visit answers per CONTENT_GUIDELINES §3.3.

**Mobile**

- Compact category dropdown or 2-column icon grid
- Selected result immediately below choice
- Sticky bottom bar: selected category, price, **Book**, **Show All Tariffs** (opens accordion/modal — not a wide table)

**Homepage:** compact tariffs preview (§4 #7) linking to full console — do not duplicate full schedule on home ([DESIGN.md](DESIGN.md) §10).

**Pricing console components:** `pricing-console`, `tariff-matrix`, `tariff-result-card`, `tariff-regulatory-section`, `tariff-logistics-strip`, `tariff-faq`, `tariff-mobile-action-bar` (plus existing `tariff-card` for homepage preview).

### Inspection Process

**6-step** visual timeline (same steps as homepage §5.1). Accepted / Suspended / Refused blocks. CTA to book. Horizontal timeline on desktop, vertical on mobile.

### Booking

Form fields unchanged — no reminder/expiry fields. See [SECURITY.md](SECURITY.md).

### Blog (index + detail)

Road-safety education. Cards: image, category, title, excerpt, read more. Ten topic titles in [DESIGN.md](DESIGN.md) / prior FRONTEND list for seeds.

### Compliance & Quality

Linked from footer; safe certification wording — [CONTENT_GUIDELINES.md](CONTENT_GUIDELINES.md).

### Careers (index + detail + apply)

Standard job listing + application form with CV upload.

### Contact

HQ data from [CENTERS_DATA.md](CENTERS_DATA.md); map; optional WhatsApp; contact form.

### Legal pages

Privacy, Terms, Cookies, Legal Notice — from `pages` table when wired.

## 6. Responsiveness & accessibility

- Mobile-first; tariff tables → cards on small screens  
- WCAG AA contrast; focus states; semantic landmarks; bilingual alt text  
- Lazy-loaded images; prefer real NACHO photos — [DESIGN.md](DESIGN.md) §9  

## 7. Language switcher

`FR | EN` in top bar; session persistence — [I18N.md](I18N.md). No mixed FR/EN on one rendered page.
