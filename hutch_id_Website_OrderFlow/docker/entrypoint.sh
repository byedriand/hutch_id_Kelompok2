#!/bin/sh
# ============================================================
# entrypoint.sh — Hutch.id Production Startup
# Dijalankan setiap kali container hutch_app dimulai.
# ============================================================

set -e

echo "🚀 Starting Hutch.id backend..."

# ── 1. Tunggu MySQL siap ─────────────────────────────────────────────────
echo "⏳ Waiting for database..."
until php -r "
    \$retries = 0;
    while (\$retries < 30) {
        try {
            \$pdo = new PDO(
                'mysql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_DATABASE'),
                getenv('DB_USERNAME'),
                getenv('DB_PASSWORD')
            );
            echo 'ok';
            exit(0);
        } catch (Exception \$e) {
            \$retries++;
            sleep(2);
        }
    }
    exit(1);
" 2>/dev/null | grep -q "ok"; do
  echo "  Database not ready, retrying in 2s..."
  sleep 2
done
echo "✅ Database ready."

# ── 2. Migrate database ───────────────────────────────────────────────────
echo "🗄️  Running migrations..."
php artisan migrate --force

# ── 3. Storage symlink ────────────────────────────────────────────────────
echo "🔗 Creating storage symlink..."
php artisan storage:link --force 2>/dev/null || true

# ── 4. Cache Laravel config / routes / views ─────────────────────────────
echo "⚡ Caching config, routes & views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ── 5. Fix permissions ───────────────────────────────────────────────────
echo "🔒 Fixing permissions for storage and public directories..."
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/public
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/public

# ── 6. Optimasi autoloader ────────────────────────────────────────────────
echo "📦 Optimizing autoloader..."
composer dump-autoload --optimize --no-dev 2>/dev/null || true

echo "✅ Hutch.id startup complete. Starting PHP-FPM..."

# ── 6. Start PHP-FPM ─────────────────────────────────────────────────────
exec php-fpm
