INSERT INTO deploy.migrations(filename) VALUES('database/migrations/2019/user_tag_recount--06-23.sql');

CREATE OR REPLACE FUNCTION theme_tags_trigger_row_ad() RETURNS trigger AS $BODY$
BEGIN
	IF EXISTS (SELECT 1 FROM tags WHERE id = old.tag_id) THEN
		PERFORM update_user_tag_reputation(bulletpoints.user_id, old.tag_id, -1::bulletpoint_ratings_point)
		FROM bulletpoints
		JOIN theme_tags ON theme_tags.theme_id = bulletpoints.theme_id;
	END IF;

	RETURN old;
END;
$BODY$ LANGUAGE plpgsql VOLATILE;

CREATE OR REPLACE FUNCTION update_user_tag_reputation(in_user_id integer, in_tag_id integer, in_point bulletpoint_ratings_point) RETURNS void AS $BODY$
DECLARE
	v_point CONSTANT integer NOT NULL DEFAULT CASE in_point WHEN 1 THEN 1 ELSE -1 END;
BEGIN
	INSERT INTO user_tag_reputations (user_id, tag_id, reputation) VALUES (in_user_id, in_tag_id, greatest(v_point, 0))
	ON CONFLICT (user_id, tag_id) DO UPDATE SET reputation = user_tag_reputations.reputation + v_point;
END;
$BODY$ LANGUAGE plpgsql VOLATILE;

TRUNCATE user_tag_reputations;

SELECT update_user_tag_reputation(bulletpoints.user_id, theme_tags.tag_id, bulletpoint_ratings.point)
FROM public_bulletpoints AS bulletpoints
JOIN theme_tags ON theme_tags.theme_id = bulletpoints.theme_id
JOIN bulletpoint_ratings ON bulletpoint_ratings.bulletpoint_id = bulletpoints.id;

REFRESH MATERIALIZED VIEW user_tag_rank_reputations;
