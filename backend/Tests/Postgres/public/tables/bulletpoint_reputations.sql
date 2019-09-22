CREATE FUNCTION tests.single_tag() RETURNS void AS $BODY$
DECLARE
	v_user_id integer NOT NULL DEFAULT samples.users();
	v_source_id integer NOT NULL DEFAULT samples.sources();
	v_tag_id integer NOT NULL DEFAULT samples.tags();
	v_theme_id integer;
BEGIN
	SELECT samples.themes(jsonb_build_object('reference_id', samples."references"(), 'user_id', v_user_id)) INTO v_theme_id;
	PERFORM samples.theme_tags(jsonb_build_object('theme_id', v_theme_id, 'tag_id', v_tag_id));
	PERFORM samples.public_bulletpoints(jsonb_build_object('theme_id', v_theme_id, 'source_id', v_source_id, 'user_id', v_user_id));

	REFRESH MATERIALIZED VIEW bulletpoint_reputations;

	PERFORM assert.same(1, (SELECT count(*)::integer FROM user_tag_reputations));
	PERFORM assert.same(1, (SELECT count(*)::integer FROM bulletpoint_reputations));

	PERFORM assert.same(1, (SELECT reputation FROM user_tag_reputations));
	PERFORM assert.same(1, (SELECT reputation::integer FROM bulletpoint_reputations));
END $BODY$ LANGUAGE plpgsql VOLATILE;

CREATE FUNCTION tests.multiple_tags() RETURNS void AS $BODY$
DECLARE
	v_user_id integer NOT NULL DEFAULT samples.users();
	v_source_id integer NOT NULL DEFAULT samples.sources();
	v_tag_id1 integer NOT NULL DEFAULT samples.tags();
	v_tag_id2 integer NOT NULL DEFAULT samples.tags();
	v_theme_id integer;
BEGIN
	SELECT samples.themes(jsonb_build_object('reference_id', samples."references"(), 'user_id', v_user_id)) INTO v_theme_id;
	PERFORM samples.theme_tags(jsonb_build_object('theme_id', v_theme_id, 'tag_id', v_tag_id1));
	PERFORM samples.theme_tags(jsonb_build_object('theme_id', v_theme_id, 'tag_id', v_tag_id2));
	PERFORM samples.public_bulletpoints(jsonb_build_object('theme_id', v_theme_id, 'source_id', v_source_id, 'user_id', v_user_id));

	REFRESH MATERIALIZED VIEW bulletpoint_reputations;

	PERFORM assert.same(2, (SELECT count(*)::integer FROM user_tag_reputations));
	PERFORM assert.same(1, (SELECT count(*)::integer FROM bulletpoint_reputations));

	PERFORM assert.same(1, (SELECT reputation FROM user_tag_reputations WHERE tag_id = v_tag_id1));
	PERFORM assert.same(1, (SELECT reputation FROM user_tag_reputations WHERE tag_id = v_tag_id2));
	PERFORM assert.same(2, (SELECT reputation::integer FROM bulletpoint_reputations));
END $BODY$ LANGUAGE plpgsql VOLATILE;

CREATE FUNCTION tests.delete_from_tags() RETURNS void AS $BODY$
DECLARE
	v_user_id integer NOT NULL DEFAULT samples.users();
	v_source_id integer NOT NULL DEFAULT samples.sources();
	v_tag_id1 integer NOT NULL DEFAULT samples.tags();
	v_tag_id2 integer NOT NULL DEFAULT samples.tags();
	v_theme_id integer;
BEGIN
	SELECT samples.themes(jsonb_build_object('reference_id', samples."references"(), 'user_id', v_user_id)) INTO v_theme_id;
	PERFORM samples.theme_tags(jsonb_build_object('theme_id', v_theme_id, 'tag_id', v_tag_id1));
	PERFORM samples.theme_tags(jsonb_build_object('theme_id', v_theme_id, 'tag_id', v_tag_id2));
	PERFORM samples.public_bulletpoints(jsonb_build_object('theme_id', v_theme_id, 'source_id', v_source_id, 'user_id', v_user_id));

	DELETE FROM tags WHERE id = v_tag_id1;

	REFRESH MATERIALIZED VIEW bulletpoint_reputations;

	PERFORM assert.same(1, (SELECT count(*)::integer FROM user_tag_reputations));
	PERFORM assert.same(1, (SELECT count(*)::integer FROM bulletpoint_reputations));

	PERFORM assert.null((SELECT reputation FROM user_tag_reputations WHERE tag_id = v_tag_id1));
	PERFORM assert.same(1, (SELECT reputation FROM user_tag_reputations WHERE tag_id = v_tag_id2));
	PERFORM assert.same(1, (SELECT reputation::integer FROM bulletpoint_reputations));
