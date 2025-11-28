FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
        git \
        unzip \
        libpng-dev \
        libonig-dev \
        libxml2-dev \
    && docker-php-ext-install pdo_mysql gd mbstring

WORKDIR /var/www/html

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer


