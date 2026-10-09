# Deployment - NOVETESCO Vehicle Inspection

**Step 46 (Dockerize):** **Done** — stack verified locally in Docker on **2026-09-30** (http://127.0.0.1:8080, migrate/seed, public site + logo).

**Step 47 (VPS):** **Done** — production live at **https://noblevehicletestingcompany.com** (Hostinger **89.117.37.202**, Docker **8083**, Certbot TLS, admin login verified **2026-09-30**).

**Step 48 (backups, monitoring, logs):** **Done** (2026-10-09) — nightly backups pulled to the owner's Mac, monitoring with healthchecks.io + UptimeRobot, log rotation; procedure in [deploy/vps/RUNBOOK.md §8](../deploy/vps/RUNBOOK.md). **Next:** Step 49 (final production testing).

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

Admin login: `/login` → `admin@novetesco.local` / `SEED_ADMIN_PASSWORD` (default `NovetescoAdmin2026!`).

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

If the public site still shows old **NACHO** center names or Yahoo contact emails (data from the first seed), run **`php artisan site:sync-public-branding`** on the VPS app container after deploying the command—see [deploy/vps/RUNBOOK.md](../deploy/vps/RUNBOOK.md).

Redis is optional and not included in v1 Compose.

No reminder system is added during Dockerization.

## 3. VPS deployment (Step 47)

**Authoritative runbook:** [deploy/vps/RUNBOOK.md](../deploy/vps/RUNBOOK.md) (Hostinger **89.117.37.202**, `noblevehicletestingcompany.com`, Docker on **127.0.0.1:8083**, host nginx + Certbot).

| Artifact | Purpose |
|----------|---------|
| [docker-compose.production.yml](../docker-compose.production.yml) | Production override (bind `8083`, `APP_DEBUG=false`) |
| [deploy/vps/env.production.example](../deploy/vps/env.production.example) | VPS `.env.production` template |
| [deploy/vps/deploy.sh](../deploy/vps/deploy.sh) | Build, up, Laravel caches |
| [deploy/vps/nginx-host/*.conf](../deploy/vps/nginx-host/) | Host reverse proxy + HTTPS (apex canonical) |
| [deploy/vps/backup.sh](../deploy/vps/backup.sh), [restore.sh](../deploy/vps/restore.sh) | Nightly DB + storage backup, restore test / live restore |
| [deploy/mac/pull-backups.sh](../deploy/mac/pull-backups.sh) | Runs on the owner's Mac: pulls and verifies VPS backups (launchd) |
| [deploy/vps/monitor.sh](../deploy/vps/monitor.sh), [logs.sh](../deploy/vps/logs.sh) | Health checks with healthchecks.io pings, log review |
| [deploy/vps/env.ops.example](../deploy/vps/env.ops.example) | `.env.ops` template for the scripts above |

**Production sign-off (2026-09-30):** HTTPS `/up` 200, public pages + booking form, `www` → apex, admin dashboard and centers CRUD reachable after login. Post-deploy: set `RUN_DB_SEED=false`, strong admin password, rotate away from seed defaults.

Step 48 (automated backups, monitoring, log review — [RUNBOOK §8](../deploy/vps/RUNBOOK.md)) is done; continue with Step 49–50 (optional production UAT, Search Console + sitemap submission).

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
