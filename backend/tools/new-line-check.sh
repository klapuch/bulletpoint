#!/bin/sh

EXIT_CODE=0

for filename in $(git ls-files); do
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
