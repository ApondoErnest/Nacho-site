#!/usr/bin/env bash
# Server-side health check, meant to run from cron every 10 minutes.
# Checks: local and public /up, containers running, disk usage, TLS expiry, backup freshness,
# and today's Laravel error count. Prints one summary line; exits 1 if any check fails.
# If MONITOR_HEALTHCHECK_URL is set, pings it on success and URL/fail on failure, so you are
# alerted both when a check fails and when this script stops running (e.g. the server is down).
set -uo pipefail

# shellcheck source=deploy/vps/lib.sh
source "$(dirname "${BASH_SOURCE[0]}")/lib.sh"
require_env_file
cd "$ROOT"

DISK_MAX="${MONITOR_DISK_MAX_PERCENT:-85}"
TLS_MIN_DAYS="${MONITOR_TLS_MIN_DAYS:-14}"
BACKUP_MAX_HOURS="${MONITOR_BACKUP_MAX_AGE_HOURS:-26}"
MAX_DAILY_ERRORS="${MONITOR_MAX_DAILY_ERRORS:-20}"

APP_URL="$(env_value APP_URL)"
APP_URL="${APP_URL:-https://noblevehicletestingcompany.com}"
APP_HOST="$(sed -E 's#^https?://##; s#/.*$##' <<< "$APP_URL")"
HOST_PORT="$(env_value DOCKER_HOST_PORT)"
HOST_PORT="${HOST_PORT:-8083}"

failures=()
notes=()

if ! curl -fsS -m 10 -o /dev/null "http://127.0.0.1:${HOST_PORT}/up"; then
    failures+=("local /up on :${HOST_PORT} not responding")
fi

if ! curl -fsS -m 15 -o /dev/null "${APP_URL}/up"; then
    failures+=("public ${APP_URL}/up not responding")
fi

running="$("${COMPOSE[@]}" ps --status running --services 2>/dev/null)"
for service in app nginx mysql; do
    grep -qx "$service" <<< "$running" || failures+=("container '${service}' not running")
done

disk_used="$(df -P / | awk 'NR==2 { gsub("%", "", $5); print $5 }')"
notes+=("disk ${disk_used}%")
if (( disk_used >= DISK_MAX )); then
    failures+=("disk ${disk_used}% used (limit ${DISK_MAX}%)")
fi

cert_end="$(echo | openssl s_client -servername "$APP_HOST" -connect "${APP_HOST}:443" 2>/dev/null \
    | openssl x509 -noout -enddate 2>/dev/null | cut -d= -f2)"
if [[ -z "$cert_end" ]]; then
    failures+=("could not read TLS certificate for ${APP_HOST}")
else
    days_left=$(( ($(date -d "$cert_end" +%s) - $(date +%s)) / 86400 ))
    notes+=("tls ${days_left}d")
    if (( days_left < TLS_MIN_DAYS )); then
        failures+=("TLS certificate expires in ${days_left} days — check certbot renewal")
    fi
fi

if (( BACKUP_MAX_HOURS > 0 )); then
    latest="$(newest_backup)"
    if [[ -z "$latest" ]]; then
        failures+=("no backups found in ${BACKUP_DIR}")
    elif [[ -z "$(find "$latest" -maxdepth 0 -mmin -$(( BACKUP_MAX_HOURS * 60 )))" ]]; then
        failures+=("newest backup $(basename "$latest") is older than ${BACKUP_MAX_HOURS}h")
    else
        notes+=("backup $(basename "$latest")")
    fi
fi

today="$(date +%Y-%m-%d)"
errors="$("${COMPOSE[@]}" exec -T app sh -c \
    "grep -cE '^\[[^]]+\] [a-z]+\.(ERROR|CRITICAL|ALERT|EMERGENCY):' storage/logs/laravel-${today}.log 2>/dev/null || true")"
errors="${errors//[^0-9]/}"
errors="${errors:-0}"
notes+=("errors today ${errors}")
if (( MAX_DAILY_ERRORS > 0 && errors >= MAX_DAILY_ERRORS )); then
    failures+=("${errors} Laravel errors logged today (limit ${MAX_DAILY_ERRORS}) — run deploy/vps/logs.sh")
fi

summary="$(printf '%s, ' "${notes[@]}")"
summary="${summary%, }"
if (( ${#failures[@]} > 0 )); then
    message="FAIL: $(printf '%s; ' "${failures[@]}")(${summary})"
    log "$message"
    ping_healthcheck "${MONITOR_HEALTHCHECK_URL:-}" /fail "$message"
    exit 1
fi

log "OK (${summary})"
ping_healthcheck "${MONITOR_HEALTHCHECK_URL:-}" "" "OK (${summary})"
