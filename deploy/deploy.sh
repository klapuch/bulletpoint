#!/bin/sh
set -eu

KEEP_RELEASES=10
REPOSITORY='https://github.com/klapuch/bulletpoint.git'
HOST='95.168.218.174'
USER='bulletpoint'
SHARED_DIR="/var/www/bulletpoint"
CURRENT_DIR="$SHARED_DIR/current"
RELEASES_DIR="$SHARED_DIR/releases"
RELEASE_DIR="$RELEASES_DIR/$TRAVIS_BUILD_NUMBER"

echo 'DIRS:CREATE'
ssh $USER@$HOST "mkdir -pv $RELEASE_DIR && mkdir -m 0766 $SHARED_DIR/logs -pv && mkdir -m 0766 $SHARED_DIR/data -pv"

echo 'SOURCE:CLONE'
ssh $USER@$HOST "git clone --branch=master $REPOSITORY $RELEASE_DIR && cd $RELEASE_DIR && git checkout -qf $TRAVIS_COMMIT"

echo 'DIRS:SHARE'
ssh $USER@$HOST "
  rm -rfv $RELEASE_DIR/backend/logs \
    && ln -sfnv $SHARED_DIR/logs $RELEASE_DIR/backend/logs \
    && mkdir -m 0766 $SHARED_DIR/data/images/avatars -pv \
    && cp -v $RELEASE_DIR/backend/data/images/avatars/* $SHARED_DIR/data/images/avatars \
    && rm -rfv $RELEASE_DIR/backend/data \
    && ln -sfnv $SHARED_DIR/data $RELEASE_DIR/backend/data \
"

# Make directories immutable
# sudo chattr +i $SHARED_DIR/data $SHARED_DIR/data/images $SHARED_DIR/data/images/avatars

echo 'COMPOSER:INSTALL'
ssh $USER@$HOST "cd $RELEASE_DIR/backend && composer install --no-dev --no-interaction --prefer-dist --no-scripts --no-progress --no-suggest --classmap-authoritative"

echo 'MIGRATIONS:RUN'
ssh $USER@$HOST "cd $RELEASE_DIR/backend && sh database/migrations/run-new.sh"

echo 'REACT:BUILD'
ssh $USER@$HOST "cd $RELEASE_DIR/frontend && yarn install && yarn run build"

echo 'SECRETS:PUT'
ssh $USER@$HOST "
  rm -rfv $RELEASE_DIR/backend/App/Configuration/secrets.ini \
    && ln -sfnv $SHARED_DIR/secrets.ini $RELEASE_DIR/backend/App/Configuration/secrets.ini \
    && mv -v $RELEASE_DIR/backend/App/Configuration/config.production.ini $RELEASE_DIR/backend/App/Configuration/config.env.ini \
"

echo 'NGINX:CONFIG:MOVE'
ssh $USER@$HOST "
  cp -v $RELEASE_DIR/docker/nginx/sites-enabled/api.prod.conf /etc/nginx/sites-enabled/api.conf \
    && cp -v $RELEASE_DIR/docker/nginx/sites-enabled/frontend.prod.conf /etc/nginx/sites-enabled/frontend.conf \
    && cp -v $RELEASE_DIR/docker/nginx/sites-enabled/static.prod.conf /etc/nginx/sites-enabled/static.conf \
    && cp -v $RELEASE_DIR/docker/nginx/nginx.prod.conf /etc/nginx/nginx.conf \
    && cp -v $RELEASE_DIR/docker/nginx/snippets/php.prod.conf /etc/nginx/snippets/php.conf \
    && cp -v $RELEASE_DIR/docker/nginx/snippets/preflight.conf /etc/nginx/snippets/preflight.conf \
    && cp -v $RELEASE_DIR/docker/nginx/snippets/preflight_headers.conf /etc/nginx/snippets/preflight_headers.conf \
    && cp -v $RELEASE_DIR/docker/nginx/snippets/routes.conf /etc/nginx/snippets/routes.conf \
    && cp -v $RELEASE_DIR/docker/nginx/snippets/security_headers.conf /etc/nginx/snippets/security_headers.conf \
    && cp -v $RELEASE_DIR/docker/nginx/snippets/letsencrypt.conf /etc/nginx/snippets/letsencrypt.conf \
"

echo 'PHP:CONFIG:MOVE'
ssh $USER@$HOST "
  cp -v $RELEASE_DIR/docker/php-fpm/php.prod.ini /etc/php/7.3/fpm/php.ini \
    && cp -v $RELEASE_DIR/docker/php-fpm/php.prod.ini /etc/php/7.3/cli/php.ini \
"

echo 'TRASH:CLEAN'
ssh $USER@$HOST "
  ls -d $RELEASE_DIR/* | grep -v $RELEASE_DIR/backend | grep -v $RELEASE_DIR/frontend | xargs --verbose --no-run-if-empty rm -rf \
    && ls -d $RELEASE_DIR/frontend/* | grep -v $RELEASE_DIR/frontend/build | xargs --verbose --no-run-if-empty rm -rf \
    && ls -d $RELEASE_DIR/backend/* \
      | grep -v $RELEASE_DIR/backend/App \
      | grep -v $RELEASE_DIR/backend/App \
      | grep -v $RELEASE_DIR/backend/data$ \
      | grep -v $RELEASE_DIR/backend/logs \
      | grep -v $RELEASE_DIR/backend/temp \
      | grep -v $RELEASE_DIR/backend/Makefile \
      | grep -v $RELEASE_DIR/backend/vendor \
      | grep -v $RELEASE_DIR/backend/www \
    | xargs --verbose --no-run-if-empty rm -rf \
    && rm -rfv $RELEASE_DIR/.git \
    && rm -rfv $RELEASE_DIR/.travis.yml \
    && rm -rfv $RELEASE_DIR/backend/.gitignore \
    && rm -rfv $RELEASE_DIR/backend/App/Configuration/secrets.sample.ini \
    && rm -rfv $RELEASE_DIR/frontend/.babelrc \
    && rm -rfv $RELEASE_DIR/frontend/.env-cmdrc \
    && rm -rfv $RELEASE_DIR/frontend/.eslintignore \
    && rm -rfv $RELEASE_DIR/frontend/.eslintrc \
    && rm -rfv $RELEASE_DIR/frontend/.flowconfig \
    && rm -rfv $RELEASE_DIR/frontend/.gitignore \
"

echo 'RELEASE'
ssh $USER@$HOST "ln -sfnv $RELEASE_DIR $CURRENT_DIR"

echo 'SERVICES:RELOAD'
ssh $USER@$HOST "sudo /usr/sbin/service nginx reload && sudo /usr/sbin/service php7.3-fpm reload"

echo 'CACHE:CLEAR'
ssh $USER@$HOST "redis-cli -p 6379 flushall"

echo 'RELEASES:CLEAN'
ssh $USER@$HOST "ls $RELEASES_DIR/* -A1td | tail -n +$KEEP_RELEASES | xargs --verbose --no-run-if-empty rm -rf"

echo '[OK] Deploy was successful.'
