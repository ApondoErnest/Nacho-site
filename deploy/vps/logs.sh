#!/usr/bin/env bash
# Weekly log review: Laravel error counts per day, the latest error lines, and container log errors.
# Usage: deploy/vps/logs.sh [DAYS]   (default 7)
set -euo pipefail

# shellcheck source=deploy/vps/lib.sh
source "$(dirname "${BASH_SOURCE[0]}")/lib.sh"
require_env_file
cd "$ROOT"

DAYS="${1:-7}"
[[ "$DAYS" =~ ^[0-9]+$ ]] || die "DAYS must be a number."

app_sh() {
    "${COMPOSE[@]}" exec -T app sh -c "$1"
}

echo "== Laravel log files (storage/logs) =="
app_sh 'ls -lh storage/logs | tail -n +2'

echo
echo "== Errors per day (last ${DAYS} days) =="
for (( i = DAYS - 1; i >= 0; i-- )); do
    day="$(date -d "-${i} days" +%Y-%m-%d)"
    count="$(app_sh "grep -cE '^\[[^]]+\] [a-z]+\.(ERROR|CRITICAL|ALERT|EMERGENCY):' storage/logs/laravel-${day}.log 2>/dev/null || true")"
    count="${count//[^0-9]/}"
    printf '  %s  %s\n' "$day" "${count:-0}"
done

echo
echo "== Latest 20 error entries (first line of each) =="
app_sh "grep -hE '^\[[^]]+\] [a-z]+\.(ERROR|CRITICAL|ALERT|EMERGENCY):' storage/logs/laravel*.log 2>/dev/null | cut -c1-300 | tail -n 20 || true"

echo
echo "== Container log errors (last ${DAYS} days) =="
"${COMPOSE[@]}" logs --no-color --since "$(( DAYS * 24 ))h" app nginx mysql 2>&1 \
    | grep -iE 'error|crit|emerg|fatal' | cut -c1-300 | tail -n 30 || true

echo
echo "Host nginx errors are not included: sudo tail -n 100 /var/log/nginx/error.log"
