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

-- constants
CREATE SCHEMA constant;

CREATE FUNCTION constant.guest_id() RETURNS integer AS $BODY$SELECT 0$BODY$ LANGUAGE sql IMMUTABLE;
CREATE FUNCTION constant.sources_type() RETURNS text[] AS $BODY$SELECT ARRAY['web', 'head'];$BODY$ LANGUAGE sql IMMUTABLE;
CREATE FUNCTION constant.bulletpoint_ratings_point_range() RETURNS integer[] AS $BODY$SELECT ARRAY[-1, 0, 1];$BODY$ LANGUAGE sql IMMUTABLE;
CREATE FUNCTION constant.theme_tags_limit() RETURNS integer AS $BODY$SELECT 4;$BODY$ LANGUAGE sql IMMUTABLE;
CREATE FUNCTION constant.roles() RETURNS text[] AS $BODY$SELECT ARRAY['member', 'admin'];$BODY$ LANGUAGE sql IMMUTABLE;

-- types
CREATE TYPE operations AS ENUM ('INSERT', 'UPDATE', 'DELETE');


-- domains
CREATE DOMAIN sources_type AS text CHECK (VALUE = ANY(constant.sources_type()));
CREATE DOMAIN bulletpoint_ratings_point AS integer CHECK (constant.bulletpoint_ratings_point_range() @> ARRAY[VALUE]);
CREATE DOMAIN roles AS text CHECK (VALUE = ANY(constant.roles()));
CREATE DOMAIN usernames AS citext CHECK (VALUE ~ '^[a-zA-Z0-9_]{3,25}$');

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

	RETURN CASE WHEN TG_OP = 'DELETE' THEN old ELSE new END;
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


-- tables
CREATE TABLE "references" (
	id integer GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
	url text NOT NULL
);

CREATE TRIGGER references_audit_trigger
	AFTER UPDATE OR DELETE OR INSERT
	ON "references"
	FOR EACH ROW EXECUTE PROCEDURE audit.trigger_table_audit();


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
	username usernames UNIQUE,
	email citext NOT NULL UNIQUE,
	password text,
	facebook_id bigint UNIQUE,
	google_id bigint UNIQUE,
	role roles NOT NULL DEFAULT 'member'::roles,
	CONSTRAINT users_password_empty_for_3rd_party CHECK (
		password IS NULL AND COALESCE(facebook_id, google_id) IS NOT NULL
		OR password IS NOT NULL AND COALESCE(facebook_id, google_id) IS NULL
	),
	CONSTRAINT users_username_empty_for_3rd_party CHECK (
		username IS NULL AND COALESCE(facebook_id, google_id) IS NOT NULL
		OR username IS NOT NULL AND COALESCE(facebook_id, google_id) IS NULL
	)
);

CREATE FUNCTION create_third_party_user(in_provider text, in_id integer, in_email text) RETURNS SETOF users AS $BODY$
DECLARE
	v_provider_column CONSTANT hstore = hstore(ARRAY['facebook', 'facebook_id', 'google', 'google_id']);
	v_column text;
	v_exists boolean;
BEGIN
	v_column = v_provider_column -> in_provider;

	IF v_column IS NULL THEN
		RAISE EXCEPTION USING MESSAGE = format('Provider "%s" is unknown', in_provider);
	END IF;

	EXECUTE format('SELECT EXISTS(SELECT 1 FROM users WHERE %I = %L)', v_column, in_id) INTO v_exists;
	IF v_exists THEN
		RETURN QUERY EXECUTE format('UPDATE users SET email = %L WHERE %I = %L RETURNING *', in_email, v_column, in_id);
	ELSE
		RETURN QUERY EXECUTE format($$
			INSERT INTO users (email, %I) VALUES (%L, %L)
			ON CONFLICT(email) DO UPDATE SET email = %L, %I = %L
			RETURNING *
		$$, v_column, in_email, in_id, in_email, v_column, in_id, in_id);
	END IF;
END;
$BODY$ LANGUAGE plpgsql VOLATILE ROWS 1;

CREATE FUNCTION users_trigger_row_ai() RETURNS trigger AS $BODY$
BEGIN
	INSERT INTO access.verification_codes (user_id, code, used_at) VALUES (
		new.id,
		format('%s:%s', encode(gen_random_bytes(25), 'hex'), encode(digest(new.id::text, 'sha1'), 'hex')),
		CASE WHEN COALESCE(new.facebook_id, new.google_id) IS NOT NULL THEN now() ELSE NULL END
	);

	RETURN new;
END;
$BODY$ LANGUAGE plpgsql VOLATILE;

