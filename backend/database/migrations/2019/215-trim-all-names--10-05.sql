INSERT INTO deploy.migrations(filename) VALUES('database/migrations/2019/215-trim-all-names--10-05.sql');

CREATE OR REPLACE FUNCTION tags_trigger_row_biu() RETURNS trigger AS $BODY$
BEGIN
	IF old.name IS DISTINCT FROM new.name THEN
		new.name = trim(new.name);
		IF array_length(string_to_array(new.name, ' '), 1) = 1 AND initcap(lower(new.name)) = new.name THEN
			new.name = lower(new.name);
		END IF;
	END IF;

	RETURN new;
END;
$BODY$ LANGUAGE plpgsql VOLATILE;

CREATE FUNCTION themes_trigger_row_biu() RETURNS trigger AS $BODY$
BEGIN
	IF old.name IS DISTINCT FROM new.name THEN
		new.name = trim(new.name);
	END IF;

	RETURN new;
END;
$BODY$ LANGUAGE plpgsql VOLATILE;

CREATE TRIGGER themes_row_biu_trigger
	BEFORE INSERT OR UPDATE
	ON themes
	FOR EACH ROW EXECUTE PROCEDURE themes_trigger_row_biu();


CREATE OR REPLACE FUNCTION sources_trigger_row_biu() RETURNS trigger AS $BODY$
BEGIN
	new.link = nullif(new.link, '');

	IF old.link IS DISTINCT FROM new.link THEN
		new.link = trim(new.link);
	END IF;

	IF new.type = 'web' AND new.link IS NUll THEN
		RAISE EXCEPTION 'Link from web can not be empty.';
	ELSIF new.type = 'head' AND new.link IS NOT NULL THEN
		RAISE EXCEPTION 'Link from head must be empty.';
	END IF;

	RETURN new;
END;
$BODY$ LANGUAGE plpgsql VOLATILE;