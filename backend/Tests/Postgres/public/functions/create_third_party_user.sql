CREATE FUNCTION tests.creating_brand_new() RETURNS void AS $BODY$
BEGIN
	PERFORM samples.sample_image();
	PERFORM assert.same(ARRAY['1', 'foo@facebook.com'], (SELECT ARRAY[facebook_id::text, email] FROM create_third_party_user('facebook', '1', 'foo@facebook.com')));
	PERFORM assert.same(ARRAY['2', 'foo@google.com'], (SELECT ARRAY[google_id, email] FROM create_third_party_user('google', '2', 'foo@google.com')));
END $BODY$ LANGUAGE plpgsql VOLATILE;

CREATE FUNCTION tests.updating_existing() RETURNS void AS $BODY$
BEGIN
	PERFORM samples.sample_image();
	PERFORM create_third_party_user('facebook', '1', 'foo@facebook.com');
	PERFORM assert.same(ARRAY['1', 'bar@facebook.com'], (SELECT ARRAY[facebook_id::text, email] FROM create_third_party_user('facebook', '1', 'bar@facebook.com')));

	PERFORM create_third_party_user('google', '1', 'foo@google.com');
	PERFORM assert.same(ARRAY['1', 'bar@google.com'], (SELECT ARRAY[google_id, email] FROM create_third_party_user('google', '1', 'bar@google.com')));
END $BODY$ LANGUAGE plpgsql VOLATILE;

CREATE FUNCTION tests.merging() RETURNS void AS $BODY$
BEGIN
	PERFORM samples.sample_image();
	PERFORM create_third_party_user('facebook', '1', 'foo@email.com');
	PERFORM create_third_party_user('google', '2', 'foo@email.com');

	PERFORM assert.same(ARRAY['1', '2'], (SELECT ARRAY[facebook_id::text, google_id] FROM users));
END $BODY$ LANGUAGE plpgsql VOLATILE;

CREATE FUNCTION tests.from_password() RETURNS void AS $BODY$
BEGIN
	PERFORM samples.sample_image();
	INSERT INTO users (username, email, password) VALUES ('facedown', 'foo@email.com', 'passwd1');
	PERFORM create_third_party_user('facebook', '1', 'foo@email.com');

	PERFORM assert.same(ARRAY['1'], (SELECT ARRAY[facebook_id]::text[] FROM users));
END $BODY$ LANGUAGE plpgsql VOLATILE;
