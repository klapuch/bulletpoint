#!/bin/bash

MIGRATION_FILENAMES=$(find database/migrations/*/*.sql | tr '\n' ',')
MIGRATION_FILENAMES_TO_RUN=$(psql -h localhost -U bulletpoint -d bulletpoint -tA -X -c "SELECT deploy.migrations_to_run('$MIGRATION_FILENAMES')")

if [[ "$MIGRATION_FILENAMES_TO_RUN" == "" ]]; then
	echo "SUCCESS! Nothing to migrate.";
	exit 0;
fi

for filename in $MIGRATION_FILENAMES_TO_RUN; do
	echo "Migrating $filename...";
	sh ./database/migrations/run.sh $filename && echo "Migration of $filename was successful.";
done
