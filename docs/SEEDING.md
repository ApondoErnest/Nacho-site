# Data Seeding - NACHO Vehicle Inspection

Default data inserted after migrations via idempotent seeders (`updateOrCreate`), so re-running is safe.

## 1. Seed order

1. Roles
2. First Super Admin user
3. Default services
4. Default tariffs
5. Initial center records (3 active + 2 construction)
6. Career departments + sample vacancies
7. Default blog categories
8. Default legal pages
9. Default site settings

## 2. Roles

Seed the six roles (see [ROLES.md](ROLES.md)): `super-admin`, `admin`, `center-manager`, `receptionist`, `inspector`, `content-manager`, each with name and description.

## 3. Super Admin user

- Name: NACHO Super Admin
- Email: `admin@nacho.local` (override via `.env`)
- Password: from `SEED_ADMIN_PASSWORD` (default `NachoAdmin2026!`) - change before any real deployment
- Role: `super-admin`
- Status: `active`

## 4. Default services

| Slug | FR title | EN title | Bookable |
|------|----------|----------|:--------:|
| periodic-inspection | Controle technique periodique | Periodic Vehicle Technical Inspection | yes |
| counter-visit | Contre-visite / Re-inspection | Counter-Visit / Re-inspection | yes |
| heavy-vehicles | Inspection des vehicules lourds | Heavy Vehicle Inspection | yes |
| pre-purchase | Inspection avant achat | Pre-Purchase Vehicle Inspection | yes |
| road-safety | Conseils en securite routiere | Road Safety Advisory | no |

Each seeded with short descriptions (FR/EN), an icon, display order, and `is_active = true`. Full body content can be expanded later via admin.

## 5. Default tariffs

Seven rows for the Master Pricing Console. Prices in FCFA (integer `price_fcfa`). Schema: [DATABASE.md](DATABASE.md) §3.6.

| category_code | category_slug | name_en | price_fcfa | validity_value | validity_unit | is_bookable |
|---------------|---------------|---------|-------------:|---------------:|:-------------:|:-----------:|
| A | `category-a-taxi` | Taxi / Driving School | 4,900 | 3 | months | yes |
| B | `category-b-private` | Private Vehicle | 17,900 | 12 | months | yes |
| B1 | `category-b1-pickup` | Pickup / Light utility (≤3.5T) | 15,500 | 6 | months | yes |
| C | `category-c-minibus` | Mini-bus | 15,500 | 3 | months | yes |
| C | `category-c-coaster` | Grand bus / Coaster | 19,080 | 3 | months | yes |
| D | `category-d-heavy-utility` | Trucks / Semi-trailers / Heavy utility | 26,235 | 6 | months | yes |
| D | `category-d-other-engins` | Other engins | 41,750 | 6 | months | yes |

Each row also seeds:

- `name_fr` (French classification from original schedule)
- `description_en`, `description_fr` — brief applicability placeholder (not unverified regulatory claims)
- `vehicle_icon` — icon key for console cards
- `display_order` (1–7)
- `is_active` = true
- `effective_date`, `regulatory_reference`, `last_verified_at` — **nullable** until NACHO confirms; do not seed “June 2022 homologated” as fact

Optional: one initial `tariff_revisions` row per tariff mirroring seed price with `effective_date` = seed date and `status` = `active` (ADR 007).

**Regulatory notice on page:** safe copy from [CONTENT_GUIDELINES.md](CONTENT_GUIDELINES.md) §3.1.

**Logistics defaults** (seed in `site_settings` §9): `tariff_logistics_payment_*`, `tariff_logistics_documents_*`.

## 6. Initial centers

**Authoritative data:** [CENTERS_DATA.md](CENTERS_DATA.md) (from `CCTs of NACHO.docx`). Seed 5 centers: **3 active + 2 construction**. Schema: [DATABASE.md](DATABASE.md) §3.3–3.8, ADR 008.

### 6.1 Center rows

| Slug | name_en | City | Region | status | booking_enabled | is_headquarters |
|------|---------|------|--------|--------|:---------------:|:-----------------:|
| `nacho-yaounde` | NACHO Yaounde | Yaounde | Centre | `active` | true | false |
| `nacho-nkwen-bamenda` | NACHO Nkwen-Bamenda | Bamenda | Northwest | `active` | true | false |
| `nacho-mankon-bamenda` | NACHO Nacho-Bamenda / Headquarters | Bamenda | Northwest | `active` | true | **true** |
| `nacho-douala` | NACHO Douala | Douala | Littoral | `construction` | false | false |
| `nacho-kumba` | NACHO Kumba | Kumba | Southwest | `construction` | false | false |

Per center, seed bilingual `address_*`, `city_*`, `region_*`, `nearby_landmark`, `search_keywords`, `latitude`, `longitude`, `google_maps_url` (when known), `featured_image` (placeholder OK), `display_order` (1–5), `target_date_text_*` for expansion centers, `expansion_phase` and `expansion_updated_at` **nullable** until NACHO confirms.

