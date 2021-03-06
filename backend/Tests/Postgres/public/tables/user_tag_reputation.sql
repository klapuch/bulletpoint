CREATE FUNCTION tests.incrementing() RETURNS void AS $BODY$
DECLARE
	v_user_id users.id%type NOT NULL DEFAULT samples.users();
	v_theme_id themes.id%type;
BEGIN
	SELECT samples.themes(jsonb_build_object('reference_id', samples."references"(), 'user_id', v_user_id)) INTO v_theme_id;
	PERFORM samples.theme_tags(jsonb_build_object('theme_id', v_theme_id, 'tag_id', samples.tags()));
	PERFORM samples.public_bulletpoints(jsonb_build_object('theme_id', v_theme_id, 'source_id', samples.sources(), 'user_id', v_user_id));
	PERFORM samples.public_bulletpoints(jsonb_build_object('theme_id', v_theme_id, 'source_id', samples.sources(), 'user_id', v_user_id));
	PERFORM samples.public_bulletpoints(jsonb_build_object('theme_id', v_theme_id, 'source_id', samples.sources(), 'user_id', v_user_id));
	PERFORM samples.public_bulletpoints(jsonb_build_object('theme_id', v_theme_id, 'source_id', samples.sources(), 'user_id', v_user_id));

	PERFORM assert.same(1, (SELECT count(*)::integer FROM user_tag_reputations));

	PERFORM assert.same(4, (SELECT reputation FROM user_tag_reputations));
END $BODY$ LANGUAGE plpgsql VOLATILE;
