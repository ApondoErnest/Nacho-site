# ADR 004 - MySQL from the start

Status: Accepted

## Context

The spec names MySQL and the database `nacho_vehicle_inspection`, and production will run MySQL in Docker. Local development could use SQLite for speed, but that risks subtle dialect differences (column types, JSON, foreign keys, migrations) between local and production.

## Decision

Use **MySQL locally from the start**, with database `nacho_vehicle_inspection`. Tests may use a separate test database (or in-memory SQLite) so the suite is fast and isolated, while application development runs on MySQL for parity.

## Consequences

- Local/production parity reduces deployment surprises.
- Requires a local MySQL server (documented in [../ENVIRONMENT.md](../ENVIRONMENT.md)).
- Slightly heavier local setup than SQLite, accepted for reliability.
