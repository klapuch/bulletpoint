CREATE FUNCTION tests.incrementing() RETURNS void AS $BODY$
	DECLARE
		v_user_id integer;
		v_theme_id integer;
BEGIN
	SELECT samples.users() INTO v_user_id;
	SELECT samples.themes(jsonb_build_object('reference_id', samples."references"(), 'user_id', v_user_id)) INTO v_theme_id;
	PERFORM samples.theme_tags(jsonb_build_object('theme_id', v_theme_id, 'tag_id', samples.tags()));
	PERFORM samples.bulletpoints(jsonb_build_object('theme_id', v_theme_id, 'source_id', samples.sources(), 'user_id', v_user_id));
	PERFORM samples.bulletpoints(jsonb_build_object('theme_id', v_theme_id, 'source_id', samples.sources(), 'user_id', v_user_id));
	PERFORM samples.bulletpoints(jsonb_build_object('theme_id', v_theme_id, 'source_id', samples.sources(), 'user_id', v_user_id));
	PERFORM samples.bulletpoints(jsonb_build_object('theme_id', v_theme_id, 'source_id', samples.sources(), 'user_id', v_user_id));

	PERFORM assert.same(1, (SELECT count(*)::integer FROM user_tag_reputations));

	PERFORM assert.same(4, (SELECT reputation FROM user_tag_reputations));
END $BODY$ LANGUAGE plpgsql VOLATILE;
