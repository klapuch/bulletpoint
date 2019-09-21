INSERT INTO deploy.migrations(filename) VALUES('database/migrations/2019/tag-format--09-21.sql');

UPDATE tags
SET name = lower(name)
WHERE array_length(string_to_array(name, ' '), 1) = 1
AND initcap(lower(name)) = name;

CREATE OR REPLACE FUNCTION tags_trigger_row_biu() RETURNS trigger AS $BODY$
BEGIN
	IF (
		old.name IS DISTINCT FROM new.name
		AND array_length(string_to_array(new.name, ' '), 1) = 1
		AND initcap(lower(new.name)) = new.name
	) THEN
		new.name = lower(new.name);
	END IF;

	RETURN new;
END;
$BODY$ LANGUAGE plpgsql VOLATILE;

CREATE TRIGGER tags_row_biu_trigger
	BEFORE INSERT OR UPDATE
	ON tags
	FOR EACH ROW EXECUTE PROCEDURE tags_trigger_row_biu();
