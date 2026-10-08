# Shared helpers for deploy/vps scripts. Source this file; do not execute it.
# shellcheck shell=bash

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
ENV_FILE="${ENV_FILE:-$ROOT/.env.production}"
OPS_ENV_FILE="${OPS_ENV_FILE:-$ROOT/.env.ops}"

if [[ -f "$OPS_ENV_FILE" ]]; then
    set -a
    # shellcheck disable=SC1090
    source "$OPS_ENV_FILE"
    set +a
fi

BACKUP_DIR="${BACKUP_DIR:-$HOME/backups/nacho-site}"
BACKUP_RETENTION_DAYS="${BACKUP_RETENTION_DAYS:-14}"

COMPOSE=(docker compose --project-directory "$ROOT" -f "$ROOT/docker-compose.yml" -f "$ROOT/docker-compose.production.yml" --env-file "$ENV_FILE")

log() {
    printf '%s %s\n' "$(date '+%Y-%m-%dT%H:%M:%S%z')" "$*"
}

die() {
    log "ERROR: $*" >&2
    exit 1
}

require_env_file() {
    [[ -f "$ENV_FILE" ]] || die "Missing $ENV_FILE — run from the app directory on the VPS."
}

# Read KEY from .env.production (last occurrence, quotes stripped).
env_value() {
    grep -E "^$1=" "$ENV_FILE" 2>/dev/null | tail -n 1 | cut -d= -f2- | tr -d '\r' | sed -e 's/^"//' -e 's/"$//'
}

# Run the mysql client as root inside the mysql container. Extra args are passed to mysql.
mysql_root() {
    "${COMPOSE[@]}" exec -T mysql sh -c 'MYSQL_PWD="$MYSQL_ROOT_PASSWORD" exec mysql -uroot "$@"' sh "$@"
}

# Ping a healthchecks.io-compatible URL. Usage: ping_healthcheck URL [""|/start|/fail] [message]
ping_healthcheck() {
    local url="${1:-}" suffix="${2:-}" body="${3:-}"
    [[ -n "$url" ]] || return 0
    curl -fsS -m 10 --retry 3 -o /dev/null --data-raw "$body" "${url}${suffix}" \
        || log "WARN: healthcheck ping failed (${url}${suffix})"
}

newest_backup() {
    find "$BACKUP_DIR" -mindepth 1 -maxdepth 1 -type d -name '20*' ! -name '*.partial' 2>/dev/null | sort | tail -n 1
}