END $BODY$ LANGUAGE plpgsql VOLATILE;

CREATE FUNCTION tests.delete_from_theme_tags() RETURNS void AS $BODY$
DECLARE
	v_user_id integer NOT NULL DEFAULT samples.users();
	v_source_id integer NOT NULL DEFAULT samples.sources();
	v_tag_id1 integer NOT NULL DEFAULT samples.tags();
	v_tag_id2 integer NOT NULL DEFAULT samples.tags();
	v_theme_id integer;
BEGIN
	SELECT samples.themes(jsonb_build_object('reference_id', samples."references"(), 'user_id', v_user_id)) INTO v_theme_id;
	PERFORM samples.theme_tags(jsonb_build_object('theme_id', v_theme_id, 'tag_id', v_tag_id1));
	PERFORM samples.theme_tags(jsonb_build_object('theme_id', v_theme_id, 'tag_id', v_tag_id2));
	PERFORM samples.public_bulletpoints(jsonb_build_object('theme_id', v_theme_id, 'source_id', v_source_id, 'user_id', v_user_id));

	DELETE FROM theme_tags WHERE tag_id = v_tag_id1;

	REFRESH MATERIALIZED VIEW bulletpoint_reputations;

	PERFORM assert.same(2, (SELECT count(*)::integer FROM user_tag_reputations));
	PERFORM assert.same(1, (SELECT count(*)::integer FROM bulletpoint_reputations));

	PERFORM assert.same(0, (SELECT reputation FROM user_tag_reputations WHERE tag_id = v_tag_id1));
	PERFORM assert.same(1, (SELECT reputation FROM user_tag_reputations WHERE tag_id = v_tag_id2));
	PERFORM assert.same(1, (SELECT reputation::integer FROM bulletpoint_reputations));
END $BODY$ LANGUAGE plpgsql VOLATILE;

CREATE FUNCTION tests.multiple_bulletpoints() RETURNS void AS $BODY$
DECLARE
	v_user_id integer NOT NULL DEFAULT samples.users();
	v_tag_id1 integer NOT NULL DEFAULT samples.tags();
	v_tag_id2 integer NOT NULL DEFAULT samples.tags();
	v_theme_id integer;
BEGIN
	SELECT samples.themes(jsonb_build_object('reference_id', samples."references"(), 'user_id', v_user_id)) INTO v_theme_id;
	PERFORM samples.theme_tags(jsonb_build_object('theme_id', v_theme_id, 'tag_id', v_tag_id1));
	PERFORM samples.theme_tags(jsonb_build_object('theme_id', v_theme_id, 'tag_id', v_tag_id2));
	PERFORM samples.public_bulletpoints(jsonb_build_object('theme_id', v_theme_id, 'source_id', samples.sources(), 'user_id', v_user_id));
	PERFORM samples.public_bulletpoints(jsonb_build_object('theme_id', v_theme_id, 'source_id', samples.sources(), 'user_id', v_user_id));

	REFRESH MATERIALIZED VIEW bulletpoint_reputations;

	PERFORM assert.same(2, (SELECT count(*)::integer FROM user_tag_reputations));
	PERFORM assert.same(2, (SELECT count(*)::integer FROM bulletpoint_reputations));

	PERFORM assert.same(2, (SELECT reputation FROM user_tag_reputations WHERE tag_id = v_tag_id1));
	PERFORM assert.same(2, (SELECT reputation FROM user_tag_reputations WHERE tag_id = v_tag_id2));
	PERFORM assert.same(4, (SELECT reputation::integer FROM bulletpoint_reputations LIMIT 1 OFFSET 0));
	PERFORM assert.same(4, (SELECT reputation::integer FROM bulletpoint_reputations LIMIT 1 OFFSET 1));
