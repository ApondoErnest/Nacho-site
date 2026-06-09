# Project Brief - NACHO Vehicle Inspection Website

## 1. Purpose

Build a modern, professional, bilingual, mobile-responsive website for **NACHO Vehicle Inspection** that positions NACHO as a reliable, approved, customer-centered, and professionally managed vehicle technical inspection network in Cameroon.

The website helps visitors:

- understand NACHO's services
- locate NACHO inspection centers
- view official vehicle inspection tariffs
- understand the inspection process
- request an inspection booking
- contact NACHO
- read road safety information
- apply for available job opportunities

It also provides an admin dashboard for authorized staff to manage content, centers, services, tariffs, bookings, contact messages, blog posts, careers, media, users, and settings.

## 2. Project identity

- **Website name:** NACHO Vehicle Inspection
- **Business type:** Vehicle technical inspection center network
- **Main audience:** Vehicle owners, drivers, transporters, companies needing inspection information, job applicants, and road safety readers
- **Main purpose:** Inform, guide, build trust, and receive booking requests

### Center status rule (critical)

NACHO has **3 operational centers today**, with **2 additional centers under construction**, expected to open **before November 2026**. The website must **not** present NACHO as having 5 fully operational centers until all 5 are functioning. See [CONTENT_GUIDELINES.md](CONTENT_GUIDELINES.md).

**Geographic footprint (verified):** Operational — NACHO Yaounde (Centre), NACHO Nkwen-Bamenda and NACHO Nacho-Bamenda (Northwest). Under construction — NACHO Douala, NACHO Kumba. Corporate HQ — Atuakum Mankon, P.O. Box 100 Bamenda. Full contacts: [CENTERS_DATA.md](CENTERS_DATA.md).

## 3. Brand positioning

> A modern, approved, customer-centered vehicle technical inspection network committed to road safety, compliance, transparency, and professional service across Cameroon.

The website communicates professionalism, trust, road safety, technical reliability, and customer care.

## 4. Slogans

- **English:** Drive Safe. Stay Compliant. Trust NACHO.
- **French:** Roulez en securite. Restez conforme. Faites confiance a NACHO.

## 5. Target users

- **Individual vehicle owners** - where to inspect, documents to bring, cost, how to book.
- **Taxi and transport vehicle owners** - tariffs, validity periods, center location, process.
- **Heavy vehicle owners** - whether NACHO handles trucks, buses, trailers, utility and special vehicles.
- **Used vehicle buyers** - pre-purchase inspection information.
- **Job applicants** - available jobs and how to apply.
- **NACHO staff** - manage centers, bookings, services, tariffs, blog, contact messages, careers, and content from the admin dashboard.

## 6. Languages

French and English. **French is the default** (most public-facing vehicle inspection communication in Cameroon is in French); English is secondary. Pages must not mix French and English on the same rendered page - each page has a properly translated version. See [I18N.md](I18N.md).

## 7. Confirmed scope

Public website; admin dashboard; center, service, tariff, booking, contact, blog/news, careers, legal page, media, and user/role management; bilingual content; SEO preparation; responsive design; local-first Laravel development; later Docker and VPS deployment.

## 8. Excluded scope

SMS reminder system; WhatsApp reminder system; vehicle expiry-date tracking; customer reminder forms; reminder dashboard; reminder reports; customer portal; fleet services; corporate services; corporate accounts; fleet dashboard; inspection equipment integration; regulatory reporting dashboard; advanced external API integrations.

The existing SMS/WhatsApp reminder system remains completely separate and continues to handle all reminder-related operations independently.

## 9. Technology and approach

Laravel full-stack (Blade + Tailwind), MySQL, Vite, Laravel Breeze auth. Local-first development; Dockerization and VPS deployment deferred until the local version is stable. See [ARCHITECTURE.md](ARCHITECTURE.md), [ENVIRONMENT.md](ENVIRONMENT.md), and [ROADMAP.md](ROADMAP.md).
