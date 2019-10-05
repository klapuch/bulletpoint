INSERT INTO deploy.migrations(filename) VALUES('database/migrations/2019/postgres12--10-05.sql');

CREATE OR REPLACE FUNCTION bulletpoints_trigger_row_iiu() RETURNS trigger AS $BODY$
BEGIN
	new.is_contribution = TG_TABLE_NAME = 'contributed_bulletpoints';

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

CREATE OR REPLACE FUNCTION bulletpoint_theme_comparisons_trigger_row_biu() RETURNS trigger AS $BODY$
DECLARE
	v_theme_from_bulletpoint integer;
BEGIN
	SELECT theme_id INTO v_theme_from_bulletpoint FROM bulletpoints WHERE id = new.bulletpoint_id;

	IF new.theme_id = v_theme_from_bulletpoint THEN
		RAISE EXCEPTION 'Compared theme must differ from the bulletpoint assigned one.';
	END IF;

	IF (
		NOT EXISTS (
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

CREATE OR REPLACE FUNCTION bulletpoint_referenced_themes_trigger_row_biu() RETURNS trigger AS $BODY$
BEGIN
	IF EXISTS (SELECT 1 FROM bulletpoints WHERE id = new.bulletpoint_id AND theme_id = new.theme_id) THEN
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

CREATE OR REPLACE FUNCTION deploy.migrations_to_run(in_filenames text) RETURNS SETOF text AS $BODY$
DECLARE
	v_filenames text[] NOT NULL DEFAULT string_to_array(trim(TRAILING ',' FROM in_filenames), ',');
BEGIN
	IF EXISTS (SELECT filename FROM unnest(v_filenames) AS filenames(filename) WHERE filename NOT ILIKE '%.sql') THEN
		RAISE EXCEPTION USING MESSAGE = 'Filenames must be in format %.sql';
	END IF;

	RETURN QUERY SELECT unnest(v_filenames)
	EXCEPT
	SELECT filename FROM deploy.migrations;
END;
$BODY$ LANGUAGE plpgsql STABLE;
