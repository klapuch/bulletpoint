INSERT INTO deploy.migrations(filename) VALUES('database/migrations/2019/images-to-folders--09-11.sql');

CREATE DOMAIN absolute_path AS character varying (255) CHECK (VALUE NOT LIKE '%..%');

CREATE SCHEMA constructs;


CREATE FUNCTION constant.default_avatar_filename_id() RETURNS integer AS $BODY$SELECT 1;$BODY$ LANGUAGE sql IMMUTABLE;


CREATE FUNCTION constructs.trigger_readonly() RETURNS trigger AS $BODY$
BEGIN
	RAISE EXCEPTION USING MESSAGE = format('Columns [%s] are READONLY', array_to_string(TG_ARGV[0]::text[], ', '));
END;
$BODY$ LANGUAGE plpgsql VOLATILE;


ALTER TABLE filesystem.trash ADD COLUMN deleted_at timestamptz NOT NULL DEFAULT now();

ALTER TABLE filesystem.trash ALTER COLUMN filename TYPE absolute_path;


CREATE TRIGGER trash_row_bu_readonly_trigger
	BEFORE UPDATE OF deleted_at, filename
	ON filesystem.trash
	FOR EACH ROW EXECUTE PROCEDURE constructs.trigger_readonly('{deleted_at,filename}');

CREATE TABLE filesystem.files (
	id integer,
	filename absolute_path NOT NULL,
	size_bytes bigint NOT NULL,
	mime_type citext NOT NULL,
	created_at timestamptz NOT NULL DEFAULT now()
);


CREATE TABLE filesystem.files$images (
	id integer GENERATED BY DEFAULT AS IDENTITY PRIMARY KEY,
	width integer NOT NULL,
	height integer NOT NULL,
	CONSTRAINT files$images_check_mime_type CHECK (mime_type IN ('image/gif', 'image/jpeg', 'image/png', 'image/webp')),
	CONSTRAINT files$images_filename_ukey UNIQUE (filename),
	CONSTRAINT size_bytes_positive CHECK (size_bytes > 0),
	CONSTRAINT width_positive CHECK (width > 0),
	CONSTRAINT height_positive CHECK (height > 0)
) INHERITS (filesystem.files);

CREATE FUNCTION filesystem.files$images_trigger_row_aud() RETURNS trigger AS $BODY$
BEGIN
	IF old.filename IS DISTINCT FROM new.filename THEN
		INSERT INTO filesystem.trash (filename) VALUES (old.filename);
	END IF;

	RETURN new;
END;
$BODY$ LANGUAGE plpgsql VOLATILE;

CREATE TRIGGER files$images_row_aud_trigger
	AFTER UPDATE OR DELETE
	ON filesystem.files$images
	FOR EACH ROW EXECUTE PROCEDURE filesystem.files$images_trigger_row_aud();


ALTER TABLE users ADD COLUMN avatar_filename_id integer NOT NULL DEFAULT constant.default_avatar_filename_id();


INSERT INTO filesystem.files$images (filename, size_bytes, mime_type, created_at, width, height) VALUES ('images/avatars/0.png', 4220, 'image/png', now(), 225, 225);


ALTER TABLE users ADD CONSTRAINT users_avatar_filename_id FOREIGN KEY (avatar_filename_id) REFERENCES filesystem.files$images(id) ON DELETE RESTRICT ON UPDATE RESTRICT;


CREATE OR REPLACE FUNCTION random_username(in_email text) RETURNS text STRICT AS $BODY$
DECLARE
	v_local_part text NOT NULL DEFAULT split_part(in_email, '@', 1);
	v_generated_username text;
	v_step integer;
	v_attempts CONSTANT integer DEFAULT 999;
BEGIN
	IF v_local_part = in_email THEN
		RAISE EXCEPTION USING MESSAGE = format('Passed value "%s" is not an email', in_email);
	END IF;

	FOR v_step IN 0 .. v_attempts LOOP
		v_generated_username = substr(v_local_part, 1, (SELECT constant.username_max_length()) - length(v_step::text));
		IF v_step != 0 THEN
			v_generated_username = v_generated_username || v_step;
		END IF;
		IF NOT EXISTS (SELECT 1 FROM users WHERE username = v_generated_username) THEN
			RETURN v_generated_username;
		END IF;
	END LOOP;
END;
$BODY$ LANGUAGE plpgsql STABLE;


CREATE OR REPLACE FUNCTION create_third_party_user(in_provider text, in_id text, in_email text) RETURNS SETOF users AS $BODY$
DECLARE
	v_provider_column CONSTANT hstore DEFAULT hstore(ARRAY['facebook', 'facebook_id', 'google', 'google_id']);
	v_column text NOT NULL DEFAULT v_provider_column -> in_provider;
	v_exists boolean;
BEGIN
	IF v_column IS NULL THEN
		RAISE EXCEPTION USING MESSAGE = format('Provider "%s" is unknown', in_provider);
	END IF;

	EXECUTE format('SELECT EXISTS (SELECT 1 FROM users WHERE %I = %L)', v_column, in_id) INTO v_exists;
	IF v_exists THEN
		RETURN QUERY EXECUTE format('UPDATE users SET email = %L WHERE %I = %L RETURNING *', in_email, v_column, in_id);
	ELSE
		RETURN QUERY EXECUTE format($$
			INSERT INTO users (email, %I) VALUES (%L, %L)
			ON CONFLICT (email) DO UPDATE SET email = %L, %I = %L
			RETURNING *
		$$, v_column, in_email, in_id, in_email, v_column, in_id, in_id);
	END IF;
END;
$BODY$ LANGUAGE plpgsql VOLATILE ROWS 1;


CREATE OR REPLACE FUNCTION users_trigger_row_aiud() RETURNS trigger AS $BODY$
BEGIN
	<<l_registration>>
	BEGIN
		IF TG_OP = 'INSERT' THEN
			INSERT INTO access.verification_codes (user_id, code, used_at) VALUES (
				new.id,
				format('%s:%s', encode(gen_random_bytes(25), 'hex'), encode(digest(new.id::text, 'sha1'), 'hex')),
				CASE WHEN COALESCE(new.facebook_id::text, new.google_id) IS NOT NULL THEN now() ELSE NULL END
			);
		END IF;
	END l_registration;


	<<l_avatars>>
	BEGIN
		IF (
			TG_OP IN ('UPDATE', 'DELETE')
			AND old.avatar_filename_id != constant.default_avatar_filename_id()
			AND old.avatar_filename_id != new.avatar_filename_id
		) THEN
			DELETE FROM filesystem.files$images WHERE id = old.avatar_filename_id;
		END IF;
	END l_avatars;

	RETURN new;
END;
$BODY$ LANGUAGE plpgsql VOLATILE;


CREATE OR REPLACE FUNCTION users_trigger_row_biu() RETURNS trigger AS $BODY$
BEGIN
	<<l_registration>>
	BEGIN
		IF new.username IS NULL AND COALESCE(new.facebook_id::text, new.google_id) IS NOT NULL THEN
			new.username = random_username(new.email);
		END IF;
	END l_registration;

	RETURN new;
END;
$BODY$ LANGUAGE plpgsql VOLATILE;


ALTER TABLE users DROP COLUMN avatar_filename;
DROP FUNCTION constant.default_avatar_filename();