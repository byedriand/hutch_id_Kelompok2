FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip git curl libonig-dev libpng-dev libxml2-dev libjpeg-dev libfreetype6-dev \
 && docker-php-ext-configure gd --with-jpeg --with-freetype \
 && docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl bcmath gd \
 && rm -rf /var/lib/apt/lists/*

# Naikkan limit upload PHP (default php:8.2-fpm cuma 2MB/8MB,
# jauh di bawah kebutuhan foto produk dari kamera HP).
RUN { \
    echo "upload_max_filesize = 12M"; \
    echo "post_max_size = 15M"; \
    echo "memory_limit = 256M"; \
    } > /usr/local/etc/php/conf.d/uploads.ini

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . /var/www/html

RUN composer install --no-interaction --prefer-dist --optimize-autoloader || true

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true

EXPOSE 9000

CMD ["php-fpm"]
