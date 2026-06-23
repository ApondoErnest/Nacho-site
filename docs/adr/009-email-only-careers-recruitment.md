# ADR 009: Email-only careers recruitment

## Status

Accepted

## Context

Early documentation assumed an online recruitment workflow: public application form, CV upload, `job_applications` table, admin application inbox, and Step 25 career application backend. NACHO's revised requirement is a **professional vacancy-discovery and employer-brand page** — not an online recruitment platform.

Applicants apply through their email client using official company addresses. The website must not store CVs or track application status.

## Decision

1. **Remove** `job_applications` table and all online application UI/backend.
2. **Expand** `career_posts` with vacancy metadata, bilingual content blocks, and mailto configuration (`application_email`, `application_subject`, `application_instructions_*`).
3. **Add** `career_departments` for career-area cards and vacancy filtering.
4. **Public UX:** index-only `/careers` with 40/60 vacancy list + detail panel; **Apply by Email** opens `mailto:` with prefilled subject/body. Optional `?vacancy={slug}` for share/deep link.
5. **General applications** via `site_settings.careers_general_application_email` (nullable until NACHO approves HR address).
6. **No** applicant accounts, application-success page, status tracker, or staff notification on application submit.

Opening mailto does **not** mean the application was submitted — copy must reflect this ([CONTENT_GUIDELINES.md](../CONTENT_GUIDELINES.md)).

## Consequences

- Step 16: 4-block careers page with mailto CTAs only.
- Step 25 (career application backend): **cancelled**.
- Step 34: admin manages vacancies only — no CV download or application statuses.
- `CareerVacancyService` builds filter results and mailto URLs.
- Recruitment documents never touch server storage; [SECURITY.md](../SECURITY.md) CV rules removed for careers.
- [ROLES.md](../ROLES.md): remove job-applications permissions.

## Related

- [DATABASE.md](../DATABASE.md) §3.16–3.17
- [FRONTEND.md](../FRONTEND.md) — Careers page
- ADR 005 — media table no longer stores career CVs
