#!/usr/bin/env bash
set -e
cd "$DEPLOY_PATH"
php artisan down || true
composer install --no-dev --prefer-dist --optimize-autoloader
php artisan migrate --force
php artisan optimize
php artisan queue:restart
php artisan l5-swagger:generate
php artisan up