END $BODY$ LANGUAGE plpgsql VOLATILE;

CREATE FUNCTION tests.multiple_bulletpoints_remove_tag() RETURNS void AS $BODY$
DECLARE
	v_user_id integer NOT NULL DEFAULT samples.users();
	v_tag_id1 integer NOT NULL DEFAULT samples.tags();
	v_tag_id2 integer NOT NULL DEFAULT samples.tags();
	v_theme_id integer;
BEGIN
	SELECT samples.themes(jsonb_build_object('reference_id', samples."references"(), 'user_id', v_user_id)) INTO v_theme_id;
	PERFORM samples.theme_tags(jsonb_build_object('theme_id', v_theme_id, 'tag_id', v_tag_id1));
	PERFORM samples.theme_tags(jsonb_build_object('theme_id', v_theme_id, 'tag_id', v_tag_id2));
	PERFORM samples.public_bulletpoints(jsonb_build_object('theme_id', v_theme_id, 'source_id', samples.sources(), 'user_id', v_user_id));
	PERFORM samples.public_bulletpoints(jsonb_build_object('theme_id', v_theme_id, 'source_id', samples.sources(), 'user_id', v_user_id));

	DELETE FROM tags WHERE id = v_tag_id1;

	REFRESH MATERIALIZED VIEW bulletpoint_reputations;

	PERFORM assert.same(1, (SELECT count(*)::integer FROM user_tag_reputations));
	PERFORM assert.same(2, (SELECT count(*)::integer FROM bulletpoint_reputations));

	PERFORM assert.null((SELECT reputation FROM user_tag_reputations WHERE tag_id = v_tag_id1));
	PERFORM assert.same(2, (SELECT reputation FROM user_tag_reputations WHERE tag_id = v_tag_id2));
	PERFORM assert.same(2, (SELECT reputation::integer FROM bulletpoint_reputations LIMIT 1 OFFSET 0));
	PERFORM assert.same(2, (SELECT reputation::integer FROM bulletpoint_reputations LIMIT 1 OFFSET 1));
END $BODY$ LANGUAGE plpgsql VOLATILE;


CREATE FUNCTION tests.multiple_bulletpoints_remove_theme_tag() RETURNS void AS $BODY$
DECLARE
	v_user_id integer NOT NULL DEFAULT samples.users();
	v_tag_id1 integer NOT NULL DEFAULT samples.tags();
	v_tag_id2 integer NOT NULL DEFAULT samples.tags();
	v_theme_id integer;
BEGIN
	SELECT samples.themes(jsonb_build_object('reference_id', samples."references"(), 'user_id', v_user_id)) INTO v_theme_id;
	PERFORM samples.theme_tags(jsonb_build_object('theme_id', v_theme_id, 'tag_id', v_tag_id1));
	PERFORM samples.theme_tags(jsonb_build_object('theme_id', v_theme_id, 'tag_id', v_tag_id2));
	PERFORM samples.public_bulletpoints(jsonb_build_object('theme_id', v_theme_id, 'source_id', samples.sources(), 'user_id', v_user_id));
	PERFORM samples.public_bulletpoints(jsonb_build_object('theme_id', v_theme_id, 'source_id', samples.sources(), 'user_id', v_user_id));

	DELETE FROM theme_tags WHERE tag_id = v_tag_id1;

	REFRESH MATERIALIZED VIEW bulletpoint_reputations;

	PERFORM assert.same(2, (SELECT count(*)::integer FROM user_tag_reputations));
	PERFORM assert.same(2, (SELECT count(*)::integer FROM bulletpoint_reputations));

	PERFORM assert.same(0, (SELECT reputation FROM user_tag_reputations WHERE tag_id = v_tag_id1));
	PERFORM assert.same(2, (SELECT reputation FROM user_tag_reputations WHERE tag_id = v_tag_id2));
	PERFORM assert.same(2, (SELECT reputation::integer FROM bulletpoint_reputations LIMIT 1 OFFSET 0));
	PERFORM assert.same(2, (SELECT reputation::integer FROM bulletpoint_reputations LIMIT 1 OFFSET 1));
END $BODY$ LANGUAGE plpgsql VOLATILE;
