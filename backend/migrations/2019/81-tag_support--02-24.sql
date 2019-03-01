INSERT INTO deploy.migrations(filename) VALUES('migrations/2019/81-tag_support--02-24.sql');

ALTER TABLE public.tags ADD UNIQUE (name);

CREATE FUNCTION tags_trigger_row_biu() RETURNS trigger AS $BODY$
BEGIN
	new.name = nullif(new.name, '');

	RETURN new;
END;
$BODY$ LANGUAGE plpgsql VOLATILE;

CREATE TRIGGER tags_row_biu_trigger
	BEFORE INSERT OR UPDATE
	ON tags
	FOR EACH ROW EXECUTE PROCEDURE tags_trigger_row_biu();