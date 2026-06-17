# Database Design - NACHO Vehicle Inspection

Database name: **`nacho_vehicle_inspection`** (MySQL, utf8mb4).

The schema is designed only for the confirmed scope. There are **no** reminder, expiry, fleet, customer-portal, or corporate tables.

## 1. Tables overview

Core spec tables (18):

1. `roles`
2. `users`
3. `centers`
4. `center_contacts`
5. `center_hours`
6. `services`
7. `center_service`
8. `center_progress_updates`
9. `tariffs`
10. `bookings`
11. `contact_messages`
12. `blog_categories`
13. `blog_posts`
14. `career_posts`
15. `job_applications`
16. `pages`
17. `media`
18. `site_settings`

Plus Laravel framework tables (`password_reset_tokens`, `sessions`, `cache`, `jobs`, `failed_jobs`, etc.) and enhancement tables:

19. `tariff_revisions` - scheduled/regulatory price versions with effective dates (see ADR 007 in [adr/](adr/))
20. `tariff_audit_logs` - immutable admin edit history (see ADR 006 in [adr/](adr/) and [SECURITY.md](SECURITY.md))

## 2. Entity relationship diagram

```mermaid
erDiagram
    roles ||--o{ users : "has"
    users ||--o{ blog_posts : "authors"
    users ||--o{ media : "uploads"
    centers ||--o{ bookings : "receives"
    services ||--o{ bookings : "for"
    tariffs ||--o{ bookings : "priced_by"
    centers ||--o{ center_contacts : "has"
    centers ||--o{ center_hours : "has"
    centers ||--o{ center_progress_updates : "has"
    centers ||--o{ center_service : ""
    services ||--o{ center_service : ""
    blog_categories ||--o{ blog_posts : "groups"
    career_posts ||--o{ job_applications : "receives"
    tariffs ||--o{ tariff_revisions : "has"
    tariffs ||--o{ tariff_audit_logs : "tracked_by"
    users ||--o{ tariff_revisions : "creates"

    roles {
        bigint id PK
        string name
        string slug UK
        string description
    }
    users {
        bigint id PK
        bigint role_id FK
        string name
        string email UK
        string phone
        string password
        string status
        datetime last_login_at
        datetime email_verified_at
    }
    centers {
        bigint id PK
        string name_en
        string name_fr
        string slug UK
        string city_en
        string city_fr
        string region_en
        string region_fr
        string status
        boolean is_headquarters
        boolean booking_enabled
        decimal latitude
        decimal longitude
        string google_maps_url
        date target_opening_date
        string expansion_phase
        datetime expansion_updated_at
        int display_order
        boolean is_active
    }
    center_contacts {
        bigint id PK
        bigint center_id FK
        string type
        string label_en
        string label_fr
        string value
        boolean is_primary
        boolean is_public
        int display_order
    }
    center_hours {
        bigint id PK
        bigint center_id FK
        string day_of_week
        time opens_at
        time closes_at
        boolean is_closed
        string special_note_en
        string special_note_fr
    }
    center_progress_updates {
        bigint id PK
        bigint center_id FK
        string phase
        text update_en
        text update_fr
        datetime published_at
        string image_path
        boolean is_published
    }
    services {
        bigint id PK
        string slug UK
        string title_fr
        string title_en
        int display_order
        boolean is_active
    }
    center_service {
        bigint id PK
        bigint center_id FK
        bigint service_id FK
        boolean is_available
        boolean booking_enabled
        date effective_date
    }
    tariffs {
        bigint id PK
        string category_code
        string category_slug UK
        int price_fcfa
        int validity_value
        string validity_unit
        date effective_date
        boolean is_active
        boolean is_bookable
    }
    tariff_revisions {
        bigint id PK
        bigint tariff_id FK
        bigint created_by FK
        json snapshot
        date effective_date
        datetime published_at
    }
    bookings {
        bigint id PK
        string booking_reference UK
        bigint center_id FK
        bigint service_id FK
        bigint tariff_id FK
        string status
        date preferred_date
    }
    contact_messages {
        bigint id PK
        string status
    }
    blog_categories {
        bigint id PK
        string slug UK
    }
    blog_posts {
        bigint id PK
        bigint blog_category_id FK
        bigint author_id FK
        string slug UK
        string status
        datetime published_at
    }
    career_posts {
        bigint id PK
        string slug UK
        string status
        date application_deadline
    }
    job_applications {
        bigint id PK
        bigint career_post_id FK
        string status
    }
    pages {
        bigint id PK
        string slug UK
        string status
    }
    media {
        bigint id PK
        bigint uploaded_by FK
        string file_path
    }
    site_settings {
        bigint id PK
        string key UK
        text value
        string type
    }
```

