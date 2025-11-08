#!/usr/bin/env bash
set -e
cd "$DEPLOY_PATH"
php artisan down || true
composer install --no-dev --prefer-dist --optimize-autoloader
# Build front-end assets if Node is available
if command -v npm >/dev/null 2>&1; then
    npm ci
    npm run build
fi
php artisan optimize:clear
php artisan vendor:publish --tag=livewire:assets --force --no-interaction
php artisan migrate --force
php artisan optimize
php artisan queue:restart
php artisan l5-swagger:generate
php artisan up
