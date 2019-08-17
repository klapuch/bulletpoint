#!/bin/sh

# BRANCH_NAME--2019-01-01.sql
MIGRATION_NAME=$(git rev-parse --abbrev-ref HEAD)--$(date +"%m-%d").sql

# database/migrations/2019/BRANCH_NAME--2019-01-01.sql
MIGRATION_FILENAME=database/migrations/$(date +"%Y")/$MIGRATION_NAME

mkdir -p database/migrations/"$(date +"%Y")"
cp -i database/migrations/template.sql $MIGRATION_FILENAME

sed -i -e "s~VAR__MIGRATION_NAME~$MIGRATION_FILENAME~g" $MIGRATION_FILENAME
