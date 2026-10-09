# VPS runbook — NOVETESCO on Hostinger (Step 47)

**Status:** Step **47 done** (2026-09-30) — production at **https://noblevehicletestingcompany.com**. Use this runbook for updates, TLS renewal checks, and troubleshooting. Step **48 done** (2026-10-09) — backups, monitoring, logs; see §8.

Deploy **NOVETESCO** on **srv1867313** alongside existing apps (`gs-autobilan` → `:8080`, `cashflow-summary` → `:8081`, `g3-control` → `:8082`).

| Item | Value |
|------|--------|
| VPS IP | `89.117.37.202` |
| Domain (canonical) | `https://noblevehicletestingcompany.com` |
| `www` | 301 → apex |
| App path | `/var/www/nacho-site` |
| Docker nginx bind | `127.0.0.1:8083` |
| Git | `git@github.com:ApondoErnest/Nacho-site.git` branch `main` |
| Admin | `admin@novetesco.local` + `SEED_ADMIN_PASSWORD` |

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
| NACHO → NOVETESCO on live copy | After deploy: `docker compose -f docker-compose.yml -f docker-compose.production.yml --env-file .env.production exec -T app php artisan site:sync-public-branding` (updates centers + site contact settings only) |
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

## 8. Operations — backups, monitoring, logs (Step 48)

Scripts live in `deploy/vps/` and run on the VPS as `ernesto` (must be in the `docker` group). They find the app directory from their own location and read:

- `.env.production` — compose settings, `APP_URL`, `DOCKER_HOST_PORT`
- `.env.ops` — backup folder, off-site remote, healthcheck URLs, monitor thresholds (gitignored; template `deploy/vps/env.ops.example`)

| Script | Purpose | When |
|--------|---------|------|
| `backup.sh` | DB dump + storage volume → `$BACKUP_DIR/<timestamp>/`, prune, optional rclone copy | Cron nightly |
| `deploy/mac/pull-backups.sh` | **On the Mac:** pull + verify backups into `~/Backups/novetesco` (14 days) | launchd, 3× daily |
| `restore.sh --test [DIR]` | Restore newest (or given) backup into a scratch DB, print row counts, drop it | Monthly |
| `restore.sh --live DIR` | Overwrite production DB + storage (safety backup + typed confirmation first) | Disaster only |
| `monitor.sh` | `/up` local + public, containers, disk, TLS expiry, backup age, Laravel errors today | Cron every 10 min |
| `logs.sh [DAYS]` | Laravel log files, errors per day, latest errors, container errors | Weekly / after an alert |

Shorthand used below:

```bash
cd /var/www/nacho-site
DC="docker compose -f docker-compose.yml -f docker-compose.production.yml --env-file .env.production"
```

### 8.1 One-time setup

```bash
cd /var/www/nacho-site
git pull origin main

cp deploy/vps/env.ops.example .env.ops
chmod 600 .env.ops
nano .env.ops            # BACKUP_DIR, then later the rclone remote and healthcheck URLs
mkdir -p ~/backups/nacho-site ~/logs

# Add to .env.production (log rotation; the production compose file defaults to these anyway):
#   LOG_STACK=daily
#   LOG_DAILY_DAYS=14
./deploy/vps/deploy.sh   # recreates containers with Docker log caps + daily Laravel logs
```

Verify the redeploy picked up the new settings:

```bash
$DC exec -T app printenv LOG_STACK                                   # daily
docker inspect --format '{{json .HostConfig.LogConfig}}' $($DC ps -q app)   # max-size 10m, max-file 5
```

The old single `storage/logs/laravel.log` stops growing after this. Optionally compress it: `$DC exec -T app gzip storage/logs/laravel.log`.

### 8.2 Backups

```bash
./deploy/vps/backup.sh
ls -lh ~/backups/nacho-site/*/
./deploy/vps/restore.sh --test      # must end with "Restore test passed"
```

Each backup folder contains `db.sql.gz` (full `mysqldump`, consistent snapshot), `storage.tar.gz` (uploads under `storage/app`, plus `storage/.app_key`) and `SHA256SUMS`. Each run keeps only backups dated within the last `BACKUP_RETENTION_DAYS` (14) days, today included, so at most 14 nightly backups (plus any manual or pre-restore safety backups from those days).

**Not in the backup — keep these elsewhere:**

- `.env.production` and `.env.ops` — store a copy in a password manager (not on the same Mac disk alone) (DB passwords, `APP_KEY`, admin seed password). Without them a restore on a new server needs new secrets.
- Host nginx config — in git (`deploy/vps/nginx-host/`).
- TLS certificates — re-issue with Certbot.
- Laravel logs and framework caches — excluded on purpose.

