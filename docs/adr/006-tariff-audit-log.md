# ADR 006 - Tariff audit log

Status: Accepted (enhancement)

## Context

Tariffs follow an official, regulated schedule (Ministry of Transport). When staff update a price or field, it is valuable - for accountability and dispute resolution - to know what changed, when, and by whom. The base spec does not require this.

## Decision

Add an append-only `tariff_audit_logs` table that records `tariff_id`, the acting `user_id`, a JSON `changes` payload (before/after), and a timestamp. The admin tariff update flow writes a log entry on every change.

## Consequences

- Clear history and accountability for regulated pricing changes.
- Small additional table and write on tariff updates; no impact on public pages.
- Purely additive; can be ignored or removed without affecting core functionality.
