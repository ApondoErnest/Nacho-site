# Content Guidelines - NACHO Vehicle Inspection

These rules govern all public-facing copy, in both French and English.

## 1. Center status messaging (critical, non-negotiable)

NACHO has **3 operational centers** and **2 centers under construction**, opening **before November 2026**.

- Never state or imply NACHO has 5 operational centers.
- Operational centers use status **Operational** / **Operationnel**.
- Future centers use status **Under Construction** / **En construction**, paired with the line:
  - EN: "Opening before November 2026."
  - FR: "Ouverture avant novembre 2026."
- Center counts shown anywhere (home, about, centers) must reflect 3 operational + 2 under construction.
- Booking is offered only for active centers (`booking_enabled`); construction centers show expansion details only — no booking CTA.
- **Centers finder (Block 2):** do **not** use large "Open Now" or "Operational" badges on current-center cards — section layout communicates availability.
- **Geolocation:** request browser location only after the user taps **Find Nearest Center** — never on page load.
- **Expansion phases:** display only verified `expansion_phase` values from admin; no fabricated completion percentages.
- **Expansion cards:** no Notify Me, SMS, or WhatsApp subscription CTAs until center is active.

**Geography (verified network):** Operational centers are **NACHO Yaounde** (Centre), **NACHO Nkwen-Bamenda**, and **NACHO Nacho-Bamenda** (Northwest). Under construction: **NACHO Douala** and **NACHO Kumba** (Littoral / Southwest) — may show “Coming soon” plus the November 2026 line. Do not reference Bafoussam, Garoua, or multiple operational Douala sites. Full contacts: [CENTERS_DATA.md](CENTERS_DATA.md) (labels A–E).

**Headquarters:** Nacho-Bamenda center row (`is_headquarters`) — progressive disclosure on finder card. Corporate contact for footer/contact may mirror same data in `site_settings`.

## 2. Bilingual parity

- French is the default language; English is secondary.
- Every public page has a fully translated counterpart - no mixing FR and EN on the same rendered page.
- Dynamic content (services, centers, blog posts, careers, legal pages, SEO fields) is stored in both languages. See [I18N.md](I18N.md).
- If an English translation is missing for dynamic content, fall back to French rather than showing empty content (documented fallback behavior).

## 3. Tariffs and pricing page copy

The tariffs page uses the **Master Pricing Console** ([FRONTEND.md](FRONTEND.md)). Prices are shown in **FCFA** (integer). Default rows: [SEEDING.md](SEEDING.md).

### 3.1 Regulatory section (safe wording — do not over-claim)

**Section heading:** Tariff and Regulatory Information / *Informations tarifaires et reglementaires*

**Primary notice (EN):** The displayed tariffs follow the applicable vehicle inspection schedule and may be revised following official regulatory decisions. Customers should confirm any recently updated rate with their selected NACHO center.

**Primary notice (FR):** Les tarifs affiches suivent la grille de controle technique applicable et peuvent etre revises conformement aux decisions reglementaires en vigueur. Les clients sont invites a confirmer tout tarif recemment mis a jour aupres du centre NACHO choisi.

**Do not publish as confirmed facts until operationally verified:**

- “Ministry homologated” / “homologue par le Ministere” as an asserted fact
- “Effective from June 2022” as mandatory copy
- “Digital Safety Report”, “Official Certificate Processing”
- Exact counter-visit charges or universal counter-visit conditions

**May display when available (from database / admin):** effective date, last verified date, regulatory reference, view/download notice link.

### 3.2 Logistics strip (configurable — generic until confirmed)

Default placeholders (site settings or admin-editable blocks):

| Topic | EN (default) | Notes |
|-------|--------------|-------|
| Payment methods | Accepted payment methods vary by center. Confirm the available options when booking. | Do not hard-code Cash, Mobile Money, or Carte Grise until NACHO confirms |
| Required documents | Bring the vehicle registration documents and any additional documents required for your vehicle category. | Replace with exact list when confirmed |

Per-row applicability notes may appear on the pricing console result card (`description_en/fr` on tariffs).

### 3.3 Tariffs FAQ (safe answers)

**Rates consistency — do NOT use unless NACHO confirms:**

- ~~All rates are officially regulated and standard across our network.~~

**Use instead (EN):** NACHO applies the published tariff schedule across its operational network. Customers should review the latest displayed rate or confirm with their selected center before visiting.

**Failed inspection / counter-visit:** Do not imply every failed inspection automatically follows the same counter-visit process or fee unless that workflow is confirmed. Use general guidance and direct customers to their center or the counter-visit service page.

### 3.4 Content to avoid on tariffs page

- Duplicate price tables (console + full schedule in consecutive sections without toggle)
- Dense spreadsheet-style default panel (use compact matrix; detail on select)
- Unverified payment or document requirements stated as facts

## 4. Compliance & certification claims

- Only claim certifications, approvals, or agrement details if real proof exists.
- When precise credentials are not confirmed, use safe wording:
  - "Our procedures are inspired by international best practices in quality management, safety, information security, and inspection traceability."
- Do not invent Ministry approval numbers, ISO certifications, or accreditation references.

## 5. Inspection results vocabulary

Use consistent result terms across the site (process page, counter-visit page, blog):

- **Accepted / Accepte** - vehicle compliant; sticker/PV issued.
- **Suspended / Suspendu** - defects to correct; counter-visit required.
- **Refused / Refuse** - serious defects; vehicle not compliant.

## 6. Services naming (canonical)

1. Periodic Vehicle Technical Inspection / Controle technique periodique
2. Counter-Visit / Re-inspection - Contre-visite / Re-inspection
3. Heavy Vehicle Inspection / Inspection des vehicules lourds
4. Pre-Purchase Vehicle Inspection / Inspection avant achat
5. Road Safety Advisory / Conseils en securite routiere

## 7. Tone

Professional, reassuring, plain-language. Prioritize clarity over jargon. Frame inspection as a commitment to road safety, compliance, and customer confidence - not merely a legal obligation.

## 8. Calls to action

- Primary CTA everywhere: "Book an Inspection" / "Reserver une inspection".
- Homepage final CTA headline: "Ready for Your Vehicle Inspection?" / French equivalent (see [DESIGN.md](DESIGN.md)).
- Secondary CTAs: Find a Center, View Tariffs, Contact NACHO.

**Preferred specific microcopy** (avoid generic "Learn More" on key actions):

| EN | FR (faithful) |
|----|----------------|
| View Inspection Steps | Voir les etapes d'inspection |
| Check Tariffs | Consulter les tarifs |
| Find Nearest Center | Trouver le centre le plus proche |
| Book This Service | Reserver ce service |
| Get Directions | Obtenir l'itineraire |
| Learn About NACHO | Decouvrir NACHO |

## 8b. Design & content to avoid

Do not use on the public site:

- Outdated image sliders, excessive animation, empty or demo pages, copied template filler
- Mixed French and English on the same page
- Unclear center counts (always 3 operational + 2 under construction)
- Fake or non-functional booking buttons
- Unsupported certification claims
- Poor-quality icons, overloaded footer, long text blocks on homepage
- Hidden contact details, inconsistent colors

See [DESIGN.md](DESIGN.md) for full UX rules.

## 9. Privacy & data

- Booking, contact, and career forms collect only the fields defined in [FRONTEND.md](FRONTEND.md) and [DATABASE.md](DATABASE.md).
- Booking forms must never request reminder/expiry-related data (no expiry date, reminder preference, SMS/WhatsApp consent, reminder channel, or reminder due date).
- Forms include a clear data-processing consent statement where required.
