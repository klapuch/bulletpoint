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

CREATE FUNCTION tests.unify_name() RETURNS void AS $BODY$
DECLARE
	v_id tags.id%type;
BEGIN
	INSERT INTO tags (name) VALUES ('abc') RETURNING id INTO v_id;
	PERFORM assert.same('abc', (SELECT name FROM tags WHERE id = v_id));

	UPDATE tags SET name = 'ABC' WHERE id = v_id;
	PERFORM assert.same('ABC', (SELECT name FROM tags WHERE id = v_id));

	UPDATE tags SET name = 'Some Name' WHERE id = v_id;
	PERFORM assert.same('Some Name', (SELECT name FROM tags WHERE id = v_id));

	UPDATE tags SET name = 'Abc' WHERE id = v_id;
	PERFORM assert.same('abc', (SELECT name FROM tags WHERE id = v_id));

	UPDATE tags SET name = 'ABc' WHERE id = v_id;
	PERFORM assert.same('ABc', (SELECT name FROM tags WHERE id = v_id));

	UPDATE tags SET name = 'Lék' WHERE id = v_id;
	PERFORM assert.same('lék', (SELECT name FROM tags WHERE id = v_id));

	UPDATE tags SET name = 'Databáze' WHERE id = v_id;
	PERFORM assert.same('databáze', (SELECT name FROM tags WHERE id = v_id));

	UPDATE tags SET name = 'Živočichové' WHERE id = v_id;
	PERFORM assert.same('živočichové', (SELECT name FROM tags WHERE id = v_id));
END $BODY$ LANGUAGE plpgsql VOLATILE;
