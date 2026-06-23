# Roles & Permissions - NACHO Vehicle Inspection

Authorization uses a **custom roles system** (no Spatie): a `roles` table and `users.role_id`, enforced by middleware and an ability helper. See ADR 003 in [adr/](adr/).

## 1. Roles

| Role | Slug | Summary |
|------|------|---------|
| Super Admin | `super-admin` | Full access to everything, including users and settings. |
| Admin | `admin` | Manage most website content and operations. |
| Center Manager | `center-manager` | Manage center info and center-related bookings. |
| Receptionist | `receptionist` | Manage booking requests and contact messages. |
| Inspector | `inspector` | View bookings and update inspection-related statuses. |
| Content Manager | `content-manager` | Manage blog posts, pages, services, and media. |

## 2. Permission matrix

Abilities are coarse-grained strings checked in routes/middleware and Blade (`@adminCan('...')`). "All" = full CRUD; "View" = read-only; "-" = no access.

| Ability area | Super Admin | Admin | Center Manager | Receptionist | Inspector | Content Manager |
|--------------|:-----------:|:-----:|:--------------:|:------------:|:---------:|:---------------:|
| Dashboard | All | All | View | View | View | View |
| Centers | All | All | Edit own/assigned | - | - | - |
| Services | All | All | - | - | - | All |
| Tariffs | All | All | View | View | View | - |
| Bookings | All | All | Manage (center) | Manage | Update status | - |
| Contact messages | All | All | - | Manage | - | - |
| Blog categories/posts | All | All | - | - | - | All |
| Careers (vacancies + departments) | All | All | - | - | - | All |
| Pages (legal/static) | All | All | - | - | - | All |
| Media | All | All | Upload | - | - | All |
| Users | All | - | - | - | - | - |
| Roles | All | - | - | - | - | - |
| Site settings | All | All | - | - | - | - |

Notes:
- Super Admin bypasses all checks.
- "Manage (center)" means a Center Manager acts on bookings tied to their center(s); for the first version this may be simplified to all centers if per-center user assignment is not yet implemented (documented decision; can tighten later).
- Inspector "Update status" is limited to the booking status workflow fields, not full edit/delete.

## 3. Enforcement

- **Middleware** gates admin route groups by ability (e.g. `admin.ability:bookings.view`) and by role for sensitive areas (e.g. `role:super-admin` for user/role management).
- **Ability helper** (`AdminAccess::can($user, $ability)`) centralizes the matrix above; a `@adminCan` Blade directive hides unavailable sidebar links/buttons.
- **Inactive users** (`status = inactive`) cannot log in or access admin, regardless of role.
- Public visitors never reach admin routes.

## 4. Account rules

- Passwords are hashed (bcrypt).
- Login is rate-limited; logout is always available; password reset optional.
- The first **Super Admin** is created during seeding (credentials via `.env`, see [SEEDING.md](SEEDING.md)).
- Only Super Admin can create/edit users and assign roles.

## 5. Relationship to data model

See [DATABASE.md](DATABASE.md) sections 3.1-3.2. One role has many users; one user belongs to one role.
