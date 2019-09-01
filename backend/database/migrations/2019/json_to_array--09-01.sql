INSERT INTO deploy.migrations(filename) VALUES('database/migrations/2019/json_to_array--09-01.sql');

CREATE OR REPLACE VIEW web.themes AS
	SELECT
		themes.id, themes.name, json_tags.tags, themes.created_at,
		"references".url AS reference_url,
		broken_references.reference_id IS NOT NULL AS reference_is_broken,
		users.id AS user_id,
		COALESCE(array_theme_alternative_names.alternative_names, ARRAY[]::text[]) AS alternative_names,
		user_starred_themes.id IS NOT NULL AS is_starred,
		user_starred_themes.starred_at,
		ARRAY(SELECT related_themes(themes.id)) AS related_themes_id,
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
		SELECT theme_id, array_agg(name) AS alternative_names
		FROM theme_alternative_names
		GROUP BY theme_id
	) AS array_theme_alternative_names ON array_theme_alternative_names.theme_id = themes.id
	LEFT JOIN user_starred_themes ON user_starred_themes.theme_id = themes.id AND user_starred_themes.user_id = globals_get_user()
	LEFT JOIN (
		SELECT DISTINCT theme_id
		FROM bulletpoints
	) AS unique_theme_bulletpoints ON unique_theme_bulletpoints.theme_id = public.themes.id;


CREATE OR REPLACE FUNCTION web.themes_trigger_row_ii() RETURNS trigger AS $BODY$
DECLARE
	v_theme_id integer;
BEGIN
	WITH inserted_reference AS (
		INSERT INTO public."references" (url) VALUES (new.reference_url)
		RETURNING id
	)
	INSERT INTO public.themes (name, reference_id, user_id) VALUES (new.name, (SELECT id FROM inserted_reference), new.user_id)
	RETURNING id INTO v_theme_id;

	INSERT INTO public.theme_tags (theme_id, tag_id)
	SELECT v_theme_id, r.tag::integer FROM jsonb_array_elements(new.tags) AS r(tag);

	INSERT INTO public.theme_alternative_names (theme_id, name)
	SELECT v_theme_id, r.alternative_name FROM unnest(new.alternative_names) AS r(alternative_name);

	new.id = v_theme_id;
	RETURN new;
END
$BODY$ LANGUAGE plpgsql VOLATILE;


CREATE OR REPLACE FUNCTION web.themes_trigger_row_iu() RETURNS trigger AS $BODY$
DECLARE
	v_theme public.themes;
BEGIN
	UPDATE public.themes SET name = new.name WHERE id = new.id RETURNING * INTO v_theme;
	UPDATE public."references" SET url = new.reference_url WHERE id = v_theme.reference_id;

	<<l_tags>>
	DECLARE
		v_current_tags integer[];
		v_new_tags integer[];
	BEGIN
		v_current_tags = array_agg(tag_id) FROM public.theme_tags WHERE theme_id = v_theme.id;
		v_new_tags = array_agg(r.tag::integer) FROM jsonb_array_elements(new.tags) AS r(tag);

		IF NOT array_equals(v_current_tags, v_new_tags) THEN
			DELETE FROM public.theme_tags WHERE theme_id = v_theme.id;
			INSERT INTO public.theme_tags (theme_id, tag_id)
			SELECT v_theme.id, r.tag FROM unnest(v_new_tags) AS r(tag);
		END IF;
	END l_tags;

	<<l_alternative_names>>
	DECLARE
		v_current_alternative_names character varying[];
	BEGIN
		v_current_alternative_names = array_agg(name) FROM public.theme_alternative_names WHERE theme_id = v_theme.id;

		IF NOT array_equals(v_current_alternative_names, new.alternative_names) THEN
			DELETE FROM public.theme_alternative_names WHERE theme_id = v_theme.id;
			INSERT INTO public.theme_alternative_names (theme_id, name)
			SELECT v_theme.id, r.alternative_name FROM unnest(new.alternative_names) AS r(alternative_name);
		END IF;
	END l_alternative_names;

	RETURN new;
END
$BODY$ LANGUAGE plpgsql VOLATILE;


