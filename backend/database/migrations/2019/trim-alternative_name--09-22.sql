INSERT INTO deploy.migrations(filename) VALUES('database/migrations/2019/trim-alternative_name--09-22.sql');

UPDATE theme_alternative_names SET name = trim(name);

CREATE FUNCTION theme_alternative_names_trigger_row_biu() RETURNS trigger AS $BODY$
BEGIN
	IF old.name IS DISTINCT FROM new.name THEN
		new.name = trim(new.name);
	END IF;

	RETURN new;
END;
$BODY$ LANGUAGE plpgsql VOLATILE;

CREATE TRIGGER theme_alternative_names_row_biu_trigger
	BEFORE INSERT OR UPDATE
	ON theme_alternative_names
	FOR EACH ROW EXECUTE PROCEDURE theme_alternative_names_trigger_row_biu();
