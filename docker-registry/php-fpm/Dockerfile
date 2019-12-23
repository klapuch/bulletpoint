FROM php:7.4-fpm-alpine

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN apk update \
    && apk add --no-cache git postgresql-dev zip unzip make autoconf g++ zlib-dev libxslt-dev libgcrypt-dev libzip-dev bash file \
    && apk add --no-cache freetype freetype-dev libjpeg-turbo libjpeg-turbo-dev libpng libpng-dev \
    && docker-php-ext-install pdo_pgsql xsl zip gd sysvsem \
    && pecl install apcu igbinary redis xdebug-beta \
    && curl -sS https://getcomposer.org/installer | php \
        && mv composer.phar /usr/local/bin/composer \
    && rm -rf /var/cache/apk/*
