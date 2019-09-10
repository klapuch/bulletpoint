SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET row_security = off;


-- extensions
CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;
CREATE EXTENSION IF NOT EXISTS citext WITH SCHEMA public;
CREATE EXTENSION IF NOT EXISTS hstore WITH SCHEMA public;
CREATE EXTENSION IF NOT EXISTS pgcrypto WITH SCHEMA public;

-- schemas (globals)
CREATE SCHEMA audit;
CREATE SCHEMA access;
CREATE SCHEMA filesystem;
CREATE SCHEMA constructs;

-- constants
CREATE SCHEMA constant;

CREATE FUNCTION constant.guest_id() RETURNS integer AS $BODY$SELECT 0$BODY$ LANGUAGE sql IMMUTABLE;
CREATE FUNCTION constant.sources_type() RETURNS text[] AS $BODY$SELECT ARRAY['web', 'head'];$BODY$ LANGUAGE sql IMMUTABLE;
CREATE FUNCTION constant.bulletpoint_ratings_point_range() RETURNS integer[] AS $BODY$SELECT ARRAY[-1, 0, 1];$BODY$ LANGUAGE sql IMMUTABLE;
CREATE FUNCTION constant.theme_tags_limit() RETURNS integer AS $BODY$SELECT 4;$BODY$ LANGUAGE sql IMMUTABLE;
CREATE FUNCTION constant.roles() RETURNS text[] AS $BODY$SELECT ARRAY['member', 'admin'];$BODY$ LANGUAGE sql IMMUTABLE;
CREATE FUNCTION constant.username_min_length() RETURNS integer AS $BODY$SELECT 3$BODY$ LANGUAGE sql IMMUTABLE;
CREATE FUNCTION constant.username_max_length() RETURNS integer AS $BODY$SELECT 25$BODY$ LANGUAGE sql IMMUTABLE;
CREATE FUNCTION constant.default_avatar_filename_id() RETURNS integer AS $BODY$SELECT 1;$BODY$ LANGUAGE sql IMMUTABLE;

-- types
CREATE TYPE operations AS ENUM ('INSERT', 'UPDATE', 'DELETE');


-- domains
CREATE DOMAIN sources_type AS text CHECK (VALUE = ANY(constant.sources_type()));
CREATE DOMAIN bulletpoint_ratings_point AS integer CHECK (constant.bulletpoint_ratings_point_range() @> ARRAY[VALUE]);
CREATE DOMAIN roles AS text CHECK (VALUE = ANY(constant.roles()));
CREATE DOMAIN usernames AS citext CHECK (VALUE ~ format('^[a-zA-Z0-9_]{%s,%s}$', constant.username_min_length(), constant.username_max_length()));
CREATE DOMAIN openid_sub AS text CHECK (VALUE ~ '^.{1,255}$');
CREATE DOMAIN http_status AS integer CHECK (VALUE BETWEEN 100 AND 504);

-- schema constructs
CREATE FUNCTION constructs.trigger_readonly() RETURNS trigger AS $BODY$
BEGIN
	RAISE EXCEPTION USING MESSAGE = format('Columns [%s] are READONLY', array_to_string(TG_ARGV[0]::text[], ', '));
END;
$BODY$ LANGUAGE plpgsql VOLATILE;

-- schema audit
CREATE TABLE audit.history (
	id bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
	"table" text NOT NULL,
	operation operations NOT NULL,
	changed_at timestamp with time zone NOT NULL DEFAULT now(),
	user_id integer,
	old jsonb,
	new jsonb
);

CREATE FUNCTION audit.trigger_table_audit() RETURNS trigger AS $BODY$
BEGIN
	EXECUTE format(
		'INSERT INTO audit.history ("table", operation, user_id, old, new) VALUES (%L, %L, %L, %L, %L)',
		TG_TABLE_NAME,
		TG_OP,
		globals_get_user(),
		CASE WHEN TG_OP IN ('UPDATE', 'DELETE') THEN row_to_json(old) ELSE NULL END,
		CASE WHEN TG_OP IN ('UPDATE', 'INSERT') THEN row_to_json(new) ELSE NULL END
	);

	RETURN CASE TG_OP WHEN 'DELETE' THEN old ELSE new END;
END;
$BODY$ LANGUAGE plpgsql;


-- functions
CREATE FUNCTION is_empty(text) RETURNS boolean AS $BODY$
	SELECT $1 IS NULL OR trim($1) = '';
$BODY$ LANGUAGE sql IMMUTABLE;

CREATE FUNCTION globals_get_variable(in_variable text) RETURNS text AS $BODY$
BEGIN
	RETURN nullif(current_setting(format('globals.%s', in_variable)), '');
	EXCEPTION WHEN OTHERS THEN RETURN NULL;
END;
$BODY$ LANGUAGE plpgsql STABLE;


CREATE FUNCTION globals_get_user() RETURNS integer AS $BODY$
	SELECT globals_get_variable('user')::integer;
$BODY$ LANGUAGE sql;


CREATE FUNCTION globals_set_variable(in_variable text, in_value text) RETURNS text AS $BODY$
	SELECT set_config(format('globals.%s', in_variable), in_value, false);
$BODY$ LANGUAGE sql;


CREATE FUNCTION globals_set_user(in_user integer) RETURNS void AS $BODY$
BEGIN
	PERFORM globals_set_variable('user', nullif(in_user, constant.guest_id())::text);
END;
$BODY$ LANGUAGE plpgsql;

CREATE FUNCTION array_diff(anyarray, anyarray) RETURNS anyarray AS $BODY$
	SELECT ARRAY(SELECT unnest($1) EXCEPT SELECT unnest($2));
$BODY$ LANGUAGE sql IMMUTABLE;

CREATE FUNCTION array_equals(anyarray, anyarray) RETURNS boolean AS $BODY$
	SELECT ($1 IS NOT NULL AND $1 <@ $2) AND ($2 IS NOT NULL AND $1 @> $2);
$BODY$ LANGUAGE sql IMMUTABLE;


-- schema filesystem
CREATE TABLE filesystem.trash (
	id integer GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
	filename character varying (255) NOT NULL UNIQUE,
	deleted_at timestamptz NOT NULL DEFAULT now()
);

CREATE TRIGGER trash_row_bu_readonly_trigger
	BEFORE UPDATE OF deleted_at
	ON filesystem.trash
	FOR EACH ROW EXECUTE PROCEDURE constructs.trigger_readonly('{deleted_at}');

