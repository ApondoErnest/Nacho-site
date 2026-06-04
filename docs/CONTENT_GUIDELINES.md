# Content Guidelines - NACHO Vehicle Inspection

These rules govern all public-facing copy, in both French and English.

## 1. Center status messaging (critical, non-negotiable)

NACHO has **3 operational centers** and **2 centers under construction**, opening **before October 2026**.

- Never state or imply NACHO has 5 operational centers.
- Operational centers use status **Operational** / **Operationnel**.
- Future centers use status **Under Construction** / **En construction**, paired with the line:
  - EN: "Opening before October 2026."
  - FR: "Ouverture avant octobre 2026."
- Center counts shown anywhere (home, about, centers) must reflect 3 operational + 2 under construction.
- Booking is offered only for operational centers; under-construction centers show the opening notice instead of a booking CTA.

## 2. Bilingual parity

- French is the default language; English is secondary.
- Every public page has a fully translated counterpart - no mixing FR and EN on the same rendered page.
- Dynamic content (services, centers, blog posts, careers, legal pages, SEO fields) is stored in both languages. See [I18N.md](I18N.md).
- If an English translation is missing for dynamic content, fall back to French rather than showing empty content (documented fallback behavior).

## 3. Tariffs

- Tariffs reflect the official homologated schedule (Ministry of Transport) for the corresponding vehicle categories.
- Always display the tariff notice near the tariff table:
  - FR: "Les tarifs ci-dessous suivent la grille homologuee par le Ministere des Transports et appliquee aux categories de vehicules correspondantes. Les montants peuvent etre mis a jour conformement aux decisions reglementaires en vigueur."
  - EN: A faithful English translation conveying the same meaning.
- Optional footnote (Satellite Ngono reference): prices homologated by the Ministry of Transport from **1 June 2022**.
- Prices are shown in FCFA. The exact default rows are listed in [SEEDING.md](SEEDING.md).
- Tariff table displays **required documents** per category (from `required_documents_fr/en`).

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
- Homepage closing CTA: "Ready to inspect your vehicle? Book your visit today at the nearest NACHO center." / French equivalent.
- Secondary CTAs: Find a Center, View Tariffs, Contact NACHO.

## 9. Privacy & data

- Booking, contact, and career forms collect only the fields defined in [FRONTEND.md](FRONTEND.md) and [DATABASE.md](DATABASE.md).
- Booking forms must never request reminder/expiry-related data (no expiry date, reminder preference, SMS/WhatsApp consent, reminder channel, or reminder due date).
- Forms include a clear data-processing consent statement where required.