**HQ center:** seed `postal_address`, HQ description in `description_*` per CENTERS_DATA §C.

### 6.2 Seed order (per center)

1. `centers` row  
2. `center_contacts` rows (phones, email — see CENTERS_DATA contact tables)  
3. `center_hours` rows (day-of-week schedule)  
4. `center_service` pivot rows (operational centers only)

### 6.3 center_service pivot

**Active centers:** link all bookable services from §4 with `is_available = true`, `booking_enabled = true` unless admin overrides later.

**Construction centers:** no `center_service` rows until operational.

Pivot fields: `is_available`, `booking_enabled`, `effective_date` (nullable), optional `note_*`.

### 6.4 center_progress_updates

Optional seed when NACHO confirms expansion phases for Douala/Kumba. Until then, leave `expansion_phase` null on center row and show target date text only.

### 6.5 site_settings overlap

Corporate HQ contact in §9 (`contact_email`, `contact_phone`, `address`) mirrors Nacho-Bamenda HQ data for footer/contact page. Inspection HQ UX lives on the `nacho-mankon-bamenda` center row (`is_headquarters`).

**Do not seed** deprecated placeholders: `douala-1`, `douala-2`, `bafoussam`, `garoua`.

## 7. Career departments and vacancies

Schema: [DATABASE.md](DATABASE.md) §3.16–3.17, ADR 009.

### 7.1 career_departments (4 rows)

| slug | name_en |
|------|---------|
| `technical-inspection` | Technical Inspection |
| `center-operations` | Center Operations |
| `quality-safety-admin` | Quality, Safety and Administration |
| `digital-support` | Digital and Technical Support |

Seed bilingual `description_*`, `icon`, `display_order`, `is_active = true`.

### 7.2 career_posts (optional samples)

Seed 1–2 sample vacancies for UAT (status `draft` or `published`):

- `reference` e.g. `NCH-CAR-2026-001`
- `department_id`, `center_id` (active center only — Douala/Kumba only when recruitment opens there)
- `employment_type`, bilingual content blocks, `closes_at`
- `application_email` — **nullable** until NACHO approves official recruitment address
- `application_subject` template: `Application — {title} — {reference}`
- `allow_email_application = true` when published

**Do not seed** `job_applications` — table removed (ADR 009).

## 8. Default blog categories

At least one category to start, e.g.:

| Slug | FR name | EN name |
|------|---------|---------|
| road-safety | Securite routiere | Road safety |
| inspection | Controle technique | Technical inspection |

## 9. Default legal pages

Seed `pages` rows (status `published`) with placeholder bilingual content to be finalized by NACHO's legal team:

- `privacy-policy` - Privacy Policy / Politique de confidentialite
- `terms-and-conditions` - Terms and Conditions / Conditions generales
- `cookie-policy` - Cookie Policy / Politique cookies
- `legal-notice` - Legal Notice / Mentions legales

Optionally also `about` and `compliance-quality` if managed via the pages table.

## 10. Default site settings

Seed `site_settings` keys:

- `site_name` = "NACHO Vehicle Inspection"
- `default_language` = `fr`
- `contact_email` = `nachovehicletestingstation@yahoo.com` (Main HQ — see [CENTERS_DATA.md](CENTERS_DATA.md))
- `contact_phone` = `(+237) 675615478` (primary HQ line; also `656901833`, `677789391` — see [CENTERS_DATA.md](CENTERS_DATA.md))
- `address` = `Atuakum Mankon, P.O. Box 100 Bamenda, Cameroon`
- `postal_box` = `P.O. Box 100 Bamenda` (optional key if implemented)
- `logo` = path from [BRAND.md](BRAND.md) / `config/branding.php` when asset exists
- `footer_text_fr`, `footer_text_en`
- `facebook_url` (empty), `whatsapp_contact` (empty)
- `primary_color` = brand burnt orange (see [BRAND.md](BRAND.md))
- `maintenance_mode` = `false`
- `tariff_logistics_payment_en`, `tariff_logistics_payment_fr` — generic payment copy ([CONTENT_GUIDELINES.md](CONTENT_GUIDELINES.md) §3.2)
- `tariff_logistics_documents_en`, `tariff_logistics_documents_fr` — generic documents copy
- `careers_general_application_email` — **empty/null** until NACHO approves HR recruitment address
- `careers_recruitment_safety_notice_en`, `careers_recruitment_safety_notice_fr` — **empty** until management approves wording ([CONTENT_GUIDELINES.md](CONTENT_GUIDELINES.md) §4.3)

## 11. Recommended blog placeholder posts

For local UAT and the static blog phase, seed (or hardcode) **10 placeholder posts** using the titles from [FRONTEND.md](FRONTEND.md) § Blog. Mark content as draft/placeholder until NACHO supplies real articles.

## 12. Notes

- No reminder, expiry, fleet, portal, or **job application** data is ever seeded.
- Seeders are idempotent; `php artisan migrate:fresh --seed` rebuilds the local database cleanly.
