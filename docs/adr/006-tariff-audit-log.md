# ADR 006 - Tariff audit log

Status: Accepted (enhancement)

## Context

Tariff prices follow an applicable regulated schedule. When staff update fields in admin, it is valuable — for accountability and dispute resolution — to know **who** changed **what**, **when**. The base spec does not require this; ADR 007 adds scheduled revisions for **effective-date publishing**.

## Decision

Add an append-only `tariff_audit_logs` table that records `tariff_id`, the acting `user_id`, a JSON `changes` payload (before/after), and a timestamp. The admin tariff update flow writes a log entry on every direct edit.

This is **separate from** `tariff_revisions` (ADR 007):

- **Audit log** = accountability for admin actions
- **Revisions** = which price is public on a given date

## Consequences

- Clear history and accountability for tariff admin changes.
- Small additional table and write on tariff updates; no direct impact on public display logic.
- Purely additive alongside ADR 007; can operate without revisions in minimal deployments (not recommended for production).
