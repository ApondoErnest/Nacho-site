# Design System & UX Direction - NACHO Vehicle Inspection

Premium vehicle inspection and road-safety platform — **not** a simple company brochure.

**Implementation:** [FRONTEND.md](FRONTEND.md) (page specs), [BRAND.md](BRAND.md) (tokens), Blade components in `resources/views/components/public/`.

**Positioning vs reference sites:** More modern than Satellite Ngono, more focused than Saptrans, more polished than Silicon, as structured as Autoservice — with a stronger NACHO identity (burnt orange + charcoal, clear center counts, conversion-focused CTAs).

---

## 1. Overall design concept

| Direction | Detail |
|-----------|--------|
| Feel | Modern inspection center + digital trust + road safety authority |
| Must beat references on | Cleaner nav, stronger hero, clearer CTAs, professional center cards, modern tariff preview, process timeline, trust indicators, real photos, bilingual FR/EN, mobile-first, consistent burnt orange brand |
| Avoid | Outdated layouts, mixed language, weak CTAs, unfinished pages, unclear hierarchy, generic footers, outdated sliders, empty pages, fake booking buttons, unsupported certification claims, poor icons, overloaded footer, long homepage text blocks, hidden contact details, inconsistent colors |

---

## 2. Visual identity (palette)

Burnt orange + deep charcoal + white/cream + green safety accents (distinct from typical blue/green inspection sites).

| Purpose | Style | Use |
|---------|-------|-----|
| Primary | Burnt orange / copper red | Buttons, highlights, active menu, section accents |
| Dark | Deep charcoal / dark brown | Top bar, footer, headings |
| Background | White / warm cream | Main content |
| Success (green) | Operational, Accepted, Compliant | Badges, result cards |
| Warning (amber) | Under construction, Suspended, counter-visit | Badges, result cards |
| Danger (red) | Refused, serious defect | Badges, result cards |

Hex tokens: [BRAND.md](BRAND.md) (`nacho-*` in Tailwind).

---

## 3. Navigation

### 3.1 Top contact bar

Slim **dark charcoal** bar.

| Side | Content |
|------|---------|
| Left | Approved Vehicle Technical Inspection Centers in Cameroon |
| Right | Phone \| Email \| Opening Hours \| `FR` \| `EN` |

Values from [CENTERS_DATA.md](CENTERS_DATA.md) (HQ phone/email; hours summary e.g. Mon–Sat).

### 3.2 Main navbar

**White or warm cream** background; NACHO logo left.

Menu order:

`Home | About | Centers | Services | Tariffs | Inspection Process | Blog | Careers | Contact | Book Inspection`

| Element | Style |
|---------|--------|
| Normal links | Dark charcoal |
| Active link | Burnt orange |
| **Book Inspection** CTA | Burnt orange background, white text (distinct from links) |
| Hover | Dark brown or deeper orange |

**Compliance & Quality** (`/compliance-quality`) — not in main nav; link from footer Quick Links or About.

### 3.3 Navbar behavior

- Sticky on scroll; slight shrink after scroll
- Current page highlighted
- Desktop: Book Inspection always visible in nav
- Mobile: polished **slide-in panel** (not basic dropdown) — see §4
- Mobile: **fixed bottom** “Book Inspection” button

### 3.4 Floating booking (unique feature)

| Viewport | Behavior |
|----------|----------|
| Desktop | Sticky “Book Inspection” on right edge after scroll |
| Mobile | Fixed bottom bar (in addition to or coordinated with nav CTA) |

---

## 4. Mobile menu (slide-in panel)

**Top:** logo + close button

**Links (order):**

1. Home  
2. About NACHO  
3. Our Centers  
4. Services  
5. Tariffs  
6. Inspection Process  
7. Blog  
8. Careers  
9. Contact  

**Bottom:** large Book Inspection button, phone number, `FR | EN`

---

## 5. Homepage structure (14 content sections)

Must answer: who, what, where, why, what next. Center data: [CENTERS_DATA.md](CENTERS_DATA.md).

