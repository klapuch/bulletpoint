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

CREATE FUNCTION constant.guest_id() RETURNS integer AS $$SELECT 0$$ LANGUAGE sql IMMUTABLE;
CREATE FUNCTION constant.sources_type() RETURNS text[] AS $$SELECT ARRAY['web', 'head'];$$ LANGUAGE sql IMMUTABLE;
CREATE FUNCTION constant.bulletpoint_ratings_point_range() RETURNS integer[] AS $$SELECT ARRAY[-1, 0, 1];$$ LANGUAGE sql IMMUTABLE;
CREATE FUNCTION constant.theme_tags_limit() RETURNS integer AS $$SELECT 4;$$ LANGUAGE sql IMMUTABLE;
CREATE FUNCTION constant.roles() RETURNS text[] AS $$SELECT ARRAY['member', 'admin'];$$ LANGUAGE sql IMMUTABLE;


-- domains
CREATE DOMAIN sources_type AS text CHECK (VALUE = ANY(constant.sources_type()));
CREATE DOMAIN bulletpoint_ratings_point AS integer CHECK (constant.bulletpoint_ratings_point_range() @> ARRAY[VALUE]);
CREATE DOMAIN roles AS text CHECK (VALUE = ANY(constant.roles()));

-- schema audit
CREATE TYPE operations AS ENUM ('INSERT', 'UPDATE', 'DELETE');


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
DECLARE
	r record;
BEGIN
	IF (TG_OP = 'DELETE') THEN
		r = old;
	ELSE
		r = new;
	END IF;

	EXECUTE format(
		'INSERT INTO audit.history ("table", operation, user_id, old, new) VALUES (%L, %L, %L, %L, %L)',
		TG_TABLE_NAME,
		TG_OP,
		globals_get_user(),
		CASE WHEN TG_OP IN ('UPDATE', 'DELETE') THEN row_to_json(old) ELSE NULL END,
		CASE WHEN TG_OP IN ('UPDATE', 'INSERT') THEN row_to_json(new) ELSE NULL END
	);

	RETURN r;
END;
$BODY$ LANGUAGE plpgsql;


-- functions
CREATE FUNCTION globals_get_variable(in_variable text) RETURNS text AS $BODY$
BEGIN
	RETURN nullif(current_setting(format('globals.%s', in_variable)), '');
	EXCEPTION WHEN OTHERS THEN RETURN NULL;
END;
$BODY$ LANGUAGE plpgsql;


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
	SELECT $1 <@ $2 AND $1 @> $2;
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
	name text NOT NULL
);

CREATE TRIGGER tags_audit_trigger
	AFTER UPDATE OR DELETE OR INSERT
	ON tags
	FOR EACH ROW EXECUTE PROCEDURE audit.trigger_table_audit();


CREATE TABLE users (
	id integer GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
	email citext NOT NULL UNIQUE,
	password text NOT NULL,
	role roles NOT NULL DEFAULT 'member'::roles
);

CREATE FUNCTION users_trigger_row_ai() RETURNS trigger AS $$
BEGIN
	INSERT INTO access.verification_codes (user_id, code) VALUES (
		new.id,
		format('%s:%s', encode(gen_random_bytes(25), 'hex'), encode(digest(new.id::text, 'sha1'), 'hex'))
	);

	RETURN new;
END;
$$ LANGUAGE plpgsql VOLATILE;

CREATE FUNCTION users_trigger_row_biu() RETURNS trigger AS $$
BEGIN
	IF EXISTS(SELECT 1 FROM users WHERE email = new.email AND id IS DISTINCT FROM CASE WHEN TG_OP = 'INSERT' THEN NULL ELSE new.id END) THEN
		RAISE EXCEPTION USING MESSAGE = format('Email %s already exists', new.email);
	END IF;

	RETURN new;
END;
$$ LANGUAGE plpgsql VOLATILE;

