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

**Content cards:** service-card, center-card (homepage preview), center-network-intro, center-filters, center-finder, center-list-item, center-profile-panel, center-expansion-card, center-expansion-detail, centers-visit-cta, center-map (lazy), tariff-card, tariff-table, tariff-category-selector, pricing-console, tariff-matrix, tariff-result-card, tariff-regulatory-section, tariff-logistics-strip, tariff-faq, tariff-mobile-action-bar, blog-card, career-card, careers-intro, careers-value-cards, career-area-card, careers-filters, careers-finder, vacancy-list-item, vacancy-detail-panel, careers-email-guidance, careers-visit-cta, careers-empty-state  

**Process & trust:** process-steps (6-step timeline), technical-checks-grid (6 checks), inspection-result (Accepted/Suspended/Refused)  

**Forms:** form-field, booking-form, contact-form  

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

### Centers page (`/centers`) — Dynamic Center Finder (4 blocks)

**Data source:** [CENTERS_DATA.md](CENTERS_DATA.md). **Index-only** — no `/centers/{slug}` detail route. Reuse lazy-map patterns from contact page where applicable.

**Block 1 — Network introduction and search controls**

Compact off-white intro (no photographic hero):

- Eyebrow: **OUR INSPECTION CENTERS**
- Headline: Find the NACHO Center Nearest to You
- Supporting text + network indicators: **3** current centers, **2** expansion, **5** locations, **10+ years** experience
- Search bar — placeholder: "Search by city or center name" (matches keywords in CENTERS_DATA)
- Region filter: All Regions, Centre, Northwest, Littoral, Southwest
- Service filter: All Services + 5 services from [SEEDING.md](SEEDING.md) §4 — only centers with `center_service.is_available` match
- **Reset Filters**
- **List View | Map View** toggle — desktop default: split List+Map; mobile default: List View
- Optional **Find Nearest Center** — requests geolocation **only** after user taps; manual search remains if denied

**Do not** show large "Open Now" or "Operational" badges on current-center cards — section separation communicates availability.

**Block 2 — Dynamic Center Finder**

| Layout | Behaviour |
|--------|-----------|
| Desktop | **42%** center list \| **58%** map + selected-center profile |
| Mobile | Filters → toggle → cards → selected details → expandable map |

**List View** works fully without map JavaScript.

**Current-center card (collapsed):** photo, name, short address, primary hours, primary phone, service summary (from pivot), expand control, **Book**, **Directions**.

**On select/expand:** full hours, alternative phones, email, services, map action; HQ card uses progressive disclosure ([CENTERS_DATA.md](CENTERS_DATA.md) §C).

**Selected-center profile (desktop):** photo, name, address, hours, primary phone, email, services, **Book at This Center**, Call, Send Email, View on Google Maps — no raw GPS coordinates.

**Map View:** lazy-load map library only when selected. Markers: solid burnt-orange (active), outlined grey-orange (expansion). Card ↔ marker sync. On load failure: message + revert to List View + preserve selection + keep Google Maps links.

**Booking:** `/book-inspection?center={slug}` — customer can change center in form.

**Block 3 — Expansion Network**

Separate section below finder — Douala and Kumba only:

- Heading: Expanding the NACHO Network
- Muted project cards: region, Under Construction, verified phase, target opening, last updated
- **View Expansion Details** (disclosure/modal) — **no** booking, call, directions, Notify Me, or SMS subscription

**Block 4 — Visit planning CTA**

Dark charcoal band: "Found Your Nearest NACHO Center?" + **Book an Inspection** primary; secondary links to Tariffs, Inspection Process, Contact.

**Mobile order:** intro → search/filters → toggle → current cards → selected details → expansion → CTA. No sticky bottom bar covering content.

**Finder components:** `center-network-intro`, `center-filters`, `center-finder`, `center-list-item`, `center-profile-panel`, `center-expansion-card`, `center-expansion-detail`, `centers-visit-cta`, `center-map`.

