# ADR 008: Normalized center contacts, hours, and service pivot

## Status

Accepted

## Context

The Centers page (Dynamic Center Finder) requires multiple phone numbers per center, structured opening hours (including Yaounde's split weekday/Saturday schedule), service-aware filtering, and expansion-phase metadata. Early schema stored a single `phone`, `email`, and `opening_hours` JSON blob on `centers`, which does not support:

- HQ progressive disclosure (three clickable phones on Nacho-Bamenda)
- Admin CRUD for contacts without schema changes
- Per-center, per-service availability and booking flags
- Verified expansion phase history

## Decision

1. **Normalize contacts** into `center_contacts` (`type`, `value`, `is_primary`, `is_public`, bilingual labels).
2. **Normalize hours** into `center_hours` (one row per `day_of_week` per center, with optional `special_note_*`).
3. **Expand `centers`** with `is_headquarters`, `booking_enabled`, `google_maps_url`, expansion fields (`expansion_phase`, `target_opening_date`, `target_date_text_*`, `expansion_updated_at`), and bilingual location fields.
4. **Expand `center_service` pivot** with `is_available`, `booking_enabled`, `note_*`, `effective_date` — drives service filter on the public finder.
5. **Add optional `center_progress_updates`** for expansion history; current phase still lives on `centers` for simple display.
6. **Status enum:** `planned`, `construction`, `active`, `inactive` (replacing `operational` / `under_construction`).
7. **Deprecate** `phone`, `email`, `opening_hours` JSON, and `map_url` on `centers` — migrate to child tables / `google_maps_url`.

Public page is **index-only** (`/centers`); no `/centers/{slug}` detail route. All center information is shown via expandable cards and selected-center profile on the index.

## Consequences

- Migrations (Step 19) add three tables and alter `centers` + `center_service`.
- Seeders (Step 20) populate contacts and hours from [CENTERS_DATA.md](../CENTERS_DATA.md).
- Admin center module (Step 28) gains contact/hours editors and expansion workflow.
- `CenterFinderService` loads centers with contacts, hours, and assigned services for filters and map payload.
- Future `center_special_hours` can handle holidays without changing this decision.

## Related

- [DATABASE.md](../DATABASE.md) §3.3–3.8
- [FRONTEND.md](../FRONTEND.md) — Dynamic Center Finder
- ADR 006 / 007 — separate concern (tariffs)
