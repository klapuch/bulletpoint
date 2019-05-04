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
	v_bulletpoint_id bulletpoints.id%type NOT NULL DEFAULT samples.public_bulletpoints();
BEGIN
	UPDATE web.bulletpoints SET
		content = 'ABC [[foo]] bar [[baz]]',
		referenced_theme_id = format('[%s,%s]', (SELECT samples.themes()), (SELECT samples.themes()))::jsonb
	WHERE id = v_bulletpoint_id;

	PERFORM assert.same(2, (SELECT count(*)::integer FROM bulletpoint_referenced_themes));
END $BODY$ LANGUAGE plpgsql VOLATILE;

CREATE FUNCTION tests.adding_with_comparisons() RETURNS void AS $BODY$
DECLARE
	v_theme_id1 themes.id%type NOT NULL DEFAULT samples.themes();
	v_theme_id2 themes.id%type NOT NULL DEFAULT samples.themes();
	v_theme_id3 themes.id%type NOT NULL DEFAULT samples.themes();
	v_tag_id tags.id%type NOT NULL DEFAULT samples.tags();
BEGIN
	PERFORM samples.theme_tags(jsonb_build_object('tag_id', v_tag_id, 'theme_id', v_theme_id1));
	PERFORM samples.theme_tags(jsonb_build_object('tag_id', v_tag_id, 'theme_id', v_theme_id2));
	PERFORM samples.theme_tags(jsonb_build_object('tag_id', v_tag_id, 'theme_id', v_theme_id3));

	INSERT INTO web.bulletpoints (content, theme_id, user_id, source_link, source_type, compared_theme_id) VALUES (
		'ABC',
		v_theme_id1,
		(SELECT samples.users()),
		NULL,
		'head'::sources_type,
		format('[%s,%s]', v_theme_id2, v_theme_id3)::jsonb
	);

	PERFORM assert.same(2, (SELECT count(*)::integer FROM bulletpoint_theme_comparisons));
END $BODY$ LANGUAGE plpgsql VOLATILE;

CREATE FUNCTION tests.updating_with_comparisons() RETURNS void AS $BODY$
DECLARE
	v_bulletpoint_id bulletpoints.id%type;
	v_theme_id1 themes.id%type NOT NULL DEFAULT samples.themes();
	v_theme_id2 themes.id%type NOT NULL DEFAULT samples.themes();
	v_theme_id3 themes.id%type NOT NULL DEFAULT samples.themes();
	v_tag_id tags.id%type NOT NULL DEFAULT samples.tags();
BEGIN
	PERFORM samples.theme_tags(jsonb_build_object('tag_id', v_tag_id, 'theme_id', v_theme_id1));
	PERFORM samples.theme_tags(jsonb_build_object('tag_id', v_tag_id, 'theme_id', v_theme_id2));
	PERFORM samples.theme_tags(jsonb_build_object('tag_id', v_tag_id, 'theme_id', v_theme_id3));

	SELECT samples.public_bulletpoints(jsonb_build_object('theme_id', v_theme_id1)) INTO v_bulletpoint_id;
	UPDATE web.bulletpoints SET
		content = 'ABC',
		compared_theme_id = format('[%s,%s]', v_theme_id2, v_theme_id3)::jsonb
	WHERE id = v_bulletpoint_id;

	PERFORM assert.same(2, (SELECT count(*)::integer FROM bulletpoint_theme_comparisons));
END $BODY$ LANGUAGE plpgsql VOLATILE;
