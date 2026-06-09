# Verified Center Data - NACHO Vehicle Inspection

**Authoritative source** for inspection center and headquarters contact data used in seeders, static pages (Steps 8, 14), and admin (Step 28).

**Origin:** extracted from [`CCTs of NACHO.docx`](../CCTs%20of%20NACHO.docx) (repository root).  
**Last verified:** 2026-06-04 (revised to match source labels A–E; official names and phones as provided by NACHO).

**Related:** [CONTENT_GUIDELINES.md](CONTENT_GUIDELINES.md) (3+2 rule, November 2026 wording), [SEEDING.md](SEEDING.md) §6, [DATABASE.md](DATABASE.md) §3.3.

---

## Network summary

NACHO has **3 operational inspection centers** and **2 centers under construction** to be operational soon (per NACHO source sheet).

| Status | Count | Locations |
|--------|:-----:|-----------|
| Operational | 3 | Yaounde (1), Bamenda (2) |
| Under construction | 2 | Douala, Kumba |
| **Total inspection centers** | **5** | — |

**Main headquarters** (corporate contact) is **not** counted as a sixth inspection center. It shares the Atuakum Mankon address with NACHO Nacho-Bamenda and is used for site-wide contact/footer settings.

---

## Main headquarters (corporate)

Used for `site_settings`, contact page, and footer — not listed in the 5-center status grid.

| Field | Value |
|-------|-------|
| Label | Main Headquarter |
| Address | Atuakum Mankon, Bamenda, Cameroon |
| Postal box | P.O. Box 100 Bamenda |
| Email | nachovehicletestingstation@yahoo.com |
| Phones | (+237) 675615478, (+237) 656901833, (+237) 677789391 |

---

## Inspection centers

### A. NACHO Yaounde — operational

| Field | Value |
|-------|-------|
| Source label | A |
| Slug | `nacho-yaounde` |
| Name | NACHO Yaounde |
| City | Yaounde |
| Region | Centre |
| Status | `operational` |
| Address | Mendong market Yaounde |
| Nearby landmark | Mendong market |
| Email | navetescoyaounde@gmail.com |
| Phones | (+237) 675117327, (+237) 656901833 |
| Latitude | 3.837496 |
| Longitude | 11.473015 |
| Opening hours | Normal days 7:30 AM–6:00 PM; Saturdays and public holidays 7:30 AM–4:00 PM (see JSON below) |
| Booking CTA | Yes |
| Vehicle categories | *Pending from NACHO* — use generic placeholder at seed until confirmed |

**Opening hours (JSON):**

```json
{
  "timezone": "Africa/Douala",
  "schedules": [
    {
      "label_fr": "Jours ouvrables",
      "label_en": "Weekdays",
      "days": ["monday", "tuesday", "wednesday", "thursday", "friday"],
      "open": "07:30",
      "close": "18:00"
    },
    {
      "label_fr": "Samedis et jours feries",
      "label_en": "Saturdays and public holidays",
      "days": ["saturday"],
      "open": "07:30",
      "close": "16:00",
      "note_fr": "Inclut les jours feries",
      "note_en": "Includes public holidays"
    }
  ]
}
```

---

### B. NACHO Nkwen-Bamenda — operational

| Field | Value |
|-------|-------|
| Source label | B |
| Slug | `nacho-nkwen-bamenda` |
| Name | NACHO Nkwen-Bamenda |
| City | Bamenda |
| Region | Northwest |
| Status | `operational` |
| Address | NTEFINKI Quarter mile 6 Nkwen |
| Nearby landmark | NTEFINKI Quarter mile 6 Nkwen |
| Email | nachovehicletestingstation@yahoo.com |
| Phones | (+237) 674036182, (+237) 696130530 |
| Latitude | 6.000978 |
| Longitude | 10.206111 |
| Opening hours | Monday–Saturday: open 8:00 AM, close 4:00 PM |
| Booking CTA | Yes |
| Vehicle categories | *Pending from NACHO* |

