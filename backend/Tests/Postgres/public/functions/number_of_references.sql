CREATE FUNCTION tests.number_of_references() RETURNS void AS $BODY$
BEGIN
	PERFORM assert.same(0, (SELECT number_of_references('abc')));
	PERFORM assert.same(0, (SELECT number_of_references(NULL)));
	PERFORM assert.same(1, (SELECT number_of_references('[[abc]]')));
	PERFORM assert.same(0, (SELECT number_of_references('[[]]')));
	PERFORM assert.same(1, (SELECT number_of_references('[[]] [[abc]]')));
	PERFORM assert.same(1, (SELECT number_of_references('something [[abc]] in center')));
	PERFORM assert.same(2, (SELECT number_of_references('[[multiple]][[next to]]')));
	PERFORM assert.same(2, (SELECT number_of_references('[[multiple]] with spaces [[next to]]')));
END $BODY$ LANGUAGE plpgsql VOLATILE;
