CREATE FUNCTION tests.check_readonly_fields() RETURNS void AS $BODY$
BEGIN
	INSERT INTO filesystem.trash (filename) VALUES ('abc.txt');
	PERFORM assert.throws(
		$$UPDATE filesystem.trash SET deleted_at = now()$$,
		ROW('Columns [deleted_at, filename] are READONLY', 'P0001')::error
	);
	PERFORM assert.throws(
		$$UPDATE filesystem.trash SET filename = 'abc.txt'$$,
		ROW('Columns [deleted_at, filename] are READONLY', 'P0001')::error
	);
END $BODY$ LANGUAGE plpgsql VOLATILE;
