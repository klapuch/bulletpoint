FROM php:7.3-fpm-alpine

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN apk update \
    && apk add --no-cache git postgresql-dev zip unzip make autoconf g++ zlib-dev libxslt-dev libzip-dev bash \
    && apk add --no-cache freetype freetype-dev libjpeg-turbo libjpeg-turbo-dev libpng libpng-dev \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ --with-png-dir=/usr/include/ \
    && docker-php-ext-install pdo_pgsql zip xsl gd \
    && pecl install apcu igbinary redis xdebug-beta \
    && curl -sS https://getcomposer.org/installer | php \
        && mv composer.phar /usr/local/bin/composer \
    && rm -rf /var/cache/apk/*

COPY sendmail /usr/sbin/fake-sendmail
COPY php.dev.ini /usr/local/etc/php/php.ini
COPY .bash_aliases /root/.bash_aliases
COPY .bash_history /root/.bash_history

RUN echo '. /root/.bash_aliases' >> ~/.bashrc