CREATE TRIGGER users_row_ai_trigger
	AFTER INSERT
	ON users
	FOR EACH ROW EXECUTE PROCEDURE users_trigger_row_ai();


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
BEGIN
	IF in_point = 1 THEN
		INSERT INTO user_tag_reputations (user_id, tag_id, reputation) VALUES (in_user_id, in_tag_id, 1)
		ON CONFLICT (user_id, tag_id) DO UPDATE SET reputation = user_tag_reputations.reputation + 1;
	ELSE
		UPDATE user_tag_reputations SET reputation = greatest(reputation - 1, 0)
		WHERE user_id = in_user_id AND tag_id = in_tag_id;
	END IF;
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
	CONSTRAINT forgotten_passwords_expire_at_future CHECK (expire_at >= NOW()),
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
	IF ((SELECT count(*) >= constant.theme_tags_limit() FROM theme_tags WHERE theme_id = new.theme_id)) THEN
		RAISE EXCEPTION USING MESSAGE = format('There can be only %s tags per theme', constant.theme_tags_limit());
	END IF;
	RETURN new;
END;
$BODY$ LANGUAGE plpgsql VOLATILE;

CREATE FUNCTION theme_tags_trigger_row_ad() RETURNS trigger AS $BODY$
BEGIN
	PERFORM update_user_tag_reputation(bulletpoints.user_id, old.tag_id, -1::bulletpoint_ratings_point)
	FROM bulletpoints
	JOIN theme_tags ON theme_tags.theme_id = bulletpoints.theme_id;

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
	IF new.type = 'web' AND is_empty(new.link) THEN
		RAISE EXCEPTION 'Link from web can not be empty.';
	ELSIF new.type = 'head' AND NOT is_empty(new.link) THEN
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
	ELSIF TG_OP = 'UPDATE' THEN
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
	v_theme_from_bulletpoint = theme_id FROM bulletpoints WHERE id = new.bulletpoint_id;

	IF (new.theme_id = v_theme_from_bulletpoint) THEN
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
	IF ((SELECT theme_id = new.theme_id FROM bulletpoints WHERE id = new.bulletpoint_id)) THEN
		RAISE EXCEPTION 'Referenced theme must differ from the assigned.';
	END IF;

	IF TG_OP = 'INSERT' THEN
		IF (number_of_references((SELECT content FROM public_bulletpoints WHERE id = new.bulletpoint_id)) = 0) THEN
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
	r = CASE WHEN TG_OP = 'DELETE' THEN old ELSE new END;

	PERFORM update_user_tag_reputation(bulletpoints.user_id, theme_tags.tag_id, r.point)
	FROM public_bulletpoints AS bulletpoints
	JOIN theme_tags ON theme_tags.theme_id = bulletpoints.theme_id
	WHERE bulletpoints.id = r.bulletpoint_id;

	RETURN r;
END;
$BODY$ LANGUAGE plpgsql VOLATILE;

CREATE TRIGGER bulletpoint_ratings_row_aiud_trigger
	AFTER INSERT OR UPDATE OR DELETE
	ON bulletpoint_ratings
	FOR EACH ROW EXECUTE PROCEDURE bulletpoint_ratings_trigger_row_aiud();


-- views
CREATE SCHEMA web;

