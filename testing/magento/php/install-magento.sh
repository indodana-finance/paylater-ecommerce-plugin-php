#!/bin/bash
set -e

MAGENTO_VERSION="${MAGENTO_VERSION:-2.4.7-p10}"
BASE_URL="${BASE_URL:-http://localhost}"
ADMIN_USER="${ADMIN_USER:-admin}"
ADMIN_PASSWORD="${ADMIN_PASSWORD:-Admin123!}"
ADMIN_EMAIL="${ADMIN_EMAIL:-admin@example.com}"
ADMIN_FIRSTNAME="${ADMIN_FIRSTNAME:-Admin}"
ADMIN_LASTNAME="${ADMIN_LASTNAME:-User}"

SEARCH_ENGINE="${SEARCH_ENGINE:-opensearch}"
SEARCH_HOST="${SEARCH_HOST:-opensearch}"
SEARCH_PORT="${SEARCH_PORT:-9200}"
DB_HOST="${DB_HOST:-db}"
MYSQL_DATABASE="${MYSQL_DATABASE:-magento}"
MYSQL_USER="${MYSQL_USER:-magento}"
MYSQL_PASSWORD="${MYSQL_PASSWORD:-magento}"

export COMPOSER_HOME=/tmp/.composer
mkdir -p "$COMPOSER_HOME"
if [ -n "$COMPOSER_AUTH" ]; then
    echo "$COMPOSER_AUTH" > "$COMPOSER_HOME/auth.json"
fi

if [ ! -f /var/www/html/composer.json ]; then
    echo "============================================"
    echo "  Installing Magento ${MAGENTO_VERSION}"
    echo "  PHP $(php -v | head -1 | awk '{print $2}')"
    echo "  Search: ${SEARCH_ENGINE} @ ${SEARCH_HOST}:${SEARCH_PORT}"
    echo "  DB: ${DB_HOST}/${MYSQL_DATABASE}"
    echo "============================================"

    echo "==> Disabling composer security advisory blocking..."
    composer config --global audit.advisories false 2>/dev/null || true
    composer config --global policy.advisories.block false 2>/dev/null || true

    echo "==> Creating Magento project..."
    composer create-project \
        --repository-url=https://repo.magento.com/ \
        "magento/project-community-edition=${MAGENTO_VERSION}" \
        /var/www/html \
        --no-interaction \
        --no-audit

    # Ensure dependencies are installed (2.4.8+ may skip this)
    if [ ! -d /var/www/html/vendor/magento ]; then
        echo "==> Running composer install..."
        composer install --no-interaction --no-audit
    fi

    # Ensure app/code exists for custom modules
    mkdir -p /var/www/html/app/code

    # Build search engine flags based on engine type
    SEARCH_ARGS=""
    if [ "$SEARCH_ENGINE" = "opensearch" ]; then
        SEARCH_ARGS="--search-engine=opensearch --opensearch-host=${SEARCH_HOST} --opensearch-port=${SEARCH_PORT}"
    else
        SEARCH_ARGS="--search-engine=elasticsearch7 --elasticsearch-host=${SEARCH_HOST} --elasticsearch-port=${SEARCH_PORT}"
    fi

    echo "==> Running setup:install..."
    php bin/magento setup:install \
        --base-url="${BASE_URL}" \
        --db-host="${DB_HOST}" \
        --db-name="${MYSQL_DATABASE}" \
        --db-user="${MYSQL_USER}" \
        --db-password="${MYSQL_PASSWORD}" \
        --admin-firstname="${ADMIN_FIRSTNAME}" \
        --admin-lastname="${ADMIN_LASTNAME}" \
        --admin-email="${ADMIN_EMAIL}" \
        --admin-user="${ADMIN_USER}" \
        --admin-password="${ADMIN_PASSWORD}" \
        --backend-frontname=admin \
        --language=en_US \
        --currency=USD \
        --timezone=America/Chicago \
        --use-rewrites=1 \
        ${SEARCH_ARGS} \
        --session-save=redis \
        --session-save-redis-host=redis \
        --session-save-redis-port=6379 \
        --session-save-redis-db=2 \
        --cache-backend=redis \
        --cache-backend-redis-server=redis \
        --cache-backend-redis-port=6379 \
        --cache-backend-redis-db=0 \
        --page-cache=redis \
        --page-cache-redis-server=redis \
        --page-cache-redis-port=6379 \
        --page-cache-redis-db=1 \
        --amqp-host=rabbitmq \
        --amqp-port=5672 \
        --amqp-user="${RABBITMQ_USER:-magento}" \
        --amqp-password="${RABBITMQ_PASSWORD:-magento}"

    echo "==> Setting developer mode..."
    php bin/magento deploy:mode:set developer

    echo "==> Disabling Two Factor Auth for dev..."
    php bin/magento module:disable Magento_AdminAdobeImsTwoFactorAuth Magento_TwoFactorAuth 2>/dev/null || \
    php bin/magento module:disable Magento_TwoFactorAuth 2>/dev/null || true

    echo "==> Setting permissions..."
    chown -R www-data:www-data /var/www/html
    chmod -R 777 /var/www/html/var /var/www/html/generated /var/www/html/pub/static /var/www/html/pub/media

    echo "==> Compiling and deploying..."
    php bin/magento setup:upgrade
    php bin/magento setup:di:compile
    php bin/magento setup:static-content:deploy -f
    php bin/magento cache:flush

    chmod -R 777 /var/www/html/var /var/www/html/generated /var/www/html/pub/static /var/www/html/pub/media

    echo ""
    echo "============================================"
    echo "  Magento ${MAGENTO_VERSION} ready!"
    echo "  Storefront: ${BASE_URL}"
    echo "  Admin:      ${BASE_URL}/admin"
    echo "  User:       ${ADMIN_USER}"
    echo "  Password:   ${ADMIN_PASSWORD}"
    echo "============================================"
else
    echo "==> Magento already installed at /var/www/html, skipping."
    echo "    To reinstall: remove the magento volume and re-run."
fi
