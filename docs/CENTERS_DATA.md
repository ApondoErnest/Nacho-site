# Verified Center Data - NOVETESCO Vehicle Inspection

**Authoritative source** for inspection center and headquarters contact data used in seeders, static pages (Steps 8, 14), and admin (Step 28).

**Origin:** center locations extracted from [`CCTs of NOVETESCO.docx`](../CCTs%20of%20NOVETESCO.docx) (repository root).  
**Last verified:** 2026-10-01. The company name is **NOVETESCO**. Headquarters email, P.O. Box, and telephones below were supplied by NOVETESCO on that date. Center locations and non-HQ phones still follow the 2026-06-04 sheet.

**Related:** [CONTENT_GUIDELINES.md](CONTENT_GUIDELINES.md) (3+2 rule, November 2026 wording), [SEEDING.md](SEEDING.md) §6, [DATABASE.md](DATABASE.md) §3.3–3.8, ADR 008.

---

## Network summary

NOVETESCO has **3 operational inspection centers** and **2 centers under construction** to be operational soon (per NOVETESCO source sheet).

| Status | Count | Locations |
|--------|:-----:|-----------|
| Active (`active`) | 3 | Yaounde (1), Bamenda (2) |
| Under construction (`construction`) | 2 | Douala, Kumba |
| **Total inspection centers** | **5** | — |

**Main headquarters** is flagged on **NOVETESCO Mankon-Bamenda** (`is_headquarters = true`). Corporate contact for `site_settings`/footer may mirror the same data but the inspection HQ card uses progressive disclosure on the Centers finder ([FRONTEND.md](FRONTEND.md)).

**Public page:** index-only at `/centers` — no `/centers/{slug}` detail route. Expansion details via in-page disclosure/modal.

---

## Searchable keywords (finder search bar)

Values the search field should match (plus center names and region names):

| Center | Keywords |
|--------|----------|
| NOVETESCO Yaounde | Yaounde, Mendong, Mendong Market, Centre |
| NOVETESCO Nkwen-Bamenda | Bamenda, Nkwen, NTEFINKI, Northwest |
| NOVETESCO Mankon-Bamenda | Bamenda, Atuakum, Mankon, Northwest, Headquarters |
| NOVETESCO Douala | Douala, Littoral |
| NOVETESCO Kumba | Kumba, Southwest |

Store in `centers.search_keywords` and/or derive from `city_*`, `nearby_landmark`, `region_*`.

---

## Main headquarters (corporate / site_settings)

Used for `site_settings`, contact page footer — mirrors Mankon-Bamenda HQ data.

| Field | Value |
|-------|-------|
| Label | Main Headquarter |
| Address | Atuakum Mankon, Bamenda, Cameroon |
| Postal box | P.O. Box 100 Mankon-Bamenda |
| Email | noblevehicletestingcompany@gmail.com |
| Phones | (+237) 33142037 (primary), (+237) 675615478 |

---

## Inspection centers

### A. NOVETESCO Yaounde — active

| Field | Value |
|-------|-------|
| Source label | A |
| Slug | `novetesco-yaounde` |
| Name EN/FR | NOVETESCO Yaounde |
| City EN/FR | Yaounde |
| Region EN/FR | Centre |
| Status | `active` |
| `booking_enabled` | true |
| `is_headquarters` | false |
| Address EN/FR | Mendong Market, Yaounde |
| Nearby landmark | Mendong market |
| Latitude / Longitude | 3.837496 / 11.473015 |
| `google_maps_url` | *Seed from Maps link when confirmed* |
| Vehicle categories | *Pending from NOVETESCO* |

**`center_contacts` seed:**

| type | value | is_primary | label_en |
|------|-------|:----------:|----------|
| email | navetescoyaounde@gmail.com | yes | Primary |
| phone | (+237) 675117327 | yes | Primary |
| phone | (+237) 656901833 | no | Alternative |

**`center_hours` seed (split schedule):**

| days | opens | closes | note_en |
|------|-------|--------|---------|
| monday–friday | 07:30 | 18:00 | Weekdays |
| saturday | 07:30 | 16:00 | Saturdays and public holidays |

**Booking link:** `/book-inspection?center=novetesco-yaounde`

**Services:** assign via `center_service` in admin — do not hard-code in templates. Operational centers typically receive all bookable services from [SEEDING.md](SEEDING.md) §4 with `is_available = true`.

---

### B. NOVETESCO Nkwen-Bamenda — active

| Field | Value |
|-------|-------|
| Source label | B |
| Slug | `novetesco-nkwen-bamenda` |
| Name EN/FR | NOVETESCO Nkwen-Bamenda |
| City EN/FR | Bamenda |
| Region EN/FR | Northwest |
| Status | `active` |
| `booking_enabled` | true |
| Address EN/FR | NTEFINKI Quarter Mile 6, Nkwen |
| Nearby landmark | NTEFINKI Quarter mile 6 Nkwen |
| Latitude / Longitude | 6.000978 / 10.206111 |
| Vehicle categories | *Pending from NOVETESCO* |

**`center_contacts` seed:**

| type | value | is_primary |
|------|-------|:----------:|
| email | noblevehicletestingcompany@gmail.com | yes |
| phone | (+237) 674036182 | yes |
| phone | (+237) 696130530 | no |

Until this center has its own email, use the headquarters address.

**`center_hours` seed:** monday–saturday 08:00–16:00

**Booking link:** `/book-inspection?center=novetesco-nkwen-bamenda`

---

### C. NOVETESCO Mankon-Bamenda / Headquarters — active

