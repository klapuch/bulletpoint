#!/bin/sh
set -e

KEEP_RELEASES=10
REPOSITORY='git@github.com:klapuch/bulletpoint.git'
HOST='95.168.218.174'
USER='bulletpoint'
SHARED_DIR="/var/www/bulletpoint"
CURRENT_DIR="$SHARED_DIR/current"
RELEASES_DIR="$SHARED_DIR/releases"
RELEASE_DIR="$RELEASES_DIR/$TRAVIS_BUILD_NUMBER"

echo 'DIRS:CREATE'
ssh $USER@$HOST "mkdir -pv $RELEASE_DIR && mkdir -pv $SHARED_DIR/logs && mkdir -pv $SHARED_DIR/data"

echo 'SOURCE:CLONE'
ssh $USER@$HOST "git clone --branch=master $REPOSITORY $RELEASE_DIR && cd $RELEASE_DIR && git checkout -qf $TRAVIS_COMMIT"

echo 'DEV:TRASH:CLEAN'
ssh $USER@$HOST "
  ls -d $RELEASE_DIR/* | grep -v $RELEASE_DIR/backend | grep -v $RELEASE_DIR/frontend | xargs --verbose --no-run-if-empty rm -rf \
    && rm -rfv $RELEASE_DIR/.git \
    && rm -rfv $RELEASE_DIR/backend/.gitignore \
    && rm -rfv $RELEASE_DIR/backend/database/fixtures \
    && rm -rfv $RELEASE_DIR/backend/database/schema.sql \
    && rm -rfv $RELEASE_DIR/backend/logs \
    && rm -rfv $RELEASE_DIR/backend/phpstan.* \
    && rm -rfv $RELEASE_DIR/backend/psalm.xml \
    && rm -rfv $RELEASE_DIR/backend/README.md \
    && rm -rfv $RELEASE_DIR/backend/ruleset.xml \
    && rm -rfv $RELEASE_DIR/backend/Tests \
    && rm -rfv $RELEASE_DIR/frontend/.env.dev \
    && rm -rfv $RELEASE_DIR/frontend/.eslintignore \
    && rm -rfv $RELEASE_DIR/frontend/.eslintrc \
    && rm -rfv $RELEASE_DIR/frontend/.flowconfig \
    && rm -rfv $RELEASE_DIR/frontend/.gitignore \
    && rm -rfv $RELEASE_DIR/frontend/README.md
"

echo 'DIRS:SHARE'
ssh $USER@$HOST "
  ln -sfnv $SHARED_DIR/logs $RELEASE_DIR/backend/logs \
    && cp -v $RELEASE_DIR/backend/data/images/avatars/* $SHARED_DIR/data/images/avatars \
    && rm -rfv $RELEASE_DIR/backend/data \
    && ln -sfnv $SHARED_DIR/data $RELEASE_DIR/backend/data
"

echo 'COMPOSER:INSTALL'
ssh $USER@$HOST "cd $RELEASE_DIR/backend && composer install --no-dev --no-interaction --prefer-dist --no-scripts --no-progress --no-suggest --classmap-authoritative"

echo 'MIGRATIONS:RUN'
ssh $USER@$HOST "cd $RELEASE_DIR/backend && sh database/migrations/run-new.sh"

echo 'REACT:BUILD'
ssh $USER@$HOST "cd $RELEASE_DIR/frontend && yarn install && yarn run build"

echo 'SECRETS:PUT'
ssh $USER@$HOST "
  cp -v $SHARED_DIR/secrets.ini $RELEASE_DIR/backend/App/Configuration/secrets.ini \
    && cp -v $RELEASE_DIR/backend/App/Configuration/config.production.ini $RELEASE_DIR/backend/App/Configuration/config.env.ini
"

echo 'NGINX:CONFIG:MOVE'
ssh $USER@$HOST "
  cp -v $RELEASE_DIR/docker/nginx/api.prod.conf /etc/nginx/sites-enabled/api.conf \
    && cp -v $RELEASE_DIR/docker/nginx/frontend.prod.conf /etc/nginx/sites-enabled/frontend.conf \
    && cp -v $RELEASE_DIR/docker/nginx/static.prod.conf /etc/nginx/sites-enabled/static.conf \
    && cp -v $RELEASE_DIR/docker/nginx/nginx.prod.conf /etc/nginx/nginx.conf \
    && cp -v $RELEASE_DIR/docker/nginx/php.prod.conf /etc/nginx/php.conf \
    && cp -v $RELEASE_DIR/docker/nginx/preflight.conf /etc/nginx/preflight.conf \
    && cp -v $RELEASE_DIR/docker/nginx/preflight_headers.conf /etc/nginx/preflight_headers.conf \
    && cp -v $RELEASE_DIR/docker/nginx/routes.conf /etc/nginx/routes.conf \
    && cp -v $RELEASE_DIR/docker/nginx/security_headers.conf /etc/nginx/security_headers.conf \
    && cp -v $RELEASE_DIR/docker/nginx/letsencrypt.conf /etc/nginx/letsencrypt.conf
"

echo 'PHP:CONFIG:MOVE'
ssh $USER@$HOST "
  cp -v $RELEASE_DIR/docker/php-fpm/php.prod.ini /etc/php/7.3/fpm/php.ini \
    && cp -v $RELEASE_DIR/docker/php-fpm/php.prod.ini /etc/php/7.3/cli/php.ini
"

echo 'RELEASE'
ssh $USER@$HOST "ln -sfnv $RELEASE_DIR $CURRENT_DIR"

echo 'SERVICES:RELOAD'
ssh $USER@$HOST "sudo /usr/sbin/service nginx reload && sudo /usr/sbin/service php7.3-fpm reload"

echo 'CACHE:CLEAR'
ssh $USER@$HOST "redis-cli -p 6379 flushall"

echo 'RELEASES:CLEAN'
ssh $USER@$HOST "ls $RELEASES_DIR/* -A1td | tail -n +$KEEP_RELEASES | xargs --verbose --no-run-if-empty rm -rf"
