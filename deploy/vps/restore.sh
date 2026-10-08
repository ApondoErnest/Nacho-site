#!/usr/bin/env bash
# Restore a backup made by backup.sh.
#
#   deploy/vps/restore.sh --test [BACKUP_DIR]   Restore the DB into a scratch database, print row counts, drop it.
#                                                Production is not touched. Defaults to the newest backup.
#   deploy/vps/restore.sh --live BACKUP_DIR     Overwrite the production DB and storage volume.
#                                                Takes a safety backup first and asks for confirmation.
set -euo pipefail

# shellcheck source=deploy/vps/lib.sh
source "$(dirname "${BASH_SOURCE[0]}")/lib.sh"
require_env_file
cd "$ROOT"

usage() {
    sed -n '2,7p' "$0" | sed 's/^# \{0,1\}//'
    exit 1
}

MODE="${1:-}"
SOURCE_DIR="${2:-}"
[[ "$MODE" == "--test" || "$MODE" == "--live" ]] || usage

if [[ -z "$SOURCE_DIR" ]]; then
    [[ "$MODE" == "--test" ]] || die "--live requires an explicit backup directory."
    SOURCE_DIR="$(newest_backup)"
    [[ -n "$SOURCE_DIR" ]] || die "No backups found in $BACKUP_DIR."
fi
[[ -f "$SOURCE_DIR/db.sql.gz" && -f "$SOURCE_DIR/storage.tar.gz" ]] || die "$SOURCE_DIR is not a backup directory."

log "Verifying checksums for $SOURCE_DIR..."
(cd "$SOURCE_DIR" && sha256sum -c --quiet SHA256SUMS)

if [[ "$MODE" == "--test" ]]; then
    SCRATCH_DB="nacho_restore_test"
    trap 'mysql_root -e "DROP DATABASE IF EXISTS ${SCRATCH_DB}" || true' EXIT

    log "Restoring into scratch database ${SCRATCH_DB}..."
    mysql_root -e "DROP DATABASE IF EXISTS ${SCRATCH_DB}; CREATE DATABASE ${SCRATCH_DB} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    gunzip -c "$SOURCE_DIR/db.sql.gz" | mysql_root "$SCRATCH_DB"

    log "Row counts in restored copy:"
    for table in users centers services tariffs bookings contact_messages blog_posts career_posts site_settings; do
        count="$(mysql_root -N -e "SELECT COUNT(*) FROM ${SCRATCH_DB}.${table}" 2>/dev/null || echo 'missing')"
        printf '  %-18s %s\n' "$table" "$count"
    done

    files="$(tar -tzf "$SOURCE_DIR/storage.tar.gz" | grep -vc '/$' || true)"
    log "Storage archive readable: ${files} files."
    log "Restore test passed. Scratch database dropped; production untouched."
    exit 0
fi

APP_HOST="$(env_value APP_URL | sed -E 's#^https?://##; s#/.*$##')"
echo "This will OVERWRITE the production database and storage for ${APP_HOST:-this site}"
echo "with the backup in: $SOURCE_DIR"
read -r -p "Type the domain name to continue: " answer
[[ "$answer" == "$APP_HOST" ]] || die "Confirmation did not match; nothing changed."

log "Taking a safety backup of the current state first..."
BACKUP_HEALTHCHECK_URL="" "$ROOT/deploy/vps/backup.sh"

bring_up() {
    "${COMPOSE[@]}" exec -T app php artisan up || true
}
trap bring_up EXIT

"${COMPOSE[@]}" exec -T app php artisan down --retry=60

log "Restoring database..."
gunzip -c "$SOURCE_DIR/db.sql.gz" \
    | "${COMPOSE[@]}" exec -T mysql sh -c 'MYSQL_PWD="$MYSQL_ROOT_PASSWORD" exec mysql -uroot "$MYSQL_DATABASE"'

log "Restoring storage volume..."
"${COMPOSE[@]}" exec -T app tar -C /var/www/html/storage -xzf - < "$SOURCE_DIR/storage.tar.gz"
"${COMPOSE[@]}" exec -T app chown -R www-data:www-data /var/www/html/storage

log "Running migrations and rebuilding caches..."
"${COMPOSE[@]}" exec -T app php artisan migrate --force
"${COMPOSE[@]}" exec -T app php artisan optimize:clear
"${COMPOSE[@]}" exec -T app php artisan config:cache
"${COMPOSE[@]}" exec -T app php artisan route:cache
"${COMPOSE[@]}" exec -T app php artisan view:cache

log "Live restore complete. Check the site and admin before closing this session."
