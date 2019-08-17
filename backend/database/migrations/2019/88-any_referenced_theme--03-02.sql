INSERT INTO deploy.migrations(filename) VALUES('database/migrations/2019/88-any_referenced_theme--03-02.sql');

CREATE OR REPLACE FUNCTION bulletpoint_referenced_themes_trigger_row_biu() RETURNS trigger AS $$
DECLARE
	r bulletpoint_referenced_themes;
BEGIN
	r = CASE WHEN TG_OP = 'DELETE' THEN old ELSE new END;

	IF ((SELECT theme_id = r.theme_id FROM bulletpoints WHERE id = r.bulletpoint_id)) THEN
		RAISE EXCEPTION 'Referenced theme must differ from the assigned.';
	END IF;

	IF TG_OP = 'INSERT' THEN
		IF (number_of_references((SELECT content FROM public_bulletpoints WHERE id = r.bulletpoint_id)) = 0) THEN
			RAISE EXCEPTION 'Bulletpoint does not include place for reference.';
		END IF;
	END IF;

	RETURN r;
END;
$$ LANGUAGE plpgsql VOLATILE;
