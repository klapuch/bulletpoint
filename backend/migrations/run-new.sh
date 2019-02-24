#!/bin/bash

for file in `git diff --diff-filter=A --name-only HEAD^ '*/*/*.sql'`; do
	sh ./migrations/run.sh ${0%/*}/../../$file;
done