CREATE TRIGGER users_row_biu_trigger
	BEFORE INSERT OR UPDATE
	ON users
	FOR EACH ROW EXECUTE PROCEDURE users_trigger_row_biu();

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
	CONSTRAINT user_log_reputations_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE RESTRICT,
	CONSTRAINT user_log_reputations_tag_id_fkey FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE ON UPDATE RESTRICT
);


CREATE FUNCTION update_user_tag_reputation(in_user_id integer, in_tag_id integer, in_point bulletpoint_ratings_point) RETURNS void AS $BODY$
BEGIN
	IF in_point = 1 THEN
		INSERT INTO user_tag_reputations (user_id, tag_id, reputation) VALUES (in_user_id, in_tag_id, 1)
		ON CONFLICT (user_id, tag_id) DO UPDATE SET reputation = EXCLUDED.reputation + 1;
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
	CONSTRAINT themes_reference_id FOREIGN KEY (reference_id) REFERENCES "references"(id) ON DELETE SET NULL ON UPDATE RESTRICT,
	CONSTRAINT themes_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE RESTRICT
);

CREATE FUNCTION themes_trigger_row_biu() RETURNS trigger AS $BODY$
BEGIN
	new.name = nullif(new.name, '');

	RETURN new;
END;
$BODY$ LANGUAGE plpgsql VOLATILE;

CREATE TRIGGER themes_row_biu_trigger
	BEFORE INSERT OR UPDATE
	ON themes
	FOR EACH ROW EXECUTE PROCEDURE themes_trigger_row_biu();

CREATE TRIGGER themes_audit_trigger
	AFTER UPDATE OR DELETE OR INSERT
	ON themes
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

CREATE TRIGGER theme_tags_row_biu_trigger
	BEFORE INSERT OR UPDATE
	ON theme_tags
	FOR EACH ROW EXECUTE PROCEDURE theme_tags_trigger_row_biu();


CREATE TABLE sources (
	id integer GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
	link text NULL,
	type sources_type NOT NULL,
	CONSTRAINT link_type_not_null CHECK (CASE WHEN type = 'web' THEN link IS NOT NULL ELSE TRUE END),
	CONSTRAINT link_type_null CHECK (CASE WHEN type = 'head' THEN link IS NULL ELSE TRUE END)
);

CREATE TRIGGER sources_audit_trigger
	AFTER UPDATE OR DELETE OR INSERT
	ON sources
	FOR EACH ROW EXECUTE PROCEDURE audit.trigger_table_audit();

CREATE FUNCTION sources_trigger_row_biu() RETURNS trigger AS $BODY$
BEGIN
	new.link = nullif(new.link, '');

	RETURN new;
END;
$BODY$ LANGUAGE plpgsql VOLATILE;

CREATE TRIGGER sources_row_biu_trigger
	BEFORE INSERT OR UPDATE
	ON sources
	FOR EACH ROW EXECUTE PROCEDURE sources_trigger_row_biu();


