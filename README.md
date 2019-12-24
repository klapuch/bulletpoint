# Bulletpoint (https://www.bulletpoint.cz)

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
sudo su bulletpoint
```

### Locales
`sudo dpkg-reconfigure locales`

Choose `cs_CZ.UTF8` and `en_US.UTF8` (default)

### Install common packages

`sudo apt-get install wget curl apt-transport-https ca-certificates vim gnupg software-properties-common make git sudo zip unzip htop dehydrated`

### nginx

#### Installation
```
curl https://nginx.org/keys/nginx_signing.key -O nginx_signing.key
sudo apt-key add nginx_signing.key && rm nginx_signing.key

sudo vim /etc/apt/sources.list
deb http://nginx.org/packages/debian/ stretch nginx
deb-src http://nginx.org/packages/debian/ stretch nginx

sudo apt-get update
sudo apt-get install nginx
sudo apt-get install nginx-module-image-filter
```

#### Create directories
```
sudo mkdir /etc/nginx/snippets
sudo chown bulletpoint:bulletpoint /etc/nginx/snippets

sudo mkdir /etc/nginx/sites-enabled
sudo chown bulletpoint:bulletpoint /etc/nginx/sites-enabled 
```

### PHP

#### Installation
```
sudo wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg
sudo sh -c 'echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list'
sudo apt-get update
sudo apt-get install php7.4-fpm php7.4-curl php7.4-xsl php7.4-mbstring php7.4-zip php7.4-pgsql php7.4-gd
sudo apt-get install php-apcu php-redis php-igbinary
```

#### Composer
```
curl -sS https://getcomposer.org/installer | php && sudo mv composer.phar /usr/local/bin/composer
```

#### Permissions
`sudo vim /etc/php/7.4/fpm/pool.d/www.conf`

```
user = bulletpoint
group = bulletpoint
listen.owner = bulletpoint
listen.group = bulletpoint
listen = 127.0.0.1:9000
```

`sudo service php7.4-fpm restart`


### PostgreSQL 12

#### Installation
```
wget --quiet -O - https://www.postgresql.org/media/keys/ACCC4CF8.asc | sudo apt-key add -
RELEASE=$(lsb_release -cs)
echo "deb http://apt.postgresql.org/pub/repos/apt/ ${RELEASE}"-pgdg main | sudo tee /etc/apt/sources.list.d/pgdg.list
sudo apt-get update
sudo apt-get install postgresql-12
```


##### Locales
```
sudo -u postgres pg_dropcluster 12 main --stop
sudo -u postgres pg_createcluster -d /var/lib/postgresql/12/main -p 5432 12 main --locale=C.UTF-8 --start --start-conf=auto -- --data-checksums
```

`sudo -u postgres psql`

```
CREATE COLLATION "cs_CZ" (LOCALE = 'cs_CZ.utf8');
ALTER COLLATION "cs_CZ" OWNER TO postgres;
ALTER COLLATION "cs_CZ" SET SCHEMA pg_catalog;

CREATE COLLATION "cs_CZ.utf8" (LOCALE = 'cs_CZ.utf8');
ALTER COLLATION "cs_CZ.utf8" OWNER TO postgres;
ALTER COLLATION "cs_CZ.utf8" SET SCHEMA pg_catalog;
```

#### Create PostgreSQL user
```
sudo -u postgres createuser bulletpoint --superuser --pwprompt
sudo -u postgres createdb bulletpoint
```

`sudo service postgresql restart`

#### Create `.pgpass` file
```
vim ~/.pgpass
localhost:5432:bulletpoint:bulletpoint:PASSWORD

sudo chmod 600 ~/.pgpass

vim ~/.profile
export PGPASSFILE=~/.pgpass

source ~/.profile
```

### PgBouncer

`sudo apt-get install pgbouncer`


`sudo vim /etc/pgbouncer/pgbouncer.ini`

```
[databases]
* = host=127.0.0.1

