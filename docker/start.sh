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

php artisan migrate --force

exec php artisan serve --host=0.0.0.0 --port="${PORT:-10000}"
