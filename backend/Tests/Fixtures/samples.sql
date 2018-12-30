CREATE SCHEMA IF NOT EXISTS samples;

CREATE FUNCTION samples.random_if_not_exists(random text, replacements jsonb, field text) RETURNS text AS $BODY$
DECLARE
	v_replacement text;
BEGIN
	IF replacements ? field THEN
		v_replacement = trim(both '"' from CAST (replacements -> field AS TEXT));
		RETURN SUBSTRING(v_replacement, 1, LENGTH(v_replacement));
	ELSE
		RETURN random;
	END IF;
END;
$BODY$ LANGUAGE plpgsql;

CREATE FUNCTION samples.random_if_not_exists(random integer, replacements jsonb, field text) RETURNS integer AS $BODY$
BEGIN
	IF replacements ? field THEN
		RETURN CAST (replacements -> field AS text);
	ELSE
		RETURN random;
	END IF;
END;
$BODY$ LANGUAGE plpgsql;

CREATE FUNCTION samples.random_if_not_exists(random text[], replacements jsonb, field text) RETURNS text[] AS $BODY$
BEGIN
	IF replacements ? field THEN
		RETURN CAST (replacements -> field AS text[]);
	ELSE
		RETURN random;
	END IF;
END;
$BODY$ LANGUAGE plpgsql;

CREATE FUNCTION samples.random_if_not_exists(random jsonb, replacements jsonb, field text) RETURNS jsonb AS $BODY$
BEGIN
	IF replacements ? field THEN
		RETURN CAST (replacements -> field AS jsonb);
	ELSE
		RETURN random;
	END IF;
END;
$BODY$ LANGUAGE plpgsql;

CREATE FUNCTION samples.random_if_not_exists(random boolean, replacements jsonb, field text) RETURNS boolean AS $BODY$
BEGIN
	IF replacements ? field THEN
		RETURN CAST (replacements -> field AS text);
	ELSE
		RETURN random;
	END IF;
END;
$BODY$ LANGUAGE plpgsql;

CREATE FUNCTION samples.themes(replacements jsonb = '{}') RETURNS integer AS $BODY$
DECLARE
	v_id themes.id%type;
BEGIN
	INSERT INTO themes (name, reference_id, user_id, created_at) VALUES (
		samples.random_if_not_exists(md5(random()::text), replacements, 'name'),
		samples.random_if_not_exists((SELECT samples."references"()), replacements, 'reference_id'),
		samples.random_if_not_exists((SELECT samples.users()), replacements, 'user_id'),
		now()
	)
	RETURNING id INTO v_id;

	RETURN v_id;
END;
$BODY$ LANGUAGE plpgsql;

CREATE FUNCTION samples.sources(replacements jsonb = '{}') RETURNS integer AS $BODY$
DECLARE
	v_id sources.id%type;
BEGIN
	INSERT INTO sources (link, type) VALUES (
		samples.random_if_not_exists(md5(random()::text), replacements, 'link'),
		samples.random_if_not_exists('web', replacements, 'type')
	)
	RETURNING id INTO v_id;

	RETURN v_id;
END;
$BODY$ LANGUAGE plpgsql;

CREATE FUNCTION samples.tags(replacements jsonb = '{}') RETURNS integer AS $BODY$
DECLARE
	v_id tags.id%type;
BEGIN
	INSERT INTO tags (name) VALUES (
		samples.random_if_not_exists(md5(random()::text), replacements, 'name')
	)
	RETURNING id INTO v_id;

	RETURN v_id;
END;
$BODY$ LANGUAGE plpgsql;

CREATE FUNCTION samples.bulletpoints(replacements jsonb = '{}') RETURNS integer AS $BODY$
DECLARE
	v_id bulletpoints.id%type;
BEGIN
	INSERT INTO bulletpoints (theme_id, source_id, content, user_id) VALUES (
		samples.random_if_not_exists((SELECT samples.themes()), replacements, 'theme_id'),
		samples.random_if_not_exists((SELECT samples.sources()), replacements, 'source_id'),
		samples.random_if_not_exists(md5(random()::text), replacements, 'content'),
		CASE WHEN replacements ? 'user_id' THEN CAST(replacements -> 'user_id' AS integer) ELSE (SELECT samples.users()) END
	)
	RETURNING id INTO v_id;

	RETURN v_id;
END;
$BODY$ LANGUAGE plpgsql;

CREATE FUNCTION samples.contributed_bulletpoints(replacements jsonb = '{}') RETURNS integer AS $BODY$
DECLARE
	v_id bulletpoints.id%type;
BEGIN
	INSERT INTO contributed_bulletpoints (theme_id, source_id, content, user_id) VALUES (
		samples.random_if_not_exists((SELECT samples.themes()), replacements, 'theme_id'),
		samples.random_if_not_exists((SELECT samples.sources()), replacements, 'source_id'),
		samples.random_if_not_exists(md5(random()::text), replacements, 'content'),
		CASE WHEN replacements ? 'user_id' THEN CAST(replacements -> 'user_id' AS integer) ELSE (SELECT samples.users()) END
	)
	RETURNING id INTO v_id;

	RETURN v_id;
END;
$BODY$ LANGUAGE plpgsql;

CREATE FUNCTION samples.users(replacements jsonb = '{}') RETURNS integer AS $BODY$
DECLARE
	v_id users.id%type;
BEGIN
	INSERT INTO users (email, username, password, role) VALUES (
		samples.random_if_not_exists(md5(random()::text), replacements, 'email'),
		samples.random_if_not_exists(substring(md5(random()::text), 1, 20), replacements, 'username'),
		samples.random_if_not_exists(md5(random()::text), replacements, 'password'),
		samples.random_if_not_exists(test_utils.random_array_pick(constant.roles()), replacements, 'role')::roles
	)
	RETURNING id INTO v_id;

	RETURN v_id;
END;
$BODY$ LANGUAGE plpgsql;

CREATE FUNCTION samples."references"(replacements jsonb = '{}') RETURNS integer AS $BODY$
DECLARE
	v_id "references".id%type;
BEGIN
	INSERT INTO "references" (url) VALUES (
		samples.random_if_not_exists(md5(random()::text), replacements, 'url')
	)
	RETURNING id INTO v_id;

	RETURN v_id;
END;
$BODY$ LANGUAGE plpgsql;

CREATE FUNCTION samples.theme_tags(replacements jsonb = '{}') RETURNS integer AS $BODY$
DECLARE
	v_id themes.id%type;
BEGIN
	INSERT INTO theme_tags (tag_id, theme_id) VALUES (
		samples.random_if_not_exists((SELECT samples.tags()), replacements, 'tag_id'),
		CASE WHEN replacements ? 'theme_id' THEN CAST(replacements -> 'theme_id' AS integer) ELSE (SELECT samples.themes()) END
	)
	RETURNING id INTO v_id;

	RETURN v_id;
END;
$BODY$ LANGUAGE plpgsql;