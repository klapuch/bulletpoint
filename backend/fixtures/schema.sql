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


-- constants
CREATE SCHEMA constant;

CREATE FUNCTION constant.references_name() RETURNS text[] AS $$SELECT ARRAY['wikipedia'];$$ LANGUAGE sql IMMUTABLE;
CREATE FUNCTION constant.sources_type() RETURNS text[] AS $$SELECT ARRAY['web', 'head'];$$ LANGUAGE sql IMMUTABLE;
CREATE FUNCTION constant.theme_tags_min() RETURNS integer AS $$SELECT 1;$$ LANGUAGE sql IMMUTABLE;
CREATE FUNCTION constant.bulletpoint_ratings_point_range() RETURNS integer[] AS $$SELECT ARRAY[-1, 0, 1];$$ LANGUAGE sql IMMUTABLE;
CREATE FUNCTION constant.roles() RETURNS text[] AS $$SELECT ARRAY['member'];$$ LANGUAGE sql IMMUTABLE;


-- domains
CREATE DOMAIN references_name AS text CHECK (VALUE = ANY(constant.references_name()));
CREATE DOMAIN sources_type AS text CHECK (VALUE = ANY(constant.sources_type()));
CREATE DOMAIN bulletpoint_ratings_point AS integer CHECK (constant.bulletpoint_ratings_point_range() @> ARRAY[VALUE]);
CREATE DOMAIN roles AS text CHECK (VALUE = ANY(constant.roles()));


-- functions
CREATE FUNCTION array_diff(anyarray, anyarray) RETURNS anyarray AS $BODY$
	SELECT ARRAY(SELECT unnest($1) EXCEPT select unnest($2));
$BODY$ LANGUAGE sql IMMUTABLE;


-- tables
CREATE TABLE "references" (
	id integer GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
	url text NOT NULL,
	name references_name NOT NULL
);


CREATE TABLE tags (
	id integer GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
	name text NOT NULL
);

-- CREATE FUNCTION tags_trigger_row_ad() RETURNS trigger AS
-- $BODY$
-- 	BEGIN
-- 		UPDATE themes
-- 		SET tags = array_diff(tags, ARRAY[old.id])
-- 		WHERE tags @> ARRAY[old.id];
-- 	END;
-- $BODY$ LANGUAGE plpgsql VOLATILE;
--
-- CREATE TRIGGER tags_row_ad_trigger
-- 	AFTER DELETE
-- 	ON tags
-- 	FOR EACH ROW EXECUTE PROCEDURE tags_trigger_row_ad();


CREATE TABLE users (
	id integer GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
	email citext NOT NULL,
	password text NOT NULL,
	role roles NOT NULL DEFAULT 'member'::roles
);

CREATE INDEX users_email_idx ON users USING btree (email);

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


-- TODO: check for existing item in tags
CREATE TABLE themes (
	id integer GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
	name text NOT NULL,
	tags jsonb NOT NULL, -- TODO: use array
	reference_id integer NOT NULL,
	user_id integer NOT NULL,
	created_at timestamptz NOT NULL DEFAULT now(),
	CONSTRAINT themes_reference_id FOREIGN KEY (reference_id) REFERENCES "references"(id) ON DELETE SET NULL ON UPDATE RESTRICT,
	CONSTRAINT themes_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE RESTRICT,
	CONSTRAINT themes_tags_min CHECK (jsonb_array_length(tags) >= constant.theme_tags_min())
);


CREATE TABLE sources (
	id integer GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
	link text NULL,
	type sources_type NOT NULL
);


CREATE TABLE bulletpoints (
	id integer GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
	theme_id integer NOT NULL,
	source_id integer NOT NULL,
	user_id integer NOT NULL,
	text text NOT NULL,
	created_at timestamptz NOT NULL DEFAULT now(),
	CONSTRAINT bulletpoints_theme_id FOREIGN KEY (theme_id) REFERENCES themes(id) ON DELETE CASCADE ON UPDATE RESTRICT,
	CONSTRAINT bulletpoint_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE RESTRICT
);

