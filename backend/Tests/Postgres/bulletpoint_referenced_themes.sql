CREATE FUNCTION tests.throwing_on_no_reference() RETURNS void AS $BODY$
DECLARE
	v_theme_id themes.id%type NOT NULL DEFAULT samples.themes();
	v_bulletpoint_id themes.id%type;
BEGIN
	SELECT samples.public_bulletpoints(jsonb_build_object('content', 'Hi there!')) INTO v_bulletpoint_id;

	PERFORM assert.throws(
		format('INSERT INTO bulletpoint_referenced_themes (bulletpoint_id, theme_id) VALUES (%L, %L)', v_bulletpoint_id, v_theme_id),
		ROW('Bulletpoint does not include place for reference.', 'P0001')::error
	);
END $BODY$ LANGUAGE plpgsql VOLATILE;


CREATE FUNCTION tests.passing_on_some_reference() RETURNS void AS $BODY$
DECLARE
	v_theme_id themes.id%type NOT NULL DEFAULT samples.themes();
	v_bulletpoint_id themes.id%type;
BEGIN
	SELECT samples.public_bulletpoints(jsonb_build_object('content', 'Hi [[there]]!')) INTO v_bulletpoint_id;

	INSERT INTO bulletpoint_referenced_themes (bulletpoint_id, theme_id) VALUES (v_bulletpoint_id, v_theme_id);
END $BODY$ LANGUAGE plpgsql VOLATILE;
