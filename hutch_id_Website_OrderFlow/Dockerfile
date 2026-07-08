# ============================================================
# Dockerfile — Hutch.id Production
# Base  : php:8.2-fpm
# Mode  : production (no dev dependencies, optimized autoloader)
# Assets: Vite build included
# ============================================================

# ── Stage 1: Build Vite assets ───────────────────────────────────────────
FROM node:20-alpine AS node_builder

WORKDIR /build

COPY package.json package-lock.json* ./
RUN npm ci 2>/dev/null || npm install

COPY vite.config.js ./
COPY resources/ ./resources/
COPY public/ ./public/

RUN npm run build


# ── Stage 2: PHP-FPM production image ───────────────────────────────────
FROM php:8.2-fpm

# System deps
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip git curl \
    libonig-dev libpng-dev libxml2-dev \
    libjpeg-dev libfreetype6-dev \
 && docker-php-ext-configure gd --with-jpeg --with-freetype \
 && docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl bcmath gd opcache \
 && rm -rf /var/lib/apt/lists/*

# PHP production config
RUN { \
    echo "upload_max_filesize = 12M"; \
    echo "post_max_size = 15M"; \
    echo "memory_limit = 256M"; \
    echo "max_execution_time = 120"; \
    } > /usr/local/etc/php/conf.d/uploads.ini

# OPcache for production performance
RUN { \
    echo "opcache.enable=1"; \
    echo "opcache.memory_consumption=128"; \
    echo "opcache.interned_strings_buffer=8"; \
    echo "opcache.max_accelerated_files=10000"; \
    echo "opcache.revalidate_freq=0"; \
    echo "opcache.validate_timestamps=0"; \
    } > /usr/local/etc/php/conf.d/opcache.ini

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy application source
COPY . /var/www/html

# Copy compiled Vite assets from stage 1
COPY --from=node_builder /build/public/build ./public/build

# Install PHP dependencies (production only, no dev)
RUN composer install \
    --no-interaction \
    --no-dev \
    --prefer-dist \
    --optimize-autoloader

# Create required directories & fix permissions
RUN mkdir -p \
    storage/framework/sessions \
    storage/framework/views \
    storage/framework/cache \
    storage/logs \
    bootstrap/cache \
 && chown -R www-data:www-data \
    storage \
    bootstrap/cache \
 && chmod -R 775 \
    storage \
    bootstrap/cache

# Copy entrypoint
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["/entrypoint.sh"]
