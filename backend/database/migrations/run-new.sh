#!/bin/sh

MIGRATION_FILENAMES=$(find database/migrations/*/*.sql | tr '\n' ',')
MIGRATION_FILENAMES_TO_RUN=$(psql -h localhost -U bulletpoint -d bulletpoint -tA -X -c "SELECT deploy.migrations_to_run('$MIGRATION_FILENAMES')")

if [ "$MIGRATION_FILENAMES_TO_RUN" = "" ]; then
	echo "SUCCESS! Nothing to migrate.";
	exit 0;
fi

for filename in $MIGRATION_FILENAMES_TO_RUN; do
	echo "Migrating $filename...";
	sh ${0%/*}/run.sh $filename;
	if [ $? != 0 ]; then
		echo "FAIL! Migration \"$filename\" was not successful.";
		exit $?;
	fi
	echo "Migration of \"$filename\" was successful.";
done
