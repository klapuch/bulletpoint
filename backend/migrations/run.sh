#!/bin/sh

psql -h localhost -U bulletpoint --single-transaction bulletpoint < $1