CREATE TABLE bulletpoints (
	id integer GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
	theme_id integer NOT NULL,
	source_id integer NOT NULL,
	user_id integer NOT NULL,
	content character varying(255) NOT NULL,
	created_at timestamptz NOT NULL DEFAULT now(),
	CONSTRAINT bulletpoints_theme_id FOREIGN KEY (theme_id) REFERENCES themes(id) ON DELETE CASCADE ON UPDATE RESTRICT,
	CONSTRAINT bulletpoint_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE RESTRICT
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

CREATE FUNCTION bulletpoints_trigger_row_biu() RETURNS trigger AS $BODY$
BEGIN
	new.content = nullif(new.content, '');

	RETURN new;
END;
$BODY$ LANGUAGE plpgsql VOLATILE;

CREATE TRIGGER bulletpoints_row_ai_trigger
	AFTER INSERT
	ON bulletpoints
	FOR EACH ROW EXECUTE PROCEDURE bulletpoints_trigger_row_ai();

CREATE TRIGGER bulletpoints_row_biu_trigger
	BEFORE INSERT OR UPDATE
	ON bulletpoints
	FOR EACH ROW EXECUTE PROCEDURE bulletpoints_trigger_row_biu();


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

CREATE FUNCTION bulletpoint_ratings_trigger_row_aiud() RETURNS trigger AS $$
	DECLARE
		r bulletpoint_ratings;
BEGIN
	r = CASE WHEN TG_OP = 'DELETE' THEN old ELSE new END;
	PERFORM update_user_tag_reputation(bulletpoints.user_id, theme_tags.tag_id, r.point)
	FROM bulletpoints
	JOIN theme_tags ON theme_tags.theme_id = bulletpoints.theme_id
	WHERE bulletpoints.id = r.bulletpoint_id;

	RETURN r;
END;
$$ LANGUAGE plpgsql VOLATILE;

CREATE TRIGGER bulletpoint_ratings_row_aiud_trigger
	AFTER INSERT OR UPDATE OR DELETE
	ON bulletpoint_ratings
	FOR EACH ROW EXECUTE PROCEDURE bulletpoint_ratings_trigger_row_aiud();


-- views
CREATE VIEW public_themes AS
	SELECT
		themes.id, themes.name, tags.tags, themes.created_at,
		"references".url AS reference_url,
		users.id AS user_id
	FROM themes
	JOIN users ON users.id = themes.user_id
	LEFT JOIN "references" ON "references".id = themes.reference_id
	LEFT JOIN (
		SELECT theme_id, jsonb_agg(tags.*) AS tags
		FROM theme_tags
		JOIN tags ON tags.id = theme_tags.tag_id
		GROUP BY theme_id
	) AS tags ON tags.theme_id = themes.id;

CREATE FUNCTION public_themes_trigger_row_ii() RETURNS trigger AS $BODY$
	DECLARE v_theme_id integer;
BEGIN
	WITH inserted_reference AS (
		INSERT INTO "references" (url) VALUES (new.reference_url)
		RETURNING id
	)
	INSERT INTO themes (name, reference_id, user_id) VALUES (new.name, (SELECT id FROM inserted_reference), new.user_id)
	RETURNING id INTO v_theme_id;

	INSERT INTO theme_tags (theme_id, tag_id)
	SELECT v_theme_id, r.tag::integer FROM jsonb_array_elements(new.tags) AS r(tag);

	new.id = v_theme_id;
	RETURN new;
END
$BODY$ LANGUAGE plpgsql VOLATILE;

CREATE FUNCTION public_themes_trigger_row_iu() RETURNS trigger AS $BODY$
	DECLARE
		v_theme themes;
		v_current_tags integer[];
		v_new_tags integer[];
BEGIN
	UPDATE themes SET name = new.name WHERE id = new.id RETURNING * INTO v_theme;
	UPDATE "references" SET url = new.reference_url WHERE id = v_theme.reference_id;

	SELECT array_agg(tag_id) INTO v_current_tags FROM theme_tags WHERE theme_id = v_theme.id;
	SELECT array_agg(r.tag::integer) INTO v_new_tags FROM jsonb_array_elements(new.tags) AS r(tag);

	IF (array_equals(v_current_tags, v_new_tags) IS FALSE) THEN
		DELETE FROM theme_tags WHERE theme_id = v_theme.id;
		INSERT INTO theme_tags (theme_id, tag_id)
		SELECT v_theme.id, r.tag FROM unnest(v_new_tags) AS r(tag);
	END IF;

	RETURN new;
END
$BODY$ LANGUAGE plpgsql VOLATILE;

CREATE TRIGGER public_themes_trigger_row_ii
	INSTEAD OF INSERT
	ON public_themes
	FOR EACH ROW EXECUTE PROCEDURE public_themes_trigger_row_ii();

CREATE TRIGGER public_themes_trigger_row_iu
	INSTEAD OF UPDATE
	ON public_themes
	FOR EACH ROW EXECUTE PROCEDURE public_themes_trigger_row_iu();


CREATE VIEW tagged_themes AS
	SELECT tag_id, public_themes.*
	FROM public_themes
	JOIN theme_tags ON theme_tags.theme_id = public_themes.id;


CREATE VIEW public_bulletpoints AS
	SELECT
		bulletpoints.id, bulletpoints.content, bulletpoints.theme_id,
		sources.link AS source_link, sources.type AS source_type,
			bulletpoint_ratings.up AS up_rating,
			abs(bulletpoint_ratings.down) AS down_rating,
			(bulletpoint_ratings.up + bulletpoint_ratings.down) AS total_rating,
		users.id AS user_id,
		bulletpoint_ratings.user_rating
	FROM bulletpoints
	JOIN (
		SELECT
			DISTINCT ON (bulletpoint_id) bulletpoint_id,
			COALESCE(sum(point) FILTER (WHERE point = 1) OVER (PARTITION BY bulletpoint_id), 0) AS up,
			COALESCE(sum(point) FILTER (WHERE point = -1) OVER (PARTITION BY bulletpoint_id), 0) AS down,
			CASE WHEN user_id = globals_get_user() THEN point ELSE 0 END AS user_rating
		FROM bulletpoint_ratings
	) AS bulletpoint_ratings ON bulletpoint_ratings.bulletpoint_id = bulletpoints.id
	JOIN users ON users.id = bulletpoints.user_id
	LEFT JOIN sources ON sources.id = bulletpoints.source_id
	LEFT JOIN (
		SELECT user_tag_reputations.reputation, bulletpoint_tags.id FROM (
			SELECT bulletpoints.id, bulletpoints.user_id, array_agg(theme_tags.tag_id) AS tag_ids
			FROM bulletpoints
			JOIN themes ON themes.id = bulletpoints.theme_id
			JOIN theme_tags ON theme_tags.theme_id = themes.id
			GROUP BY bulletpoints.id
		) AS bulletpoint_tags, LATERAL (
			SELECT sum(reputation) AS reputation
			FROM user_tag_reputations
			WHERE user_id = bulletpoint_tags.user_id AND tag_id = ANY(bulletpoint_tags.tag_ids)
		) AS user_tag_reputations
	) AS bulletpoint_reputations ON bulletpoint_reputations.id = bulletpoints.id
	ORDER BY total_rating DESC, bulletpoint_reputations.reputation DESC, length(bulletpoints.content) ASC, created_at DESC, id DESC;

CREATE FUNCTION public_bulletpoints_trigger_row_ii() RETURNS trigger AS $BODY$
BEGIN
	WITH inserted_source AS (
		INSERT INTO sources (link, type) VALUES (new.source_link, new.source_type) RETURNING id
	)
	INSERT INTO bulletpoints (theme_id, source_id, content, user_id) VALUES (
		new.theme_id,
		(SELECT id FROM inserted_source),
		new.content,
		new.user_id
	);
	RETURN new;
END
$BODY$ LANGUAGE plpgsql VOLATILE;

CREATE FUNCTION public_bulletpoints_trigger_row_iu() RETURNS trigger AS $BODY$
BEGIN
	WITH updated_bulletpoint AS (
		UPDATE bulletpoints SET content = new.content WHERE id = new.id
		RETURNING *
	)
	UPDATE sources SET link = new.source_link, type = new.source_type WHERE id = (SELECT source_id FROM updated_bulletpoint);
	RETURN new;
END
$BODY$ LANGUAGE plpgsql VOLATILE;

CREATE TRIGGER public_bulletpoints_trigger_row_ii
	INSTEAD OF INSERT
	ON public_bulletpoints
	FOR EACH ROW EXECUTE PROCEDURE public_bulletpoints_trigger_row_ii();

CREATE TRIGGER public_bulletpoints_trigger_row_iu
	INSTEAD OF UPDATE
	ON public_bulletpoints
	FOR EACH ROW EXECUTE PROCEDURE public_bulletpoints_trigger_row_iu();


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