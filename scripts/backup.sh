#!/bin/sh
# Nightly backup (GitHub issue #19) - run via konsoleH cron:
#   30 3 * * *  /bin/sh /usr/www/users/itguru/itguruswiki/scripts/backup.sh >> /usr/www/users/itguru/backups/backup.log 2>&1
#
# Creates db-<ts>.sql.gz (full MariaDB dump) and uploads-<ts>.tar.gz (images
# people uploaded), deletes backups older than RETENTION_DAYS, and optionally
# copies them off-site (Hetzner Storage Box) - keep at least one copy elsewhere!
#
# DB credentials come from an option file, never the command line:
#   /usr/www/users/itguru/backups/.my.cnf  (chmod 600)
#     [client]
#     host=sqlXXX.your-server.de
#     user=itguru_web
#     password=...

set -eu

APP_DIR="${APP_DIR:-/usr/www/users/itguru/itguruswiki}"
BACKUP_DIR="${BACKUP_DIR:-/usr/www/users/itguru/backups}"
DB_NAME="${DB_NAME:?set DB_NAME (the database name from konsoleH)}"
RETENTION_DAYS="${RETENTION_DAYS:-30}"
OFFSITE="${OFFSITE:-}" # e.g. u123456@u123456.your-storagebox.de:itgurus

TS="$(date +%Y%m%d-%H%M)"
umask 077
mkdir -p "$BACKUP_DIR"

mysqldump --defaults-extra-file="$BACKUP_DIR/.my.cnf" \
  --single-transaction --quick --routines --no-tablespaces \
  "$DB_NAME" | gzip -9 > "$BACKUP_DIR/db-$TS.sql.gz"

tar -czf "$BACKUP_DIR/uploads-$TS.tar.gz" -C "$APP_DIR/storage/app" public

# An empty dump means the backup failed - make the cron mail/log show it.
if [ "$(gzip -cd "$BACKUP_DIR/db-$TS.sql.gz" | head -c 2048 | wc -c)" -lt 500 ]; then
  echo "ERROR: database dump looks empty" >&2
  exit 1
fi

find "$BACKUP_DIR" -maxdepth 1 -type f \( -name 'db-*' -o -name 'uploads-*' \) -mtime +"$RETENTION_DAYS" -delete

if [ -n "$OFFSITE" ]; then
  scp -q -o BatchMode=yes "$BACKUP_DIR/db-$TS.sql.gz" "$BACKUP_DIR/uploads-$TS.tar.gz" "$OFFSITE/"
fi

echo "[$(date)] backup ok: db-$TS.sql.gz uploads-$TS.tar.gz"