## 3. Table specifications

Conventions: every table has `id` (bigint PK, auto-increment), `created_at`, and `updated_at` unless noted. Soft-deletable tables also have `deleted_at`. Bilingual fields use `_fr` / `_en` suffixes.

### 3.1 roles
- `name` (string)
- `slug` (string, unique) - e.g. `super-admin`, `admin`, `center-manager`, `receptionist`, `inspector`, `content-manager`
- `description` (string, nullable)

Default roles seeded: Super Admin, Admin, Center Manager, Receptionist, Inspector, Content Manager. See [ROLES.md](ROLES.md).

### 3.2 users
- `role_id` (FK -> roles, nullable on delete restrict/set null)
- `name` (string)
- `email` (string, unique)
- `phone` (string, nullable)
- `password` (string, hashed)
- `status` (enum-like string: `active`, `inactive`; default `active`)
- `last_login_at` (datetime, nullable)
- `email_verified_at` (datetime, nullable)
- `remember_token`

Relationships: a role has many users; a user belongs to one role.

### 3.3 centers

Each row is one inspection center in the Dynamic Center Finder ([FRONTEND.md](FRONTEND.md)). Verified data: [CENTERS_DATA.md](CENTERS_DATA.md). See ADR 008 for normalized contacts/hours.

- `name_en`, `name_fr` (string) - display name (proper nouns may match across locales, e.g. "NACHO Yaounde")
- `slug` (string, unique) - URL-safe identifier; used for booking preselect `?center={slug}`
- `city_en`, `city_fr` (string)
- `region_en`, `region_fr` (string, nullable) - e.g. Centre, Northwest, Littoral, Southwest
- `address_en`, `address_fr` (text, nullable) - public street address
- `postal_address` (string, nullable) - e.g. P.O. Box 100 Bamenda (HQ center)
- `status` (string, default `active`) - `planned`, `construction`, `active`, `inactive`. **Migration note:** map legacy `operational` → `active`, `under_construction` → `construction`
- `is_headquarters` (boolean, default false) - true for NACHO Nacho-Bamenda / administrative HQ
- `booking_enabled` (boolean, default false) - controls "Book at This Center" in finder; false for expansion centers
- `description_fr`, `description_en` (text, nullable) - narrative / HQ supporting note
- `latitude`, `longitude` (decimal 10,7, nullable) - internal map coordinates; **not** shown as raw GPS on public pages
- `google_maps_url` (string, nullable) - public directions link (replaces legacy `map_url`)
- `nearby_landmark` (string, nullable) - searchable keyword, e.g. "Mendong market", "NTEFINKI Quarter mile 6 Nkwen"
- `search_keywords` (text, nullable) - comma-separated extra search terms (Mendong, Atuakum, Nkwen, etc.)
- `vehicle_categories_fr`, `vehicle_categories_en` (text, nullable) - accepted vehicle types
- `featured_image` (string, nullable) - main center photograph path
- `target_opening_date` (date, nullable) - structured expected opening for expansion centers
- `target_date_text_en`, `target_date_text_fr` (string, nullable) - public display text, e.g. "Before November 2026"
- `expansion_phase` (string, nullable) - verified project phase for construction centers (see CENTERS_DATA.md)
- `expansion_updated_at` (datetime, nullable) - when phase was last confirmed
- `display_order` (int, default 0) - card ordering in finder / expansion sections
- `is_featured` (boolean, default false) - optional visual emphasis
- `is_active` (boolean, default true)
- soft deletes