CREATE VIEW web.themes AS
	SELECT
		themes.id, themes.name, json_tags.tags, themes.created_at,
		"references".url AS reference_url,
		users.id AS user_id,
		COALESCE(json_theme_alternative_names.alternative_names, '[]') AS alternative_names,
		user_starred_themes.id IS NOT NULL AS is_starred,
		array_to_json(ARRAY(SELECT related_themes(themes.id)))::jsonb AS related_themes_id
	FROM public.themes
	JOIN users ON users.id = themes.user_id
	LEFT JOIN "references" ON "references".id = themes.reference_id
	LEFT JOIN (
		SELECT theme_id, jsonb_agg(tags.*) AS tags
		FROM theme_tags
		JOIN tags ON tags.id = theme_tags.tag_id
		GROUP BY theme_id
	) AS json_tags ON json_tags.theme_id = themes.id
	LEFT JOIN (
		SELECT theme_id, jsonb_agg(name) AS alternative_names
		FROM theme_alternative_names
		GROUP BY theme_id
	) AS json_theme_alternative_names ON json_theme_alternative_names.theme_id = themes.id
	LEFT JOIN user_starred_themes ON user_starred_themes.theme_id = themes.id AND user_starred_themes.user_id = globals_get_user();

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
	SELECT v_theme_id, r.alternative_name FROM jsonb_array_elements_text(new.alternative_names) AS r(alternative_name);

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

		IF (NOT array_equals(v_current_tags, v_new_tags)) THEN
			DELETE FROM public.theme_tags WHERE theme_id = v_theme.id;
			INSERT INTO public.theme_tags (theme_id, tag_id)
			SELECT v_theme.id, r.tag FROM unnest(v_new_tags) AS r(tag);
		END IF;
	END l_tags;

	<<l_alternative_names>>
	DECLARE
		v_current_alternative_names text[];
		v_new_alternative_names text[];
	BEGIN
		v_current_alternative_names = array_agg(name) FROM public.theme_alternative_names WHERE theme_id = v_theme.id;
		v_new_alternative_names = array_agg(r.alternative_name) FROM jsonb_array_elements_text(new.alternative_names) AS r(alternative_name);

		IF (NOT array_equals(v_current_alternative_names, v_new_alternative_names)) THEN
			DELETE FROM public.theme_alternative_names WHERE theme_id = v_theme.id;
			INSERT INTO public.theme_alternative_names (theme_id, name)
			SELECT v_theme.id, r.alternative_name FROM unnest(v_new_alternative_names) AS r(alternative_name);
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
		bulletpoints.id, bulletpoints.content, bulletpoints.theme_id, bulletpoints.user_id,
		sources.link AS source_link, sources.type AS source_type,
			bulletpoint_ratings.up AS up_rating,
			abs(bulletpoint_ratings.down) AS down_rating,
			(bulletpoint_ratings.up + bulletpoint_ratings.down) AS total_rating,
		bulletpoint_ratings.user_rating,
		COALESCE(bulletpoint_referenced_themes.referenced_theme_id, '[]') AS referenced_theme_id,
		COALESCE(bulletpoint_theme_comparisons.compared_theme_id, '[]') AS compared_theme_id
	FROM public.public_bulletpoints AS bulletpoints
	-- TODO: will be pre-counted
	JOIN (
		SELECT
			DISTINCT ON (bulletpoint_ratings.bulletpoint_id) bulletpoint_ratings.bulletpoint_id,
			COALESCE(sum(point) FILTER (WHERE point = 1) OVER (PARTITION BY bulletpoint_ratings.bulletpoint_id), 0) AS up,
			COALESCE(sum(point) FILTER (WHERE point = -1) OVER (PARTITION BY bulletpoint_ratings.bulletpoint_id), 0) AS down,
			COALESCE(user_bulletpoint_ratings.user_rating, 0) AS user_rating
		FROM public.bulletpoint_ratings
		LEFT JOIN (
			SELECT bulletpoint_id, CASE WHEN user_id = globals_get_user() THEN point ELSE 0 END AS user_rating
			FROM public.bulletpoint_ratings
			WHERE user_id = globals_get_user()
		) AS user_bulletpoint_ratings ON user_bulletpoint_ratings.bulletpoint_id = bulletpoint_ratings.bulletpoint_id
	) AS bulletpoint_ratings ON bulletpoint_ratings.bulletpoint_id = bulletpoints.id
	LEFT JOIN public.sources ON sources.id = bulletpoints.source_id
	LEFT JOIN public.bulletpoint_reputations ON bulletpoint_reputations.bulletpoint_id = bulletpoints.id
	LEFT JOIN (
		SELECT bulletpoint_id, jsonb_agg(public.bulletpoint_referenced_themes.theme_id) AS referenced_theme_id
		FROM public.bulletpoint_referenced_themes
		GROUP BY bulletpoint_id
	) AS bulletpoint_referenced_themes ON bulletpoint_referenced_themes.bulletpoint_id = bulletpoints.id
	LEFT JOIN (
		SELECT bulletpoint_id, jsonb_agg(public.bulletpoint_theme_comparisons.theme_id) AS compared_theme_id
		FROM public.bulletpoint_theme_comparisons
		GROUP BY bulletpoint_id
	) AS bulletpoint_theme_comparisons ON bulletpoint_theme_comparisons.bulletpoint_id = bulletpoints.id
	ORDER BY total_rating DESC, bulletpoint_reputations.reputation DESC, length(bulletpoints.content) ASC, created_at DESC, id DESC;

CREATE FUNCTION web.bulletpoints_trigger_row_ii() RETURNS trigger AS $BODY$
DECLARE
	v_bulletpoint_id integer;
	v_source_id integer;
