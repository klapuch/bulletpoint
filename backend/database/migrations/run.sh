#!/bin/sh
set -eu

psql -h localhost -U bulletpoint --single-transaction -d bulletpoint -v ON_ERROR_STOP=1 -X --file $1
