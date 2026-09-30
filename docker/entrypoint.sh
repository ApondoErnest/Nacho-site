#!/bin/sh
set -e

cd /var/www/html

if [ ! -f .env ] && [ -f .env.docker.example ]; then
    cp .env.docker.example .env
fi

KEY_FILE="/var/www/html/storage/.app_key"

is_valid_app_key() {
    case "${1:-}" in
        base64:*) return 0 ;;
        *) return 1 ;;
    esac
}

if is_valid_app_key "${APP_KEY:-}"; then
    : # Explicit APP_KEY from the container environment.
else
    stored=""
    if [ -f "${KEY_FILE}" ]; then
        stored="$(tr -d '\n\r' < "${KEY_FILE}")"
    fi

    if is_valid_app_key "${stored}"; then
        APP_KEY="${stored}"
    elif grep -q '^APP_KEY=base64:' .env 2>/dev/null; then
        APP_KEY="$(grep '^APP_KEY=' .env | head -n1 | cut -d= -f2- | tr -d '\n\r')"
    else
        if ! grep -q '^APP_KEY=' .env 2>/dev/null; then
            echo 'APP_KEY=' >> .env
        fi
        php artisan key:generate --force --no-interaction
        APP_KEY="$(grep '^APP_KEY=' .env | head -n1 | cut -d= -f2- | tr -d '\n\r')"
    fi

    if ! is_valid_app_key "${APP_KEY}"; then
        echo "ERROR: Could not configure APP_KEY for Laravel." >&2
        exit 1
    fi

    export APP_KEY
    printf '%s' "${APP_KEY}" > "${KEY_FILE}"
fi

grep -v '^APP_KEY=' .env > .env.tmp 2>/dev/null || cp .env .env.tmp
echo "APP_KEY=${APP_KEY}" >> .env.tmp
mv .env.tmp .env

echo "Waiting for MySQL at ${DB_HOST}:${DB_PORT}..."
until php -r "
    try {
        new PDO(
            'mysql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT'),
            getenv('DB_USERNAME'),
            getenv('DB_PASSWORD')
        );
        exit(0);
    } catch (Throwable \$e) {
        exit(1);
    }
" 2>/dev/null; do
    sleep 2
done

php artisan config:clear --no-interaction
php artisan migrate --force --no-interaction

if [ "${RUN_DB_SEED:-false}" = "true" ]; then
    php artisan db:seed --force --no-interaction
fi

php artisan storage:link --force --no-interaction 2>/dev/null || true

chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

exec "$@"