[pgbouncer]
listen_addr = 127.0.0.1
listen_port = 6543
server_reset_query = DISCARD ALL
max_client_conn = 10
default_pool_size = 10
```

Copy `passwd` from command `psql -c "SELECT * FROM pg_shadow"`

`sudo vim /etc/pgbouncer/userlist.txt`

```
"bulletpoint" "MD5_PASSWORD"
```

`sudo service pgbouncer restart`


### NodeJS
```
curl -sL https://deb.nodesource.com/setup_10.x | sudo bash -
sudo apt-get install -y nodejs
curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | sudo apt-key add -
echo "deb https://dl.yarnpkg.com/debian/ stable main" | sudo tee /etc/apt/sources.list.d/yarn.list
sudo apt-get update && sudo apt-get install yarn
```

### Redis

#### Installation
```
sudo apt-get install redis-server
sudo service redis-server stop
```

#### Setting up CACHE
```
sudo cp /etc/redis/redis.conf /etc/redis/redis-cache.conf
sudo chown redis.redis /etc/redis/redis-cache.conf
```

`sudo vim /etc/redis/redis-cache.conf`

```
pidfile /var/run/redis/redis-server-cache.pid
port 6379
dbfilename dump-cache.rdb
maxmemory 500mb
```

`sudo cp /etc/init.d/redis-server /etc/init.d/redis-server-cache`

`sudo vim /etc/init.d/redis-server-cache`

```
NAME=redis-server-cache
DESC=redis-server-cache
DAEMON_ARGS=/etc/redis/redis-cache.conf
PIDFILE=$RUNDIR/redis-server-cache.pid
```

`sudo cp /lib/systemd/system/redis-server.service /lib/systemd/system/redis-server-cache.service`


`sudo vim /lib/systemd/system/redis-server-cache.service`

```
ExecStart=/usr/bin/redis-server /etc/redis/redis-cache.conf
PIDFile=/var/run/redis/redis-server-cache.pid
Alias=redis.service-cache
```

#### Setting up SESSION
```
sudo cp /etc/redis/redis.conf /etc/redis/redis-session.conf
sudo chown redis.redis /etc/redis/redis-session.conf
```

`sudo vim /etc/redis/redis-session.conf`

```
pidfile /var/run/redis/redis-server-session.pid
port 6380
dbfilename dump-session.rdb
maxmemory 500mb
```

`sudo cp /etc/init.d/redis-server /etc/init.d/redis-server-session`

`sudo vim /etc/init.d/redis-server-session`

```
NAME=redis-server-session
DESC=redis-server-session
DAEMON_ARGS=/etc/redis/redis-session.conf
PIDFILE=$RUNDIR/redis-server-session.pid
```

`sudo cp /lib/systemd/system/redis-server.service /lib/systemd/system/redis-server-session.service`


`sudo vim /lib/systemd/system/redis-server-session.service`

```
ExecStart=/usr/bin/redis-server /etc/redis/redis-session.conf
PIDFile=/var/run/redis/redis-server-session.pid
Alias=redis.service-session
```

#### Remove original files
```
sudo rm /etc/redis/redis.conf
sudo rm /etc/init.d/redis-server
sudo rm /lib/systemd/system/redis-server.service
```

#### Enable and start
```
sudo systemctl enable redis-server-cache.service
sudo systemctl enable redis-server-session.service

