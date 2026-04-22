#!/usr/bin/env bash
set -euo pipefail

echo "[render-start] Disabling Vite hot mode in production"
rm -f public/hot

echo "[render-start] Running migrations"
php artisan migrate --force

echo "[render-start] Seeding default users"
php artisan db:seed --force

echo "[render-start] Ensuring storage symlink exists"
php artisan storage:link || true

echo "[render-start] Running Laravel cache optimization"
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "[render-start] Starting Apache"
apache2-foreground