**Deprecated on `centers`:** single `phone`, `email`, `opening_hours` JSON — migrate to `center_contacts` and `center_hours` (ADR 008). Contacts and hours are child rows, not Blade-hard-coded.

**Public grouping:** `status = active` + `booking_enabled` → Block 2 (current centers finder). `status = construction` (or `planned`) → Block 3 (expansion network). Admin activating an expansion center updates status and moves it automatically.

### 3.4 center_contacts

Multiple phones, WhatsApp lines, and emails per center. Replaces inflexible `phone_1` / `phone_2` fields.

- `center_id` (FK -> centers, cascade on delete)
- `type` (string) - `phone`, `whatsapp`, `email`
- `label_en`, `label_fr` (string, nullable) - e.g. "Primary", "Alternative"
- `value` (string) - phone number or email address
- `is_primary` (boolean, default false) - one primary per type recommended
- `is_public` (boolean, default true) - hide internal-only contacts
- `display_order` (int, default 0)

Public `tel:` links strip formatting to digits (e.g. `+237675117327`). HQ progressive disclosure on Nacho-Bamenda reveals non-primary phones when expanded.

### 3.5 center_hours

Structured weekly schedule. Replaces `centers.opening_hours` JSON.

- `center_id` (FK -> centers, cascade on delete)
- `day_of_week` (string) - `monday` … `sunday`
- `opens_at`, `closes_at` (time, nullable) - null when closed
- `is_closed` (boolean, default false)
- `special_note_en`, `special_note_fr` (string, nullable) - e.g. "Includes public holidays" for Yaounde Saturday block

**Future (out of scope v1):** `center_special_hours` for one-off holiday closures and temporary schedule changes.

### 3.6 services
- `slug` (string, unique)
- `title_fr`, `title_en` (string)
- `short_description_fr`, `short_description_en` (text, nullable)
- `full_description_fr`, `full_description_en` (longtext, nullable)
- `icon` (string, nullable)
- `featured_image` (string, nullable)
- `is_active` (boolean, default true)
- `display_order` (int, default 0)
- `seo_title_fr`, `seo_title_en` (string, nullable)
- `meta_description_fr`, `meta_description_en` (text, nullable)
- soft deletes

Default services seeded - see [SEEDING.md](SEEDING.md).

### 3.7 center_service (pivot)

Many-to-many: one center offers many services; one service is available at many centers. Drives **service filter** on the Centers finder — only assigned services appear on a center card.

- `center_id` (FK -> centers, cascade on delete)
- `service_id` (FK -> services, cascade on delete)
- `is_available` (boolean, default true) - show service on center card/profile
- `booking_enabled` (boolean, default true) - allow online booking for this service at this center
- `note_en`, `note_fr` (text, nullable) - optional availability note
- `effective_date` (date, nullable) - when service becomes available at center
- unique(`center_id`, `service_id`)

### 3.8 center_progress_updates

Optional expansion history for Douala/Kumba — preserves verified project timeline instead of overwriting a single phase field.

- `center_id` (FK -> centers, cascade on delete)
- `phase` (string) - same enum values as `centers.expansion_phase`
- `update_en`, `update_fr` (text, nullable) - progress note
- `published_at` (datetime, nullable)
- `image_path` (string, nullable) - optional progress photo
- `is_published` (boolean, default false)

Current public phase displays `centers.expansion_phase` + `expansion_updated_at`; history table powers "View Expansion Details" modal when populated.

### 3.9 tariffs

Each row is one **bookable vehicle category line** in the Master Pricing Console. `category_code` alone is **not** unique (Category D has two rows); `category_slug` is the unique public identifier.

