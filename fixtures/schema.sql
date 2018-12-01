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


-- schemas
CREATE SCHEMA constant;


-- constants
CREATE FUNCTION constant.references_name() RETURNS text[] AS $$SELECT ARRAY['wikipedia'];$$ LANGUAGE sql IMMUTABLE;
CREATE FUNCTION constant.sources_type() RETURNS text[] AS $$SELECT ARRAY['web', 'head'];$$ LANGUAGE sql IMMUTABLE;


-- domains
CREATE DOMAIN references_name AS text CHECK (VALUE = ANY(constant.references_name()));
CREATE DOMAIN sources_type AS text CHECK (VALUE = ANY(constant.sources_type()));


-- tables
CREATE TABLE "references" (
	id integer GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
	url text NOT NULL,
	name references_name NOT NULL
);

CREATE TABLE themes (
	id integer GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
	name text NOT NULL,
	tags text[] NOT NULL,
	created_at timestamptz NOT NULL DEFAULT now()
);

CREATE TABLE theme_references (
	id integer GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
	theme_id integer NOT NULL,
	reference_id integer NOT NULL,
	CONSTRAINT theme_references_theme_id FOREIGN KEY (theme_id) REFERENCES themes(id),
	CONSTRAINT theme_references_reference_id FOREIGN KEY (reference_id) REFERENCES "references"(id)
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
	text text NOT NULL,
	created_at timestamptz NOT NULL DEFAULT now(),
	CONSTRAINT bulletpoints_theme_id FOREIGN KEY (theme_id) REFERENCES themes(id)
);

-- views
CREATE VIEW public_themes AS
	SELECT
		themes.id, themes.name, themes.tags, themes.created_at,
		"references".id AS reference_id, "references".name AS reference_name, "references".url AS reference_url
	FROM themes
	LEFT JOIN theme_references ON themes.id = theme_references.theme_id
	LEFT JOIN "references" ON "references".id = theme_references.reference_id;

