CREATE FUNCTION tests.null() RETURNS void AS $BODY$
BEGIN
	PERFORM assert.false(array_equals(NULL, ARRAY[1]));
	PERFORM assert.false(array_equals(ARRAY[1], NULL));
	PERFORM assert.true(array_equals(ARRAY[1], ARRAY[1]));
	PERFORM assert.false(array_equals(ARRAY[1, 2], ARRAY[1]));
	PERFORM assert.false(array_equals(ARRAY[1], ARRAY[1, 2]));
	PERFORM assert.true(array_equals(ARRAY[1, 2], ARRAY[1, 2]));
	PERFORM assert.true(array_equals(ARRAY[2, 1], ARRAY[1, 2]));
END $BODY$ LANGUAGE plpgsql VOLATILE;
