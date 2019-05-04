CREATE FUNCTION tests.rating_step_by_step() RETURNS void AS $BODY$
DECLARE
	v_bulletpoint_id bulletpoints.id%type;
	v_user_id users.id%type NOT NULL DEFAULT samples.users();
BEGIN
	SELECT samples.public_bulletpoints(jsonb_build_object('user_id', v_user_id)) INTO v_bulletpoint_id;

	PERFORM assert.same(ARRAY[1, 0], (SELECT ARRAY[up_points, down_points] FROM bulletpoint_rating_summary WHERE bulletpoint_id = v_bulletpoint_id));
	UPDATE bulletpoint_ratings SET point = 0 WHERE user_id = v_user_id AND bulletpoint_id = v_bulletpoint_id;
	PERFORM assert.same(ARRAY[0, 0], (SELECT ARRAY[up_points, down_points] FROM bulletpoint_rating_summary WHERE bulletpoint_id = v_bulletpoint_id));

	PERFORM assert.same(ARRAY[0, 0], (SELECT ARRAY[up_points, down_points] FROM bulletpoint_rating_summary WHERE bulletpoint_id = v_bulletpoint_id));
	UPDATE bulletpoint_ratings SET point = -1 WHERE user_id = v_user_id AND bulletpoint_id = v_bulletpoint_id;
	PERFORM assert.same(ARRAY[0, 1], (SELECT ARRAY[up_points, down_points] FROM bulletpoint_rating_summary WHERE bulletpoint_id = v_bulletpoint_id));

	UPDATE bulletpoint_ratings SET point = 0 WHERE user_id = v_user_id AND bulletpoint_id = v_bulletpoint_id;
	PERFORM assert.same(ARRAY[0, 0], (SELECT ARRAY[up_points, down_points] FROM bulletpoint_rating_summary WHERE bulletpoint_id = v_bulletpoint_id));
	UPDATE bulletpoint_ratings SET point = 1 WHERE user_id = v_user_id AND bulletpoint_id = v_bulletpoint_id;
	PERFORM assert.same(ARRAY[1, 0], (SELECT ARRAY[up_points, down_points] FROM bulletpoint_rating_summary WHERE bulletpoint_id = v_bulletpoint_id));

	UPDATE bulletpoint_ratings SET point = 0 WHERE user_id = v_user_id AND bulletpoint_id = v_bulletpoint_id;
	PERFORM assert.same(ARRAY[0, 0], (SELECT ARRAY[up_points, down_points] FROM bulletpoint_rating_summary WHERE bulletpoint_id = v_bulletpoint_id));
	UPDATE bulletpoint_ratings SET point = 1 WHERE user_id = v_user_id AND bulletpoint_id = v_bulletpoint_id;
	UPDATE bulletpoint_ratings SET point = -1 WHERE user_id = v_user_id AND bulletpoint_id = v_bulletpoint_id;
	PERFORM assert.same(ARRAY[0, 1], (SELECT ARRAY[up_points, down_points] FROM bulletpoint_rating_summary WHERE bulletpoint_id = v_bulletpoint_id));

	PERFORM assert.same(ARRAY[0, 1], (SELECT ARRAY[up_points, down_points] FROM bulletpoint_rating_summary WHERE bulletpoint_id = v_bulletpoint_id));
	UPDATE bulletpoint_ratings SET point = 1 WHERE user_id = v_user_id AND bulletpoint_id = v_bulletpoint_id;
	PERFORM assert.same(ARRAY[1, 0], (SELECT ARRAY[up_points, down_points] FROM bulletpoint_rating_summary WHERE bulletpoint_id = v_bulletpoint_id));

END $BODY$ LANGUAGE plpgsql VOLATILE;
