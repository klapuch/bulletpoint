INSERT INTO deploy.migrations(filename) VALUES('database/migrations/2019/mobile-references--08-18.sql');


UPDATE "references" SET url = replace(url, 'https://cs.m.wikipedia', 'https://cs.wikipedia');
UPDATE sources SET url = replace(url, 'https://cs.m.wikipedia', 'https://cs.wikipedia');


CREATE OR REPLACE FUNCTION sources_trigger_row_biu() RETURNS trigger AS $BODY$
BEGIN
	new.link = nullif(new.link, '');

	IF new.type = 'web' AND new.link IS NUll THEN
		RAISE EXCEPTION 'Link from web can not be empty.';
	ELSIF new.type = 'head' AND new.link IS NOT NULL THEN
		RAISE EXCEPTION 'Link from head must be empty.';
	END IF;

	RETURN new;
END;
$BODY$ LANGUAGE plpgsql VOLATILE;


UPDATE sources SET link = nullif(link, '');
