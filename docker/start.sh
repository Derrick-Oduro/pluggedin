#!/usr/bin/env bash
set -euo pipefail

export APP_URL="${APP_URL:-${RENDER_EXTERNAL_URL:-http://localhost}}"

mkdir -p \
  storage/app/public \
  storage/framework/cache \
  storage/framework/sessions \
  storage/framework/views \
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

  # Optionally run seeders after migrations (set RUN_SEEDERS_ON_BOOT=true)
  if [[ "${RUN_SEEDERS_ON_BOOT:-false}" == "true" ]]; then
    echo "[startup] Running seeders as RUN_SEEDERS_ON_BOOT is true."
    if php artisan db:seed --force; then
      echo "[startup] Database seeding completed."
    else
      echo "[startup] Database seeding failed; continuing startup."
    fi
  fi
fi

exec php artisan serve --host=0.0.0.0 --port="${PORT:-10000}"
