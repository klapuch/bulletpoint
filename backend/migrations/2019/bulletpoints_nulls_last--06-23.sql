INSERT INTO deploy.migrations(filename) VALUES('migrations/2019/bulletpoints_nulls_last--06-23.sql');

DROP VIEW web.bulletpoints;
CREATE VIEW web.bulletpoints AS
	SELECT
		bulletpoints.id, bulletpoints.content, bulletpoints.theme_id, bulletpoints.user_id, bulletpoints.created_at,
		sources.link AS source_link,
		sources.type AS source_type,
		broken_sources.source_id IS NOT NULL AS source_is_broken,
			bulletpoint_rating_summary.up_points AS up_rating,
			bulletpoint_rating_summary.down_points AS down_rating,
			(bulletpoint_rating_summary.up_points + bulletpoint_rating_summary.down_points) AS total_rating,
		COALESCE(user_bulletpoint_ratings.user_rating, 0) AS user_rating,
		COALESCE(bulletpoint_referenced_themes.referenced_theme_id, '[]') AS referenced_theme_id,
		COALESCE(bulletpoint_theme_comparisons.compared_theme_id, '[]') AS compared_theme_id,
		bulletpoint_groups.root_bulletpoint_id
	FROM public.public_bulletpoints AS bulletpoints
	LEFT JOIN (
		SELECT bulletpoint_id, CASE user_id WHEN globals_get_user() THEN point ELSE 0 END AS user_rating
		FROM public.bulletpoint_ratings
		WHERE user_id = globals_get_user()
	) AS user_bulletpoint_ratings ON user_bulletpoint_ratings.bulletpoint_id = bulletpoints.id
	LEFT JOIN bulletpoint_rating_summary ON bulletpoint_rating_summary.bulletpoint_id = bulletpoints.id
	LEFT JOIN public.sources ON sources.id = bulletpoints.source_id
	LEFT JOIN public.broken_sources ON broken_sources.source_id = sources.id
	LEFT JOIN public.bulletpoint_reputations ON bulletpoint_reputations.bulletpoint_id = bulletpoints.id
	LEFT JOIN (
		SELECT bulletpoint_id, jsonb_agg(public.bulletpoint_referenced_themes.theme_id) AS referenced_theme_id
		FROM public.bulletpoint_referenced_themes
		GROUP BY bulletpoint_id
	) AS bulletpoint_referenced_themes ON bulletpoint_referenced_themes.bulletpoint_id = bulletpoints.id
	LEFT JOIN (
		SELECT bulletpoint_id, jsonb_agg(public.bulletpoint_theme_comparisons.theme_id) AS compared_theme_id
		FROM public.bulletpoint_theme_comparisons
		GROUP BY bulletpoint_id
	) AS bulletpoint_theme_comparisons ON bulletpoint_theme_comparisons.bulletpoint_id = bulletpoints.id
	LEFT JOIN bulletpoint_groups ON bulletpoint_groups.bulletpoint_id = bulletpoints.id
	ORDER BY total_rating DESC, bulletpoint_reputations.reputation DESC NULLS LAST, length(bulletpoints.content) ASC, created_at DESC, id DESC;


CREATE TRIGGER bulletpoints_trigger_row_ii
	INSTEAD OF INSERT
	ON web.bulletpoints
	FOR EACH ROW EXECUTE PROCEDURE web.bulletpoints_trigger_row_ii();

CREATE TRIGGER bulletpoints_trigger_row_iu
	INSTEAD OF UPDATE
	ON web.bulletpoints
	FOR EACH ROW EXECUTE PROCEDURE web.bulletpoints_trigger_row_iu();
