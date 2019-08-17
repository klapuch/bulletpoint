INSERT INTO deploy.migrations(filename) VALUES('database/migrations/2019/range--06-23.sql');

ALTER DOMAIN http_status DROP CONSTRAINT http_status_check;
ALTER DOMAIN http_status ADD CONSTRAINT http_status_check CHECK (VALUE BETWEEN 100 AND 504);

DROP VIEW web.tagged_themes;
DROP VIEW web.themes;
DROP MATERIALIZED VIEW broken_references;
CREATE MATERIALIZED VIEW broken_references AS
	SELECT reference_id
	FROM reference_pings
	WHERE now() - INTERVAL '3 days' < ping_at
	AND (status IS NULL OR status BETWEEN 400 AND 599)
	GROUP BY reference_id
	HAVING count(*) >= 3;
CREATE UNIQUE INDEX broken_references_reference_id_uidx ON broken_references(reference_id);

CREATE VIEW web.themes AS
	SELECT
		themes.id, themes.name, json_tags.tags, themes.created_at,
		"references".url AS reference_url,
		broken_references.reference_id IS NOT NULL AS reference_is_broken,
		users.id AS user_id,
		COALESCE(json_theme_alternative_names.alternative_names, '[]') AS alternative_names,
		user_starred_themes.id IS NOT NULL AS is_starred,
		user_starred_themes.starred_at,
		array_to_json(ARRAY(SELECT related_themes(themes.id)))::jsonb AS related_themes_id,
		unique_theme_bulletpoints.theme_id IS NULL AS is_empty
	FROM public.themes
	JOIN users ON users.id = themes.user_id
	LEFT JOIN "references" ON "references".id = themes.reference_id
	LEFT JOIN broken_references ON broken_references.reference_id = themes.reference_id
	LEFT JOIN (
		SELECT theme_id, jsonb_agg(tags.*) AS tags
		FROM theme_tags
		JOIN tags ON tags.id = theme_tags.tag_id
		GROUP BY theme_id
	) AS json_tags ON json_tags.theme_id = themes.id
	LEFT JOIN (
		SELECT theme_id, jsonb_agg(name) AS alternative_names
		FROM theme_alternative_names
		GROUP BY theme_id
	) AS json_theme_alternative_names ON json_theme_alternative_names.theme_id = themes.id
	LEFT JOIN user_starred_themes ON user_starred_themes.theme_id = themes.id AND user_starred_themes.user_id = globals_get_user()
	LEFT JOIN (
		SELECT DISTINCT theme_id
		FROM bulletpoints
	) AS unique_theme_bulletpoints ON unique_theme_bulletpoints.theme_id = public.themes.id;


CREATE VIEW web.tagged_themes AS
	SELECT tag_id, themes.*
	FROM web.themes
	LEFT JOIN theme_tags ON theme_tags.theme_id = themes.id;



DROP VIEW web.bulletpoints;
DROP VIEW web.contributed_bulletpoints;
DROP MATERIALIZED VIEW broken_sources;
CREATE MATERIALIZED VIEW broken_sources AS
	SELECT source_id
	FROM source_pings
	WHERE now() - INTERVAL '3 days' < ping_at
	AND (status IS NULL OR status BETWEEN 400 AND 599)
	GROUP BY source_id
	HAVING count(*) >= 3;
CREATE UNIQUE INDEX broken_sources_source_id_uidx ON broken_sources(source_id);



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
	ORDER BY total_rating DESC, bulletpoint_reputations.reputation DESC, length(bulletpoints.content) ASC, created_at DESC, id DESC;


CREATE VIEW web.contributed_bulletpoints AS
SELECT
	contributed_bulletpoints.id, contributed_bulletpoints.content, contributed_bulletpoints.theme_id, contributed_bulletpoints.user_id,
	sources.link AS source_link, sources.type AS source_type, broken_sources.source_id IS NOT NULL AS source_is_broken,
	COALESCE(bulletpoint_referenced_themes.referenced_theme_id, '[]') AS referenced_theme_id,
	COALESCE(bulletpoint_theme_comparisons.compared_theme_id, '[]') AS compared_theme_id,
	bulletpoint_groups.root_bulletpoint_id
	FROM public.contributed_bulletpoints
	LEFT JOIN public.sources ON sources.id = contributed_bulletpoints.source_id
	LEFT JOIN public.broken_sources ON broken_sources.source_id = sources.id
	LEFT JOIN (
		SELECT bulletpoint_id, jsonb_agg(public.bulletpoint_referenced_themes.theme_id) AS referenced_theme_id
		FROM public.bulletpoint_referenced_themes
		GROUP BY bulletpoint_id
	) AS bulletpoint_referenced_themes ON bulletpoint_referenced_themes.bulletpoint_id = contributed_bulletpoints.id
	LEFT JOIN (
		SELECT bulletpoint_id, jsonb_agg(public.bulletpoint_theme_comparisons.theme_id) AS compared_theme_id
		FROM public.bulletpoint_theme_comparisons
		GROUP BY bulletpoint_id
	) AS bulletpoint_theme_comparisons ON bulletpoint_theme_comparisons.bulletpoint_id = contributed_bulletpoints.id
	LEFT JOIN bulletpoint_groups ON bulletpoint_groups.bulletpoint_id = contributed_bulletpoints.id
	ORDER BY contributed_bulletpoints.created_at DESC, length(contributed_bulletpoints.content) ASC;
