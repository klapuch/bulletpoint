CREATE FUNCTION tests.from_email() RETURNS void AS $BODY$
BEGIN
	PERFORM assert.same('foo', random_username('foo@email.cz'));
	PERFORM assert.null(random_username(NULL));
	PERFORM assert.same('012345678901234567890123', random_username('012345678901234567890123456789@email.cz')); -- 30

	PERFORM samples.users(jsonb_build_object('username', '012345678901234567890abc'));
	PERFORM assert.same('012345678901234567890abc1', random_username('012345678901234567890abc@email.cz'));

	PERFORM samples.users(jsonb_build_object('username', '012345678901234567890abc1'));
	PERFORM assert.same('012345678901234567890abc2', random_username('012345678901234567890abc@email.cz'));

	PERFORM samples.users(jsonb_build_object('username', '012345678901234567890abc2'));
	PERFORM samples.users(jsonb_build_object('username', '012345678901234567890abc3'));
	PERFORM samples.users(jsonb_build_object('username', '012345678901234567890abc4'));
	PERFORM samples.users(jsonb_build_object('username', '012345678901234567890abc5'));
	PERFORM samples.users(jsonb_build_object('username', '012345678901234567890abc6'));
	PERFORM samples.users(jsonb_build_object('username', '012345678901234567890abc7'));
	PERFORM samples.users(jsonb_build_object('username', '012345678901234567890abc8'));
	PERFORM samples.users(jsonb_build_object('username', '012345678901234567890abc9'));

	PERFORM assert.same('012345678901234567890ab10', random_username('012345678901234567890abc@email.cz'));

END $BODY$ LANGUAGE plpgsql VOLATILE;

CREATE FUNCTION tests.invalid_email() RETURNS void AS $BODY$
BEGIN
	PERFORM assert.throws(
		$$SELECT random_username('thisIsNotEmail');$$,
		ROW('Passed value "thisIsNotEmail" is not an email', 'P0001')::error
	);
END $BODY$ LANGUAGE plpgsql VOLATILE;
