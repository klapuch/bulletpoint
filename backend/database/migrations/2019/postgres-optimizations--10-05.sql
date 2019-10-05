INSERT INTO deploy.migrations(filename) VALUES('database/migrations/2019/postgres-optimizations--10-05.sql');

CREATE OR REPLACE FUNCTION theme_tags_trigger_row_ad() RETURNS trigger AS $BODY$
BEGIN
	IF EXISTS (SELECT 1 FROM tags WHERE id = old.tag_id) THEN
		PERFORM update_user_tag_reputation(bulletpoints.user_id, old.tag_id, -1::bulletpoint_ratings_point)
		FROM bulletpoints
		WHERE theme_id = old.theme_id;
	END IF;

	RETURN old;
END;
$BODY$ LANGUAGE plpgsql VOLATILE;


CREATE OR REPLACE FUNCTION bulletpoint_ratings_trigger_row_aiud() RETURNS trigger AS $BODY$
DECLARE
	r bulletpoint_ratings;
	v_multiply integer NOT NULL DEFAULT CASE TG_OP WHEN 'DELETE' THEN -1 ELSE 1 END;
BEGIN
	r = CASE TG_OP WHEN 'DELETE' THEN old ELSE new END;

	IF TG_OP = 'DELETE' AND NOT EXISTS (SELECT 1 FROM bulletpoints WHERE id = r.bulletpoint_id) THEN
		RETURN r;
	END IF;

	PERFORM update_user_tag_reputation(bulletpoints.user_id, theme_tags.tag_id, r.point * v_multiply)
	FROM public_bulletpoints AS bulletpoints
	JOIN theme_tags ON theme_tags.theme_id = bulletpoints.theme_id
	WHERE bulletpoints.id = r.bulletpoint_id;

	INSERT INTO bulletpoint_rating_summary (bulletpoint_id, up_points, down_points)
	SELECT
		r.bulletpoint_id,
		COALESCE(sum(point) FILTER (WHERE point = 1), 0),
		abs(COALESCE(sum(point) FILTER (WHERE point = -1), 0))
	FROM bulletpoint_ratings
	WHERE bulletpoint_id = r.bulletpoint_id
	ON CONFLICT (bulletpoint_id)
	DO UPDATE SET up_points = EXCLUDED.up_points, down_points = EXCLUDED.down_points;

	RETURN r;
END;
$BODY$ LANGUAGE plpgsql VOLATILE;