CREATE OR REPLACE VIEW web.bulletpoints AS
	SELECT
		bulletpoints.id, bulletpoints.content, bulletpoints.theme_id, bulletpoints.user_id, bulletpoints.created_at,
		sources.link AS source_link,
		sources.type AS source_type,
		broken_sources.source_id IS NOT NULL AS source_is_broken,
			bulletpoint_rating_summary.up_points AS up_rating,
			bulletpoint_rating_summary.down_points AS down_rating,
			(bulletpoint_rating_summary.up_points + bulletpoint_rating_summary.down_points) AS total_rating,
		COALESCE(user_bulletpoint_ratings.user_rating, 0) AS user_rating,
		COALESCE(bulletpoint_referenced_themes.referenced_theme_id, ARRAY[]::integer[]) AS referenced_theme_id,
		COALESCE(bulletpoint_theme_comparisons.compared_theme_id, ARRAY[]::integer[]) AS compared_theme_id,
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
		SELECT bulletpoint_id, array_agg(public.bulletpoint_referenced_themes.theme_id) AS referenced_theme_id
		FROM public.bulletpoint_referenced_themes
		GROUP BY bulletpoint_id
	) AS bulletpoint_referenced_themes ON bulletpoint_referenced_themes.bulletpoint_id = bulletpoints.id
	LEFT JOIN (
		SELECT bulletpoint_id, array_agg(public.bulletpoint_theme_comparisons.theme_id) AS compared_theme_id
		FROM public.bulletpoint_theme_comparisons
		GROUP BY bulletpoint_id
	) AS bulletpoint_theme_comparisons ON bulletpoint_theme_comparisons.bulletpoint_id = bulletpoints.id
	LEFT JOIN bulletpoint_groups ON bulletpoint_groups.bulletpoint_id = bulletpoints.id
	ORDER BY total_rating DESC, bulletpoint_reputations.reputation DESC NULLS LAST, length(bulletpoints.content) ASC, created_at DESC, id DESC;


CREATE OR REPLACE FUNCTION web.bulletpoints_trigger_row_ii() RETURNS trigger AS $BODY$
DECLARE
	v_bulletpoint_id integer;
	v_source_id integer;
BEGIN
	IF number_of_references(new.content) != array_length(new.referenced_theme_id, 1) THEN
		RAISE EXCEPTION USING MESSAGE = format(
			'Number of referenced themes in text (%s) is not matching with passed ID list (%s).',
			number_of_references(new.content),
			array_length(new.referenced_theme_id, 1)
		 );
	END IF;

	INSERT INTO public.sources (link, type) VALUES (new.source_link, new.source_type) RETURNING id INTO v_source_id;

	IF TG_TABLE_NAME = 'contributed_bulletpoints' THEN
		INSERT INTO public.contributed_bulletpoints (theme_id, source_id, content, user_id) VALUES (
			new.theme_id,
			v_source_id,
			new.content,
			new.user_id
		)
		RETURNING id INTO v_bulletpoint_id;
	ELSIF TG_TABLE_NAME = 'bulletpoints' THEN
		INSERT INTO public.public_bulletpoints (theme_id, source_id, content, user_id) VALUES (
			new.theme_id,
			v_source_id,
			new.content,
			new.user_id
		)
		RETURNING id INTO v_bulletpoint_id;
	ELSE
		RAISE EXCEPTION USING MESSAGE = format('Trigger for table "%s" is not defined.', TG_TABLE_NAME);
	END IF;

	IF new.root_bulletpoint_id IS NOT NULL THEN
		INSERT INTO bulletpoint_groups (bulletpoint_id, root_bulletpoint_id) VALUES (v_bulletpoint_id, new.root_bulletpoint_id);
	END IF;

	INSERT INTO public.bulletpoint_referenced_themes (theme_id, bulletpoint_id)
	SELECT r.theme_id::integer, v_bulletpoint_id FROM unnest(new.referenced_theme_id) AS r(theme_id);

	INSERT INTO public.bulletpoint_theme_comparisons (theme_id, bulletpoint_id)
	SELECT r.theme_id::integer, v_bulletpoint_id FROM unnest(new.compared_theme_id) AS r(theme_id);

	RETURN new;
END
$BODY$ LANGUAGE plpgsql VOLATILE;


CREATE OR REPLACE FUNCTION web.bulletpoints_trigger_row_iu() RETURNS trigger AS $BODY$
DECLARE
	v_source_id integer;
