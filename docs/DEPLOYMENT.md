# Deployment - NACHO Vehicle Inspection (deferred)

Deployment happens only after the local version is stable ([UAT_CHECKLIST.md](UAT_CHECKLIST.md)). This document is a forward plan; nothing here is built during the local phase.

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

## 2. Dockerization (after local sign-off)

Containers:
- Laravel application (PHP-FPM)
- Nginx (web server)
- MySQL (database)
- Redis (optional, later)

Steps:
1. Prepare app for containers (config caching, env strategy)
2. Configure the application container (PHP-FPM, extensions)
3. Configure the Nginx container (vhost, static assets, fastcgi)
4. Configure the MySQL container (volume, credentials)
5. Configure environment variables (production `.env`)
6. Configure storage permissions and volumes
7. Configure migrations on deploy
8. Test the Dockerized app locally
9. Prepare production configuration

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
