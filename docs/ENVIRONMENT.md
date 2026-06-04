# Local Environment - NACHO Vehicle Inspection

Local-first development. Docker is not used at this stage (see [DEPLOYMENT.md](DEPLOYMENT.md) for later).

## 1. Prerequisites

- PHP 8.2+ with common extensions (`pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`, `gd` or `imagick`)
- Composer
- Node.js 18+ and npm
- A local MySQL server (8.x) - via Homebrew, DBngin, MAMP, or similar

### Verified local toolchain (Step 1)

| Tool | Version | Status |
|------|---------|--------|
| PHP | 8.5.6 | OK — all required extensions present |
| Composer | 2.9.8 | OK |
| Node.js | 26.x | OK |
| npm | 11.x | OK |
| MySQL | 9.6.0 (Homebrew) | OK — server reachable |
| Database `nacho_vehicle_inspection` | utf8mb4 / utf8mb4_unicode_ci | OK — created |
| Laravel | 13.8 (laravel/laravel v13.8.0) | OK — Step 2 |
| Laravel Breeze | 2.4 (Blade stack) | OK — Step 3 |
| Tailwind `nacho-*` tokens | `tailwind.config.js` | OK — Step 3 |

## 2. Stack

| Layer | Technology |
|-------|-----------|
| Backend | Laravel |
| Rendering | Blade |
| Styling | Tailwind CSS |
| Database | MySQL (`nacho_vehicle_inspection`) |
| Local server | `php artisan serve` |
| Assets | Vite |
| Auth | Laravel Breeze (+ custom roles) |
| File storage | Laravel public storage |
| VCS | Git + GitHub |

## 3. Create the database

```sql
CREATE DATABASE nacho_vehicle_inspection
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

(Optionally create a dedicated MySQL user with privileges on that database.)

## 4. Environment file

Copy `.env.example` to `.env` and set:

```
APP_NAME="NACHO Vehicle Inspection"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

APP_LOCALE=fr
APP_FALLBACK_LOCALE=fr

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nacho_vehicle_inspection
DB_USERNAME=root
DB_PASSWORD=your_local_mysql_password

MAIL_MAILER=log

SEED_ADMIN_PASSWORD=NachoAdmin2026!
BOOKING_NOTIFICATION_EMAIL=bookings@nacho.local
CONTACT_NOTIFICATION_EMAIL=contact@nacho.local
WHATSAPP_NUMBER=
GOOGLE_MAPS_EMBED_KEY=
```

Notes:
- `MAIL_MAILER=log` keeps staff notification emails in `storage/logs` during local dev (no real mail server needed).
- `WHATSAPP_NUMBER` here is only for a general "contact us" link if desired - NOT the excluded reminder system.

## 5. Install & run

```bash
composer install
npm install
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm run dev
php artisan serve
```

Visit `http://127.0.0.1:8000`. Admin at `http://127.0.0.1:8000/admin` (login with the seeded Super Admin).

## 6. Useful commands

- `php artisan migrate:fresh --seed` - rebuild the database from scratch with seed data
- `php artisan test` - run the test suite
- `php artisan route:list` - inspect routes
- `npm run build` - production asset build

## 7. Conventions

- Never commit `.env` or secrets.
- Keep `.env.example` updated when adding new env keys.
- Use feature branches and pull requests (see repository conventions).
