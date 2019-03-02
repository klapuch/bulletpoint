CREATE FUNCTION tests.differ_lower_and_upper() RETURNS void AS $BODY$
BEGIN
	INSERT INTO tags (name) VALUES ('abc');
	INSERT INTO tags (name) VALUES ('ABC');
END $BODY$ LANGUAGE plpgsql VOLATILE;

CREATE FUNCTION tests.throwing_on_duplicity() RETURNS void AS $BODY$
BEGIN
	INSERT INTO tags (name) VALUES ('abc');

	PERFORM assert.throws(
		$$INSERT INTO tags (name) VALUES ('abc')$$,
		ROW('duplicate key value violates unique constraint "tags_name_key"', '23505')::error
	);
END $BODY$ LANGUAGE plpgsql VOLATILE;