BEGIN
	IF (number_of_references(new.content) != jsonb_array_length(new.referenced_theme_id)) THEN
		RAISE EXCEPTION USING MESSAGE = format(
			'Number of referenced themes in text (%s) is not matching with passed ID list (%s).',
			number_of_references(new.content),
			jsonb_array_length(new.referenced_theme_id)
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

	INSERT INTO public.bulletpoint_referenced_themes (theme_id, bulletpoint_id)
	SELECT r.theme_id::integer, v_bulletpoint_id FROM jsonb_array_elements(new.referenced_theme_id) AS r(theme_id);

	INSERT INTO public.bulletpoint_theme_comparisons (theme_id, bulletpoint_id)
	SELECT r.theme_id::integer, v_bulletpoint_id FROM jsonb_array_elements(new.compared_theme_id) AS r(theme_id);

	RETURN new;
END
$BODY$ LANGUAGE plpgsql VOLATILE;

CREATE FUNCTION web.bulletpoints_trigger_row_iu() RETURNS trigger AS $BODY$
DECLARE
	v_source_id integer;
BEGIN
	IF (number_of_references(new.content) != jsonb_array_length(new.referenced_theme_id)) THEN
		RAISE EXCEPTION USING MESSAGE = format(
			'Number of referenced themes in text (%s) is not matching with passed ID list (%s).',
			number_of_references(new.content),
			jsonb_array_length(new.referenced_theme_id)
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


	<<l_referenced_themes>>
	DECLARE
		v_current_referenced_themes int[];
		v_new_referenced_themes int[];
	BEGIN
		v_current_referenced_themes = array_agg(bulletpoint_id) FROM bulletpoint_referenced_themes WHERE id = new.id;
		v_new_referenced_themes = array_agg(r.theme_id::integer) FROM jsonb_array_elements(new.referenced_theme_id) AS r(theme_id);

		IF (NOT array_equals(v_current_referenced_themes, v_new_referenced_themes)) THEN
			DELETE FROM public.bulletpoint_referenced_themes WHERE bulletpoint_id = new.id;
			INSERT INTO public.bulletpoint_referenced_themes (theme_id, bulletpoint_id)
			SELECT r.theme_id::integer, new.id FROM jsonb_array_elements(new.referenced_theme_id) AS r(theme_id);
		END IF;
	END l_referenced_themes;


	<<l_compared_themes>>
	DECLARE
		v_current_compared_themes int[];
		v_new_compared_themes int[];
	BEGIN
		v_current_compared_themes = array_agg(bulletpoint_id) FROM bulletpoint_theme_comparisons WHERE id = new.id;
		v_new_compared_themes = array_agg(r.theme_id::integer) FROM jsonb_array_elements(new.compared_theme_id) AS r(theme_id);

		IF (NOT array_equals(v_current_compared_themes, v_new_compared_themes)) THEN
			DELETE FROM public.bulletpoint_theme_comparisons WHERE bulletpoint_id = new.id;
			INSERT INTO public.bulletpoint_theme_comparisons (theme_id, bulletpoint_id)
			SELECT r.theme_id::integer, new.id FROM jsonb_array_elements(new.compared_theme_id) AS r(theme_id);
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
	sources.link AS source_link, sources.type AS source_type,
	COALESCE(bulletpoint_referenced_themes.referenced_theme_id, '[]') AS referenced_theme_id,
	COALESCE(bulletpoint_theme_comparisons.compared_theme_id, '[]') AS compared_theme_id
	FROM public.contributed_bulletpoints
	LEFT JOIN public.sources ON sources.id = contributed_bulletpoints.source_id
	LEFT JOIN (
		SELECT bulletpoint_id, jsonb_agg(public.bulletpoint_referenced_themes.theme_id) AS referenced_theme_id
		FROM public.bulletpoint_referenced_themes
		GROUP BY bulletpoint_id
	) AS bulletpoint_referenced_themes ON bulletpoint_referenced_themes.bulletpoint_id = contributed_bulletpoints.id
	LEFT JOIN (
		SELECT bulletpoint_id, jsonb_agg(public.bulletpoint_theme_comparisons.theme_id) AS compared_theme_id
		FROM public.bulletpoint_theme_comparisons
		GROUP BY bulletpoint_id
	) AS bulletpoint_theme_comparisons ON bulletpoint_theme_comparisons.bulletpoint_id = contributed_bulletpoints.id
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
	id integer GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
	marked_at timestamp with time zone NOT NULL DEFAULT now(),
	name text NOT NULL,
	self_id integer,
	status job_statuses NOT NULL,
	CONSTRAINT cron_jobs_id_fk FOREIGN KEY (self_id) REFERENCES log.cron_jobs(id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE FUNCTION cron_jobs_trigger_row_bi() RETURNS trigger AS $BODY$
BEGIN
	IF (new.status = 'processing' AND (
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
	v_filenames text[];
BEGIN
	v_filenames = string_to_array(trim(TRAILING ',' FROM in_filenames), ',');

	IF EXISTS(SELECT filename FROM unnest(v_filenames) AS filenames(filename) WHERE filename NOT ILIKE '%.sql') THEN
		RAISE EXCEPTION USING MESSAGE = 'Filenames must be in format %.sql';
	END IF;

	RETURN QUERY SELECT unnest(v_filenames)
	EXCEPT
	SELECT filename FROM deploy.migrations;
END;
$BODY$ LANGUAGE plpgsql STABLE;
