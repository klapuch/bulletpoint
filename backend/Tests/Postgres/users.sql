CREATE FUNCTION tests.auto_verify_for_facebook() RETURNS void AS $BODY$
BEGIN
	INSERT INTO users (facebook_id, email) VALUES (123, 'foo@bar.cz');
	PERFORM assert.true((SELECT used_at IS NOT NULL FROM access.verification_codes));
END $BODY$ LANGUAGE plpgsql VOLATILE;

CREATE FUNCTION tests.not_verify_for_default() RETURNS void AS $BODY$
BEGIN
	INSERT INTO users (email, username, password) VALUES ('foo@bar.cz', 'foo', '123');
	PERFORM assert.false((SELECT used_at IS NOT NULL FROM access.verification_codes));
END $BODY$ LANGUAGE plpgsql VOLATILE;
