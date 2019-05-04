CREATE FUNCTION tests.auto_verify_for_facebook() RETURNS void AS $BODY$
BEGIN
	INSERT INTO users (facebook_id, email) VALUES ('123', 'foo@bar.cz');
	PERFORM assert.true((SELECT used_at IS NOT NULL FROM access.verification_codes));
END $BODY$ LANGUAGE plpgsql VOLATILE;

CREATE FUNCTION tests.auto_verify_for_google() RETURNS void AS $BODY$
BEGIN
	INSERT INTO users (google_id, email) VALUES ('123', 'foo@bar.cz');
	PERFORM assert.true((SELECT used_at IS NOT NULL FROM access.verification_codes));
END $BODY$ LANGUAGE plpgsql VOLATILE;

CREATE FUNCTION tests.not_verify_for_default() RETURNS void AS $BODY$
BEGIN
	INSERT INTO users (email, username, password) VALUES ('foo@bar.cz', 'foo', '123');
	PERFORM assert.false((SELECT used_at IS NOT NULL FROM access.verification_codes));
END $BODY$ LANGUAGE plpgsql VOLATILE;

CREATE FUNCTION tests.changing_image() RETURNS void AS $BODY$
DECLARE
	v_user_id users.id%type NOT NULL DEFAULT samples.users();
	c_destination_image constant character varying default 'images/avatars/abc1.jpg';
BEGIN
	-- updating from default to new
	UPDATE users SET avatar_filename = c_destination_image;
	PERFORM assert.same(c_destination_image, (SELECT avatar_filename FROM users WHERE id = v_user_id));
	PERFORM assert.same(0, (SELECT count(*)::integer FROM filesystem.trash));

	-- updating to the same
	UPDATE users SET avatar_filename = c_destination_image;
	PERFORM assert.same(c_destination_image, (SELECT avatar_filename FROM users WHERE id = v_user_id));
	PERFORM assert.same(0, (SELECT count(*)::integer FROM filesystem.trash));
END $BODY$ LANGUAGE plpgsql VOLATILE;

CREATE FUNCTION tests.throwing_on_existing() RETURNS void AS $BODY$
DECLARE
	v_user_id users.id%type NOT NULL DEFAULT samples.users();
	c_destination_image constant character varying default 'images/avatars/abc1.jpg';
	c_destination_image_trash constant character varying default 'images/avatars/abc2.jpg';
BEGIN
	SELECT samples.users() INTO v_user_id;
	INSERT INTO users (username, email, password, avatar_filename) VALUES ('abcd', 'foo@email.cz', 'heslo1', c_destination_image);
	INSERT INTO filesystem.trash(filename) VALUES (c_destination_image_trash);

	PERFORM assert.throws(
		format($$UPDATE users SET avatar_filename = %L WHERE id = %L$$, c_destination_image, v_user_id),
		ROW('Avatar "images/avatars/abc1.jpg" already exists', '23505')::error
	);

	PERFORM assert.throws(
		format($$UPDATE users SET avatar_filename = %L WHERE id = %L$$, c_destination_image_trash, v_user_id),
		ROW('Avatar "images/avatars/abc2.jpg" already exists', '23505')::error
	);

END $BODY$ LANGUAGE plpgsql VOLATILE;
