#!/bin/sh

MIGRATION_FILENAME=migrations/`date +"%Y"`/`git rev-parse --abbrev-ref HEAD`--`date +"%m-%d"`--$1.sql

mkdir -p migrations/`date +"%Y"`
cp -i migrations/template.sql $MIGRATION_FILENAME

sed -i -e "s~VAR__MIGRATION_NAME~migrations/`date +"%Y"`/$MIGRATION_FILENAME~g" $MIGRATION_FILENAME