CREATE TABLE filesystem.files (
	id integer,
	filename character varying (255) NOT NULL,
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


-- schema public
CREATE TABLE "references" (
	id integer GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
	url text NOT NULL
);

CREATE TRIGGER references_audit_trigger
	AFTER UPDATE OR DELETE OR INSERT
	ON "references"
	FOR EACH ROW EXECUTE PROCEDURE audit.trigger_table_audit();


CREATE VIEW references_to_ping AS
	SELECT array_agg(id) AS ids, url
	FROM "references"
	GROUP BY url;

CREATE TABLE reference_pings (
	id integer GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
	reference_id integer NOT NULL,
	status http_status NULL,
	ping_at timestamptz NOT NULL DEFAULT now(),
	CONSTRAINT reference_pings_reference_id FOREIGN KEY (reference_id) REFERENCES "references"(id) ON DELETE CASCADE ON UPDATE RESTRICT
);

CREATE MATERIALIZED VIEW broken_references AS
	SELECT reference_id
	FROM reference_pings
	WHERE now() - INTERVAL '3 days' < ping_at
	AND (status IS NULL OR status BETWEEN 400 AND 599)
	GROUP BY reference_id
	HAVING count(*) >= 3;
CREATE UNIQUE INDEX broken_references_reference_id_uidx ON broken_references(reference_id);


CREATE TABLE tags (
	id integer GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
	name text NOT NULL UNIQUE,
	CONSTRAINT tags_name_not_empty CHECK (NOT is_empty(name))
);

CREATE TRIGGER tags_audit_trigger
	AFTER UPDATE OR DELETE OR INSERT
	ON tags
	FOR EACH ROW EXECUTE PROCEDURE audit.trigger_table_audit();


CREATE TABLE users (
	id integer GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
	username usernames NOT NULL UNIQUE,
	email citext NOT NULL UNIQUE,
	password text,
	facebook_id bigint UNIQUE,
	google_id openid_sub UNIQUE,
	role roles NOT NULL DEFAULT 'member'::roles,
	avatar_filename_id integer NOT NULL DEFAULT constant.default_avatar_filename_id(),
	CONSTRAINT users_avatar_filename_id FOREIGN KEY (avatar_filename_id) REFERENCES filesystem.files$images(id) ON DELETE RESTRICT ON UPDATE RESTRICT,
	CONSTRAINT users_password_empty_for_3rd_party CHECK (
		CASE WHEN password IS NULL THEN
			COALESCE(facebook_id::text, google_id) IS NOT NULL
		ELSE
			TRUE
		END
	)
);

CREATE FUNCTION random_username(in_email text) RETURNS text STRICT AS $BODY$
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

CREATE FUNCTION create_third_party_user(in_provider text, in_id text, in_email text) RETURNS SETOF users AS $BODY$
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

CREATE FUNCTION users_trigger_row_aiud() RETURNS trigger AS $BODY$
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

CREATE FUNCTION users_trigger_row_biu() RETURNS trigger AS $BODY$
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

CREATE TRIGGER users_row_aiud_trigger
	AFTER INSERT OR UPDATE OR DELETE
	ON users
	FOR EACH ROW EXECUTE PROCEDURE users_trigger_row_aiud();

CREATE TRIGGER users_row_biu_trigger
	BEFORE INSERT OR UPDATE
	ON users
	FOR EACH ROW EXECUTE PROCEDURE users_trigger_row_biu();


CREATE TABLE user_tag_reputations (
	id integer GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
	user_id integer NOT NULL,
	tag_id integer NOT NULL,
	reputation integer NOT NULL DEFAULT 0,
	CONSTRAINT user_tag_reputations_reputation_positive CHECK (reputation >= 0),
	CONSTRAINT user_tag_reputations_user_id_tag_id UNIQUE (user_id, tag_id),
	CONSTRAINT user_tag_reputations_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE RESTRICT,
	CONSTRAINT user_tag_reputations_tag_id_fkey FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE ON UPDATE RESTRICT
);

CREATE FUNCTION update_user_tag_reputation(in_user_id integer, in_tag_id integer, in_point bulletpoint_ratings_point) RETURNS void AS $BODY$
DECLARE
	v_point CONSTANT integer NOT NULL DEFAULT CASE in_point WHEN 1 THEN 1 ELSE -1 END;
BEGIN
	INSERT INTO user_tag_reputations (user_id, tag_id, reputation) VALUES (in_user_id, in_tag_id, greatest(v_point, 0))
	ON CONFLICT (user_id, tag_id) DO UPDATE SET reputation = user_tag_reputations.reputation + v_point;
END;
$BODY$ LANGUAGE plpgsql VOLATILE;


CREATE TABLE access.forgotten_passwords (
	id integer GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
	user_id integer NOT NULL,
	reminder text NOT NULL UNIQUE,
	used_at timestamp with time zone,
	reminded_at timestamp with time zone NOT NULL,
	expire_at timestamp with time zone NOT NULL,
	CONSTRAINT forgotten_passwords_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE RESTRICT,
	CONSTRAINT forgotten_passwords_reminder_exact_length CHECK (length(reminder) = 141),
	CONSTRAINT forgotten_passwords_expire_at_future CHECK (expire_at >= now()),
	CONSTRAINT forgotten_passwords_expire_at_greater_than_reminded_at CHECK (expire_at > reminded_at)
);
CREATE INDEX forgotten_passwords_user_id ON access.forgotten_passwords USING btree (user_id);


CREATE TABLE access.verification_codes (
	id integer GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
	user_id integer NOT NULL UNIQUE,
	code text NOT NULL UNIQUE,
	used_at timestamp with time zone,
	CONSTRAINT verification_codes_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE RESTRICT,
	CONSTRAINT verification_codes_code_exact_length CHECK (length(code) = 91)
);
CREATE INDEX verification_codes_user_id ON access.verification_codes USING btree (user_id);


CREATE TABLE themes (
	id integer GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
	name character varying(255) NOT NULL,
	reference_id integer NOT NULL,
	user_id integer NOT NULL,
	created_at timestamptz NOT NULL DEFAULT now(),
	CONSTRAINT themes_name_not_empty CHECK (NOT is_empty(name)),
	CONSTRAINT themes_reference_id FOREIGN KEY (reference_id) REFERENCES "references"(id) ON DELETE SET NULL ON UPDATE RESTRICT,
	CONSTRAINT themes_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE RESTRICT
);

CREATE TRIGGER themes_audit_trigger
	AFTER UPDATE OR DELETE OR INSERT
	ON themes
	FOR EACH ROW EXECUTE PROCEDURE audit.trigger_table_audit();

CREATE FUNCTION related_themes(in_theme_id integer) RETURNS SETOF integer AS $BODY$
SELECT theme_id
FROM bulletpoints
JOIN (
	SELECT bulletpoint_id, priority FROM (
		SELECT bulletpoint_id, bulletpoint_theme_comparisons.theme_id, 100 AS priority
		FROM bulletpoint_theme_comparisons
		UNION ALL
		SELECT bulletpoint_id, bulletpoint_referenced_themes.theme_id, 10 AS priority
		FROM bulletpoint_referenced_themes
	) AS referenced_bulletpoints
	WHERE referenced_bulletpoints.theme_id = in_theme_id
) AS related_bulletpoints ON related_bulletpoints.bulletpoint_id = bulletpoints.id
GROUP BY theme_id, priority
ORDER BY count(theme_id) * priority DESC
LIMIT 10
$BODY$ LANGUAGE sql STABLE ROWS 10;


CREATE TABLE theme_alternative_names (
	id integer GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
	name character varying(255) NOT NULL,
	theme_id integer NOT NULL,
	CONSTRAINT theme_alternative_names_name_not_empty CHECK (NOT is_empty(name)),
	CONSTRAINT theme_alternative_names_theme_id FOREIGN KEY (theme_id) REFERENCES themes(id) ON DELETE CASCADE ON UPDATE RESTRICT
);

CREATE TRIGGER theme_alternative_names_audit_trigger
	AFTER UPDATE OR DELETE OR INSERT
	ON theme_alternative_names
	FOR EACH ROW EXECUTE PROCEDURE audit.trigger_table_audit();


CREATE TABLE theme_tags (
	id integer GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
	theme_id integer NOT NULL,
	tag_id integer NOT NULL,
	CONSTRAINT theme_tags_theme_id FOREIGN KEY (theme_id) REFERENCES themes(id) ON DELETE CASCADE ON UPDATE RESTRICT,
	CONSTRAINT theme_tags_tag_id FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE ON UPDATE RESTRICT,
	CONSTRAINT theme_tags_theme_id_tag_id UNIQUE (theme_id, tag_id)
);

CREATE FUNCTION theme_tags_trigger_row_biu() RETURNS trigger AS $BODY$
BEGIN
	IF (SELECT count(*) >= constant.theme_tags_limit() FROM theme_tags WHERE theme_id = new.theme_id) THEN
		RAISE EXCEPTION USING MESSAGE = format('There can be only %s tags per theme', constant.theme_tags_limit());
	END IF;
	RETURN new;
END;
$BODY$ LANGUAGE plpgsql VOLATILE;

CREATE FUNCTION theme_tags_trigger_row_ad() RETURNS trigger AS $BODY$
BEGIN
	IF EXISTS (SELECT 1 FROM tags WHERE id = old.tag_id) THEN
		PERFORM update_user_tag_reputation(bulletpoints.user_id, old.tag_id, -1::bulletpoint_ratings_point)
		FROM bulletpoints
		JOIN theme_tags ON theme_tags.theme_id = bulletpoints.theme_id;
	END IF;

	RETURN old;
END;
$BODY$ LANGUAGE plpgsql VOLATILE;

CREATE TRIGGER theme_tags_row_biu_trigger
	BEFORE INSERT OR UPDATE
	ON theme_tags
	FOR EACH ROW EXECUTE PROCEDURE theme_tags_trigger_row_biu();

CREATE TRIGGER theme_tags_row_ad_trigger
	AFTER DELETE
	ON theme_tags
	FOR EACH ROW EXECUTE PROCEDURE theme_tags_trigger_row_ad();


CREATE TABLE user_starred_themes (
	id integer GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
	theme_id integer NOT NULL,
	user_id integer NOT NULL,
	starred_at timestamptz NOT NULL DEFAULT now(),
	CONSTRAINT user_starred_themes_theme_id FOREIGN KEY (theme_id) REFERENCES themes(id) ON DELETE CASCADE ON UPDATE RESTRICT,
	CONSTRAINT user_starred_themes_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE RESTRICT,
	CONSTRAINT user_starred_themes_theme_id_user_id UNIQUE (theme_id, user_id)
);

CREATE MATERIALIZED VIEW user_tag_rank_reputations AS
	SELECT tags.id AS tag_id, tags.name, user_id, reputation, dense_rank() OVER (PARTITION BY tag_id ORDER BY reputation DESC) AS rank
	FROM user_tag_reputations
	JOIN tags ON user_tag_reputations.tag_id = tags.id
	ORDER BY rank ASC, reputation DESC;
CREATE UNIQUE INDEX user_tag_rank_reputations_tag_id_user_id_uidx ON user_tag_rank_reputations(tag_id, user_id);

CREATE MATERIALIZED VIEW starred_themes AS
	SELECT theme_id, count(*) AS stars
	FROM user_starred_themes
	GROUP BY theme_id;
CREATE UNIQUE INDEX starred_themes_theme_id_uidx ON starred_themes(theme_id);

CREATE TABLE sources (
	id integer GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
	link text NULL,
	type sources_type NOT NULL
);

CREATE FUNCTION sources_trigger_row_biu() RETURNS trigger AS $BODY$
BEGIN
	new.link = nullif(new.link, '');

	IF new.type = 'web' AND new.link IS NUll THEN
		RAISE EXCEPTION 'Link from web can not be empty.';
	ELSIF new.type = 'head' AND new.link IS NOT NULL THEN
		RAISE EXCEPTION 'Link from head must be empty.';
	END IF;

	RETURN new;
END;
$BODY$ LANGUAGE plpgsql VOLATILE;

CREATE TRIGGER sources_row_biu_trigger
	BEFORE INSERT OR UPDATE
	ON sources
	FOR EACH ROW EXECUTE PROCEDURE sources_trigger_row_biu();

CREATE TRIGGER sources_audit_trigger
	AFTER UPDATE OR DELETE OR INSERT
	ON sources
	FOR EACH ROW EXECUTE PROCEDURE audit.trigger_table_audit();


CREATE VIEW sources_to_ping AS
	SELECT array_agg(id) AS ids, link
	FROM sources
	WHERE link IS NOT NULL
	GROUP BY link;

CREATE TABLE source_pings (
	id integer GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
	source_id integer NOT NULL,
	status http_status NULL,
	ping_at timestamptz NOT NULL DEFAULT now(),
	CONSTRAINT source_pings_source_id FOREIGN KEY (source_id) REFERENCES sources(id) ON DELETE CASCADE ON UPDATE RESTRICT
);

CREATE MATERIALIZED VIEW broken_sources AS
	SELECT source_id
	FROM source_pings
	WHERE now() - INTERVAL '3 days' < ping_at
	AND (status IS NULL OR status BETWEEN 400 AND 599)
	GROUP BY source_id
	HAVING count(*) >= 3;
CREATE UNIQUE INDEX broken_sources_source_id_uidx ON broken_sources(source_id);


CREATE FUNCTION number_of_references(text) RETURNS integer AS $BODY$
	SELECT count(*)::integer FROM regexp_matches($1, '\[\[.+?\]\]', 'g');
$BODY$ LANGUAGE sql IMMUTABLE;

CREATE TABLE bulletpoints (
	id integer GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
	theme_id integer NOT NULL,
	source_id integer NOT NULL UNIQUE,
	user_id integer NOT NULL,
	content character varying(255) NOT NULL,
	created_at timestamptz NOT NULL DEFAULT now(),
	is_contribution boolean NOT NULL,
	CONSTRAINT bulletpoints_content_not_empty CHECK (NOT is_empty(content)),
	CONSTRAINT bulletpoints_theme_id FOREIGN KEY (theme_id) REFERENCES themes(id) ON DELETE CASCADE ON UPDATE RESTRICT,
	CONSTRAINT bulletpoints_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE RESTRICT
);

CREATE TRIGGER bulletpoints_audit_trigger
	AFTER UPDATE OR DELETE OR INSERT
	ON bulletpoints
	FOR EACH ROW EXECUTE PROCEDURE audit.trigger_table_audit();

CREATE FUNCTION bulletpoints_trigger_row_ai() RETURNS trigger AS $BODY$
BEGIN
	INSERT INTO bulletpoint_ratings (point, user_id, bulletpoint_id) VALUES (1, new.user_id, new.id);

	RETURN new;
END;
$BODY$ LANGUAGE plpgsql VOLATILE;

CREATE FUNCTION bulletpoints_trigger_row_ad() RETURNS trigger AS $BODY$
BEGIN
	DELETE FROM sources WHERE id = old.source_id;

	RETURN old;
END;
$BODY$ LANGUAGE plpgsql VOLATILE;

CREATE FUNCTION bulletpoints_trigger_row_bd() RETURNS trigger AS $BODY$
DECLARE
	v_successor_root_bulletpoint_id integer;
BEGIN
	SELECT bulletpoint_group_successor(old.id) INTO v_successor_root_bulletpoint_id;

	UPDATE bulletpoint_groups
	SET root_bulletpoint_id = v_successor_root_bulletpoint_id
	WHERE root_bulletpoint_id = old.id AND bulletpoint_id != v_successor_root_bulletpoint_id;

	RETURN old;
END;
$BODY$ LANGUAGE plpgsql VOLATILE;

CREATE TRIGGER bulletpoints_row_bd_trigger
	BEFORE DELETE
	ON bulletpoints
	FOR EACH ROW EXECUTE PROCEDURE bulletpoints_trigger_row_bd();

CREATE TRIGGER bulletpoints_row_ai_trigger
	AFTER INSERT
	ON bulletpoints
	FOR EACH ROW EXECUTE PROCEDURE bulletpoints_trigger_row_ai();

CREATE TRIGGER bulletpoints_row_ad_trigger
	AFTER DELETE
	ON bulletpoints
	FOR EACH ROW EXECUTE PROCEDURE bulletpoints_trigger_row_ad();

CREATE VIEW contributed_bulletpoints AS
	SELECT * FROM bulletpoints WHERE is_contribution = TRUE;

CREATE VIEW public_bulletpoints AS
	SELECT * FROM bulletpoints WHERE is_contribution = FALSE;

CREATE FUNCTION bulletpoints_trigger_row_iiu() RETURNS trigger AS $BODY$
BEGIN
	IF TG_TABLE_NAME = 'contributed_bulletpoints' THEN
		new.is_contribution = TRUE;
	ELSIF TG_TABLE_NAME = 'public_bulletpoints' THEN
		new.is_contribution = FALSE;
	ELSE
		RAISE EXCEPTION USING MESSAGE = format('Trigger for table "%s" is not defined.', TG_TABLE_NAME);
	END IF;

	IF TG_OP = 'INSERT' THEN
		INSERT INTO bulletpoints (theme_id, source_id, user_id, content, created_at, is_contribution) VALUES (
			new.theme_id,
			new.source_id,
			new.user_id,
			new.content,
			COALESCE(new.created_at, now()),
			new.is_contribution
		)
		RETURNING * INTO new;
	ELSIF TG_OP = 'UPDATE' AND old IS DISTINCT FROM new THEN
		UPDATE bulletpoints
		SET theme_id = new.theme_id, source_id = new.source_id, user_id = new.user_id, content = new.content, created_at = new.created_at, is_contribution = new.is_contribution
		WHERE id = new.id
		RETURNING * INTO new;
	END IF;

	RETURN new;
END
$BODY$ LANGUAGE plpgsql VOLATILE;

CREATE TRIGGER contributed_bulletpoints_trigger_row_iiu
	INSTEAD OF INSERT OR UPDATE
	ON contributed_bulletpoints
	FOR EACH ROW EXECUTE PROCEDURE bulletpoints_trigger_row_iiu();

CREATE TRIGGER public_bulletpoints_trigger_row_iiu
	INSTEAD OF INSERT OR UPDATE
	ON public_bulletpoints
	FOR EACH ROW EXECUTE PROCEDURE bulletpoints_trigger_row_iiu();


CREATE TABLE bulletpoint_theme_comparisons (
	id integer GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
	bulletpoint_id integer NOT NULL,
	theme_id integer NOT NULL,
	CONSTRAINT bulletpoint_theme_comparisons_bulletpoint_id FOREIGN KEY (bulletpoint_id) REFERENCES bulletpoints(id) ON DELETE CASCADE ON UPDATE RESTRICT,
	CONSTRAINT bulletpoint_theme_comparisons_theme_id FOREIGN KEY (theme_id) REFERENCES themes(id) ON DELETE CASCADE ON UPDATE RESTRICT
);

CREATE FUNCTION bulletpoint_theme_comparisons_trigger_row_biu() RETURNS trigger AS $BODY$
DECLARE
	v_theme_from_bulletpoint integer;
BEGIN
	SELECT theme_id INTO v_theme_from_bulletpoint FROM bulletpoints WHERE id = new.bulletpoint_id;

	IF new.theme_id = v_theme_from_bulletpoint THEN
		RAISE EXCEPTION 'Compared theme must differ from the bulletpoint assigned one.';
	END IF;

	IF (
		NOT EXISTS(
			SELECT tag_id
			FROM theme_tags
			WHERE theme_id = new.theme_id
			INTERSECT
			SELECT tag_id
			FROM theme_tags
			WHERE theme_id = v_theme_from_bulletpoint
		)
	) THEN
		RAISE EXCEPTION 'Themes must have some common tags.';
	END IF;

	RETURN new;
END;
$BODY$ LANGUAGE plpgsql VOLATILE;

CREATE TRIGGER bulletpoint_theme_comparisons_row_biu_trigger
	BEFORE INSERT OR UPDATE
	ON bulletpoint_theme_comparisons
	FOR EACH ROW EXECUTE PROCEDURE bulletpoint_theme_comparisons_trigger_row_biu();


CREATE TABLE bulletpoint_referenced_themes (
	id integer GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
	theme_id integer NOT NULL,
	bulletpoint_id integer NOT NULL,
	CONSTRAINT bulletpoint_referenced_themes_theme_id FOREIGN KEY (theme_id) REFERENCES themes(id) ON DELETE CASCADE ON UPDATE RESTRICT,
	CONSTRAINT bulletpoint_referenced_themes_bulletpoint_id FOREIGN KEY (bulletpoint_id) REFERENCES bulletpoints(id) ON DELETE CASCADE ON UPDATE RESTRICT
);

CREATE FUNCTION bulletpoint_referenced_themes_trigger_row_biu() RETURNS trigger AS $BODY$
BEGIN
	IF (SELECT theme_id = new.theme_id FROM bulletpoints WHERE id = new.bulletpoint_id) THEN
		RAISE EXCEPTION 'Referenced theme must differ from the assigned.';
	END IF;

	IF TG_OP = 'INSERT' THEN
		IF number_of_references((SELECT content FROM bulletpoints WHERE id = new.bulletpoint_id)) = 0 THEN
			RAISE EXCEPTION 'Bulletpoint does not include place for reference.';
		END IF;
	END IF;

	RETURN new;
END;
$BODY$ LANGUAGE plpgsql VOLATILE;

CREATE TRIGGER bulletpoint_referenced_themes_row_biu_trigger
	BEFORE INSERT OR UPDATE
	ON bulletpoint_referenced_themes
	FOR EACH ROW EXECUTE PROCEDURE bulletpoint_referenced_themes_trigger_row_biu();


CREATE MATERIALIZED VIEW bulletpoint_reputations AS
	SELECT bulletpoints.id AS bulletpoint_id, sum(user_tag_reputations.reputation) AS reputation
	FROM public_bulletpoints AS bulletpoints
	JOIN themes ON themes.id = bulletpoints.theme_id
	JOIN theme_tags ON theme_tags.theme_id = themes.id
	JOIN user_tag_reputations ON user_tag_reputations.user_id = bulletpoints.user_id AND user_tag_reputations.tag_id = theme_tags.tag_id
	GROUP BY bulletpoints.id;
CREATE UNIQUE INDEX bulletpoint_reputations_bulletpoint_id_uidx ON bulletpoint_reputations(bulletpoint_id);


CREATE TABLE bulletpoint_ratings (
	id integer GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
	point bulletpoint_ratings_point NOT NULL,
	user_id integer NOT NULL,
	bulletpoint_id integer NOT NULL,
	rated_at timestamptz NOT NULL DEFAULT now(),
	CONSTRAINT bulletpoint_ratings_bulletpoint_id FOREIGN KEY (bulletpoint_id) REFERENCES bulletpoints(id) ON DELETE CASCADE ON UPDATE RESTRICT,
	CONSTRAINT bulletpoint_ratings_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE RESTRICT,
	CONSTRAINT bulletpoint_ratings_user_id_bulletpoint_id UNIQUE (user_id, bulletpoint_id)
);

CREATE FUNCTION bulletpoint_ratings_trigger_row_aiud() RETURNS trigger AS $BODY$
DECLARE
	r bulletpoint_ratings;
BEGIN
	r = CASE TG_OP WHEN 'DELETE' THEN old ELSE new END;

	PERFORM update_user_tag_reputation(bulletpoints.user_id, theme_tags.tag_id, r.point)
	FROM public_bulletpoints AS bulletpoints
	JOIN theme_tags ON theme_tags.theme_id = bulletpoints.theme_id
	WHERE bulletpoints.id = r.bulletpoint_id;

	IF TG_OP IN ('INSERT', 'UPDATE') THEN
		INSERT INTO bulletpoint_rating_summary (bulletpoint_id, up_points, down_points)
		SELECT
			new.bulletpoint_id,
			COALESCE(sum(point) FILTER (WHERE point = 1), 0),
			abs(COALESCE(sum(point) FILTER (WHERE point = -1), 0))
		FROM public.bulletpoint_ratings
		WHERE bulletpoint_id = new.bulletpoint_id
		ON CONFLICT (bulletpoint_id)
		DO UPDATE SET up_points = EXCLUDED.up_points, down_points = EXCLUDED.down_points;
	END IF;

	RETURN r;
END;
$BODY$ LANGUAGE plpgsql VOLATILE;

CREATE TRIGGER bulletpoint_ratings_row_aiud_trigger
	AFTER INSERT OR UPDATE OR DELETE
	ON bulletpoint_ratings
	FOR EACH ROW EXECUTE PROCEDURE bulletpoint_ratings_trigger_row_aiud();


CREATE TABLE bulletpoint_rating_summary (
	id integer GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
	bulletpoint_id integer NOT NULL UNIQUE,
	up_points integer NOT NULL,
	down_points integer NOT NULL,
	CONSTRAINT bulletpoint_rating_summary_bulletpoint_id FOREIGN KEY (bulletpoint_id) REFERENCES bulletpoints(id) ON DELETE CASCADE ON UPDATE RESTRICT,
	CONSTRAINT bulletpoint_rating_summary_up_points_positive CHECK (up_points >= 0),
	CONSTRAINT bulletpoint_rating_summary_down_points_positive CHECK (down_points >= 0)
);


CREATE TABLE bulletpoint_groups (
	id integer GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
	bulletpoint_id integer NOT NULL UNIQUE,
	root_bulletpoint_id integer NOT NULL,
	grouped_at timestamptz NOT NULL DEFAULT now(),
	CONSTRAINT bulletpoint_groups_bulletpoint_not_in_self CHECK (bulletpoint_id != root_bulletpoint_id),
	CONSTRAINT bulletpoint_groups_bulletpoint_id FOREIGN KEY (bulletpoint_id) REFERENCES bulletpoints(id) ON DELETE CASCADE ON UPDATE RESTRICT,
	CONSTRAINT bulletpoint_groups_root_bulletpoint_id FOREIGN KEY (root_bulletpoint_id) REFERENCES bulletpoints(id) ON DELETE CASCADE ON UPDATE RESTRICT
);

CREATE FUNCTION refresh_bulletpoint_group_successors() RETURNS void AS $BODY$
BEGIN
	WITH deleted_groups AS (
		DELETE FROM bulletpoint_groups
		RETURNING *
	)
	INSERT INTO bulletpoint_groups (bulletpoint_id, root_bulletpoint_id)
	SELECT new_groups.bulletpoint_id, new_groups.root_bulletpoint_id FROM (
		SELECT array_agg(bulletpoint_id) AS bulletpoint_id, root_bulletpoint_id
		FROM deleted_groups
		GROUP BY root_bulletpoint_id
	) grouped
	JOIN LATERAL (
		SELECT id AS bulletpoint_id, first_value(id) OVER () AS root_bulletpoint_id
		FROM web.bulletpoints
		WHERE id = ANY(grouped.bulletpoint_id || grouped.root_bulletpoint_id)
	) new_groups ON TRUE
	WHERE new_groups.bulletpoint_id != new_groups.root_bulletpoint_id;
END;
$BODY$ LANGUAGE plpgsql VOLATILE;

CREATE FUNCTION bulletpoint_group_successor(in_root_bulletpoint_id integer, include_self boolean DEFAULT FALSE) RETURNS SETOF integer AS $BODY$
BEGIN
	RETURN QUERY SELECT id FROM web.bulletpoints
	WHERE id IN (
		SELECT bulletpoint_id
		FROM bulletpoint_groups
		WHERE root_bulletpoint_id = in_root_bulletpoint_id
		UNION ALL
		SELECT CASE include_self WHEN TRUE THEN in_root_bulletpoint_id ELSE NULL END
	)
	LIMIT 1;
END;
$BODY$ LANGUAGE plpgsql VOLATILE ROWS 1;

CREATE FUNCTION bulletpoint_groups_trigger_row_biu() RETURNS trigger AS $BODY$
DECLARE
	v_bulletpoint_theme_id integer;
	v_root_bulletpoint_theme_id integer;
BEGIN
	SELECT theme_id INTO v_bulletpoint_theme_id FROM bulletpoints WHERE id = new.bulletpoint_id;
	SELECT theme_id INTO v_root_bulletpoint_theme_id FROM bulletpoints WHERE id = new.root_bulletpoint_id;

	IF v_bulletpoint_theme_id != v_root_bulletpoint_theme_id THEN
		RAISE EXCEPTION 'Bulletpoints do not belong to the same theme.';
	END IF;

	RETURN new;
END;
$BODY$ LANGUAGE plpgsql VOLATILE;

CREATE FUNCTION bulletpoint_groups_trigger_row_ai() RETURNS trigger AS $BODY$
DECLARE
	v_successor_root_bulletpoint_id integer;
BEGIN
	SELECT bulletpoint_group_successor(new.root_bulletpoint_id, include_self := TRUE) INTO v_successor_root_bulletpoint_id;

	IF v_successor_root_bulletpoint_id != new.root_bulletpoint_id THEN
		WITH deleted_group AS (
			DELETE FROM bulletpoint_groups
			WHERE bulletpoint_id = new.bulletpoint_id
			RETURNING root_bulletpoint_id
		)
		INSERT INTO bulletpoint_groups (bulletpoint_id, root_bulletpoint_id)
		SELECT root_bulletpoint_id, new.bulletpoint_id FROM deleted_group;

		UPDATE bulletpoint_groups
		SET root_bulletpoint_id = v_successor_root_bulletpoint_id
		WHERE root_bulletpoint_id = new.root_bulletpoint_id;
	END IF;

	RETURN new;
END;
$BODY$ LANGUAGE plpgsql VOLATILE;

CREATE TRIGGER bulletpoint_groups_row_ai_trigger
	AFTER INSERT
	ON bulletpoint_groups
	FOR EACH ROW EXECUTE PROCEDURE bulletpoint_groups_trigger_row_ai();

CREATE TRIGGER bulletpoint_groups_row_biu_trigger
	BEFORE INSERT OR UPDATE
	ON bulletpoint_groups
	FOR EACH ROW EXECUTE PROCEDURE bulletpoint_groups_trigger_row_biu();


-- views
CREATE SCHEMA web;

CREATE VIEW web.starred_tags AS
	SELECT DISTINCT tags.id, tags.name, user_starred_themes.user_id
	FROM user_starred_themes
	JOIN theme_tags ON theme_tags.theme_id = user_starred_themes.theme_id
	JOIN tags ON tags.id = theme_tags.tag_id;


CREATE VIEW web.themes AS
	SELECT
		themes.id, themes.name, json_tags.tags, themes.created_at,
		"references".url AS reference_url,
		broken_references.reference_id IS NOT NULL AS reference_is_broken,
		users.id AS user_id,
		COALESCE(array_theme_alternative_names.alternative_names, ARRAY[]::text[]) AS alternative_names,
		user_starred_themes.id IS NOT NULL AS is_starred,
		user_starred_themes.starred_at,
		ARRAY(SELECT related_themes(themes.id)) AS related_themes_id,
		unique_theme_bulletpoints.theme_id IS NULL AS is_empty
	FROM public.themes
	JOIN users ON users.id = themes.user_id
	LEFT JOIN "references" ON "references".id = themes.reference_id
	LEFT JOIN broken_references ON broken_references.reference_id = themes.reference_id
	LEFT JOIN (
		SELECT theme_id, jsonb_agg(tags.*) AS tags
		FROM theme_tags
		JOIN tags ON tags.id = theme_tags.tag_id
		GROUP BY theme_id
	) AS json_tags ON json_tags.theme_id = themes.id
	LEFT JOIN (
		SELECT theme_id, array_agg(name) AS alternative_names
		FROM theme_alternative_names
		GROUP BY theme_id
	) AS array_theme_alternative_names ON array_theme_alternative_names.theme_id = themes.id
	LEFT JOIN user_starred_themes ON user_starred_themes.theme_id = themes.id AND user_starred_themes.user_id = globals_get_user()
	LEFT JOIN (
		SELECT DISTINCT theme_id
		FROM bulletpoints
	) AS unique_theme_bulletpoints ON unique_theme_bulletpoints.theme_id = public.themes.id;

CREATE FUNCTION web.themes_trigger_row_ii() RETURNS trigger AS $BODY$
DECLARE
	v_theme_id integer;
BEGIN
	WITH inserted_reference AS (
		INSERT INTO public."references" (url) VALUES (new.reference_url)
		RETURNING id
	)
	INSERT INTO public.themes (name, reference_id, user_id) VALUES (new.name, (SELECT id FROM inserted_reference), new.user_id)
	RETURNING id INTO v_theme_id;

	INSERT INTO public.theme_tags (theme_id, tag_id)
	SELECT v_theme_id, r.tag::integer FROM jsonb_array_elements(new.tags) AS r(tag);

	INSERT INTO public.theme_alternative_names (theme_id, name)
	SELECT v_theme_id, r.alternative_name FROM unnest(new.alternative_names) AS r(alternative_name);

	new.id = v_theme_id;
	RETURN new;
END
$BODY$ LANGUAGE plpgsql VOLATILE;

CREATE FUNCTION web.themes_trigger_row_iu() RETURNS trigger AS $BODY$
DECLARE
	v_theme public.themes;
BEGIN
	UPDATE public.themes SET name = new.name WHERE id = new.id RETURNING * INTO v_theme;
	UPDATE public."references" SET url = new.reference_url WHERE id = v_theme.reference_id;

	<<l_tags>>
	DECLARE
		v_current_tags integer[];
		v_new_tags integer[];
	BEGIN
		v_current_tags = array_agg(tag_id) FROM public.theme_tags WHERE theme_id = v_theme.id;
		v_new_tags = array_agg(r.tag::integer) FROM jsonb_array_elements(new.tags) AS r(tag);

		IF NOT array_equals(v_current_tags, v_new_tags) THEN
			DELETE FROM public.theme_tags WHERE theme_id = v_theme.id;
			INSERT INTO public.theme_tags (theme_id, tag_id)
			SELECT v_theme.id, r.tag FROM unnest(v_new_tags) AS r(tag);
		END IF;
	END l_tags;

	<<l_alternative_names>>
	DECLARE
		v_current_alternative_names character varying[];
	BEGIN
		v_current_alternative_names = array_agg(name) FROM public.theme_alternative_names WHERE theme_id = v_theme.id;

		IF NOT array_equals(v_current_alternative_names, new.alternative_names) THEN
			DELETE FROM public.theme_alternative_names WHERE theme_id = v_theme.id;
			INSERT INTO public.theme_alternative_names (theme_id, name)
			SELECT v_theme.id, r.alternative_name FROM unnest(new.alternative_names) AS r(alternative_name);
		END IF;
	END l_alternative_names;

	RETURN new;
END
$BODY$ LANGUAGE plpgsql VOLATILE;

CREATE TRIGGER themes_trigger_row_ii
	INSTEAD OF INSERT
	ON web.themes
	FOR EACH ROW EXECUTE PROCEDURE web.themes_trigger_row_ii();

CREATE TRIGGER themes_trigger_row_iu
	INSTEAD OF UPDATE
	ON web.themes
	FOR EACH ROW EXECUTE PROCEDURE web.themes_trigger_row_iu();


CREATE VIEW web.tagged_themes AS
	SELECT tag_id, themes.*
	FROM web.themes
	LEFT JOIN theme_tags ON theme_tags.theme_id = themes.id;


CREATE VIEW web.bulletpoints AS
	SELECT
		bulletpoints.id, bulletpoints.content, bulletpoints.theme_id, bulletpoints.user_id, bulletpoints.created_at,
		sources.link AS source_link,
		sources.type AS source_type,
		broken_sources.source_id IS NOT NULL AS source_is_broken,
			bulletpoint_rating_summary.up_points AS up_rating,
			bulletpoint_rating_summary.down_points AS down_rating,
			(bulletpoint_rating_summary.up_points + bulletpoint_rating_summary.down_points) AS total_rating,
		COALESCE(user_bulletpoint_ratings.user_rating, 0) AS user_rating,
		COALESCE(bulletpoint_referenced_themes.referenced_theme_id, ARRAY[]::integer[]) AS referenced_theme_id,
		COALESCE(bulletpoint_theme_comparisons.compared_theme_id, ARRAY[]::integer[]) AS compared_theme_id,
		bulletpoint_groups.root_bulletpoint_id
	FROM public.public_bulletpoints AS bulletpoints
	LEFT JOIN (
		SELECT bulletpoint_id, CASE user_id WHEN globals_get_user() THEN point ELSE 0 END AS user_rating
		FROM public.bulletpoint_ratings
		WHERE user_id = globals_get_user()
	) AS user_bulletpoint_ratings ON user_bulletpoint_ratings.bulletpoint_id = bulletpoints.id
	LEFT JOIN bulletpoint_rating_summary ON bulletpoint_rating_summary.bulletpoint_id = bulletpoints.id
	LEFT JOIN public.sources ON sources.id = bulletpoints.source_id
	LEFT JOIN public.broken_sources ON broken_sources.source_id = sources.id
	LEFT JOIN public.bulletpoint_reputations ON bulletpoint_reputations.bulletpoint_id = bulletpoints.id
	LEFT JOIN (
		SELECT bulletpoint_id, array_agg(public.bulletpoint_referenced_themes.theme_id) AS referenced_theme_id
		FROM public.bulletpoint_referenced_themes
		GROUP BY bulletpoint_id
	) AS bulletpoint_referenced_themes ON bulletpoint_referenced_themes.bulletpoint_id = bulletpoints.id
	LEFT JOIN (
		SELECT bulletpoint_id, array_agg(public.bulletpoint_theme_comparisons.theme_id) AS compared_theme_id
		FROM public.bulletpoint_theme_comparisons
		GROUP BY bulletpoint_id
	) AS bulletpoint_theme_comparisons ON bulletpoint_theme_comparisons.bulletpoint_id = bulletpoints.id
	LEFT JOIN bulletpoint_groups ON bulletpoint_groups.bulletpoint_id = bulletpoints.id
	ORDER BY total_rating DESC, bulletpoint_reputations.reputation DESC NULLS LAST, length(bulletpoints.content) ASC, created_at DESC, id DESC;

CREATE FUNCTION web.bulletpoints_trigger_row_ii() RETURNS trigger AS $BODY$
DECLARE
	v_bulletpoint_id integer;
	v_source_id integer;
BEGIN
	IF number_of_references(new.content) != array_length(new.referenced_theme_id, 1) THEN
		RAISE EXCEPTION USING MESSAGE = format(
			'Number of referenced themes in text (%s) is not matching with passed ID list (%s).',
			number_of_references(new.content),
			array_length(new.referenced_theme_id, 1)
		 );
	END IF;

	INSERT INTO public.sources (link, type) VALUES (new.source_link, new.source_type) RETURNING id INTO v_source_id;

	IF TG_TABLE_NAME = 'contributed_bulletpoints' THEN
		INSERT INTO public.contributed_bulletpoints (theme_id, source_id, content, user_id) VALUES (
			new.theme_id,
			v_source_id,
			new.content,
			new.user_id
		)
		RETURNING id INTO v_bulletpoint_id;
	ELSIF TG_TABLE_NAME = 'bulletpoints' THEN
		INSERT INTO public.public_bulletpoints (theme_id, source_id, content, user_id) VALUES (
			new.theme_id,
			v_source_id,
			new.content,
			new.user_id
		)
		RETURNING id INTO v_bulletpoint_id;
	ELSE
		RAISE EXCEPTION USING MESSAGE = format('Trigger for table "%s" is not defined.', TG_TABLE_NAME);
	END IF;

	IF new.root_bulletpoint_id IS NOT NULL THEN
		INSERT INTO bulletpoint_groups (bulletpoint_id, root_bulletpoint_id) VALUES (v_bulletpoint_id, new.root_bulletpoint_id);
	END IF;

	INSERT INTO public.bulletpoint_referenced_themes (theme_id, bulletpoint_id)
	SELECT r.theme_id::integer, v_bulletpoint_id FROM unnest(new.referenced_theme_id) AS r(theme_id);

	INSERT INTO public.bulletpoint_theme_comparisons (theme_id, bulletpoint_id)
	SELECT r.theme_id::integer, v_bulletpoint_id FROM unnest(new.compared_theme_id) AS r(theme_id);

	RETURN new;
END
$BODY$ LANGUAGE plpgsql VOLATILE;

CREATE FUNCTION web.bulletpoints_trigger_row_iu() RETURNS trigger AS $BODY$
DECLARE
	v_source_id integer;
BEGIN
	IF number_of_references(new.content) != array_length(new.referenced_theme_id, 1) THEN
		RAISE EXCEPTION USING MESSAGE = format(
			'Number of referenced themes in text (%s) is not matching with passed ID list (%s).',
			number_of_references(new.content),
			array_length(new.referenced_theme_id, 1)
		);
	END IF;

	IF TG_TABLE_NAME = 'contributed_bulletpoints' THEN
		UPDATE public.contributed_bulletpoints SET content = new.content
		WHERE id = new.id
		RETURNING source_id INTO v_source_id;
	ELSIF TG_TABLE_NAME = 'bulletpoints' THEN
		UPDATE public.public_bulletpoints SET content = new.content
		WHERE id = new.id
		RETURNING source_id INTO v_source_id;
	ELSE
		RAISE EXCEPTION USING MESSAGE = format('Trigger for table "%s" is not defined.', TG_TABLE_NAME);
	END IF;

	UPDATE public.sources
	SET link = new.source_link, type = new.source_type
	WHERE id = v_source_id;

	<<l_groups>>
	BEGIN
		IF old.root_bulletpoint_id IS DISTINCT FROM new.root_bulletpoint_id THEN
			IF new.root_bulletpoint_id IS NULL THEN
				DELETE FROM bulletpoint_groups
				WHERE root_bulletpoint_id = old.root_bulletpoint_id
				AND bulletpoint_id = new.id;
			ELSE
				INSERT INTO bulletpoint_groups (bulletpoint_id, root_bulletpoint_id) VALUES (new.id, new.root_bulletpoint_id)
				ON CONFLICT (bulletpoint_id) DO UPDATE SET root_bulletpoint_id = EXCLUDED.root_bulletpoint_id;
			END IF;
		END IF;
	END l_groups;


	<<l_referenced_themes>>
	DECLARE
		v_current_referenced_themes int[];
	BEGIN
		v_current_referenced_themes = array_agg(bulletpoint_id) FROM bulletpoint_referenced_themes WHERE id = new.id;

		IF NOT array_equals(v_current_referenced_themes, new.referenced_theme_id) THEN
			DELETE FROM public.bulletpoint_referenced_themes WHERE bulletpoint_id = new.id;
			INSERT INTO public.bulletpoint_referenced_themes (theme_id, bulletpoint_id)
			SELECT r.theme_id::integer, new.id FROM unnest(new.referenced_theme_id) AS r(theme_id);
		END IF;
	END l_referenced_themes;


	<<l_compared_themes>>
	DECLARE
		v_current_compared_themes int[];
	BEGIN
		v_current_compared_themes = array_agg(bulletpoint_id) FROM bulletpoint_theme_comparisons WHERE id = new.id;

		IF NOT array_equals(v_current_compared_themes, new.compared_theme_id) THEN
			DELETE FROM public.bulletpoint_theme_comparisons WHERE bulletpoint_id = new.id;
			INSERT INTO public.bulletpoint_theme_comparisons (theme_id, bulletpoint_id)
			SELECT r.theme_id::integer, new.id FROM unnest(new.compared_theme_id) AS r(theme_id);
		END IF;
	END l_compared_themes;

	RETURN new;
END
$BODY$ LANGUAGE plpgsql VOLATILE;

CREATE TRIGGER bulletpoints_trigger_row_ii
	INSTEAD OF INSERT
	ON web.bulletpoints
	FOR EACH ROW EXECUTE PROCEDURE web.bulletpoints_trigger_row_ii();

CREATE TRIGGER bulletpoints_trigger_row_iu
	INSTEAD OF UPDATE
	ON web.bulletpoints
	FOR EACH ROW EXECUTE PROCEDURE web.bulletpoints_trigger_row_iu();


CREATE VIEW web.contributed_bulletpoints AS
SELECT
	contributed_bulletpoints.id, contributed_bulletpoints.content, contributed_bulletpoints.theme_id, contributed_bulletpoints.user_id,
	sources.link AS source_link, sources.type AS source_type, broken_sources.source_id IS NOT NULL AS source_is_broken,
	COALESCE(bulletpoint_referenced_themes.referenced_theme_id, ARRAY[]::integer[]) AS referenced_theme_id,
	COALESCE(bulletpoint_theme_comparisons.compared_theme_id, ARRAY[]::integer[]) AS compared_theme_id,
	bulletpoint_groups.root_bulletpoint_id
	FROM public.contributed_bulletpoints
	LEFT JOIN public.sources ON sources.id = contributed_bulletpoints.source_id
	LEFT JOIN public.broken_sources ON broken_sources.source_id = sources.id
	LEFT JOIN (
		SELECT bulletpoint_id, array_agg(public.bulletpoint_referenced_themes.theme_id) AS referenced_theme_id
		FROM public.bulletpoint_referenced_themes
		GROUP BY bulletpoint_id
	) AS bulletpoint_referenced_themes ON bulletpoint_referenced_themes.bulletpoint_id = contributed_bulletpoints.id
	LEFT JOIN (
		SELECT bulletpoint_id, array_agg(public.bulletpoint_theme_comparisons.theme_id) AS compared_theme_id
		FROM public.bulletpoint_theme_comparisons
		GROUP BY bulletpoint_id
	) AS bulletpoint_theme_comparisons ON bulletpoint_theme_comparisons.bulletpoint_id = contributed_bulletpoints.id
	LEFT JOIN bulletpoint_groups ON bulletpoint_groups.bulletpoint_id = contributed_bulletpoints.id
	ORDER BY contributed_bulletpoints.created_at DESC, length(contributed_bulletpoints.content) ASC;

CREATE TRIGGER contributed_bulletpoints_trigger_row_ii
	INSTEAD OF INSERT
	ON web.contributed_bulletpoints
	FOR EACH ROW EXECUTE PROCEDURE web.bulletpoints_trigger_row_ii();

CREATE TRIGGER contributed_bulletpoints_trigger_row_iu
	INSTEAD OF UPDATE
	ON web.contributed_bulletpoints
	FOR EACH ROW EXECUTE PROCEDURE web.bulletpoints_trigger_row_ii();


-- tables
CREATE SCHEMA log;

CREATE TYPE job_statuses AS ENUM (
	'pending',
	'processing',
	'succeed',
	'failed'
);

CREATE TABLE log.cron_jobs (
	id bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
	marked_at timestamp with time zone NOT NULL DEFAULT now(),
	name text NOT NULL,
	self_id integer,
	status job_statuses NOT NULL,
	CONSTRAINT cron_jobs_id_fk FOREIGN KEY (self_id) REFERENCES log.cron_jobs(id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE FUNCTION cron_jobs_trigger_row_bi() RETURNS trigger AS $BODY$
BEGIN
	IF (
		new.status = 'processing' AND (
			SELECT status NOT IN ('succeed', 'failed')
			FROM log.cron_jobs
			WHERE name = new.name
			ORDER BY id DESC
			LIMIT 1
		)
	) THEN
		RAISE EXCEPTION USING MESSAGE = format('Job "%s" can not be run, because previous is not fulfilled.', new.name);
	END IF;

	RETURN new;
END;
$BODY$ LANGUAGE plpgsql VOLATILE;

CREATE TRIGGER cron_jobs_row_bi_trigger
	BEFORE INSERT
	ON log.cron_jobs
	FOR EACH ROW EXECUTE PROCEDURE cron_jobs_trigger_row_bi();


CREATE SCHEMA deploy;

CREATE TABLE deploy.migrations (
	id integer GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
	filename citext NOT NULL UNIQUE,
	migrated_at timestamp with time zone NOT NULL DEFAULT now()
);


CREATE FUNCTION deploy.migrations_to_run(in_filenames text) RETURNS SETOF text AS $BODY$
DECLARE
	v_filenames text[] NOT NULL DEFAULT string_to_array(trim(TRAILING ',' FROM in_filenames), ',');
BEGIN
	IF EXISTS(SELECT filename FROM unnest(v_filenames) AS filenames(filename) WHERE filename NOT ILIKE '%.sql') THEN
		RAISE EXCEPTION USING MESSAGE = 'Filenames must be in format %.sql';
	END IF;

	RETURN QUERY SELECT unnest(v_filenames)
	EXCEPT
	SELECT filename FROM deploy.migrations;
END;
$BODY$ LANGUAGE plpgsql STABLE;