CREATE FUNCTION bulletpoints_trigger_row_ai() RETURNS trigger AS
$BODY$
BEGIN
	INSERT INTO bulletpoint_ratings (point, user_id, bulletpoint_id) VALUES (1, new.user_id, new.id);

	RETURN new;
END;
$BODY$ LANGUAGE plpgsql VOLATILE;

CREATE TRIGGER bulletpoints_row_ai_trigger
	AFTER INSERT
	ON bulletpoints
	FOR EACH ROW EXECUTE PROCEDURE bulletpoints_trigger_row_ai();


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


-- views
CREATE VIEW public_themes AS
	SELECT
		themes.id, themes.name, themes.tags, themes.created_at,
		"references".name AS reference_name, "references".url AS reference_url,
		users.id AS user_id
	FROM themes
	JOIN users ON users.id = themes.user_id
	LEFT JOIN "references" ON "references".id = themes.reference_id;

CREATE FUNCTION public_themes_trigger_row_ii() RETURNS trigger AS $BODY$
BEGIN
	WITH inserted_reference AS (
		INSERT INTO "references" (name, url) VALUES (new.reference_name, new.reference_url) RETURNING id
	)
	INSERT INTO themes (name, tags, reference_id, user_id) VALUES (new.name, new.tags, (SELECT id FROM inserted_reference), new.user_id);

	RETURN new;
END
$BODY$ LANGUAGE plpgsql VOLATILE;

CREATE TRIGGER public_themes_trigger_row_ii
	INSTEAD OF INSERT
	ON public_themes
	FOR EACH ROW EXECUTE PROCEDURE public_themes_trigger_row_ii();


CREATE VIEW public_bulletpoints AS
	SELECT
		bulletpoints.id, bulletpoints.text, bulletpoints.theme_id,
		sources.link AS source_link, sources.type AS source_type,
			bulletpoint_ratings.up AS up_rating,
			bulletpoint_ratings.down AS down_rating,
			bulletpoint_ratings.neutral AS neutral_rating,
			(bulletpoint_ratings.up + bulletpoint_ratings.down) AS total_rating,
		users.id AS user_id
	FROM bulletpoints
	JOIN (
		SELECT
			bulletpoint_id,
			COALESCE(sum(point) FILTER (WHERE point = 1) OVER (PARTITION BY bulletpoint_id), 0) AS up,
			COALESCE(sum(point) FILTER (WHERE point = -1) OVER (PARTITION BY bulletpoint_id), 0) AS down,
			COALESCE(sum(point) FILTER (WHERE point = 0) OVER (PARTITION BY bulletpoint_id), 0) AS neutral
		FROM bulletpoint_ratings
	) AS bulletpoint_ratings ON bulletpoint_ratings.bulletpoint_id = bulletpoints.id
	JOIN users ON users.id = bulletpoints.user_id
	LEFT JOIN sources ON sources.id = bulletpoints.source_id
	ORDER BY total_rating DESC, length(bulletpoints.text) ASC, created_at DESC, id DESC;

CREATE FUNCTION public_bulletpoints_trigger_row_ii() RETURNS trigger AS $BODY$
BEGIN
	WITH inserted_source AS (
		INSERT INTO sources (link, type) VALUES (new.source_link, new.source_type) RETURNING id
	)
	INSERT INTO bulletpoints (theme_id, source_id, text, user_id) VALUES (
		new.theme_id,
		(SELECT id FROM inserted_source),
		new.text,
		new.user_id
	);
	RETURN new;
END
$BODY$ LANGUAGE plpgsql VOLATILE;

CREATE TRIGGER public_bulletpoints_trigger_row_ii
	INSTEAD OF INSERT
	ON public_bulletpoints
	FOR EACH ROW EXECUTE PROCEDURE public_bulletpoints_trigger_row_ii();


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