- `category_code` (string) - e.g. `A`, `B`, `B1`, `C`, `D`
- `category_slug` (string, unique) - e.g. `category-a-taxi`, `category-d-heavy-utility`, `category-d-other-engins`
- `name_en`, `name_fr` (string) - vehicle classification label
- `description_en`, `description_fr` (text, nullable) - brief applicability guidance on result card
- `price_fcfa` (unsigned int) - FCFA, no decimals
- `validity_value` (unsigned int) - e.g. 3, 6, 12
- `validity_unit` (string) - e.g. `months` (display labels computed or stored in accessors)
- `minimum_weight_kg`, `maximum_weight_kg` (unsigned int, nullable) - optional classification support
- `vehicle_icon` (string, nullable) - icon key for console cards
- `effective_date` (date, nullable) - start of current published schedule for this row
- `expiry_date` (date, nullable) - optional end date
- `regulatory_reference` (string, nullable) - notice/decision reference (unverified until admin confirms)
- `last_verified_at` (datetime, nullable) - last NACHO verification timestamp for display
- `is_active` (boolean, default true)
- `is_bookable` (boolean, default true)
- `display_order` (int, default 0)
- timestamps; soft deletes discouraged for historical rows — deactivate instead

**Public resolution:** `TariffService` returns active rows where `is_active = true` and the current revision (if any) has `effective_date <= today` and (`expiry_date` is null or `expiry_date >= today`). See [ARCHITECTURE.md](ARCHITECTURE.md) and ADR 007.

**Logistics copy** (payment methods, generic documents) lives in `site_settings` or editable page blocks — not hard-coded per row unless confirmed.

Default tariffs seeded — see [SEEDING.md](SEEDING.md).

### 3.10 tariff_revisions

Scheduled or regulatory price updates without overwriting history. See ADR 007.

- `tariff_id` (FK -> tariffs, cascade)
- `created_by` (FK -> users, nullable on delete set null)
- `snapshot` (json) - price_fcfa and other fields at revision time
- `effective_date` (date) - when this revision becomes public
- `published_at` (datetime, nullable) - when admin published/previewed
- `status` (string, default `scheduled`) - `scheduled`, `active`, `superseded`, `cancelled`
- `created_at`, `updated_at`

The application activates the revision whose `effective_date` is current — admins do not manually swap prices at midnight.

### 3.11 tariff_audit_logs (enhancement)
- `tariff_id` (FK -> tariffs, cascade)
- `user_id` (FK -> users, nullable) - who changed it
- `changes` (json) - before/after of changed fields
- `created_at`

Written automatically by the admin tariff update flow. No `updated_at` (append-only). Complements `tariff_revisions` (scheduled publishing) — see ADR 006 vs ADR 007.

### 3.12 bookings
- `booking_reference` (string, unique) - format `NACHO-YYYYMMDD-XXXX`
- `full_name` (string)
- `phone` (string)
- `email` (string, nullable)
- `center_id` (FK -> centers)
- `service_id` (FK -> services)
- `tariff_id` (FK -> tariffs, nullable)
- `vehicle_registration` (string)
- `preferred_date` (date)
- `preferred_time` (string)
- `document_path` (string, nullable) - optional upload
- `comment` (text, nullable)
- `consent` (boolean) - data processing consent
- `status` (string, default `pending`)
- `admin_notes` (text, nullable)

Status values: `pending`, `confirmed`, `arrived`, `in_inspection`, `completed`, `cancelled`, `no_show`, `rescheduled`.

Must NOT include: expiry date, reminder date, reminder status, SMS status, WhatsApp status, reminder consent.

### 3.13 contact_messages
- `full_name` (string)
- `email` (string)
- `phone` (string, nullable)
- `subject` (string, nullable)
- `message` (text)
- `status` (string, default `new`)
- `admin_notes` (text, nullable)

Status values: `new`, `read`, `replied`, `archived`.

### 3.14 blog_categories
- `name_fr`, `name_en` (string)
- `slug` (string, unique)
- `description_fr`, `description_en` (text, nullable)

