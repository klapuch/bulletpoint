CREATE FUNCTION tests.new_delete_successor() RETURNS void AS $BODY$
DECLARE
	v_theme_id themes.id%type;
	v_bulletpoint_id1 bulletpoints.id%type;
	v_bulletpoint_id2 bulletpoints.id%type;
	v_bulletpoint_id3 bulletpoints.id%type;
BEGIN
	SELECT samples.themes() INTO v_theme_id;
	SELECT samples.public_bulletpoints(jsonb_build_object('theme_id', v_theme_id, 'content', 'A')) INTO v_bulletpoint_id1;
	SELECT samples.public_bulletpoints(jsonb_build_object('theme_id', v_theme_id, 'content', 'AB')) INTO v_bulletpoint_id2;
	SELECT samples.public_bulletpoints(jsonb_build_object('theme_id', v_theme_id, 'content', 'ABC')) INTO v_bulletpoint_id3;

	INSERT INTO bulletpoint_groups (bulletpoint_id, root_bulletpoint_id) VALUES (v_bulletpoint_id2, v_bulletpoint_id1);
	INSERT INTO bulletpoint_groups (bulletpoint_id, root_bulletpoint_id) VALUES (v_bulletpoint_id3, v_bulletpoint_id1);

	DELETE FROM bulletpoints WHERE id = v_bulletpoint_id1;

	PERFORM assert.same(1, (SELECT count(*)::integer FROM bulletpoint_groups));
	PERFORM assert.same(v_bulletpoint_id2, (SELECT root_bulletpoint_id FROM bulletpoint_groups WHERE bulletpoint_id = v_bulletpoint_id3));
END $BODY$ LANGUAGE plpgsql VOLATILE;

CREATE FUNCTION tests.new_insert_successor() RETURNS void AS $BODY$
DECLARE
	v_theme_id themes.id%type;
	v_bulletpoint_id1 bulletpoints.id%type;
	v_bulletpoint_id2 bulletpoints.id%type;
	v_bulletpoint_id3 bulletpoints.id%type;
	v_bulletpoint_id4 bulletpoints.id%type;
BEGIN
	SELECT samples.themes() INTO v_theme_id;
	SELECT samples.public_bulletpoints(jsonb_build_object('theme_id', v_theme_id, 'content', 'A')) INTO v_bulletpoint_id1;
	SELECT samples.public_bulletpoints(jsonb_build_object('theme_id', v_theme_id, 'content', 'AB')) INTO v_bulletpoint_id2;
	SELECT samples.public_bulletpoints(jsonb_build_object('theme_id', v_theme_id, 'content', 'ABC')) INTO v_bulletpoint_id3;
	SELECT samples.public_bulletpoints(jsonb_build_object('theme_id', v_theme_id, 'content', 'ABCD')) INTO v_bulletpoint_id4;

	INSERT INTO bulletpoint_groups (bulletpoint_id, root_bulletpoint_id) VALUES (v_bulletpoint_id3, v_bulletpoint_id2);
	INSERT INTO bulletpoint_groups (bulletpoint_id, root_bulletpoint_id) VALUES (v_bulletpoint_id4, v_bulletpoint_id2);
	INSERT INTO bulletpoint_groups (bulletpoint_id, root_bulletpoint_id) VALUES (v_bulletpoint_id1, v_bulletpoint_id2);

	PERFORM assert.same(3, (SELECT count(*)::integer FROM bulletpoint_groups));
	PERFORM assert.same(v_bulletpoint_id1, (SELECT root_bulletpoint_id FROM bulletpoint_groups WHERE bulletpoint_id = v_bulletpoint_id2));
	PERFORM assert.same(v_bulletpoint_id1, (SELECT root_bulletpoint_id FROM bulletpoint_groups WHERE bulletpoint_id = v_bulletpoint_id3));
	PERFORM assert.same(v_bulletpoint_id1, (SELECT root_bulletpoint_id FROM bulletpoint_groups WHERE bulletpoint_id = v_bulletpoint_id4));
END $BODY$ LANGUAGE plpgsql VOLATILE;
