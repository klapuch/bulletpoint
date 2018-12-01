CREATE SCHEMA IF NOT EXISTS samples;

CREATE FUNCTION samples.random_if_not_exists(random text, replacements jsonb, field text) RETURNS text
AS $$
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
$$ LANGUAGE plpgsql;

CREATE FUNCTION samples.random_if_not_exists(random integer, replacements jsonb, field text) RETURNS integer
AS $$
BEGIN
	IF replacements ? field THEN
		RETURN CAST (replacements -> field AS text);
	ELSE
		RETURN random;
	END IF;
END;
$$ LANGUAGE plpgsql;

CREATE FUNCTION samples.random_if_not_exists(random text[], replacements jsonb, field text) RETURNS text[]
AS $$
BEGIN
	IF replacements ? field THEN
		RETURN CAST (replacements -> field AS text[]);
	ELSE
		RETURN random;
	END IF;
END;
$$ LANGUAGE plpgsql;

CREATE FUNCTION samples.random_if_not_exists(random boolean, replacements jsonb, field text) RETURNS boolean
AS $$
BEGIN
	IF replacements ? field THEN
		RETURN CAST (replacements -> field AS text);
	ELSE
		RETURN random;
	END IF;
END;
$$ LANGUAGE plpgsql;

CREATE FUNCTION samples.themes(replacements jsonb = '{}') RETURNS integer
AS $$
DECLARE
	v_id themes.id%type;
BEGIN
	INSERT INTO themes (name, tags, created_at) VALUES (
		samples.random_if_not_exists(md5(random()::text), replacements, 'name'),
		samples.random_if_not_exists('{}'::text[], replacements, 'tags'),
		now()
	)
	RETURNING id INTO v_id;

	RETURN v_id;
END;
$$ LANGUAGE plpgsql;