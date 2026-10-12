# Step 49 — Final production testing (runbook)

Site: **https://noblevehicletestingcompany.com** · Admin: `/login` · Time: about **2 hours** (one desktop + one phone).

Step 49 is **done** when every box below is ticked (or explicitly deferred by the owner) and findings F1–F5 are fixed or accepted. Then continue with Step 50 (launch + Search Console).

Record problems in §10 as you go: page, language, device, what you saw, screenshot.

---

## 0. Before you start

On the VPS:

```bash
cd /var/www/nacho-site
git log -1 --oneline            # same commit as GitHub main
./deploy/vps/monitor.sh         # OK (...)
./deploy/vps/backup.sh          # fresh backup before test submissions
```

- [ ] Monitor OK, fresh backup written
- [ ] Test devices ready: desktop browser (Chrome) and a phone on **mobile data** (not office Wi-Fi)
- [ ] Use the name **`TEST Step49`** in every form you submit, so test records are easy to find and close

---

## 1. Automated pre-check (run 2026-10-12)

| Check | Result |
|-------|--------|
| 15 public pages (`/`, about, centers, services, book-inspection, tariffs, inspection-process, blog, compliance-quality, careers, contact, 4 legal pages) | ✅ all **200**, 0.8–1.4 s from outside Cameroon |
| `http://` → `https://`, `www` → apex | ✅ 301 |
| `/admin`, `/dashboard` without login | ✅ 302 to login |
| `/.env`, `/storage/.app_key` | ✅ 403 |
| Unknown URL | ✅ 404 — but generic page, see **F5** |
| `robots.txt` | ✅ allows site, blocks admin/auth, lists sitemap |
| `sitemap.xml` | ✅ 15 URLs, all on the canonical domain |
| HSTS, `nosniff`, `X-Frame-Options`, `Referrer-Policy`, `Permissions-Policy`, CSP | ✅ present — CSP has dev entries, see **F2** |
| Session cookie `secure; httponly; samesite=lax` | ✅ — cookie name still `nacho-…`, see **F3** |
| Canonical, description, OG title/url/image, JSON-LD, `<html lang>` FR default, EN switch | ✅ |
| Visible "NACHO" text on main pages | ✅ none (only internal CSS class names) |
| `/register` | ⚠️ **200 — public sign-up open**, see **F1** |
| `Server` header | ⚠️ `nginx/1.24.0 (Ubuntu)`, see **F4** |

Re-run any time (from a Mac or the VPS):

```bash
B=https://noblevehicletestingcompany.com
for p in / /about /centers /services /book-inspection /tariffs /inspection-process /blog /compliance-quality /careers /contact /privacy-policy /terms-and-conditions /cookie-policy /legal-notice /robots.txt /sitemap.xml /register /admin /does-not-exist /.env; do
  printf '%-22s %s\n' "$p" "$(curl -s -o /dev/null -m 20 -w '%{http_code} %{time_total}s' "$B$p")"
done
```

---

## 2. Public pages — desktop, French then English

Switch language with the header FR/EN toggle. For each page check: correct language everywhere (menu, footer, buttons), images load, no broken layout, no placeholder/lorem text, links in the page go somewhere sensible.

| Page | What to look at | FR | EN |
|------|-----------------|:--:|:--:|
| Home `/` | Hero carousel, stats, services, process, 5 center cards with photos, tariffs teaser, CTA band, footer | [ ] | [ ] |
| About `/about` | Copy, images, values | [ ] | [ ] |
| Centers `/centers` | 3 operational + Douala/Kumba under construction, photos, filters, list/map toggle, map pins, "Directions" links | [ ] | [ ] |
| Services `/services` | All services, prices/links consistent with tariffs | [ ] | [ ] |
| Tariffs `/tariffs` | Categories, amounts in FCFA match the official list, regulatory notice | [ ] | [ ] |
| Inspection process `/inspection-process` | Steps, documents to bring | [ ] | [ ] |
| Book `/book-inspection` | Form loads; only operational centers can be chosen (not Douala/Kumba) | [ ] | [ ] |
| Contact `/contact` | HQ block (P.O. Box 100 Mankon-Bamenda, phones, Gmail), per-center cards with photos | [ ] | [ ] |
| Careers `/careers` | Vacancies, "Apply by email" opens mail to the right address | [ ] | [ ] |
| Blog, Compliance | Placeholder text acceptable for launch | [ ] | [ ] |
| Legal (privacy, terms, cookies, legal notice) | Company name NOVETESCO, contact details, no draft markers | [ ] | [ ] |
| Header / footer | Every menu and footer link works; logo → home; phone and email correct | [ ] | [ ] |
| Cookie banner | Appears once; Accept and Reject both close it and stay closed after reload | [ ] | [ ] |

---

## 3. Forms end-to-end

The site sends **no emails** (`MAIL_MAILER=log`): bookings and messages are only visible in the admin. See **F6**.

**Booking**

