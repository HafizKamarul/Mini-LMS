#!/usr/bin/env bash
set -euo pipefail

echo "[render-deploy] Installing PHP dependencies"
composer install --no-dev --optimize-autoloader --no-interaction

echo "[render-deploy] Installing Node dependencies and building assets"
npm ci
npm run build

echo "[render-deploy] Running Laravel deployment optimizations"
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "[render-deploy] Running migrations"
php artisan migrate --force

echo "[render-deploy] Ensuring storage symlink exists"
php artisan storage:link || true

echo "[render-deploy] Deployment preparation complete"
