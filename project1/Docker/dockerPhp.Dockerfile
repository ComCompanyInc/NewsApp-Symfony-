FROM php:8.1.18-fpm-alpine

WORKDIR /var/www

RUN apk add --no-cache unzip curl bash freetype libzip-dev gmp gmp-dev libpq-dev icu-dev \
    nginx autoconf libpng tzdata gettext libjpeg-turbo freetype-dev libpng-dev libjpeg-turbo-dev && \
  docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg \
  NPROC=$(grep -c ^processor /proc/cpuinfo 2>/dev/null || 1) && \
  docker-php-ext-install -j$(nproc) gd && \
  apk del --no-cache freetype-dev libpng-dev libjpeg-turbo-dev

RUN docker-php-ext-install zip gmp pdo pdo_pgsql intl opcache
RUN apk add git

# Устанавливаем Composer и выполняем устанвоку зависимостей 
RUN php -r "copy('https://getcomposer.org/installer', '/tmp/composer-setup.php');" \
                && php /tmp/composer-setup.php --install-dir=/usr/bin --filename=composer \
                && rm /tmp/composer-setup.php
