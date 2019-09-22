CREATE FUNCTION tests.throwing_on_not_common() RETURNS void AS $BODY$
DECLARE
	v_theme_id1 themes.id%type NOT NULL DEFAULT samples.themes();
	v_theme_id2 themes.id%type NOT NULL DEFAULT samples.themes();
	v_tag_id1 tags.id%type NOT NULL DEFAULT samples.tags();
	v_tag_id2 tags.id%type NOT NULL DEFAULT samples.tags();
	v_bulletpoint_id public_bulletpoints.id%type;
BEGIN
	PERFORM samples.theme_tags(jsonb_build_object('tag_id', v_tag_id1, 'theme_id', v_theme_id1));
	PERFORM samples.theme_tags(jsonb_build_object('tag_id', v_tag_id2, 'theme_id', v_theme_id2));
	v_bulletpoint_id = samples.public_bulletpoints(jsonb_build_object('theme_id', v_theme_id1));

	PERFORM assert.throws(
		format(
			$$INSERT INTO bulletpoint_theme_comparisons(bulletpoint_id, theme_id) VALUES (%L, %L)$$,
			v_bulletpoint_id,
			v_theme_id2
		),
		ROW('Themes must have some common tags.', 'P0001')::error
	);
END $BODY$ LANGUAGE plpgsql VOLATILE;

CREATE FUNCTION tests.throwing_on_same() RETURNS void AS $BODY$
DECLARE
	v_theme_id1 themes.id%type NOT NULL DEFAULT samples.themes();
	v_tag_id1 tags.id%type NOT NULL DEFAULT samples.tags();
	v_bulletpoint_id public_bulletpoints.id%type;
BEGIN
	PERFORM samples.theme_tags(jsonb_build_object('tag_id', v_tag_id1, 'theme_id', v_theme_id1));
	v_bulletpoint_id = samples.public_bulletpoints(jsonb_build_object('theme_id', v_theme_id1));

	PERFORM assert.throws(
		format(
			$$INSERT INTO bulletpoint_theme_comparisons(bulletpoint_id, theme_id) VALUES (%L, %L)$$,
			v_bulletpoint_id,
			v_theme_id1
		),
		ROW('Compared theme must differ from the bulletpoint assigned one.', 'P0001')::error
	);
END $BODY$ LANGUAGE plpgsql VOLATILE;

CREATE FUNCTION tests.passing() RETURNS void AS $BODY$
DECLARE
	v_theme_id1 themes.id%type NOT NULL DEFAULT samples.themes();
	v_theme_id2 themes.id%type NOT NULL DEFAULT samples.themes();
	v_tag_id1 tags.id%type NOT NULL DEFAULT samples.tags();
	v_bulletpoint_id public_bulletpoints.id%type;
BEGIN
	PERFORM samples.theme_tags(jsonb_build_object('tag_id', v_tag_id1, 'theme_id', v_theme_id1));
	PERFORM samples.theme_tags(jsonb_build_object('tag_id', v_tag_id1, 'theme_id', v_theme_id2));
	v_bulletpoint_id = samples.public_bulletpoints(jsonb_build_object('theme_id', v_theme_id1));

	INSERT INTO bulletpoint_theme_comparisons(bulletpoint_id, theme_id) VALUES (v_bulletpoint_id, v_theme_id2);
END $BODY$ LANGUAGE plpgsql VOLATILE;