### 3.15 blog_posts
- `blog_category_id` (FK -> blog_categories, nullable on delete set null)
- `author_id` (FK -> users, nullable on delete set null)
- `title_fr`, `title_en` (string)
- `slug` (string, unique)
- `excerpt_fr`, `excerpt_en` (text, nullable)
- `content_fr`, `content_en` (longtext, nullable)
- `featured_image` (string, nullable)
- `status` (string, default `draft`)
- `published_at` (datetime, nullable)
- `seo_title_fr`, `seo_title_en` (string, nullable)
- `meta_description_fr`, `meta_description_en` (text, nullable)
- soft deletes

Status values: `draft`, `published`, `archived`.

### 3.16 career_posts
- `title_fr`, `title_en` (string)
- `slug` (string, unique)
- `location` (string, nullable)
- `department` (string, nullable)
- `employment_type` (string, nullable)
- `description_fr`, `description_en` (longtext, nullable)
- `requirements_fr`, `requirements_en` (longtext, nullable)
- `application_deadline` (date, nullable)
- `status` (string, default `draft`)
- soft deletes

Status values: `draft`, `open`, `closed`.

### 3.17 job_applications
- `career_post_id` (FK -> career_posts, nullable on delete set null)
- `full_name` (string)
- `email` (string)
- `phone` (string)
- `cv_path` (string, nullable)
- `cover_letter` (text, nullable)
- `status` (string, default `new`)
- `admin_notes` (text, nullable)

Status values: `new`, `reviewed`, `shortlisted`, `rejected`, `accepted`.

### 3.18 pages
- `title_fr`, `title_en` (string)
- `slug` (string, unique)
- `content_fr`, `content_en` (longtext, nullable)
- `status` (string, default `published`)
- `seo_title_fr`, `seo_title_en` (string, nullable)
- `meta_description_fr`, `meta_description_en` (text, nullable)
- soft deletes

Used for: Privacy Policy, Terms and Conditions, Cookie Policy, Legal Notice (and optionally About / Compliance). Status values: `draft`, `published`, `archived`.

### 3.19 media
- `uploaded_by` (FK -> users, nullable on delete set null)
- `file_name` (string)
- `file_path` (string)
- `file_type` (string, nullable)
- `mime_type` (string, nullable)
- `file_size` (unsigned big int, nullable)
- `alt_text_fr`, `alt_text_en` (string, nullable)

Stores center/service/blog/page images, career CVs, optional booking documents.

### 3.20 site_settings
- `key` (string, unique)
- `value` (text, nullable)
- `type` (string, default `text`) - e.g. `text`, `boolean`, `image`, `color`

Example keys: `site_name`, `default_language`, `contact_email`, `contact_phone`, `address`, `logo`, `footer_text_fr`, `footer_text_en`, `facebook_url`, `whatsapp_contact`, `primary_color`, `maintenance_mode`, `tariff_logistics_payment_fr`, `tariff_logistics_payment_en`, `tariff_logistics_documents_fr`, `tariff_logistics_documents_en`. Defaults seeded - see [SEEDING.md](SEEDING.md).

## 4. Relationships summary

- one role has many users; one user belongs to one role
- one center has many bookings, many contacts, many hours rows, and many progress updates
- one service has many bookings; one tariff has many bookings
- centers and services are many-to-many via `center_service` (with availability flags)
- one blog category has many blog posts; one blog post belongs to one category
- one user authors many blog posts
- one career post has many job applications; one application belongs to one career post
- one user uploads many media files
- one tariff has many tariff revisions and many tariff audit logs

## 5. Conventions & integrity

- All bilingual content stored in `_fr` / `_en` columns; rendering chooses by active locale with FR fallback.
- Foreign keys use sensible `onDelete` behavior: pivots cascade; content authorship/category use `set null`; booking FKs restrict deletion of referenced centers/services/tariffs (or use soft deletes to avoid orphaning).
- Slugs are unique and URL-safe; generated from the English (or French) title when not supplied.
- Money stored as integer FCFA (no decimals).
- Timestamps via Laravel defaults; soft deletes on content tables noted above.
