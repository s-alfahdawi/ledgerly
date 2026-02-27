#!/bin/bash
# ─────────────────────────────────────────────────
# Ledgerly Database Backup Script
#
# Usage:
#   ./docker/backup-db.sh
#
# Schedule via cron (daily at 2 AM):
#   0 2 * * * /path/to/ledgerly/docker/backup-db.sh >> /var/log/ledgerly-backup.log 2>&1
#
# Environment variables (from .env or export):
#   DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD
# ─────────────────────────────────────────────────

set -euo pipefail

# Load .env if it exists
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
APP_DIR="$(dirname "$SCRIPT_DIR")"

if [ -f "$APP_DIR/.env" ]; then
    export $(grep -v '^#' "$APP_DIR/.env" | grep -E '^DB_' | xargs)
fi

# Configuration
BACKUP_DIR="${BACKUP_DIR:-$APP_DIR/storage/backups}"
RETENTION_DAYS="${RETENTION_DAYS:-30}"
DATE=$(date +%Y-%m-%d_%H%M%S)
BACKUP_FILE="$BACKUP_DIR/ledgerly_${DB_DATABASE}_$DATE.sql.gz"

# Ensure backup directory exists
mkdir -p "$BACKUP_DIR"

echo "[$(date)] Starting backup of database: $DB_DATABASE"

# Perform backup
mysqldump \
    --host="${DB_HOST:-127.0.0.1}" \
    --port="${DB_PORT:-3306}" \
    --user="${DB_USERNAME}" \
    --password="${DB_PASSWORD}" \
    --single-transaction \
    --routines \
    --triggers \
    --quick \
    "$DB_DATABASE" | gzip > "$BACKUP_FILE"

# Verify backup
if [ -s "$BACKUP_FILE" ]; then
    SIZE=$(du -h "$BACKUP_FILE" | cut -f1)
    echo "[$(date)] Backup complete: $BACKUP_FILE ($SIZE)"
else
    echo "[$(date)] ERROR: Backup file is empty!"
    rm -f "$BACKUP_FILE"
    exit 1
fi

# Cleanup old backups
if [ "$RETENTION_DAYS" -gt 0 ]; then
    DELETED=$(find "$BACKUP_DIR" -name "ledgerly_*.sql.gz" -mtime +$RETENTION_DAYS -delete -print | wc -l)
    echo "[$(date)] Cleaned up $DELETED backup(s) older than $RETENTION_DAYS days"
fi

echo "[$(date)] Backup finished successfully"
