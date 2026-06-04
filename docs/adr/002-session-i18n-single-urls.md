# ADR 002 - Session locale with single URLs

Status: Accepted

## Context

The site is bilingual (FR default, EN secondary). The spec states the language choice should be remembered during the session. Two common approaches: (a) locale-prefixed URLs (`/fr/...`, `/en/...`), or (b) single URLs with locale stored in the session.

## Decision

Use **session-stored locale with single, clean URLs**. A `GET /language/{locale}` route switches the session locale and redirects back; a `SetLocaleFromSession` middleware applies it on each public request. French is the default.

## Consequences

- Clean, stable canonical URLs; simpler link structure and routing.
- Matches the spec's "remember during session" requirement.
- SEO trade-off: only one URL exists per page, so per-language indexing is limited and `hreflang` cannot map distinct per-language URLs. The default (French) rendering is what crawlers primarily see. Documented in [../SEO.md](../SEO.md).
- Mitigation/escape hatch: if strong English organic visibility is later required, introduce optional `/en` locale-prefixed URLs with `hreflang` as a future enhancement (not in current scope).