sudo service redis-server-cache restart
sudo service redis-server-session restart
```

### SSH

#### From your computer
```
ssh-copy-id -i ~/.ssh/id_rsa.pub bulletpoint@95.168.218.174
```

```
sudo vim /etc/ssh/sshd_config
PermitRootLogin no
PasswordAuthentication no
PermitEmptyPasswords no
```

### WWW
#### Create directories
```
sudo mkdir /var/www
sudo chown bulletpoint:bulletpoint /var/www
mkdir /var/www/bulletpoint
```

### Let's encrypt
#### Create directories

```
mkdir /var/www/letsencrypt
sudo mkdir /etc/letsencrypt
```

#### Config

`sudo vim /etc/dehydrated/conf.d/config.sh`

```
CA="https://acme-staging-v02.api.letsencrypt.org/directory" # as staging - comment for production
BASEDIR="/etc/letsencrypt"
WELLKNOWN="/var/www/letsencrypt"
CONTACT_EMAIL="klapuchdominik@gmail.com"
# afters tests - remove accounts and certs !!
```


#### Domains
`sudo vim /etc/dehydrated/domains.txt`

```
api.bulletpoint.cz
static.bulletpoint.cz
www.bulletpoint.cz
```

#### Generate
- `sudo /usr/bin/dehydrated --register --accept-terms`
- `sudo /usr/bin/dehydrated -c`

#### Move to correct folders

### Deploy

#### From your computer
```
ssh-keygen -t rsa -b 4096 -C 'build.bulletpoint@travis-ci.org' -f ./id_rsa
travis encrypt-file id_rsa --add
ssh-copy-id -i id_rsa.pub bulletpoint@95.168.218.174

rm -f id_rsa id_rsa.pub
```

#### Sudo
```
sudo visudo
bulletpoint ALL=(ALL) NOPASSWD: /usr/sbin/service nginx reload,/usr/sbin/service php7.4-fpm reload
```

```
sudo chown bulletpoint:bulletpoint /etc/nginx/snippets/letsencrypt.conf /etc/nginx/snippets/php.conf /etc/nginx/nginx.conf /etc/nginx/snippets/preflight.conf /etc/nginx/snippets/preflight_headers.conf /etc/nginx/snippets/routes.conf /etc/nginx/snippets/security_headers.conf
sudo chown bulletpoint:bulletpoint /etc/nginx/sites-enabled/*
sudo chown bulletpoint:bulletpoint /etc/php/7.4/fpm/php.ini
sudo chown bulletpoint:bulletpoint /etc/php/7.4/cli/php.ini
```


### CRON

#### Directories

- `sudo mkdir -p /backup/database`
- `sudo chown bulletpoint:bulletpoint /backup && sudo chown bulletpoint:bulletpoint /backup/`

```
sudo crontab -e


# Let's encrypt (once a week - sunday at 22:00)
0 22 * * 0 /usr/bin/dehydrated -c && /usr/sbin/service nginx reload
```

```
sudo crontab -e -u bulletpoint

# postgres backup (every day at 01:00)
0 1 * * * /usr/bin/pg_dump --file=/backup/database/bulletpoint_$(/bin/date +"\%Y\%m\%d_\%H\%M\%S") --format=custom --dbname=bulletpoint --host=localhost --port=5432 --username=bulletpoint -w
# for restore use "pg_restore --format=cutom --create --clean --exit-on-error -h localhost -p 5432 -U bulletpoint -d postgres -v -O postgres_DATE_TIME"


# remove everything except last 10 database backups (every day at 02:00)
0 2 * * * ls /backup/database/* -A1t | tail -n +10 | xargs --no-run-if-empty rm


# remove and gzip logs older than 4 days (at 03:00 on Sunday)
0 3 * * 0 find /var/www/bulletpoint/logs -name '*.html' -type f -mtime +4 -print0 | xargs --null --no-run-if-empty tar --remove-files -cvzf /var/www/bulletpoint/logs/logs.$(/bin/date +"\%Y\%m\%d_\%H\%M\%S").tar.gz


# remove everything except last 5 log archives (at 04:00 on Sunday)
0 4 * * 0 ls /var/www/bulletpoint/logs/logs.*.tar.gz -A1t | tail -n +5 | xargs --no-run-if-empty rm


# application cron (every minute)
* * * * * (cd /var/www/bulletpoint/current/backend && BULLETPOINT_ENV=production make cron)
```

### Logrotate

#### PHP logs

`sudo vim /etc/logrotate.d/bulletpoint`

```
/var/www/bulletpoint/logs/info.log /var/www/bulletpoint/logs/exception.log /var/www/bulletpoint/logs/error.log {
  rotate 15
  daily
  missingok
  notifempty
  compress
  delaycompress
}
```
