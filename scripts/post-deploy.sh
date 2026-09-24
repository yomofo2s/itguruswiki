#!/bin/sh
# Runs ON the server after files were uploaded (called by deploy.sh).
# Without SSH (Webhosting S/M) run it once via the konsoleH cron manager:
#   /bin/sh /usr/www/users/itguru/itguruswiki/scripts/post-deploy.sh
set -eu

PHP="${PHP:-php}"
cd "$(dirname "$0")/.."

if [ ! -f .env ]; then
  echo "ERROR: .env missing in $(pwd) - see docs/HOSTING.md" >&2
  exit 1
fi

$PHP artisan down --retry=30 || true
trap '$PHP artisan up' EXIT
$PHP artisan migrate --force
$PHP artisan db:seed --class=CategorySeeder --force
[ -e public/storage ] || $PHP artisan storage:link
$PHP artisan optimize:clear
$PHP artisan optimize

echo "post-deploy ok"
