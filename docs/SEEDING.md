# Data Seeding - NACHO Vehicle Inspection

Default data inserted after migrations via idempotent seeders (`updateOrCreate`), so re-running is safe.

## 1. Seed order

1. Roles
2. First Super Admin user
3. Default services
4. Default tariffs
5. Initial center records (3 operational + 2 under construction)
6. Default blog categories
7. Default legal pages
8. Default site settings

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

**Authoritative data:** [CENTERS_DATA.md](CENTERS_DATA.md) (from `CCTs of NACHO.docx`). Seed 5 centers: **3 operational + 2 under construction**.

| Slug | Name | City | Region | Status |
|------|------|------|--------|--------|
| `nacho-yaounde` | NACHO Yaounde | Yaounde | Centre | operational |
| `nacho-nkwen-bamenda` | NACHO Nkwen-Bamenda | Bamenda | Northwest | operational |
| `nacho-mankon-bamenda` | NACHO Nacho-Bamenda | Bamenda | Northwest | operational |
| `nacho-douala` | NACHO Douala | Douala | Littoral | under_construction |
| `nacho-kumba` | NACHO Kumba | Kumba | Southwest | under_construction |

Per center, seed from [CENTERS_DATA.md](CENTERS_DATA.md): `name`, `address`, `nearby_landmark`, phones, email, `latitude`, `longitude`, `opening_hours` (JSON), bilingual descriptions (short placeholder OK).

- **Operational:** link all bookable services via `center_service`; enable booking CTA.
- **Under construction:** no booking CTA; EN/FR opening notice per [CONTENT_GUIDELINES.md](CONTENT_GUIDELINES.md).
- **`vehicle_categories_fr/en`:** generic placeholder until NACHO supplies per-center lists (see CENTERS_DATA.md).

**Do not seed** deprecated placeholders: `douala-1`, `douala-2`, `bafoussam`, `garoua`.

## 7. Default blog categories

At least one category to start, e.g.:

| Slug | FR name | EN name |
|------|---------|---------|
| road-safety | Securite routiere | Road safety |
| inspection | Controle technique | Technical inspection |

## 8. Default legal pages

Seed `pages` rows (status `published`) with placeholder bilingual content to be finalized by NACHO's legal team:

- `privacy-policy` - Privacy Policy / Politique de confidentialite
- `terms-and-conditions` - Terms and Conditions / Conditions generales
- `cookie-policy` - Cookie Policy / Politique cookies
- `legal-notice` - Legal Notice / Mentions legales

Optionally also `about` and `compliance-quality` if managed via the pages table.

## 9. Default site settings

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

## 10. Recommended blog placeholder posts

For local UAT and the static blog phase, seed (or hardcode) **10 placeholder posts** using the titles from [FRONTEND.md](FRONTEND.md) § Blog. Mark content as draft/placeholder until NACHO supplies real articles. Also optionally seed one open career post so listing pages are not empty.

## 11. Notes

- No reminder, expiry, fleet, or portal data is ever seeded.
- Seeders are idempotent; `php artisan migrate:fresh --seed` rebuilds the local database cleanly.
