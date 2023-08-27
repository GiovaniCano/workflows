# [ Dev Dockerfile ]
FROM php:8.2.4-apache
    # this comes with almost all php extensions required by laravel (and its apt dependencies)
    # php -m
    # apt list --installed

WORKDIR /var/www/html

# [ Linux ]
RUN apt update
RUN apt install -y \
    # these are for composer
    unzip zip \
    # these are for gd
    zlib1g-dev libpng-dev libjpeg-dev libwebp-dev libgd-dev \
    # dev
    sudo vim less

COPY --from=composer /usr/bin/composer /usr/bin/composer

# [ Apache & PHP ]
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf \
    && a2enmod rewrite \
    && mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini" \
    && sed -i 's/^\s*max_execution_time\s*=.*/max_execution_time = 60/' "$PHP_INI_DIR/php.ini"
    
RUN docker-php-ext-configure \
    gd --enable-gd --with-freetype --with-jpeg --with-webp
RUN docker-php-ext-install \
    pdo_mysql bcmath gd
    # gd install and config is required by intervention/image and UploadedFile::fake()->image('photo1.jpg'),

# [ wsl dev working ] 
RUN useradd debian -s /bin/bash -d /home/debian -m -u 1000 -g 33 -G sudo \
    && echo 'debian:q' | chpasswd
USER debian

EXPOSE 80

# ----------------------------------
# sudo chmod -R 775 .
# sudo chown -R www-data:www-data .
# sudo usermod -a -G www-data debian

# docker exec laravel php artisan key:generate
# docker exec laravel php artisan migrate
# ----------------------------------