- [ ] Submit empty form → clear validation errors, in the current language
- [ ] Submit a valid booking (`TEST Step49`, your real phone, a date next week) → success message with a booking reference
- [ ] `/admin/bookings` shows that reference with the same details; open it, set status to **cancelled**, add note "Step 49 test"
- [ ] Repeat once in the other language → success message in that language

**Contact**

- [ ] Empty submit → validation errors
- [ ] Valid message (`TEST Step49`) → success message
- [ ] `/admin/contact-messages` shows it; set status to **archived**

**Links**

- [ ] Phone numbers: tap on the phone → dialer opens with the right number
- [ ] Email links open a new mail to `noblevehicletestingcompany@gmail.com` (or the center email)
- [ ] Map / Directions links open the right location for each operational center

---

## 4. Admin — desktop

- [ ] Wrong password → error message, no details leaked
- [ ] Correct login → dashboard; counts look right (bookings, messages, centers)
- [ ] Admin password is **not** the seed default; account email is one you can receive (see **F7**)
- [ ] Centers: open one, edit a harmless field (e.g. nearby landmark), save → visible on `/centers`; change it back
- [ ] Tariffs, services, pages, site settings: open each list and one record (view only)
- [ ] Media: upload a small JPG → appears in the library; delete it
- [ ] Users: only expected accounts exist (no strangers — see **F1**)
- [ ] Log out → `/admin` redirects to login again

---

## 5. Phone (mobile data)

- [ ] Home loads in a few seconds; hero and center photos appear
- [ ] Menu opens/closes; dropdowns usable with a thumb
- [ ] Centers: list/map toggle works; map can be scrolled past
- [ ] Tariffs table readable or scrolls sideways cleanly
- [ ] Booking form: date picker, select fields and submit work; keyboard does not hide the submit button
- [ ] FR/EN switch works; cookie banner does not cover the submit buttons
- [ ] Second browser: Safari on iPhone **or** Chrome on Android (whichever you did not use) — home + booking

---

## 6. SEO and sharing

- [ ] Paste the home URL in a WhatsApp chat → preview shows NOVETESCO title and image
- [ ] [Rich Results Test](https://search.google.com/test/rich-results) on the home URL → structured data detected, no errors
- [ ] [PageSpeed Insights](https://pagespeed.web.dev/) (mobile) for `/`, `/centers`, `/book-inspection` — record scores in §10 (aim: Performance ≥ 70, SEO ≥ 90, Accessibility ≥ 90)

---

## 7. Security spot checks

- [ ] [SSL Labs](https://www.ssllabs.com/ssltest/analyze.html?d=noblevehicletestingcompany.com) → grade **A** or better
- [ ] [securityheaders.com](https://securityheaders.com/?q=noblevehicletestingcompany.com&followRedirects=on) → record grade (CSP with `unsafe-inline` keeps it below A+; acceptable)
- [ ] F1–F4 fixed (§9), then re-run §1

---

## 8. After testing

On the VPS:

```bash
./deploy/vps/logs.sh 1          # no new ERROR lines from your test session
./deploy/vps/monitor.sh         # OK
```

- [ ] Test booking(s) cancelled and message(s) archived in admin; test media deleted
- [ ] No new errors in logs

---

## 9. Findings from the pre-check

| # | Finding | Risk | Fix |
|---|---------|------|-----|
| F1 | `/register` is public: anyone can create an account (no admin access, but junk accounts and a profile page) | Medium | Remove public registration routes; admins create users in `/admin/users` |
| F2 | CSP allows `localhost` / `127.0.0.1` (Vite dev server) in production | Low | Add the dev entries only when `APP_ENV=local` |
| F3 | Session cookie named `nacho-vehicle-inspection-session` | Low (visible in browser tools only) | `SESSION_COOKIE=novetesco_session` in `.env.production`, redeploy (logs everyone out once) |
| F4 | `Server: nginx/1.24.0 (Ubuntu)` reveals versions | Low | `server_tokens off;` in `/etc/nginx/nginx.conf` `http {}` on the VPS, `sudo nginx -t && sudo systemctl reload nginx` |
| F5 | 404 (and other error pages) are Laravel's bare default: no logo, menu or link home | Low (UX) | Branded `resources/views/errors/404.blade.php` (+ 403, 419, 429, 500, 503), FR/EN |
| F6 | No email notification for new bookings/messages | **Owner decision** — deferred to after launch (2026-10-12); until then staff check `/admin` | Either a daily admin check routine, or SMTP (e.g. Gmail app password) + notification emails — separate small step |
| F7 | Seed admin uses `admin@novetesco.local` (cannot receive password-reset mail) | **Owner decision** | Change the admin email to a real mailbox in `/admin/users` (useful once mail is set up) |

---

## 10. Results log

| Date | Section | Device / lang | Result / issue | Status |
|------|---------|---------------|----------------|--------|
| 2026-10-12 | §1 automated | curl | All pages 200; findings F1–F5 | open |
| | | | | |

**Sign-off:** Step 49 complete on ____ by ____ — then update [ROADMAP.md](ROADMAP.md) and continue with Step 50.
