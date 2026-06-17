# Maintenance - NACHO Vehicle Inspection

Ongoing tasks after launch to keep the website healthy, secure, and current.

## 1. Routine technical

- update Laravel and dependencies (review changelogs before applying)
- verify automated backups (and periodically test a restore)
- monitor uptime and respond to alerts
- review error logs
- apply security updates promptly

## 2. Content

- publish road-safety blog posts regularly
- update center information per [CENTERS_DATA.md](CENTERS_DATA.md) via admin (`center_contacts`, `center_hours`, expansion phase); sync doc when NACHO supplies changes
- **Activate expansion center:** status → `active`, add contacts/hours/services, enable `booking_enabled`, publish `google_maps_url` — checklist in [ADMIN_MODULES.md](ADMIN_MODULES.md) §3
- update tariffs via **revision workflow** (ADR 007); set `last_verified_at` when NACHO confirms rates
- when regulatory reference is confirmed, update `regulatory_reference` in admin — do not hard-code in lang files
- keep legal pages current with NACHO's legal team

## 3. Operations

- review and process booking requests
- review and respond to contact messages
- manage career posts and applications

## 4. Periodic review

- check SEO basics (titles, meta, sitemap freshness)
- review user accounts and roles; deactivate unused accounts
- confirm [CENTERS_DATA.md](CENTERS_DATA.md) matches live site (centers, HQ contact, logo); update doc and seeders when data changes

## 5. Cadence (suggested)

- Daily: bookings, contact messages
- Weekly: blog/content, error log scan
- Monthly: dependency/security review, backup restore test
- As needed: tariff updates, center status changes, legal updates