### 8.3 Off-server copy — pulled to your Mac

A backup that only lives on the VPS is lost with the VPS. The Mac **pulls** backups over SSH (the VPS cannot push to a Mac behind a home router). `deploy/mac/pull-backups.sh` copies only finished backups, verifies each with `SHA256SUMS`, keeps the last **14 days** (same as the VPS), and retries when the network is not ready yet.

Run on the **Mac**, in the repo checkout (`/Users/admin/NACHO-site`), after §8.2 has produced a backup on the VPS:

```bash
cp deploy/mac/env.backup-pull.example .env.backup-pull
chmod 600 .env.backup-pull
nano .env.backup-pull                       # defaults: ernesto@89.117.37.202, key ~/.ssh/g3-backup, ~/Backups/novetesco

./deploy/mac/pull-backups.sh                # first pull — ends with "OK (newest ..., N new, ...)"
./deploy/mac/pull-backups.sh --install      # launchd job: at login + daily 09:30, 14:30, 20:30
./deploy/mac/pull-backups.sh --status       # job state, local backups, last log lines
```

- The SSH key must log in without a prompt: `ssh -i ~/.ssh/g3-backup -o BatchMode=yes ernesto@89.117.37.202 true`.
- launchd catches up on a missed run when the Mac wakes from sleep; if the Mac is shut down, the next run is at login.
- Log: `~/Library/Logs/novetesco-pull-backups.log`. Remove the job with `--uninstall` (local backups are kept).
- The folder holds full database dumps (customer bookings, contact messages): keep **FileVault** on. Time Machine will also back up `~/Backups/novetesco`.
- If the repo checkout moves, run `--install` again (the job stores the script path).

**Optional extra cloud copy (rclone on the VPS).** Not needed with the Mac pull. If you want one, configure a remote (`curl https://rclone.org/install.sh | sudo bash`, `rclone config`), then set `BACKUP_RCLONE_REMOTE=b2:novetesco-backups/nacho-site` in `.env.ops`. The remote path must be **dedicated** to these backups: `backup.sh` deletes files older than `BACKUP_REMOTE_RETENTION_DAYS` under it.

### 8.4 Restore

**Monthly test** (safe; production untouched):

```bash
./deploy/vps/restore.sh --test                                   # newest local backup
./deploy/vps/restore.sh --test ~/backups/nacho-site/2026-10-08_021500
```

**Live restore** (overwrites production data):

```bash
ls ~/backups/nacho-site/
./deploy/vps/restore.sh --live ~/backups/nacho-site/<timestamp>
```

It verifies checksums, asks you to type the domain, takes a safety backup of the current state, puts the site in maintenance mode, restores DB + storage, runs migrations, rebuilds caches, and brings the site back up (also on failure). Uploads added after the backup are kept, not deleted.

**From the Mac copy** (VPS lost or rebuilt): clone the repo (§2), restore `.env.production` / `.env.ops` from the password manager, `./deploy/vps/deploy.sh`, then upload a backup from the Mac and restore it on the VPS:

```bash
# On the Mac
ls ~/Backups/novetesco/
rsync -az -e "ssh -i ~/.ssh/g3-backup" --exclude=.verified \
  ~/Backups/novetesco/<timestamp>/ ernesto@89.117.37.202:backups/nacho-site/<timestamp>/

# On the VPS
./deploy/vps/restore.sh --live ~/backups/nacho-site/<timestamp>
```

### 8.5 Monitoring and alerts

`monitor.sh` prints one line and exits non-zero on any failure:

```bash
./deploy/vps/monitor.sh
# 2026-10-08T02:20:00+0000 OK (disk 41%, tls 82d, backup 2026-10-08_021500, errors today 0)
```

Thresholds are in `.env.ops` (`MONITOR_DISK_MAX_PERCENT=85`, `MONITOR_TLS_MIN_DAYS=14`, `MONITOR_BACKUP_MAX_AGE_HOURS=26`, `MONITOR_MAX_DAILY_ERRORS=20`).

