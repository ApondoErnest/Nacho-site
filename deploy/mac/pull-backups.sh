#!/bin/bash
# Pull the VPS backups made by deploy/vps/backup.sh to this Mac over SSH, verify and keep them.
#
#   deploy/mac/pull-backups.sh              Pull new backups, verify checksums, prune old local copies.
#   deploy/mac/pull-backups.sh --install    Run automatically via launchd (at login, 09:30, 14:30, 20:30).
#   deploy/mac/pull-backups.sh --uninstall  Remove the launchd job.
#   deploy/mac/pull-backups.sh --status     Show the job, local backups and recent log lines.
#
# Settings: .env.backup-pull in the repo root (template: deploy/mac/env.backup-pull.example).
# Written for macOS /bin/bash 3.2, BSD date and openrsync.
set -Eeuo pipefail
umask 077

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
SCRIPT="$ROOT/deploy/mac/pull-backups.sh"
CONFIG_FILE="${BACKUP_PULL_ENV_FILE:-$ROOT/.env.backup-pull}"

if [[ -f "$CONFIG_FILE" ]]; then
    set -a
    # shellcheck disable=SC1090
    source "$CONFIG_FILE"
    set +a
fi

SSH_HOST="${BACKUP_SSH_HOST:-ernesto@89.117.37.202}"
SSH_KEY="${BACKUP_SSH_KEY:-$HOME/.ssh/g3-backup}"
REMOTE_DIR="${BACKUP_REMOTE_DIR:-backups/nacho-site}"
LOCAL_DIR="${BACKUP_LOCAL_DIR:-$HOME/Backups/novetesco}"
RETENTION_DAYS="${BACKUP_LOCAL_RETENTION_DAYS:-14}"
HEALTHCHECK_URL="${BACKUP_PULL_HEALTHCHECK_URL:-}"
RSH="${BACKUP_RSYNC_RSH:-ssh -i $SSH_KEY -o IdentitiesOnly=yes -o BatchMode=yes -o ConnectTimeout=20}"
RETRY_DELAY="${BACKUP_PULL_RETRY_DELAY:-60}"

LABEL="com.novetesco.pull-backups"
PLIST="$HOME/Library/LaunchAgents/$LABEL.plist"
LOG_FILE="$HOME/Library/Logs/novetesco-pull-backups.log"
LOCK="$LOCAL_DIR/.pull.lock"

log() {
    printf '%s %s\n' "$(date '+%Y-%m-%dT%H:%M:%S%z')" "$*"
}

die() {
    log "ERROR: $*" >&2
    exit 1
}

ping_healthcheck() {
    [[ -n "$HEALTHCHECK_URL" ]] || return 0
    curl -fsS -m 10 --retry 3 -o /dev/null --data-raw "$2" "${HEALTHCHECK_URL}$1" \
        || log "WARN: healthcheck ping failed (${HEALTHCHECK_URL}$1)"
}

size_of() {
    du -sh "$1" 2>/dev/null | awk '{ print $1 }'
}

list_backups() {
    find "$LOCAL_DIR" -mindepth 1 -maxdepth 1 -type d -name '20*' ! -name '*.partial' 2>/dev/null | sort
}

install_job() {
    [[ -f "$SSH_KEY" || -n "${BACKUP_RSYNC_RSH:-}" ]] || die "SSH key $SSH_KEY not found (set BACKUP_SSH_KEY)."
    mkdir -p "$(dirname "$PLIST")" "$(dirname "$LOG_FILE")"
    cat > "$PLIST" <<EOF
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
    <key>Label</key>
    <string>$LABEL</string>
    <key>ProgramArguments</key>
    <array>
        <string>/bin/bash</string>
        <string>$SCRIPT</string>
    </array>
    <key>RunAtLoad</key>
    <true/>
    <key>StartCalendarInterval</key>
    <array>
        <dict><key>Hour</key><integer>9</integer><key>Minute</key><integer>30</integer></dict>
        <dict><key>Hour</key><integer>14</integer><key>Minute</key><integer>30</integer></dict>
        <dict><key>Hour</key><integer>20</integer><key>Minute</key><integer>30</integer></dict>
    </array>
    <key>StandardOutPath</key>
    <string>$LOG_FILE</string>
    <key>StandardErrorPath</key>
    <string>$LOG_FILE</string>
</dict>
</plist>
EOF
    plutil -lint -s "$PLIST"
    launchctl bootout "gui/$(id -u)/$LABEL" 2>/dev/null || true
    launchctl bootstrap "gui/$(id -u)" "$PLIST"
    log "Installed $LABEL (runs now, at login, and daily at 09:30, 14:30, 20:30). Log: $LOG_FILE"
}

