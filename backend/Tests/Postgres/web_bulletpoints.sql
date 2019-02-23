CREATE FUNCTION tests.adding_with_references() RETURNS void AS $BODY$
BEGIN
	INSERT INTO web.bulletpoints (content, theme_id, user_id, source_link, source_type, referenced_theme_id) VALUES (
		'ABC [[foo]] bar [[baz]]',
		(SELECT samples.themes()),
		(SELECT samples.users()),
		NULL,
		'head'::sources_type,
		format('[%s,%s]', (SELECT samples.themes()), (SELECT samples.themes()))::jsonb
	);

	PERFORM assert.same(2, (SELECT count(*)::integer FROM bulletpoint_referenced_themes));
END $BODY$ LANGUAGE plpgsql VOLATILE;

CREATE FUNCTION tests.updating_with_references() RETURNS void AS $BODY$
DECLARE
	v_bulletpoint_id bulletpoints.id%type;
BEGIN
	SELECT samples.public_bulletpoints() INTO v_bulletpoint_id;
	UPDATE web.bulletpoints SET
		content = 'ABC [[foo]] bar [[baz]]',
		referenced_theme_id = format('[%s,%s]', (SELECT samples.themes()), (SELECT samples.themes()))::jsonb
	WHERE id = v_bulletpoint_id;

	PERFORM assert.same(2, (SELECT count(*)::integer FROM bulletpoint_referenced_themes));
END $BODY$ LANGUAGE plpgsql VOLATILE;