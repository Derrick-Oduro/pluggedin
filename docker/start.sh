#!/usr/bin/env bash
set -euo pipefail

export APP_URL="${APP_URL:-${RENDER_EXTERNAL_URL:-http://localhost}}"

mkdir -p \
  storage/app/public \
  storage/framework/cache \
  storage/framework/sessions \
  storage/framework/views \
  storage/logs \
  bootstrap/cache

php artisan storage:link || true

if [[ "${CONTAINER_ROLE:-web}" == "worker" ]]; then
  exec php artisan queue:work --sleep=3 --tries=1 --timeout=90
fi

if [[ "${RUN_MIGRATIONS_ON_BOOT:-true}" == "true" ]]; then
  attempts="${DB_MIGRATE_ATTEMPTS:-15}"
  delay="${DB_MIGRATE_DELAY_SECONDS:-4}"

  if [[ "${DB_CONNECTION:-}" == "pgsql" ]] && [[ -z "${DB_URL:-}" ]] && [[ "${DB_HOST:-127.0.0.1}" =~ ^(127\.0\.0\.1|localhost)$ ]]; then
    echo "[startup] DB_URL is not set for pgsql; skipping migrations to avoid boot failure."
  else
    migrated=0
    for ((i=1; i<=attempts; i++)); do
      if php artisan migrate --force; then
        migrated=1
        break
      fi

      echo "[startup] Migration attempt ${i}/${attempts} failed; retrying in ${delay}s..."
      sleep "${delay}"
    done

    if [[ "${migrated}" -ne 1 ]]; then
      echo "[startup] Migrations failed after ${attempts} attempts; continuing startup."
    fi
  fi

  # Seeders only add missing reference data (roles, categories, services). They never
  # create login accounts or overwrite existing records in production, so they're safe
  # to leave on; set RUN_SEEDERS_ON_BOOT=false to skip them entirely.
  if [[ "${RUN_SEEDERS_ON_BOOT:-false}" == "true" ]]; then
    echo "[startup] Seeding reference data."
    php artisan db:seed --force || echo "[startup] Seeding failed; continuing startup."
  fi
fi

# Cache config, routes, views and events for speed. Must run after env vars are set.
php artisan optimize || echo "[startup] optimize failed; continuing without caches."

# Laravel scheduler (expires unpaid orders, etc.). Runs every minute in the background.
if [[ "${RUN_SCHEDULER:-true}" == "true" ]]; then
  (
    while true; do
      php artisan schedule:run --no-interaction >> /dev/stdout 2>&1 || true
      sleep 60
    done
  ) &
fi

# Production web server: FrankenPHP serving the public/ directory with Laravel's front controller.
exec frankenphp php-server --root public/ --listen ":${PORT:-10000}"