uninstall_job() {
    launchctl bootout "gui/$(id -u)/$LABEL" 2>/dev/null || true
    rm -f "$PLIST"
    log "Removed $LABEL. Local backups in $LOCAL_DIR are kept."
}

show_status() {
    if launchctl print "gui/$(id -u)/$LABEL" > /dev/null 2>&1; then
        echo "launchd job: installed ($PLIST)"
        launchctl print "gui/$(id -u)/$LABEL" | grep -E '^\s*(state|runs|last exit code) =' | sed 's/^[[:space:]]*/  /' | awk '!seen[$1]++'
    else
        echo "launchd job: not installed (run: $SCRIPT --install)"
    fi
    echo
    echo "Local backups in $LOCAL_DIR:"
    list_backups | tail -n 5 | while read -r dir; do
        printf '  %s  %s\n' "$(basename "$dir")" "$(size_of "$dir")"
    done
    echo "  total: $(list_backups | wc -l | tr -d ' ') backups, $(size_of "$LOCAL_DIR")"
    if [[ -f "$LOG_FILE" ]]; then
        echo
        echo "Last log lines ($LOG_FILE):"
        tail -n 8 "$LOG_FILE" | sed 's/^/  /'
    fi
}

finish() {
    local status=$?
    rmdir "$LOCK" 2>/dev/null || true
    if (( status != 0 )); then
        ping_healthcheck /fail "Backup pull to $(hostname -s) failed (exit $status)."
    fi
}

pull() {
    mkdir -p "$LOCAL_DIR"
    chmod 700 "$LOCAL_DIR"
    if [[ -d "$LOCK" && -n "$(find "$LOCK" -maxdepth 0 -mmin +360)" ]]; then
        rmdir "$LOCK"
    fi
    mkdir "$LOCK" 2>/dev/null || die "Another pull is running (remove $LOCK if it is stale)."
    trap finish EXIT

    log "Pulling ${SSH_HOST}:${REMOTE_DIR}/ -> ${LOCAL_DIR}/"
    local attempt=1
    until rsync -az --timeout=300 -e "$RSH" --exclude='*.partial' --exclude='.lock' \
        "${SSH_HOST}:${REMOTE_DIR}/" "${LOCAL_DIR}/"; do
        (( attempt < 3 )) || die "rsync failed 3 times (Mac offline, VPS down, or no backups on the server yet)."
        log "rsync failed (attempt ${attempt}); retrying in ${RETRY_DELAY}s..."
        sleep "$RETRY_DELAY"
        attempt=$(( attempt + 1 ))
    done

    local dir verified=0 corrupt=0
    while IFS= read -r dir; do
        [[ -f "$dir/.verified" ]] && continue
        if (cd "$dir" && shasum -a 256 -c SHA256SUMS > /dev/null 2>&1); then
            touch "$dir/.verified"
            verified=$(( verified + 1 ))
            log "Verified $(basename "$dir") ($(size_of "$dir"))"
        else
            log "Checksum mismatch in $(basename "$dir"); deleted so the next run downloads it again."
            rm -rf "$dir"
            corrupt=$(( corrupt + 1 ))
        fi
    done < <(list_backups)

    (( RETENTION_DAYS >= 1 )) || die "BACKUP_LOCAL_RETENTION_DAYS must be at least 1."
    local cutoff name
    cutoff="$(date -v-"$(( RETENTION_DAYS - 1 ))"d +%Y-%m-%d)"
    while IFS= read -r dir; do
        name="$(basename "$dir")"
        if [[ "${name:0:10}" < "$cutoff" ]]; then
            rm -rf "$dir"
            log "Pruned $name (keeping the last ${RETENTION_DAYS} days)"
        fi
    done < <(list_backups)

    local newest
    newest="$(list_backups | tail -n 1)"
    [[ -n "$newest" ]] || die "No backups found on the server yet."
    (( corrupt == 0 )) || die "${corrupt} backup(s) failed checksum verification."

    local summary="newest $(basename "$newest"), ${verified} new, $(list_backups | wc -l | tr -d ' ') kept, $(size_of "$LOCAL_DIR") total"
    if [[ "$(basename "$newest" | cut -c1-10)" < "$(date -v-2d +%Y-%m-%d)" ]]; then
        log "WARN: newest backup is more than 2 days old — check backup.sh on the VPS."
    fi
    log "OK (${summary})"
    ping_healthcheck "" "OK (${summary})"
}

case "${1:-}" in
    --install) install_job ;;
    --uninstall) uninstall_job ;;
    --status) show_status ;;
    "") pull ;;
    *) sed -n '2,7p' "$0" | sed 's/^# \{0,1\}//'; exit 1 ;;
esac
