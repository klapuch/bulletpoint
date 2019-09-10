CREATE FUNCTION tests.auto_verify_for_facebook() RETURNS void AS $BODY$
BEGIN
	INSERT INTO users (facebook_id, email, avatar_filename_id) VALUES ('123', 'foo@bar.cz', (SELECT samples.sample_image()));
	PERFORM assert.true((SELECT used_at IS NOT NULL FROM access.verification_codes));
END $BODY$ LANGUAGE plpgsql VOLATILE;

CREATE FUNCTION tests.auto_verify_for_google() RETURNS void AS $BODY$
BEGIN
	PERFORM samples.sample_image();
	INSERT INTO users (google_id, email, avatar_filename_id) VALUES ('123', 'foo@bar.cz', (SELECT samples.sample_image()));
	PERFORM assert.true((SELECT used_at IS NOT NULL FROM access.verification_codes));
END $BODY$ LANGUAGE plpgsql VOLATILE;

CREATE FUNCTION tests.not_verify_for_DEFAULT() RETURNS void AS $BODY$
BEGIN
	PERFORM samples.sample_image();
	INSERT INTO users (email, username, password, avatar_filename_id) VALUES ('foo@bar.cz', 'foo', '123', (SELECT samples.sample_image()));
	PERFORM assert.false((SELECT used_at IS NOT NULL FROM access.verification_codes));
END $BODY$ LANGUAGE plpgsql VOLATILE;

CREATE FUNCTION tests.changing_image() RETURNS void AS $BODY$
DECLARE
	v_user_id users.id%type NOT NULL DEFAULT samples.users();
	c_destination_image_id CONSTANT integer DEFAULT samples.filesystem_image();
BEGIN
	-- updating from default to new
	UPDATE users SET avatar_filename_id = c_destination_image_id;
	PERFORM assert.same(c_destination_image_id, (SELECT avatar_filename_id FROM users WHERE id = v_user_id));
	PERFORM assert.same(0, (SELECT count(*)::integer FROM filesystem.trash));

	-- updating to the same
	UPDATE users SET avatar_filename_id = c_destination_image_id;
	PERFORM assert.same(c_destination_image_id, (SELECT avatar_filename_id FROM users WHERE id = v_user_id));
	PERFORM assert.same(0, (SELECT count(*)::integer FROM filesystem.trash));
END $BODY$ LANGUAGE plpgsql VOLATILE;
