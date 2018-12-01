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
	INSERT INTO themes (name, tags, created_at) VALUES (
		samples.random_if_not_exists(md5(random()::text), replacements, 'name'),
		samples.random_if_not_exists(jsonb_build_array(md5(random()::text)), replacements, 'tags'),
		now()
	)
	RETURNING id INTO v_id;

	RETURN v_id;
END;
$BODY$ LANGUAGE plpgsql;

CREATE FUNCTION samples.sources(replacements jsonb = '{}') RETURNS integer AS $BODY$
DECLARE
	v_id themes.id%type;
BEGIN
	INSERT INTO sources (link, type) VALUES (
		samples.random_if_not_exists(md5(random()::text), replacements, 'link'),
		samples.random_if_not_exists('web', replacements, 'type')
	)
	RETURNING id INTO v_id;

	RETURN v_id;
END;
$BODY$ LANGUAGE plpgsql;

CREATE FUNCTION samples.bulletpoints(replacements jsonb = '{}') RETURNS integer AS $BODY$
DECLARE
	v_id themes.id%type;
BEGIN
	INSERT INTO bulletpoints (theme_id, source_id, text) VALUES (
		samples.random_if_not_exists((SELECT samples.themes()), replacements, 'theme_id'),
		samples.random_if_not_exists((SELECT samples.sources()), replacements, 'source_id'),
		samples.random_if_not_exists(md5(random()::text), replacements, 'text')
	)
	RETURNING id INTO v_id;

	RETURN v_id;
END;
$BODY$ LANGUAGE plpgsql;