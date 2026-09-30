#!/usr/bin/env bash
# Run from repo root on the VPS (e.g. /var/www/nacho-site).
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
cd "$ROOT"

ENV_FILE="${ENV_FILE:-.env.production}"

if [[ ! -f "$ENV_FILE" ]]; then
    echo "Missing $ENV_FILE — copy deploy/vps/env.production.example first." >&2
    exit 1
fi

COMPOSE=(docker compose -f docker-compose.yml -f docker-compose.production.yml --env-file "$ENV_FILE")

echo "Building images..."
"${COMPOSE[@]}" build

echo "Starting stack..."
"${COMPOSE[@]}" up -d

HOST_PORT="$(grep -E '^DOCKER_HOST_PORT=' "$ENV_FILE" 2>/dev/null | cut -d= -f2- | tr -d '\r' || true)"
HOST_PORT="${HOST_PORT:-8083}"

echo "Waiting for PHP-FPM and /up (up to ~3 min)..."
ready=0
for _ in $(seq 1 90); do
    if curl -fsS -o /dev/null "http://127.0.0.1:${HOST_PORT}/up" 2>/dev/null; then
        ready=1
        break
    fi
    sleep 2
done
if [[ "$ready" -ne 1 ]]; then
    echo "WARN: /up not ready yet — check: ${COMPOSE[*]} ps && logs app --tail=50" >&2
fi

echo "Optimizing Laravel..."
"${COMPOSE[@]}" exec -T app php artisan config:cache
"${COMPOSE[@]}" exec -T app php artisan route:cache
"${COMPOSE[@]}" exec -T app php artisan view:cache

echo "Health check (127.0.0.1:${HOST_PORT})..."
curl -fsS -o /dev/null -w "Docker nginx: HTTP %{http_code}\n" "http://127.0.0.1:${HOST_PORT}/up" || true

echo "Done. Configure host nginx + Certbot (see deploy/vps/RUNBOOK.md)."
