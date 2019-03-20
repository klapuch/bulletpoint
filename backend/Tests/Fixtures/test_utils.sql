CREATE SCHEMA IF NOT EXISTS test_utils;

CREATE FUNCTION test_utils.random_array_pick(text[]) RETURNS text AS $BODY$
BEGIN
	RETURN $1[floor((random() * array_length($1, 1) + 1))::integer];
END
$BODY$ LANGUAGE plpgsql VOLATILE;
