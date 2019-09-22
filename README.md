# Bulletpoint

[![Hits-of-Code](https://hitsofcode.com/github/klapuch/bulletpoint)](https://hitsofcode.com/view/github/klapuch/bulletpoint)

[![Build Status](https://travis-ci.org/klapuch/bulletpoint.svg?branch=master)](https://travis-ci.org/klapuch/bulletpoint)
[![codecov](https://codecov.io/gh/klapuch/bulletpoint/branch/master/graph/badge.svg)](https://codecov.io/gh/klapuch/bulletpoint)
[![PHPStan](https://img.shields.io/badge/PHPStan-enabled-brightgreen.svg?style=flat)](https://github.com/phpstan/phpstan)

[![CircleCI](https://circleci.com/gh/klapuch/bulletpoint/tree/master.svg?style=svg)](https://circleci.com/gh/klapuch/bulletpoint/tree/master)

## Production installation

### Create user and give him sudo
```
apt-get update && apt-get install sudo 
adduser bulletpoint
usermod -a -G sudo bulletpoint
```

### Install common packages

`sudo apt-get install curl apt-transport-https ca-certificates vim gnupg software-properties-common make git sudo zip unzip htop fail2ban`

### nginx

#### Installation
```
curl https://nginx.org/keys/nginx_signing.key -O nginx_signing.key
apt-key add nginx_signing.key
rm nginx_signing.key

vim /etc/apt/sources.list
deb http://nginx.org/packages/debian/ stretch nginx
deb-src http://nginx.org/packages/debian/ stretch nginx

apt-get update
apt-get install nginx
apt-get install nginx-module-image-filter
```

### PHP

#### Installation
```
wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg
sh -c 'echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list'
apt-get update
apt-get install php7.3-fpm php7.3-xsl php7.3-curl php7.3-mbstring php7.3-zip php7.3-pgsql php7.3-gd
apt-get install php-apcu php-redis php-igbinary
```

#### Composer
```
curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer
```

#### Permissions
```
vim /etc/php/7.3/fpm/pool.d/www.conf
user = bulletpoint
group = bulletpoint
listen.owner = bulletpoint
listen.group = bulletpoint
listen = 9000
```



### PostgreSQL 11

#### Installation
```
wget --quiet -O - https://www.postgresql.org/media/keys/ACCC4CF8.asc | apt-key add -
RELEASE=$(lsb_release -cs)
echo "deb http://apt.postgresql.org/pub/repos/apt/ ${RELEASE}"-pgdg main | tee /etc/apt/sources.list.d/pgdg.list
apt-get update
apt install postgresql-11
```


##### Locales
`sudo dpkg-reconfigure locales`

```
sudo -u postgres pg_dropcluster 11 main --stop
sudo -u postgres pg_createcluster -d /var/lib/postgresql/11/main -p 5432 11 main --locale=C.UTF-8 --start --start-conf=auto -- --data-checksums
```

```
CREATE COLLATION "cs_CZ" (LOCALE = 'cs_CZ.utf8');
ALTER COLLATION "cs_CZ" OWNER TO postgres;
ALTER COLLATION "cs_CZ" SET SCHEMA pg_catalog;

CREATE COLLATION "cs_CZ.utf8" (LOCALE = 'cs_CZ.utf8');
ALTER COLLATION "cs_CZ.utf8" OWNER TO postgres;
ALTER COLLATION "cs_CZ.utf8" SET SCHEMA pg_catalog;
```

`sudo /etc/init.d postgresql restart`

#### Create PostgreSQL user
```
sudo -u postgres createuser bulletpoint --superuser --pwprompt
sudo -u postgres createdb bulletpoint
```

#### Create `.pgpass` file
```
vim ~/.pgpass
localhost:5432:bulletpoint:bulletpoint:PASSWORD

sudo chmod 600 ~/.pgpass

sudo vim ~/.profile
export PGPASSFILE=~/.pgpass

source ~/.profile
```

### NodeJS
```
curl -sL https://deb.nodesource.com/setup_10.x | bash -
apt-get install -y nodejs
curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | sudo apt-key add -
echo "deb https://dl.yarnpkg.com/debian/ stable main" | sudo tee /etc/apt/sources.list.d/yarn.list
sudo apt-get update && sudo apt-get install yarn
```

### Redis

#### Installation
```
apt-get install redis-server
service redis-server stop
```

#### Setting up CACHE
```
cp /etc/redis/redis.conf /etc/redis/redis-cache.conf
chown redis.redis /etc/redis/redis-cache.conf
```

```
vim /etc/redis/redis-cache.conf
pidfile /var/run/redis/redis-server-cache.pid
port 6379
dbfilename dump-cache.rdb
maxmemory 500mb
```

```
cp /etc/init.d/redis-server /etc/init.d/redis-server-cache
```

```
vim /etc/init.d/redis-server-cache
NAME=redis-server-cache
DESC=redis-server-cache
DAEMON_ARGS=/etc/redis/redis-cache.conf
PIDFILE=$RUNDIR/redis-server-cache.pid
```

```
cp /lib/systemd/system/redis-server.service /lib/systemd/system/redis-server-cache.service
```

```
vim /lib/systemd/system/redis-server-cache.service
ExecStart=/usr/bin/redis-server /etc/redis/redis-cache.conf
PIDFile=/var/run/redis/redis-server-cache.pid
Alias=redis.service-cache
```

#### Setting up SESSION
```
cp /etc/redis/redis.conf /etc/redis/redis-session.conf
chown redis.redis /etc/redis/redis-session.conf
```

```
vim /etc/redis/redis-session.conf
pidfile /var/run/redis/redis-server-session.pid
port 6380
dbfilename dump-session.rdb
maxmemory 500mb
```

```
cp /etc/init.d/redis-server /etc/init.d/redis-server-session
```

```
vim /etc/init.d/redis-server-session
NAME=redis-server-session
DESC=redis-server-session
DAEMON_ARGS=/etc/redis/redis-session.conf
PIDFILE=$RUNDIR/redis-server-session.pid
```

```
cp /lib/systemd/system/redis-server.service /lib/systemd/system/redis-server-session.service
```

```
vim /lib/systemd/system/redis-server-session.service
ExecStart=/usr/bin/redis-server /etc/redis/redis-session.conf
PIDFile=/var/run/redis/redis-server-session.pid
Alias=redis.service-session
```

#### Remove original files
```
rm /etc/redis/redis.conf
rm /etc/init.d/redis-server
rm /lib/systemd/system/redis-server.service
```

#### Enable and start
```
systemctl enable redis-server-cache.service
systemctl enable redis-server-session.service

service redis-server-cache restart
service redis-server-session restart
```

### SSH
```
sudo vim /etc/ssh/sshd_config
PermitRootLogin no
PasswordAuthentication no
PermitEmptyPasswords no
```

### Let's encrypt
```
sudo vim /etc/dehydrated/conf.d/config.sh

CA="https://acme-staging.api.letsencrypt.org/directory" # as staging
BASEDIR="/etc/letsencrypt"
WELLKNOWN="/data/www/letsencrypt"
CONTACT_EMAIL="klapuchdominik@gmail.com"
# afters tests - remove accounts and certs !!
```

#### Create directories
```
sudo mkdir /etc/letsencrypt
mkdir /data/www/letsencrypt
```

#### Domains
```
sudo vim /etc/dehydrated/domains.txt
api.bulletpoint.cz
static.bulletpoint.cz
www.bulletpoint.cz
```

### Deploy

#### From your computer
```
ssh-copy-id -i ~/.ssh/id_rsa.pub bulletpoint@95.168.218.174

ssh-keygen -t rsa -b 4096 -C 'build.bulletpoint@travis-ci.org' -f ./id_rsa
travis encrypt-file id_rsa --add
ssh-copy-id -i id_rsa.pub bulletpoint@95.168.218.174

rm -f id_rsa id_rsa.pub
```

#### From server:
```
ssh-keyscan -H 140.82.118.3 >> ~/.ssh/known_hosts
ssh-keyscan -H github.com >> ~/.ssh/known_hosts
```

#### Sudo
```
sudo visudo
bulletpoint ALL=(ALL) NOPASSWD: /usr/sbin/service nginx reload,/usr/sbin/service php7.3-fpm reload
```

```
sudo chown bulletpoint:bulletpoint /etc/nginx/letsencrypt.conf /etc/nginx/php.conf /etc/nginx/nginx.conf /etc/nginx/preflight.conf /etc/nginx/preflight_headers.conf /etc/nginx/routes.conf /etc/nginx/security_headers.conf
sudo chown bulletpoint:bulletpoint /etc/sites-enabled/*
sudo chown bulletpoint:bulletpoint /etc/php/7.3/fpm/php.ini 
sudo chown bulletpoint:bulletpoint /etc/php/7.3/cli/php.ini 
```


### CRON

#### Directories

- `mkdir /backup`

```
sudo crontab -e


# Let's encrypt (once a week - sunday at 22:00)
0 22 * * 0 /usr/bin/dehydrated -c && /usr/sbin/service nginx reload
```

```
sudo crontab -e -u bulletpoint


# postgres backup (every day at 01:00)
0 1 * * * /usr/bin/pg_dump --file=/backup/bulletpoint_$(/bin/date +"\%Y\%m\%d_\%H\%M\%S") --format=custom --dbname=bulletpoint --host=localhost --port=5432 --username=bulletpoint -w

# remove everything except last 10 backups (every day at 02:00)
0 2 * * * ls /backup/* -A1t | tail -n +10 | xargs --no-run-if-empty rm

# application cron (every minute)
* * * * * (cd /var/www/bulletpoint/current/backend && BULLETPOINT_ENV=production make cron)
```
