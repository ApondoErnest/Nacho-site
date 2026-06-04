# ADR 003 - Custom roles (no Spatie)

Status: Accepted

## Context

The admin area needs role-based access for six roles (Super Admin, Admin, Center Manager, Receptionist, Inspector, Content Manager) with a relatively simple, coarse-grained permission model. A third-party package (e.g. spatie/laravel-permission) is powerful but adds tables, concepts, and dependency surface beyond what is needed.

## Decision

Implement a **custom roles system**: a `roles` table, `users.role_id`, an `AdminAccess` ability helper encoding the permission matrix, middleware (`EnsureRole` / `EnsureAdminAbility`), and a `@adminCan` Blade directive. No Spatie dependency.

## Consequences

- Minimal, transparent authorization that exactly fits the matrix in [../ROLES.md](../ROLES.md).
- Fewer dependencies and migrations to maintain.
- If granular per-permission management is needed later, the helper can be extended or replaced with a package without changing controllers much.
