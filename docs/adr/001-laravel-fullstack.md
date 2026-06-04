# ADR 001 - Laravel full-stack

Status: Accepted

## Context

NACHO needs public pages, database-driven content, an admin dashboard, authentication, forms, file uploads, bilingual content, and future deployment flexibility. A single, well-supported framework keeps the team productive and the stack simple.

## Decision

Use **Laravel full-stack**: Blade templates for rendering, Tailwind CSS for styling, Vite for assets, Laravel Breeze for authentication, and Eloquent/MySQL for data. No separate SPA/JS framework for the public site.

## Consequences

- One codebase, one mental model; fast delivery of CRUD-heavy admin features.
- Server-rendered Blade is SEO-friendly and simple to host.
- Rich ecosystem (validation, queues, mail, testing) available out of the box.
- Heavy client-side interactivity (if ever needed) would be added incrementally with light JS, not a full SPA.
