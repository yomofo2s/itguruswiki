#!/bin/sh
# Build locally (or in CI) and deploy to Hetzner Webhosting over SSH (plans L/XL).
#
#   SSH_TARGET=itguru@www.itgurusgermany.com ./scripts/deploy.sh
#
# Hetzner Webhosting has no Node.js and usually no Composer, so dependencies and
# frontend assets are built here and uploaded ready-to-run.

set -eu

SSH_TARGET="${SSH_TARGET:?set SSH_TARGET=user@host}"
REMOTE_DIR="${REMOTE_DIR:-/usr/www/users/itguru/itguruswiki}"
REMOTE_PHP="${REMOTE_PHP:-php}"

cd "$(dirname "$0")/.."

echo "==> Building"
composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction --no-progress
npm ci --no-audit --no-fund
npm run build

echo "==> Uploading to $SSH_TARGET:$REMOTE_DIR"
ssh "$SSH_TARGET" "mkdir -p $REMOTE_DIR"
rsync -az --delete \
  --exclude='.git/' --exclude='.github/' --exclude='node_modules/' --exclude='tests/' \
  --exclude='.env' --exclude='.env.*' --exclude='database/*.sqlite' \
  --exclude='storage/app/public/*' --exclude='storage/app/private/*' \
  --exclude='storage/logs/*' --exclude='storage/framework/sessions/*' \
  --exclude='storage/framework/cache/data/*' --exclude='storage/framework/views/*' \
  --exclude='public/storage' --exclude='backups/' \
  ./ "$SSH_TARGET:$REMOTE_DIR/"

echo "==> Post-deploy"
ssh "$SSH_TARGET" "cd $REMOTE_DIR && PHP=$REMOTE_PHP sh scripts/post-deploy.sh"

echo "==> Done: https://www.itgurusgermany.com"