**Homepage (§4 #8):** compact centers preview linking to `/centers` — do not duplicate full finder ([DESIGN.md](DESIGN.md) §11).

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

Form fields unchanged — no reminder/expiry fields. Accepts `?center={slug}` query param from Centers finder (preselect center; user may change). See [SECURITY.md](SECURITY.md).

### Blog (index + detail)

Road-safety education. Cards: image, category, title, excerpt, read more. Ten topic titles in [DESIGN.md](DESIGN.md) / prior FRONTEND list for seeds.

### Compliance & Quality

Linked from footer; safe certification wording — [CONTENT_GUIDELINES.md](CONTENT_GUIDELINES.md).

### Careers page (`/careers`) — Email-based recruitment (4 blocks)

**Index-only** — no `/careers/{slug}` route. Optional `?vacancy={slug}` for share/deep link. See [CONTENT_GUIDELINES.md](CONTENT_GUIDELINES.md) §4, ADR 009.

**Block 1 — Compact employer introduction**

Off-white intro + workplace image (right): eyebrow **CAREERS AT NACHO**, headline, supporting text. Actions: **View Open Positions** (scroll to vacancies), **Submit a General Application** (`mailto:` from `site_settings`). Trust indicators (4 items). Imagery: diverse technicians, uniforms, brand colours — not executives only.

**Block 2 — Why Build Your Career at NACHO**

Four value cards: Meaningful Impact, Practical Development, Professional Standards, Growing Opportunities. Optional employee testimonial only if approved. No unapproved benefit promises.

**Block 3 — Career areas and open vacancies**

- **Career-area cards** (4 families from [SEEDING.md](SEEDING.md) §7.1) — paths, not vacancies
- **Finder:** search (title/keyword), department, center, employment-type filters, reset
- Desktop **40%** vacancy list \| **60%** selected job detail panel
- Mobile: vacancy cards → expandable details → Apply by Email / Share / Print
- **Vacancy card:** title, department, center, employment type, deadline, summary, **View Position**
- **Job detail:** metadata, role purpose, responsibilities (4–6), essential/preferred requirements, skills, required documents, deadline, **Apply by Email** (`mailto:` with prefilled subject/body per `reference`), Share, Print — disabled when closed/filled
- **General application** block when no matching vacancy ("Do Not See the Right Position?")
- **Empty state** when no published vacancies
- Status labels: Published, Closing Soon (amber), Closed, Filled; archived hidden

**Block 4 — Email application guidance + final CTA**

Three-step how-to-apply; recruitment safety notice (`site_settings`); dark charcoal closing CTA (View Open Positions, general application, About, Contact HR).

**Routes:** `/careers` only; `?vacancy={slug}` preselects detail panel.

**No** online form, CV upload, applicant account, or application-success page.

### Contact

HQ data from [CENTERS_DATA.md](CENTERS_DATA.md); map; optional WhatsApp; contact form.

### Legal pages

Privacy, Terms, Cookies, Legal Notice — from `pages` table when wired.

## 6. Responsiveness & accessibility

- Mobile-first; tariff tables → cards on small screens  
- WCAG AA contrast; focus states; semantic landmarks; bilingual alt text  
- Lazy-loaded images; prefer real NACHO photos — [DESIGN.md](DESIGN.md) §9  
- **Centers finder:** keyboard navigation; visible focus on List/Map toggle and expandable cards (`aria-expanded`); screen-reader labels on phone/actions; map-independent access to all center info; marker distinction by colour **and** shape; geolocation consent copy before request
- **Careers page:** accessible vacancy filters; meaningful `mailto` link labels; keyboard nav on list/detail panel; printable job descriptions  

## 7. Language switcher

`FR | EN` in top bar; session persistence — [I18N.md](I18N.md). No mixed FR/EN on one rendered page.
