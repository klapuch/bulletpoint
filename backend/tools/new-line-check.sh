#!/bin/sh
set -e

BASE_DIR=/var/www/bulletpoint

EXIT_CODE=0
FILES=$(git --no-pager diff --diff-filter=d --name-only | xargs --no-run-if-empty printf "$BASE_DIR/%s\n")

for filename in $FILES; do
	END=$(tail -c -1 $filename | cat -e)
	MIME_TYPE=$(file --mime-type $filename | grep 'text/*')
	if [ "$MIME_TYPE" != '' ] && [ "$END" != '$' ]; then
		EXIT_CODE=1
		echo "File \"$filename\" needs to end with new line!"
	fi
done;

if [ "$EXIT_CODE" = 0 ]; then
	echo '[OK] All files end with new line!'
fi

exit $EXIT_CODE
