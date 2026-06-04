# Frontend Design - NACHO Vehicle Inspection

Blade + Tailwind CSS, mobile-first, one consistent public layout, reusable components, and brand styling from [BRAND.md](BRAND.md).

## 1. Public layout

A single layout wraps all public pages and includes:

- top contact bar (phone / email, language switcher)
- main navigation bar with logo
- mobile menu (hamburger)
- main content area
- footer: contact info, quick links, social links (if available), legal page links
- cookie consent banner

The layout stays consistent across every public page.

## 2. Navigation menu

Main nav order (Book stays a highlighted button, not a plain link):

1. Home
2. About
3. Centers
4. Services
5. **Book an Inspection** (highlighted button — primary conversion action)
6. Tariffs
7. Inspection Process
8. Blog (Road Safety Education)
9. Compliance & Quality
10. Careers
11. Contact

Language switcher: `FR | EN` in the top contact bar.

## 3. Reusable components

Built as Blade components (`resources/views/components/`):

- header, footer
- hero section (includes slogan tagline), page-title / breadcrumb
- service card, center card
- tariff table (desktop), tariff mobile card
- blog card, career card
- contact form, booking form (and career application form)
- alert message (success/error)
- call-to-action section
- language switcher
- pagination
- process steps / inspection-result blocks
- trust strip
- admin table component, admin form component (admin side)

Components keep markup DRY and visually consistent.

## 4. Home page (10 sections, in order)

The homepage must answer five visitor questions: **who** is NACHO, **what** it does, **where** it operates, **why** to trust it, and **what to do next**.

1. **Hero** - title "Professional Vehicle Technical Inspection for Safer Roads"; slogan tagline (*Drive Safe. Stay Compliant. Trust NACHO.* / FR equivalent); subtitle stating 3 operational + 2 under construction (before Oct 2026); actions: Book an Inspection, Find a Center, View Tariffs, Contact NACHO.
2. **Trust strip** - Approved Centers, Modern Equipment, Trained Inspectors, Clear Tariffs, Road Safety Focus.
3. **About preview** - brief intro; inspection as a road-safety commitment, not just a legal requirement.
4. **Center status preview** - center cards showing 3 operational + 2 under construction (opening before Oct 2026).
5. **Services preview** - the 5 services.
6. **Why choose NACHO** - approved centers, trained technicians, modern equipment, transparent process, clear tariffs, professional service, road-safety commitment.
7. **Inspection journey** - 5 simple steps (book/walk-in -> register -> machine inspection -> visual control & validation -> report/sticker/PV & guidance).
8. **Tariffs preview** - category preview linking to full tariffs page.
9. **Blog preview** - recent road-safety articles.
10. **Final CTA** - "Ready to inspect your vehicle? Book your visit today at the nearest NACHO center."

## 5. Public pages

### About
Company intro, mission, vision, values, road-safety commitment, professional inspection approach, center-expansion statement, CTA.

### Centers (index + detail)
Index lists centers via cards: name, city, region, status, address, **nearby landmark**, phone, email, opening hours, map link, services available, **vehicle categories accepted**, photo, "Book at this center" + "Get directions". Status is Operational or Under Construction (with "Opening before October 2026"). Each center has a detail page: full description, address, landmark, map, opening hours, services, vehicle categories, image gallery, contact details, booking button. Booking CTA only for operational centers.

Routes: `/centers`, `/centers/{slug}` (English paths; slugs use city/center name).

### Services (index + 5 detail pages)
Index shows all services (title, short description, image/icon, link, booking button where applicable). Detail pages:
- **Periodic Technical Inspection** - definition, why required, who needs it, accepted categories, required documents, duration, possible results, failure handling, inspection points (braking, suspension, alignment/ripage, lights/headlamp alignment, pollution/emissions, tires, chassis, mirrors, seat belts, horn, windshield, wipers, visual), CTA.
- **Counter-Visit** - what it is, when required, example defects, what to repair, documents to bring, how to book; results (Accepted/Suspended/Refused).
- **Heavy Vehicle Inspection** - trucks, buses, semi-trailers, transport/utility/special vehicles; why stricter (passenger safety, goods, heavy loads, road risk). Inspection points: braking, chassis, suspension, tires, lights, emissions, body structure, safety equipment, heavy-duty vehicle identification.
- **Pre-Purchase Inspection** - reduce risk before buying; checks visible mechanical/brake/suspension condition, emissions, visible defects, accident signs, safety risks, recommendation.
- **Road Safety Advisory** - prepare a vehicle before inspection; why brakes/tires/lighting/visibility matter; risk of expired inspection; maintenance for road safety.

### Tariffs
Searchable, mobile-friendly table with category, vehicle type, price, validity, **required documents**, notes, booking button. Includes the official tariff notice and optional homologation footnote (1 June 2022) per [CONTENT_GUIDELINES.md](CONTENT_GUIDELINES.md). Mobile uses tariff cards. Booking buttons can preselect the tariff.

### Inspection Process
Visual **5-step** timeline: (1) arrival & registration (includes document check), (2) machine-based inspection, (3) visual inspection, (4) result validation, (5) report/sticker/PV & customer guidance. Explains Accepted / Suspended / Refused with colored result blocks. Icons, simple language.

### Booking
Form fields: full name, phone, email (optional), preferred center, vehicle registration, vehicle category, service type, preferred date, preferred time, document upload (optional), comment (optional), data-processing consent. After submit: confirmation message + booking reference. Never collects expiry/reminder fields. See [SECURITY.md](SECURITY.md).

### Blog (index + detail)
Road-safety education focus. Index cards: title, featured image, category, excerpt, publish date, read more. Detail: title, image, author, publish date, content, related articles, SEO metadata.

Recommended placeholder topics (seed or static phase):

1. What is vehicle technical inspection?
2. Documents required for vehicle inspection in Cameroon
3. How to prepare your vehicle before inspection
4. Why brake testing matters for road safety
5. What happens when a vehicle fails inspection?
6. Difference between Accepted, Suspended, and Refused results
7. How often should taxis, buses, trucks, and private vehicles be inspected?
8. Why you should not wait until your vehicle inspection expires
9. How technical inspection improves road safety in Cameroon
10. Basic vehicle maintenance tips before going for inspection

### Compliance & Quality
Authorization/agrement details only if real proof exists; equipment standards, staff training, inspection procedure, QA process, complaint process, data-protection statement. Uses safe wording when credentials unconfirmed ([CONTENT_GUIDELINES.md](CONTENT_GUIDELINES.md)).

### Careers (index + detail + apply)
Index lists jobs (title, location, department, employment type, deadline, apply button). Detail shows description and requirements. Application form: full name, email, phone, selected position, CV upload, cover letter (optional).

### Contact
General phone, email, main office/center address, map, center contact links, optional **WhatsApp click-to-chat** (general contact only — via `whatsapp_contact` setting, not reminders), contact form (full name, email, phone optional, subject, message). After submit: confirmation; admin sees message in dashboard.

### Legal pages
Privacy Policy, Terms and Conditions, Cookie Policy, Legal Notice - rendered from the `pages` table, editable in admin.

## 6. Responsiveness & accessibility

- Mobile-first; tables collapse to cards on small screens (tariffs, where useful).
- Target WCAG AA contrast; visible focus states; semantic landmarks; alt text on images (bilingual).
- Lazy-loaded, responsive images.

## 7. Language switcher

`FR | EN` in the header/top bar; selecting a language stores it in the session and re-renders the current page in that language. See [I18N.md](I18N.md).
