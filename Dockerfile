FROM php:8.2.4-apache

WORKDIR /var/www/html

# [ Linux ]
RUN apt update && \
    apt upgrade -y && \
    apt install -y \
    unzip zip \
    zlib1g-dev libpng-dev libjpeg-dev libwebp-dev libgd-dev

COPY . .
COPY --from=composer /usr/bin/composer /usr/bin/composer

# [ Apache & PHP ]
RUN sed -ri -e 's!Listen 80!Listen 8080!g' /etc/apache2/ports.conf \
    && sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!\*:80!\*:8080!g' /etc/apache2/sites-available/*.conf \
    && a2enmod rewrite \
    && mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && sed -i 's/^\s*max_execution_time\s*=.*/max_execution_time = 60/' "$PHP_INI_DIR/php.ini" \
    && docker-php-ext-install pdo_mysql bcmath \
    && composer install \
        --no-interaction \
        --no-dev \
        --optimize-autoloader \
        --no-progress \
        --no-scripts \
        --no-plugins \
        --no-ansi \
    && php artisan view:cache \
    && php artisan route:cache \
    && php artisan event:cache \
    && chown -R www-data:www-data .

# fly.io needs 8080 here and in apache (above this)
EXPOSE 8080