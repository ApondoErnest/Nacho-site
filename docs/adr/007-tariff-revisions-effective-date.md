# ADR 007 - Tariff revisions and effective-date publishing

Status: Accepted

## Context

Tariffs follow a regulated schedule that may change on specific effective dates. Admins need to schedule future price updates, preview them, and preserve historical regulated prices. Editing the live `tariffs` row in place (even with an audit log) does not model **when** a price becomes public or allow safe rollback of scheduled changes.

ADR 006 covers **who changed what in admin** (`tariff_audit_logs`). This ADR covers **what price is public on a given date** (`tariff_revisions`).

## Decision

Add a `tariff_revisions` table linked to `tariffs`:

- `snapshot` (json) — price and fields at revision time
- `effective_date` — when the revision becomes active on the public site
- `published_at`, `created_by`, `status` (`scheduled`, `active`, `superseded`, `cancelled`)

Introduce `TariffService::resolveActiveTariffs()` (and related helpers) that:

1. Loads active tariff rows (`is_active = true`)
2. Applies the revision whose `effective_date <= today` and is the latest valid revision per tariff
3. Respects optional `expiry_date` on the tariff row

Admins create future revisions with preview; the app activates them automatically — no manual midnight price swap.

Historical revisions and tariffs are **not hard-deleted**; deactivate or mark `superseded` only.

## Relationship to ADR 006

| Mechanism | Purpose |
|-----------|---------|
| `tariff_revisions` | Scheduled/regulatory price versions and effective dating |
| `tariff_audit_logs` | Append-only record of admin edits (accountability) |

Both may fire on admin actions; public display uses revision resolution.

## Consequences

- Public Pricing Console and booking preselect always show the **current** effective price.
- Clear audit trail for regulated changes over time.
- Additional table, service layer, and admin UI for revision workflow (Step 30).
- Seed data may include initial revision snapshots aligned with `effective_date` on tariff rows.

See [DATABASE.md](../DATABASE.md) §3.6–3.8, [ADMIN_MODULES.md](../ADMIN_MODULES.md) §5, [ARCHITECTURE.md](../ARCHITECTURE.md).
