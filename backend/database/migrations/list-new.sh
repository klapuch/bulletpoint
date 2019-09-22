#!/bin/sh
set -eu

MIGRATION_FILENAMES=$(find database/migrations/*/*.sql | tr '\n' ',');
MIGRATION_FILENAMES_TO_RUN=$(psql -h localhost -U bulletpoint -d bulletpoint -tA -X -c "SELECT deploy.migrations_to_run('$MIGRATION_FILENAMES')");

if [ -z "$MIGRATION_FILENAMES_TO_RUN" ]; then
	echo '[OK] No migrations.';
	exit 0;
fi

counter=1;
for filename in $MIGRATION_FILENAMES_TO_RUN; do
	echo $counter. $filename;
	counter=$((counter + 1))
done