BEGIN
	IF number_of_references(new.content) != array_length(new.referenced_theme_id, 1) THEN
		RAISE EXCEPTION USING MESSAGE = format(
			'Number of referenced themes in text (%s) is not matching with passed ID list (%s).',
			number_of_references(new.content),
			array_length(new.referenced_theme_id, 1)
		);
	END IF;

	IF TG_TABLE_NAME = 'contributed_bulletpoints' THEN
		UPDATE public.contributed_bulletpoints SET content = new.content
		WHERE id = new.id
		RETURNING source_id INTO v_source_id;
	ELSIF TG_TABLE_NAME = 'bulletpoints' THEN
		UPDATE public.public_bulletpoints SET content = new.content
		WHERE id = new.id
		RETURNING source_id INTO v_source_id;
	ELSE
		RAISE EXCEPTION USING MESSAGE = format('Trigger for table "%s" is not defined.', TG_TABLE_NAME);
	END IF;

	UPDATE public.sources
	SET link = new.source_link, type = new.source_type
	WHERE id = v_source_id;

	<<l_groups>>
	BEGIN
		IF old.root_bulletpoint_id IS DISTINCT FROM new.root_bulletpoint_id THEN
			IF new.root_bulletpoint_id IS NULL THEN
				DELETE FROM bulletpoint_groups
				WHERE root_bulletpoint_id = old.root_bulletpoint_id
				AND bulletpoint_id = new.id;
			ELSE
				INSERT INTO bulletpoint_groups (bulletpoint_id, root_bulletpoint_id) VALUES (new.id, new.root_bulletpoint_id)
				ON CONFLICT (bulletpoint_id) DO UPDATE SET root_bulletpoint_id = EXCLUDED.root_bulletpoint_id;
			END IF;
		END IF;
	END l_groups;


	<<l_referenced_themes>>
	DECLARE
		v_current_referenced_themes int[];
	BEGIN
		v_current_referenced_themes = array_agg(bulletpoint_id) FROM bulletpoint_referenced_themes WHERE id = new.id;

		IF NOT array_equals(v_current_referenced_themes, new.referenced_theme_id) THEN
			DELETE FROM public.bulletpoint_referenced_themes WHERE bulletpoint_id = new.id;
			INSERT INTO public.bulletpoint_referenced_themes (theme_id, bulletpoint_id)
			SELECT r.theme_id::integer, new.id FROM unnest(new.referenced_theme_id) AS r(theme_id);
		END IF;
	END l_referenced_themes;


	<<l_compared_themes>>
	DECLARE
		v_current_compared_themes int[];
	BEGIN
		v_current_compared_themes = array_agg(bulletpoint_id) FROM bulletpoint_theme_comparisons WHERE id = new.id;

		IF NOT array_equals(v_current_compared_themes, new.compared_theme_id) THEN
			DELETE FROM public.bulletpoint_theme_comparisons WHERE bulletpoint_id = new.id;
			INSERT INTO public.bulletpoint_theme_comparisons (theme_id, bulletpoint_id)
			SELECT r.theme_id::integer, new.id FROM unnest(new.compared_theme_id) AS r(theme_id);
		END IF;
	END l_compared_themes;

	RETURN new;
END
$BODY$ LANGUAGE plpgsql VOLATILE;

CREATE OR REPLACE VIEW web.contributed_bulletpoints AS
SELECT
	contributed_bulletpoints.id, contributed_bulletpoints.content, contributed_bulletpoints.theme_id, contributed_bulletpoints.user_id,
	sources.link AS source_link, sources.type AS source_type, broken_sources.source_id IS NOT NULL AS source_is_broken,
	COALESCE(bulletpoint_referenced_themes.referenced_theme_id, ARRAY[]::integer[]) AS referenced_theme_id,
	COALESCE(bulletpoint_theme_comparisons.compared_theme_id, ARRAY[]::integer[]) AS compared_theme_id,
	bulletpoint_groups.root_bulletpoint_id
	FROM public.contributed_bulletpoints
	LEFT JOIN public.sources ON sources.id = contributed_bulletpoints.source_id
	LEFT JOIN public.broken_sources ON broken_sources.source_id = sources.id
	LEFT JOIN (
		SELECT bulletpoint_id, array_agg(public.bulletpoint_referenced_themes.theme_id) AS referenced_theme_id
		FROM public.bulletpoint_referenced_themes
		GROUP BY bulletpoint_id
	) AS bulletpoint_referenced_themes ON bulletpoint_referenced_themes.bulletpoint_id = contributed_bulletpoints.id
	LEFT JOIN (
		SELECT bulletpoint_id, array_agg(public.bulletpoint_theme_comparisons.theme_id) AS compared_theme_id
		FROM public.bulletpoint_theme_comparisons
		GROUP BY bulletpoint_id
	) AS bulletpoint_theme_comparisons ON bulletpoint_theme_comparisons.bulletpoint_id = contributed_bulletpoints.id
	LEFT JOIN bulletpoint_groups ON bulletpoint_groups.bulletpoint_id = contributed_bulletpoints.id
	ORDER BY contributed_bulletpoints.created_at DESC, length(contributed_bulletpoints.content) ASC;