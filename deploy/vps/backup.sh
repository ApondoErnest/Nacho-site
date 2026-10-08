#!/usr/bin/env bash
# Back up the production database and storage volume, prune old backups, and optionally copy off-site.
# Run from cron nightly; settings come from .env.ops (see deploy/vps/env.ops.example).
#
# Output: $BACKUP_DIR/<YYYY-mm-dd_HHMMSS>/{db.sql.gz,storage.tar.gz,SHA256SUMS}
set -euo pipefail
umask 077

# shellcheck source=deploy/vps/lib.sh
source "$(dirname "${BASH_SOURCE[0]}")/lib.sh"
require_env_file
cd "$ROOT"

mkdir -p "$BACKUP_DIR"
chmod 700 "$BACKUP_DIR"

exec 9>"$BACKUP_DIR/.lock"
flock -n 9 || die "Another backup is already running."

STAMP="$(date '+%Y-%m-%d_%H%M%S')"
RUN_DIR="$BACKUP_DIR/$STAMP"
PARTIAL="$RUN_DIR.partial"

on_failure() {
    rm -rf "$PARTIAL"
    ping_healthcheck "${BACKUP_HEALTHCHECK_URL:-}" /fail "Backup $STAMP failed on $(hostname)."
}
trap on_failure ERR

ping_healthcheck "${BACKUP_HEALTHCHECK_URL:-}" /start
mkdir -p "$PARTIAL"

log "Dumping database..."
"${COMPOSE[@]}" exec -T mysql sh -c \
    'MYSQL_PWD="$MYSQL_ROOT_PASSWORD" exec mysqldump -uroot --single-transaction --quick --routines --triggers --events --no-tablespaces --set-gtid-purged=OFF "$MYSQL_DATABASE"' \
    | gzip -9 > "$PARTIAL/db.sql.gz"

gzip -t "$PARTIAL/db.sql.gz"
gunzip -c "$PARTIAL/db.sql.gz" | tail -n 1 | grep -q 'Dump completed' \
    || die "Database dump is incomplete (no 'Dump completed' trailer)."

log "Archiving storage volume (uploads, app key; logs and framework caches excluded)..."
"${COMPOSE[@]}" exec -T app tar -C /var/www/html/storage -czf - \
    --exclude=./logs --exclude=./framework . \
    > "$PARTIAL/storage.tar.gz"
gzip -t "$PARTIAL/storage.tar.gz"

(cd "$PARTIAL" && sha256sum db.sql.gz storage.tar.gz > SHA256SUMS)
mv "$PARTIAL" "$RUN_DIR"
log "Backup written: $RUN_DIR ($(du -sh "$RUN_DIR" | cut -f1))"

(( BACKUP_RETENTION_DAYS >= 1 )) || die "BACKUP_RETENTION_DAYS must be at least 1."
CUTOFF="$(date -d "-$(( BACKUP_RETENTION_DAYS - 1 )) days" +%Y-%m-%d)"
log "Pruning local backups dated before ${CUTOFF} (keeping the last ${BACKUP_RETENTION_DAYS} days)..."
while IFS= read -r old; do
    if [[ "$(basename "$old" | cut -c1-10)" < "$CUTOFF" ]]; then
        rm -rf "$old"
        log "Pruned $(basename "$old")"
    fi
done < <(find "$BACKUP_DIR" -mindepth 1 -maxdepth 1 -type d -name '20*' ! -name '*.partial')
find "$BACKUP_DIR" -mindepth 1 -maxdepth 1 -type d -name '*.partial' -mtime +1 -exec rm -rf {} +

if [[ -n "${BACKUP_RCLONE_REMOTE:-}" ]]; then
    command -v rclone > /dev/null || die "BACKUP_RCLONE_REMOTE is set but rclone is not installed."
    log "Copying off-site to ${BACKUP_RCLONE_REMOTE}/${STAMP}..."
    rclone copy "$RUN_DIR" "${BACKUP_RCLONE_REMOTE}/${STAMP}"
    rclone delete --min-age "${BACKUP_REMOTE_RETENTION_DAYS:-30}d" "$BACKUP_RCLONE_REMOTE"
    rclone rmdirs --leave-root "$BACKUP_RCLONE_REMOTE"
else
    log "No rclone remote set; off-server copy comes from the Mac pull (deploy/mac/pull-backups.sh)."
fi

ping_healthcheck "${BACKUP_HEALTHCHECK_URL:-}" "" "Backup $STAMP OK."
log "Done."
