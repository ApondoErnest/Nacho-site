# Brand & Visual Identity - NACHO Vehicle Inspection

NACHO's preferred theme color is a brownish red / burnt orange. The site should read as a **premium vehicle inspection and road-safety platform** — not a simple brochure. Full UX direction: [DESIGN.md](DESIGN.md).

## 1. Color system

Recommended brand direction: **burnt orange + dark charcoal + white + small green safety accents.**

| Purpose | Direction | Meaning | Suggested hex |
|---------|-----------|---------|---------------|
| Primary | Brownish red / burnt orange | Strength, road safety, inspection, authority | `#B5471F` |
| Primary (dark) | Deeper burnt orange | Hover/active states | `#8F3414` |
| Dark | Deep charcoal / dark brown | Trust and seriousness | `#2A2724` |
| Background | White / warm cream | Clean professional look | `#FFFFFF` / `#FAF6F0` |
| Success | Green | Accepted, Operational, Compliant | `#2E7D32` |
| Warning | Amber | Suspended, counter-visit, Under construction | `#F59E0B` |
| Danger | Red | Refused, serious defect | `#C62828` |

> Hex values are the recommended starting palette and may be fine-tuned once a final logo is supplied. They are encoded as Tailwind tokens (see below) so a change is one-line.

## 2. Tailwind tokens

Brand colors are exposed as `nacho-*` Tailwind tokens so markup stays semantic and themable:

- `nacho-primary`, `nacho-primary-dark`
- `nacho-dark`
- `nacho-cream`
- `nacho-success`, `nacho-warning`, `nacho-danger`

Defined in `tailwind.config.js` under `theme.extend.colors`. A `primary_color` value in `site_settings` may later override the primary token at runtime if needed (see [ADMIN_MODULES.md](ADMIN_MODULES.md)).

## 3. Typography

- Clean, legible sans-serif (system UI stack or a Google font such as Inter/Figtree).
- Strong, confident headings; comfortable body line-height for readability on mobile.
- Consistent type scale across all public pages.

## 4. Logo

- Official logo asset: `public/images/nacho-logo.png` (NACHO Industries Cameroon — Vehicle Inspection).
- Rendered via the `<x-nacho-logo>` Blade component (header, footer, Breeze guest/auth layout).
- Accessible alt text: `lang/*/branding.php` → `branding.logo_alt`.
- Path is configurable in `config/branding.php`; later overridable through `site_settings` (`logo`) in admin (Step 38).
- Text wordmark (`<x-nacho-wordmark>`) remains as fallback if the image file is missing.
- The admin dashboard uses a simple "NACHO Admin" text wordmark (Step 27).

## 5. Imagery

- Real inspection center photos where available; otherwise clean, professional placeholder imagery.
- Road-safety-oriented visuals (vehicles, inspection lines, technicians).
- All images use responsive sizing and lazy loading, with bilingual alt text (see [SEO.md](SEO.md) and the `media` table in [DATABASE.md](DATABASE.md)).

## 6. Tone of voice

Professional, trustworthy, clear, and reassuring. Emphasizes road safety, compliance, transparency, and customer care. Content tone rules and the center-status messaging are defined in [CONTENT_GUIDELINES.md](CONTENT_GUIDELINES.md).

## 7. UI principles

- Mobile-first; sticky nav; floating/fixed Book Inspection on scroll (see [DESIGN.md](DESIGN.md)).
- Burnt orange CTAs on charcoal/white — avoid generic blue/green inspection-site clichés.
- Status badges: green (Operational/Accepted), amber (Under construction/Suspended), red (Refused). **Centers finder (Block 2):** active-center cards omit large Operational/Open Now badges — use section separation instead. Expansion section (Block 3) keeps Under Construction styling.
- Prefer real center photos over stock; specific microcopy CTAs (not vague “Learn More”).
- Accessible: WCAG AA, focus states, semantic HTML, keyboard navigability.
- Components: [FRONTEND.md](FRONTEND.md).