**Alerts by email** — free [healthchecks.io](https://healthchecks.io) account, two checks:

| Check | Period | Grace | `.env.ops` key |
|-------|--------|-------|----------------|
| `novetesco-backup` | 1 day | 2 hours | `BACKUP_HEALTHCHECK_URL` |
| `novetesco-monitor` | 10 minutes | 10 minutes | `MONITOR_HEALTHCHECK_URL` |
| `novetesco-mac-pull` | 1 day | 2 days | `BACKUP_PULL_HEALTHCHECK_URL` in the Mac's `.env.backup-pull` |

Paste each check's ping URL (`https://hc-ping.com/<uuid>`). The scripts ping on success and `/fail` on failure, so you are emailed when a check fails **and** when the VPS or cron stops reporting.

**External uptime** — free [UptimeRobot](https://uptimerobot.com) HTTP(s) monitor on `https://noblevehicletestingcompany.com/up`, 5-minute interval. This sees DNS, host nginx and TLS problems from outside the server.

### 8.6 Cron

`crontab -e` as `ernesto`:

```cron
PATH=/usr/local/bin:/usr/bin:/bin
APP_DIR=/var/www/nacho-site

15 2 * * * $APP_DIR/deploy/vps/backup.sh >> $HOME/logs/nacho-backup.log 2>&1
*/10 * * * * $APP_DIR/deploy/vps/monitor.sh >> $HOME/logs/nacho-monitor.log 2>&1
```

Keep the cron logs small with `/etc/logrotate.d/nacho-ops` (`sudo nano`):

```text
/home/ernesto/logs/nacho-*.log {
    su ernesto ernesto
    weekly
    rotate 8
    compress
    missingok
    notifempty
    copytruncate
}
```

### 8.7 Logs and TLS checks

```bash
./deploy/vps/logs.sh            # last 7 days
./deploy/vps/logs.sh 30
sudo tail -n 100 /var/log/nginx/error.log
sudo grep -E '" 5[0-9]{2} ' /var/log/nginx/access.log | tail -n 20
```

Log retention: Laravel `storage/logs/laravel-YYYY-MM-DD.log` keeps 14 days; Docker container logs are capped at 5 × 10 MB per service; host nginx logs rotate via the system logrotate.

TLS renewal is automatic (Certbot timer); `monitor.sh` alerts when fewer than 14 days remain. Check after any nginx change:

```bash
systemctl list-timers | grep certbot
sudo certbot renew --dry-run
```

### 8.8 Step 48 sign-off (completed 2026-10-09)

- [x] `.env.ops` created, `chmod 600`
- [x] Redeployed; `LOG_STACK=daily` and Docker log caps verified (§8.1)
- [x] `backup.sh` run manually; `restore.sh --test` passed (users 1, centers 5, bookings 2, contact messages 4)
- [x] Mac: `pull-backups.sh` pulled and verified a backup; `--install` done; `--status` shows the job
- [x] healthchecks.io checks green (backup, monitor, mac-pull); UptimeRobot monitor green
- [x] Cron installed; nightly backup `2026-10-09_021501` ran unattended and was pulled by the Mac
- [x] `certbot renew --dry-run` succeeds
- [x] `.env.production` and `.env.ops` saved in the password manager

---

## 9. Troubleshooting

| Symptom | Fix |
|---------|-----|
| 502 from host nginx | `docker compose ... ps`; `curl http://127.0.0.1:8083/up`; check `app` logs |
| Redirect loop | `APP_URL` must match canonical HTTPS URL |
| Login/session lost | Confirm host nginx sends `X-Forwarded-Proto https`; production TrustProxies |
| `8083` bind error but `ss` empty | Remove **`DOCKER_HTTP_PORT`** from `.env.production` (double mapping with production compose). Ensure `docker-compose.production.yml` uses `ports: !override`. Then `docker rm -f nacho-nginx-1` and `up -d nginx`. |
| Port conflict (`8083` in use) | Set **`DOCKER_HOST_PORT=8084`**, update host nginx `proxy_pass`, `docker compose ... up -d nginx` |
| Missing logo | Asset must be `public/images/novetesco-logo.png` (Linux case-sensitive) |
| HTTP **500**, log `Uninitialized string offset 0` in `Request.php` (TrustProxies / CORS) | Pull latest `main` (nginx passes `$host` for `X-Forwarded-Host`, not empty client headers). Rebuild web image: `docker compose -f docker-compose.yml -f docker-compose.production.yml --env-file .env.production up -d --build nginx`. Then `curl -fsS http://127.0.0.1:8083/up`. |
| `curl: (56) Connection reset` right after `up --build` | App entrypoint (migrate/seed) runs **before** PHP-FPM listens. Wait until logs show `ready to handle connections`, or `docker compose ... ps` shows **app (healthy)**. Then retry `curl`. Prefer `./deploy/vps/deploy.sh` (waits for `/up`). Set **`RUN_DB_SEED=false`** after first seed so restarts are faster. |

See also [docs/DEPLOYMENT.md](../../docs/DEPLOYMENT.md).