| Field | Value |
|-------|-------|
| Source label | C |
| Slug | `novetesco-mankon-bamenda` |
| Name EN | NOVETESCO Mankon-Bamenda / Headquarters |
| Name FR | NOVETESCO Mankon-Bamenda / Siège |
| City EN/FR | Bamenda |
| Region EN/FR | Northwest |
| Status | `active` |
| `booking_enabled` | true |
| `is_headquarters` | **true** |
| Address EN/FR | Atuakum Mankon, Bamenda |
| Postal address | P.O. Box 100 Mankon-Bamenda |
| Nearby landmark | Atuakum Mankon |
| Latitude / Longitude | 5.9418158 / 10.1493449 |
| HQ label EN | NOVETESCO Administrative Headquarters |
| HQ note EN | This location serves as both an operational vehicle inspection center and NOVETESCO's principal administrative headquarters. |
| Vehicle categories | *Pending from NOVETESCO* |

**`center_contacts` seed:**

| type | value | is_primary | disclosure |
|------|-------|:----------:|------------|
| email | noblevehicletestingcompany@gmail.com | yes | expanded card |
| phone | (+237) 33142037 | yes | collapsed + expanded |
| phone | (+237) 675615478 | no | expanded only |

**Progressive disclosure:** collapsed card shows primary phone only (`Call Center: (+237) 33142037 ▾`); expanded reveals both phones, email, postal address, HQ note, services. Card height matches other centers when collapsed.

**`center_hours` seed:** monday–saturday 08:00–16:00

**Booking link:** `/book-inspection?center=novetesco-mankon-bamenda`

**Card actions:** Book at This Center, Call Center, Contact Headquarters, View on Google Maps

---

### D. NOVETESCO Douala — construction

| Field | Value |
|-------|-------|
| Source label | D |
| Slug | `novetesco-douala` |
| Name EN/FR | NOVETESCO Douala |
| City EN/FR | Douala |
| Region EN/FR | Littoral |
| Status | `construction` |
| `booking_enabled` | **false** |
| Address | *To be announced* |
| `target_date_text_en` | Before November 2026 |
| `target_date_text_fr` | Avant novembre 2026 |
| `expansion_phase` | *Nullable until NOVETESCO confirms* — e.g. Equipment Installation |
| `expansion_updated_at` | *Nullable* |
| Public notice EN | NOVETESCO is developing this inspection center to improve access to professional vehicle inspection services in the Littoral Region. |
| Card action | **View Expansion Details** only — no booking, call, directions, or Notify Me |

**Expansion phase values (when verified):** Planning and Approvals, Civil Works in Progress, Civil Works Completed, Equipment Installation, Systems Testing, Pre-Opening Preparation, Opening Soon. Do not show completion percentages unless NOVETESCO maintains reliable reporting.

---

### E. NOVETESCO Kumba — construction

| Field | Value |
|-------|-------|
| Source label | E |
| Slug | `novetesco-kumba` |
| Name EN/FR | NOVETESCO Kumba |
| City EN/FR | Kumba |
| Region EN/FR | Southwest |
| Status | `construction` |
| `booking_enabled` | **false** |
| Address | *To be announced* |
| `target_date_text_en` | Before November 2026 |
| `target_date_text_fr` | Avant novembre 2026 |
| `expansion_phase` | *Nullable until NOVETESCO confirms* |
| Card action | **View Expansion Details** only |

---

## Service filter (finder)

The five service filters map to seeded services ([SEEDING.md](SEEDING.md) §4):

1. Periodic Technical Inspection (`periodic-inspection`)
2. Light Vehicle Inspection — *map to periodic or dedicated slug when assigned*
3. Heavy Vehicle Inspection (`heavy-vehicles`)
4. Counter-Visit / Re-inspection (`counter-visit`)
5. Pre-Purchase Inspection (`pre-purchase`)

A service appears on a center card **only** when assigned in `center_service` with `is_available = true`.

---

## Hours and contacts schema

Normalized tables replace JSON on `centers`. See [DATABASE.md](DATABASE.md) §3.4–3.5 and ADR 008.

Legacy JSON examples (for migration reference only):

**Yaounde split schedule:**

```json
{
  "timezone": "Africa/Douala",
  "schedules": [
    { "days": ["monday","tuesday","wednesday","thursday","friday"], "open": "07:30", "close": "18:00" },
    { "days": ["saturday"], "open": "07:30", "close": "16:00", "note_en": "Includes public holidays" }
  ]
}
```

**Bamenda Mon–Sat:** single block 08:00–16:00 for all weekdays + Saturday.

---

## URL routes

English paths (locked decision):

- **Index only:** `/centers` — Dynamic Center Finder (4 blocks)
- **No public detail route** — `/centers/{slug}` removed; use expandable cards + selected profile on index
- **Booking preselect:** `/book-inspection?center={slug}` — e.g. `novetesco-yaounde`, `novetesco-nkwen-bamenda`, `novetesco-mankon-bamenda`

**Deprecated slug:** `novetesco-yaounde-1` (official name is **NOVETESCO Yaounde** only).

---

## Still pending from NOVETESCO

- Center photos and gallery images
- Per-center **vehicle categories accepted** (beyond generic placeholders)
- Final Douala and Kumba addresses, phones, GPS when construction completes
- Verified certifications for Compliance page (see [CONTENT_GUIDELINES.md](CONTENT_GUIDELINES.md) §4)

---

## Deprecated placeholders (do not use)

The following seed placeholders from early documentation are **incorrect** and must not appear in the site or docs:

- `douala-1`, `douala-2` as operational centers
- `bafoussam`, `garoua` as under-construction centers
- `novetesco-yaounde-1` / **NOVETESCO Yaounde 1** (official name is **NOVETESCO Yaounde**)
