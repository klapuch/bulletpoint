#!/bin/bash

MIGRATION_FILENAMES=$(find database/migrations/*/*.sql | tr '\n' ',');
MIGRATION_FILENAMES_TO_RUN=$(psql -h localhost -U bulletpoint -d bulletpoint -tA -X -c "SELECT deploy.migrations_to_run('$MIGRATION_FILENAMES')");

if [[ "$MIGRATION_FILENAMES_TO_RUN" == "" ]]; then
	echo "No migrations.";
	exit 0;
fi