| # | Section | Summary |
|---|---------|---------|
| 1 | **Hero** | Split layout: left — headline, subtitle (3+2 centers), CTAs (Book primary, Find Center, View Tariffs link), trust icon row; right — hero image + **floating status card** (“3 Centers Operational / 2 Opening Before November 2026”) |
| 2 | **Center availability strip** | Horizontal: `3 Operational \| 2 Under Construction \| Opening Before November 2026` — green / amber |
| 3 | **About preview** | Title: *Vehicle Inspection Built Around Safety, Trust, and Compliance*; 3 cards (Safety First, Professional Inspection, Clear Customer Guidance); CTA: Learn About NACHO |
| 4 | **Services preview** | 5 cards — icon, short copy, Learn more, optional Book now; white card, shadow, orange icon |
| 5 | **Inspection process timeline** | Title: *How Your Inspection Works* — **6 steps** (horizontal desktop / vertical mobile); CTA: View Full Inspection Process |
| 6 | **Six technical checks** | Inspired by Satellite — ripage, braking, suspension, pollution, headlight alignment, visual; icon blocks (charcoal bg + orange icons or white + orange line icons) |
| 7 | **Tariffs preview** | Common categories + price + validity + Book this category; **category selector** (Private Car \| Taxi \| Pickup \| Bus \| Truck \| Other); View All Tariffs |
| 8 | **Centers** | *Find a NACHO Center Near You* — cards with name, city, status badge, hours, phone, address, Get Directions, Book at this Center |
| 9 | **Why choose NACHO** | 6 benefit blocks (concrete, not vague “best”) |
| 10 | **Inspection results** | 3 cards: Accepted (green), Suspended (amber), Refused (red) |
| 11 | **Blog preview** | 3 articles with image, category, title, excerpt, read more |
| 12 | **Final CTA** | Dark charcoal or orange gradient + road/vehicle overlay; Book, View Tariffs, Contact |
| 13 | **Footer CTA band** | “Need help choosing the right inspection service?” — Contact + Book |
| 14 | **Main footer** | See §6 |

**Slogan** in hero/tagline: *Drive Safe. Stay Compliant. Trust NACHO.* / FR equivalent ([PROJECT_BRIEF.md](PROJECT_BRIEF.md)).

### 5.1 Process timeline steps (homepage & full page)

1. Book or Walk In  
2. Register Vehicle and Documents  
3. Machine-Based Inspection  
4. Visual Control  
5. Result Validation  
6. Report, Sticker/PV, and Guidance  

Dedicated [Inspection Process page](FRONTEND.md) uses the same **6-step** timeline plus result explanation detail.

### 5.2 Homepage blog placeholders (3 cards)

- What is vehicle technical inspection?  
- How to prepare your vehicle before inspection  
- What happens when a vehicle fails inspection?  

---

## 6. Footer

**Background:** dark charcoal.

### Footer CTA band (above main footer)

Text: *Need help choosing the right inspection service? Contact NACHO today.*  
Buttons: Contact NACHO, Book Inspection.

### Main footer columns

| Column | Content |
|--------|---------|
| Brand | Logo + short NACHO description |
| Quick Links | Home, About, Centers, Services, Tariffs, Process, Blog, Careers, Contact, Compliance |
| Services | 5 service names (links to detail slugs) |
| Centers | 5 centers from CENTERS_DATA — operational names + “Opening before November 2026” for Douala/Kumba |
| Contact | HQ phone, email, address, hours, optional WhatsApp (general contact only) |

### Footer bottom

`© 2026 NACHO Vehicle Inspection. All rights reserved.`  
Privacy \| Terms \| Cookies \| Legal Notice  
Tagline: *Designed for road safety, compliance, and professional vehicle inspection.*

---

## 7. Status badges

| Badge | Color | Use |
|-------|-------|-----|
| Operational | Green | Open centers |
| Under Construction | Amber | Douala, Kumba |
| Opening Before November 2026 | Amber (+ copy) | Future centers |

---

## 8. Microcopy (preferred CTAs)

Avoid generic “Learn More” where possible:

| Instead of | Use |
|------------|-----|
| Learn More | View Inspection Steps, Check Tariffs, Find Nearest Center, Book This Service, Get Directions |

Full rules: [CONTENT_GUIDELINES.md](CONTENT_GUIDELINES.md).

---

## 9. Imagery

Prefer **real NACHO photos** over stock:

- Center exterior, inspection lane, vehicle on equipment, technician, customer with report

Until photos exist, use tasteful placeholders; replace via [media library](ADMIN_MODULES.md) (Step 36).

---

## 10. Tariffs UX — Master Pricing Console

Premium **digital pricing tool**, not a conventional brochure tariff table.

**Full page (`/tariffs`) — four blocks:**

1. **Master Pricing Console** — split-screen: category cards (left) + compact matrix or detail result card (right); booking connected to selection  
2. **Tariff and Regulatory Information** — two-column inclusions/exclusions + metadata (effective date, last verified, reference link)  
3. **Logistics strip** — configurable payment/docs (generic until confirmed)  
4. **FAQ** — safe wording per [CONTENT_GUIDELINES.md](CONTENT_GUIDELINES.md) §3.3  

