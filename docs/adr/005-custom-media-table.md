# ADR 005 - Custom media table (no Spatie Media Library)

Status: Accepted

## Context

The site stores center/service/blog/page images and optional booking documents. Career CVs are **not** stored on the website (email-only recruitment — ADR 009). The spec defines an explicit `media` table with bilingual alt text and uploader tracking. Spatie Media Library is feature-rich but introduces conversions, collections, and a schema/model footprint beyond the spec's needs.

## Decision

Implement a **custom `media` table** and model per [../DATABASE.md](../DATABASE.md), with uploads stored under Laravel public storage and validated per [../SECURITY.md](../SECURITY.md). No Spatie Media Library.

## Consequences

- Simple, predictable schema matching the spec exactly (incl. FR/EN alt text).
- Full control over validation, storage paths, and access rules.
- Image conversions/responsive variants are handled manually/lightweight if needed, rather than via a package.
