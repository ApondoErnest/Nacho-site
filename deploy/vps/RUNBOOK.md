# VPS runbook — NACHO on Hostinger (Step 47)

Deploy **NACHO** on **srv1867313** alongside existing apps (`gs-autobilan` → `:8080`, `cashflow-summary` → `:8081`, `g3-control` → `:8082`).

| Item | Value |
|------|--------|
| VPS IP | `89.117.37.202` |
| Domain (canonical) | `https://noblevehicletestingcompany.com` |
| `www` | 301 → apex |
| App path | `/var/www/nacho-site` |
| Docker nginx bind | `127.0.0.1:8083` |
| Git | `git@github.com:ApondoErnest/Nacho-site.git` branch `main` |
| Admin | `admin@nacho.local` + `SEED_ADMIN_PASSWORD` |

Host pattern: **host nginx (80/443 + Certbot)** → **Docker nginx (8083)** → **PHP-FPM** + **MySQL container**.

---

## 1. Prerequisites

- [ ] DNS **A** `@` → `89.117.37.202` (`dig +short noblevehicletestingcompany.com A`)
- [ ] SSH as `ernesto` with sudo
- [ ] Port **8083** free: `ss -tlnp | grep 8083` (no output)
- [ ] `git clone git@github.com:ApondoErnest/Nacho-site.git` works

---

## 2. Clone and configure env

```bash
sudo mkdir -p /var/www
sudo chown ernesto:ernesto /var/www
git clone git@github.com:ApondoErnest/Nacho-site.git /var/www/nacho-site
cd /var/www/nacho-site

cp deploy/vps/env.production.example .env.production
chmod 600 .env.production
nano .env.production   # set DB passwords, SEED_ADMIN_PASSWORD, review APP_URL
```

**First deploy:** keep `RUN_DB_SEED=true`. After a successful seed, set `RUN_DB_SEED=false` and redeploy `app` only.

**APP_KEY:** leave empty to use entrypoint + `storage/.app_key`, or set a fixed `APP_KEY=base64:...` from `php artisan key:generate --show` (run once in a throwaway container or locally).

---

## 3. Start Docker stack

```bash
cd /var/www/nacho-site
chmod +x deploy/vps/deploy.sh
./deploy/vps/deploy.sh
```

Manual equivalent:

```bash
docker compose -f docker-compose.yml -f docker-compose.production.yml --env-file .env.production up -d --build
docker compose -f docker-compose.yml -f docker-compose.production.yml --env-file .env.production exec app php artisan config:cache
docker compose -f docker-compose.yml -f docker-compose.production.yml --env-file .env.production exec app php artisan route:cache
docker compose -f docker-compose.yml -f docker-compose.production.yml --env-file .env.production exec app php artisan view:cache
```

Verify internally:

```bash
curl -I http://127.0.0.1:8083/
curl -fsS http://127.0.0.1:8083/up
docker compose -f docker-compose.yml -f docker-compose.production.yml --env-file .env.production ps
docker compose -f docker-compose.yml -f docker-compose.production.yml --env-file .env.production logs app --tail=40
```

---

## 4. Host nginx (HTTP first)

```bash
cd /var/www/nacho-site
sudo cp deploy/vps/nginx-host/noblevehicletestingcompany.com.conf \
  /etc/nginx/sites-available/noblevehicletestingcompany.com
sudo ln -sf /etc/nginx/sites-available/noblevehicletestingcompany.com /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx
```

Browse **http://noblevehicletestingcompany.com** (should show the site; not HTTPS yet).

---

## 5. TLS (Certbot)

```bash
sudo certbot --nginx -d noblevehicletestingcompany.com -d www.noblevehicletestingcompany.com
```

Then install the **canonical HTTPS** config (apex serves app, `www` redirects to apex):

```bash
cd /var/www/nacho-site
sudo cp deploy/vps/nginx-host/noblevehicletestingcompany.com.ssl.conf \
  /etc/nginx/sites-available/noblevehicletestingcompany.com
sudo nginx -t && sudo systemctl reload nginx
```

Verify:

- `https://noblevehicletestingcompany.com` → 200, logo, CSS
- `https://www.noblevehicletestingcompany.com` → 301 → apex
- `/login` → admin → `/admin`

---

## 6. Post-deploy checklist

| Check | Command / action |
|--------|------------------|
| `APP_DEBUG=false` | In `.env.production` |
| Seed once | Set `RUN_DB_SEED=false`, `docker compose ... up -d app` |
| Sessions / HTTPS | Login works on HTTPS (TrustProxies enabled in production) |
| Forms | Booking + contact submit (mail in `storage/logs` until SMTP) |
| Health | `https://noblevehicletestingcompany.com/up` |

Optional scheduler (if you add scheduled tasks later):

```cron
* * * * * cd /var/www/nacho-site && docker compose -f docker-compose.yml -f docker-compose.production.yml --env-file .env.production exec -T app php artisan schedule:run >> /dev/null 2>&1
```

---

## 7. Updates (new releases on `main`)

```bash
cd /var/www/nacho-site
git pull origin main
./deploy/vps/deploy.sh
```

If migrations changed, entrypoint runs `migrate --force` on `app` restart.

---

## 8. Backups (recommended)

- MySQL volume: `nacho_mysql_data` — periodic `docker compose exec mysql mysqldump ...`
- Files: volume `nacho_app_storage` (uploads)

---

## 9. Troubleshooting

| Symptom | Fix |
|---------|-----|
| 502 from host nginx | `docker compose ... ps`; `curl http://127.0.0.1:8083/up`; check `app` logs |
| Redirect loop | `APP_URL` must match canonical HTTPS URL |
| Login/session lost | Confirm host nginx sends `X-Forwarded-Proto https`; production TrustProxies |
| `8083` bind error but `ss` empty | Remove **`DOCKER_HTTP_PORT`** from `.env.production` (double mapping with production compose). Ensure `docker-compose.production.yml` uses `ports: !override`. Then `docker rm -f nacho-nginx-1` and `up -d nginx`. |
| Port conflict (`8083` in use) | Set **`DOCKER_HOST_PORT=8084`**, update host nginx `proxy_pass`, `docker compose ... up -d nginx` |
| Missing logo | Asset must be `public/images/nacho-logo.png` (Linux case-sensitive) |

See also [docs/DEPLOYMENT.md](../../docs/DEPLOYMENT.md).
