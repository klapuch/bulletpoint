CREATE SCHEMA IF NOT EXISTS test_utils;

CREATE FUNCTION test_utils.random_array_pick(text[]) RETURNS text AS $BODY$
BEGIN
	RETURN $1[floor((random() * array_length($1, 1) + 1))::integer];
END
$BODY$ LANGUAGE plpgsql VOLATILE;

CREATE FUNCTION test_utils.better_random(low integer = 1, high bigint = 2147483647) RETURNS integer AS $BODY$
BEGIN
	RETURN floor(random()* (high - low + 1) + low);
END;
$BODY$ LANGUAGE plpgsql STRICT;

CREATE FUNCTION test_utils.better_random(type text) RETURNS integer AS $BODY$
DECLARE
	types CONSTANT hstore DEFAULT 'smallint=>32767,integer=>2147483647,bigint=>9223372036854775807'::hstore;
BEGIN
	RETURN test_utils.better_random(1, CAST(types -> lower(type) AS bigint));
END;
$BODY$ LANGUAGE plpgsql STRICT;
