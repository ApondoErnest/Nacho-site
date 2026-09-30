# Deployment - NACHO Vehicle Inspection

**Step 46 (Dockerize):** **Done** — stack verified locally in Docker on **2026-09-30** (http://127.0.0.1:8080, migrate/seed, public site + logo). **Next:** Step 47 VPS deploy.

Deployment follows this document step by step after the local stabilization gate ([UAT_CHECKLIST.md](UAT_CHECKLIST.md)).

## 1. Target architecture

| Layer | Technology |
|-------|-----------|
| Application | Laravel |
| Web server | Nginx |
| PHP runtime | PHP-FPM |
| Database | MySQL |
| Containerization | Docker |
| Orchestration | Docker Compose |
| SSL | Let's Encrypt |
| Security/CDN | Cloudflare (optional) |
| Backups | Automated DB + file backups |
| Monitoring | Uptime + error monitoring |

## 2. Docker stack (Step 46)

| Service | Image / target | Role |
|---------|----------------|------|
| `app` | [Dockerfile](../Dockerfile) `target: app` | PHP 8.4-FPM, Laravel, Vite build baked in |
| `nginx` | [Dockerfile](../Dockerfile) `target: web` | Serves `public/`, FastCGI to `app:9000` |
| `mysql` | `mysql:8.4` | Database volume `mysql_data` |

Config files: [docker/nginx/default.conf](../docker/nginx/default.conf), [docker/entrypoint.sh](../docker/entrypoint.sh), [docker/php/php.ini](../docker/php/php.ini), [.env.docker.example](../.env.docker.example).

### Run locally (production-like)

Requires Docker Desktop or Docker Engine + Compose v2.

```bash
cp .env.docker.example .env.docker   # optional: customize ports/passwords
docker compose --env-file .env.docker.example up --build -d
```

Open **http://127.0.0.1:8080** (override with `DOCKER_HTTP_PORT`).

On first start the `app` entrypoint waits for MySQL, runs `migrate --force`, and seeds when `RUN_DB_SEED=true` (default in `.env.docker.example`).

Admin login: `/login` → `admin@nacho.local` / `SEED_ADMIN_PASSWORD` (default `NachoAdmin2026!`).

Useful commands:

```bash
docker compose logs -f app nginx
docker compose exec app php artisan about
docker compose down          # keep volumes
docker compose down -v       # destroy DB + storage volumes
```

Automated config checks: `Step46DockerConfigTest` (optional `docker compose config` when Docker CLI is installed).

**`MissingAppKeyException`:** Do not set an empty `APP_KEY` in Compose (it overrides `.env`). Leave `APP_KEY` unset in `.env.docker` so the entrypoint generates one and persists it at `storage/.app_key`, then rebuild/restart `app`: `docker compose --env-file .env.docker up --build -d app`.

Production on VPS: set `APP_ENV=production`, `APP_DEBUG=false`, strong `APP_KEY`, `DB_*` and `SEED_ADMIN_PASSWORD`, set `RUN_DB_SEED=false` after first seed, then enable `config:cache` / `route:cache` / `view:cache` in your deploy script (Step 47).

Redis is optional and not included in v1 Compose.

No reminder system is added during Dockerization.

## 3. VPS deployment

1. Prepare the VPS (OS, firewall, users)
2. Install required tools (Docker, Docker Compose, Git)
3. Upload or clone the project
4. Configure the production environment (`.env`, `APP_DEBUG=false`)
5. Start Docker services
6. Configure Nginx (domain, reverse proxy)
7. Configure the domain (DNS)
8. Configure SSL (Let's Encrypt)
9. Run database migrations
10. Insert seed data
11. Configure storage access (`storage:link`, permissions)
12. Test public website
13. Test admin login
14. Test forms
15. Configure backups
16. Configure uptime monitoring
17. Configure error logging
18. Disable debug mode
19. Submit sitemap to search engines
20. Register site in **Google Search Console**
21. Configure **privacy-friendly analytics** (optional Google Analytics or alternative)

## 4. Production checklist

- `APP_DEBUG=false`, `APP_ENV=production`
- HTTPS enforced; secure cookies
- strong admin credentials and `APP_KEY`
- config/route/view caching enabled
- backups verified (restore tested)
- monitoring and alerting active
- logs collected and reviewed

## 5. Out of scope

Reminder systems, customer portal, fleet/corporate features, and equipment integrations are not part of this deployment.
