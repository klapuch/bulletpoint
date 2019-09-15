#!/bin/sh
set -e

BASE_DIR=/var/www/bulletpoint
ACTUAL_SCHEMA_FILENAME=/tmp/migrations/actual.sql
MIGRATED_SCHEMA_FILENAME=/tmp/migrations/migrated.sql

mkdir -pv "$(dirname $ACTUAL_SCHEMA_FILENAME)"
mkdir -pv "$(dirname $MIGRATED_SCHEMA_FILENAME)"

pg_dump --host=127.0.0.1 --port=5432 --username=bulletpoint --no-owner --no-privileges --schema-only --format=p --file=$ACTUAL_SCHEMA_FILENAME bulletpoint
git --no-pager diff --diff-filter=A --name-only master.. -- $BASE_DIR/backend/database/migrations/**/*.sql | xargs --no-run-if-empty printf "$BASE_DIR/%s\n" | xargs --no-run-if-empty --max-lines=1 sh ${0%/*}/run.sh
pg_dump --host=127.0.0.1 --port=5432 --username=bulletpoint --no-owner --no-privileges --schema-only --format=p --file=$MIGRATED_SCHEMA_FILENAME bulletpoint

diff $ACTUAL_SCHEMA_FILENAME $MIGRATED_SCHEMA_FILENAME

echo "[OK] Schemas after and before migrations are same."
