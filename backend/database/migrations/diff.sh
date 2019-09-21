#!/bin/sh
set -e

BASE_DIR=/var/www/bulletpoint
TMP_DATABASE_NAME=tmp_bulletpoint
ACTUAL_SCHEMA_FILENAME=/tmp/migrations/actual.sql
MIGRATED_SCHEMA_FILENAME=/tmp/migrations/migrated.sql
MASTER_SCHEMA_FILENAME=/tmp/migrations/master.sql

mkdir -pv "$(dirname $ACTUAL_SCHEMA_FILENAME)"
mkdir -pv "$(dirname $MIGRATED_SCHEMA_FILENAME)"
mkdir -pv "$(dirname $MASTER_SCHEMA_FILENAME)"

psql -U bulletpoint -h 127.0.0.1 -d postgres -c "DROP DATABASE IF EXISTS $TMP_DATABASE_NAME";
psql -U bulletpoint -h 127.0.0.1 -d postgres -c "CREATE DATABASE $TMP_DATABASE_NAME";
psql -U bulletpoint -h 127.0.0.1 $TMP_DATABASE_NAME < $BASE_DIR/backend/database/schema.sql;
pg_dump --host=127.0.0.1 --port=5432 --username=bulletpoint --no-owner --no-privileges --schema-only --format=p --file=$ACTUAL_SCHEMA_FILENAME $TMP_DATABASE_NAME

psql -U bulletpoint -h 127.0.0.1 -d postgres -c "DROP DATABASE $TMP_DATABASE_NAME";
psql -U bulletpoint -h 127.0.0.1 -d postgres -c "CREATE DATABASE $TMP_DATABASE_NAME";
git --no-pager show "$(git merge-base master HEAD):backend/database/schema.sql" > $MASTER_SCHEMA_FILENAME;
psql -U bulletpoint -h 127.0.0.1 $TMP_DATABASE_NAME < $MASTER_SCHEMA_FILENAME;
git --no-pager diff --diff-filter=A --name-only master.. -- $BASE_DIR/backend/database/migrations/**/*.sql \
  | xargs --no-run-if-empty -I {} psql -h 127.0.0.1 -U bulletpoint --single-transaction -d $TMP_DATABASE_NAME -v ON_ERROR_STOP=1 -X --file $BASE_DIR/{}

pg_dump --host=127.0.0.1 --port=5432 --username=bulletpoint --no-owner --no-privileges --schema-only --format=p --file=$MIGRATED_SCHEMA_FILENAME $TMP_DATABASE_NAME

diff $ACTUAL_SCHEMA_FILENAME $MIGRATED_SCHEMA_FILENAME

echo "[OK] Schemas after and before migrations are same."