**Opening hours (JSON):**

```json
{
  "timezone": "Africa/Douala",
  "schedules": [
    {
      "label_fr": "Lundi - Samedi",
      "label_en": "Monday - Saturday",
      "days": ["monday", "tuesday", "wednesday", "thursday", "friday", "saturday"],
      "open": "08:00",
      "close": "16:00"
    }
  ]
}
```

---

### C. NACHO Nacho-Bamenda — operational

| Field | Value |
|-------|-------|
| Source label | C |
| Slug | `nacho-mankon-bamenda` |
| Name | NACHO Nacho-Bamenda |
| City | Bamenda |
| Region | Northwest |
| Status | `operational` |
| Address | Atuakum Mankon |
| Nearby landmark | Atuakum Mankon |
| Email | nachovehicletestingstation@yahoo.com |
| Phones | (+237) 675615478, (+237) 656901833 |
| Latitude | 5.9418158 |
| Longitude | 10.1493449 |
| Opening hours | Monday–Saturday: open 8:00 AM, close 4:00 PM |
| Booking CTA | Yes |
| Vehicle categories | *Pending from NACHO* |

**Opening hours (JSON):** same structure as NACHO Nkwen-Bamenda (Mon–Sat 08:00–16:00).

---

### D. NACHO Douala — under construction

| Field | Value |
|-------|-------|
| Source label | D |
| Slug | `nacho-douala` |
| Name | NACHO Douala |
| Display name (source) | NACHO Douala (Coming soon) |
| City | Douala |
| Region | Littoral |
| Status | `under_construction` |
| Address | *To be announced* |
| Email | — |
| Phones | — |
| GPS | — |
| Booking CTA | **No** — show opening notice only |
| Public notice EN | Coming soon — opening before November 2026. This NACHO center is under construction and will soon provide professional vehicle inspection services to customers in this area. |
| Public notice FR | Bientot disponible — ouverture avant novembre 2026. Ce centre NACHO est en construction et offrira bientot des services professionnels de controle technique aux clients de cette zone. |

---

### E. NACHO Kumba — under construction

| Field | Value |
|-------|-------|
| Source label | E |
| Slug | `nacho-kumba` |
| Name | NACHO Kumba |
| Display name (source) | NACHO Kumba (coming soon) |
| City | Kumba |
| Region | Southwest |
| Status | `under_construction` |
| Address | *To be announced* |
| Email | — |
| Phones | — |
| GPS | — |
| Booking CTA | **No** — show opening notice only |
| Public notice | Same EN/FR under-construction copy as NACHO Douala |

---

## Opening hours JSON shape

Stored in `centers.opening_hours` (JSON or text). See [DATABASE.md](DATABASE.md) §3.3.

- **Simple schedule:** one block for Mon–Sat with `open` / `close` (Bamenda centers).
- **Split schedule:** multiple blocks when weekday and Saturday/holiday hours differ (Yaounde).

Render human-readable summaries in FR/EN on center cards and detail pages.

---

## URL routes

English paths (locked decision):

- Index: `/centers`
- Detail: `/centers/{slug}` — e.g. `/centers/nacho-yaounde`, `/centers/nacho-nkwen-bamenda`

**Deprecated slug:** `nacho-yaounde-1` (early doc parse added “1”; official name is **NACHO Yaounde** only).

---

## Still pending from NACHO

- Center photos and gallery images
- Per-center **vehicle categories accepted** (beyond generic placeholders)
- Final Douala and Kumba addresses, phones, GPS when construction completes
- Verified certifications for Compliance page (see [CONTENT_GUIDELINES.md](CONTENT_GUIDELINES.md) §4)

---

## Deprecated placeholders (do not use)

The following seed placeholders from early documentation are **incorrect** and must not appear in the site or docs:

- `douala-1`, `douala-2` as operational centers
- `bafoussam`, `garoua` as under-construction centers
- `nacho-yaounde-1` / **NACHO Yaounde 1** (official name is **NACHO Yaounde**)
