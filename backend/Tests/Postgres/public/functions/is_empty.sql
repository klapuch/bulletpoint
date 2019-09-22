CREATE FUNCTION tests.is_empty() RETURNS void AS $BODY$
BEGIN
	PERFORM assert.true(is_empty(NULL));
	PERFORM assert.true(is_empty(''));
	PERFORM assert.true(is_empty(' '));
	PERFORM assert.true(is_empty('  '));
	PERFORM assert.false(is_empty('0'));
	PERFORM assert.false(is_empty('A'));
	PERFORM assert.false(is_empty('0.0'));
END $BODY$ LANGUAGE plpgsql VOLATILE;
