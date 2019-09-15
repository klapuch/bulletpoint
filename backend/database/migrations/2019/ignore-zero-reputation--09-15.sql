INSERT INTO deploy.migrations(filename) VALUES('database/migrations/2019/ignore-zero-reputation--09-15.sql');

CREATE OR REPLACE FUNCTION update_user_tag_reputation(in_user_id integer, in_tag_id integer, in_point bulletpoint_ratings_point) RETURNS void AS $BODY$
DECLARE
	v_point CONSTANT integer NOT NULL DEFAULT CASE in_point WHEN 1 THEN 1 ELSE -1 END;
BEGIN
	INSERT INTO user_tag_reputations (user_id, tag_id, reputation) VALUES (in_user_id, in_tag_id, greatest(v_point, 0))
	ON CONFLICT (user_id, tag_id) DO UPDATE SET reputation = greatest(user_tag_reputations.reputation + v_point, 0);
END;
$BODY$ LANGUAGE plpgsql VOLATILE;