**Desktop:** split-screen console. **Mobile:** category picker → result below → sticky bottom bar (category, price, Book, Show All Tariffs via accordion/modal).

**Homepage (§5 #7):** preview subset + category filter + “View All Tariffs” — no duplicate full schedule.

**Data:** [SEEDING.md](SEEDING.md) (7 rows), [DATABASE.md](DATABASE.md) §3.9–3.11, `TariffService` ([ARCHITECTURE.md](ARCHITECTURE.md)).

---

## 11. Centers UX — Dynamic Center Finder

Professional **center locator and visit-planning** interface — not a static address directory.

**Full page (`/centers`) — four blocks:**

1. **Network introduction and search controls** — compact off-white intro (no hero); network indicators; search + region + service filters; List \| Map toggle; optional Find Nearest Center (opt-in geolocation)  
2. **Dynamic Center Finder** — desktop **42% / 58%** split (list \| map + profile); expandable cards; selected-center profile; lazy map; HQ progressive disclosure on Nacho-Bamenda  
3. **Expansion Network** — Douala + Kumba only; muted construction cards; verified phase + target date; View Expansion Details — no booking/notify  
4. **Visit planning CTA** — dark charcoal band with Book + secondary links  

**Visual language:**

- **Active centers:** full-colour photos; solid burnt-orange map markers; selected card — orange border + tint (reuse §9 tokens)
- **Expansion centers:** grey/cream background, desaturated image, outlined marker, construction icon, "Under Construction" label
- **No** large Operational/Open Now badges on active finder cards — Block 2 vs Block 3 separation is sufficient
- **HQ card:** collapsed height matches peers; disclosure reveals extra phones, postal address, HQ note

**Desktop:** split-screen finder with map lazy-loaded on Map View. **Mobile:** list-first; details before map; no content-covering sticky bar.

**Homepage (§5 #8):** compact preview + link to full finder — do not duplicate 4-block console on home.

**Data:** [CENTERS_DATA.md](CENTERS_DATA.md), [DATABASE.md](DATABASE.md) §3.3–3.8, `CenterFinderService` ([ARCHITECTURE.md](ARCHITECTURE.md)), ADR 008.

**Performance:** map JS not in initial page load; responsive compressed images; lazy-load below-fold photos.

---

## 13. Careers UX — Email-Based Recruitment

Professional **vacancy-discovery and employer-brand** page — not an online recruitment platform (ADR 009).

**Full page (`/careers`) — four blocks:**

1. **Compact employer introduction** — off-white + workplace image; trust indicators; View Open Positions / General Application (`mailto:`)  
2. **Why Build Your Career at NACHO** — 4 value cards; optional approved testimonial only  
3. **Career areas and open vacancies** — 4 career-family cards (paths); finder with filters; desktop **40% / 60%** list + detail; Apply by Email (`mailto:`); general application + empty state  
4. **Email application guidance + final CTA** — 3-step how-to; recruitment safety notice; dark charcoal close  

**Visual language:**

- Off-white background, charcoal headings, burnt-orange primary actions, white vacancy cards, soft-grey filters
- Selected vacancy — orange accent border (reuse §9 tokens)
- **Closing Soon** — restrained amber label ([BRAND.md](BRAND.md))
- Closed/filled — muted panel; Apply disabled
- Authentic workplace imagery — diverse technicians, not generic job-board template

**Desktop:** split-panel finder on same page. **Mobile:** cards → expandable details; no sticky bar blocking content.

**Data:** [DATABASE.md](DATABASE.md) §3.16–3.17, [SEEDING.md](SEEDING.md) §7, `CareerVacancyService` ([ARCHITECTURE.md](ARCHITECTURE.md)).

---

## 12. Build step mapping

| Step | Design deliverable |
|------|-------------------|
| 4 | Top bar + main nav + sticky/shrink + mobile panel shell |
| 5 | Cards, timeline, result blocks, tariff preview, floating book button |
| 6 | Full homepage §5 (14 sections + layout chrome §3–§6) |
| 8 | Centers page — 4-block Dynamic Center Finder (index-only) + mobile UX |
| 11 | Tariffs page — 4-block Master Pricing Console + mobile UX |
| 12 | Full 6-step process page |
| 16 | Careers page — 4-block email apply (index-only, no form) |

Step 4–5 may need **enhancement pass** after Step 6 spec was expanded — align existing layout/components to this document.
