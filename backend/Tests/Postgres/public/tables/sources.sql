CREATE FUNCTION tests.not_empty_sources() RETURNS void AS $BODY$
BEGIN
	INSERT INTO sources (link, type) VALUES (NULL, 'head');
	INSERT INTO sources (link, type) VALUES ('', 'head');
	INSERT INTO sources (link, type) VALUES ('https://www.google.com', 'web');
END $BODY$ LANGUAGE plpgsql VOLATILE;

CREATE FUNCTION tests.test_empty_to_null() RETURNS void AS $BODY$
DECLARE
	v_link sources.link%type;
BEGIN
	INSERT INTO sources (link, type) VALUES ('', 'head') RETURNING link INTO v_link;
	PERFORM assert.null(v_link);
END $BODY$ LANGUAGE plpgsql VOLATILE;

CREATE FUNCTION tests.empty_sources() RETURNS void AS $BODY$
BEGIN
	PERFORM assert.throws(
		$$INSERT INTO sources (link, type) VALUES ('X', 'head')$$,
		ROW('Link from head must be empty.', 'P0001')::error
	);
	PERFORM assert.throws(
		$$INSERT INTO sources (link, type) VALUES ('', 'web')$$,
		ROW('Link from web can not be empty.', 'P0001')::error
	);
	PERFORM assert.throws(
		$$INSERT INTO sources (link, type) VALUES (NULL, 'web')$$,
		ROW('Link from web can not be empty.', 'P0001')::error
	);
END $BODY$ LANGUAGE plpgsql VOLATILE;
