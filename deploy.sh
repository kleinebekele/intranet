#!/usr/bin/env bash
export PATH="/opt/plesk/php/8.3/bin:$PATH"
#
# Deploy-Skript für das Intranet (Plesk / Laravel).
#
# Erstes Mal:
#   1) Repo klonen:  git clone https://github.com/kleinebekele/intranet.git intranet
#   2) cd intranet && cp .env.example .env  (DB-Zugang eintragen) && php artisan key:generate
#   3) chmod +x deploy.sh
#
# Danach bei jedem Update einfach:  ./deploy.sh
#
set -euo pipefail

cd "$(dirname "$0")"

echo "==> Wartungsmodus aktivieren"
php artisan down --render="errors::503" || true
trap 'php artisan up || true' EXIT

echo "==> Aktuellen Stand aus Git holen (main)"
git pull origin main

echo "==> PHP-Abhängigkeiten installieren"
composer install --no-dev --optimize-autoloader --no-interaction

echo "==> Datenbank-Migrationen ausführen"
php artisan migrate --force

if [ -f package.json ]; then
    echo "==> Frontend-Assets bauen"
    npm ci
    npm run build
fi

echo "==> Storage-Link sicherstellen"
php artisan storage:link || true

echo "==> Caches neu aufbauen"
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Wartungsmodus deaktivieren"
php artisan up
trap - EXIT

echo "==> Fertig. Aktueller Stand ist live."
