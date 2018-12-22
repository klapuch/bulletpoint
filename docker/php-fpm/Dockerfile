FROM php:7.3-fpm-alpine

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN apk update \
    && apk add --no-cache git postgresql-dev zip unzip make autoconf g++ zlib-dev libxslt-dev libzip-dev \
    && docker-php-ext-install pdo_pgsql zip xsl \
    && pecl install apcu igbinary redis xdebug-beta \
    && curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer

RUN apk add --update bash
RUN rm -rf /var/cache/apk/*

COPY sendmail /usr/sbin/fake-sendmail
COPY php.ini /usr/local/etc/php/php.ini
COPY .bash_aliases /root/.bash_aliases
COPY .bash_history /root/.bash_history

RUN echo '. /root/.bash_aliases' >> ~/.bashrc
