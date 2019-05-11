INSERT INTO deploy.migrations(filename) VALUES('migrations/2019/master-groups_not_remove--05-11.sql');

CREATE OR REPLACE FUNCTION refresh_bulletpoint_group_successors() RETURNS void AS $BODY$
BEGIN
	WITH deleted_groups AS (
		DELETE FROM bulletpoint_groups
		RETURNING *
	)
	INSERT INTO bulletpoint_groups (bulletpoint_id, root_bulletpoint_id)
	SELECT new_groups.bulletpoint_id, new_groups.root_bulletpoint_id FROM (
		SELECT array_agg(bulletpoint_id) AS bulletpoint_id, root_bulletpoint_id
		FROM deleted_groups
		GROUP BY root_bulletpoint_id
	) grouped
	JOIN LATERAL (
		SELECT id AS bulletpoint_id, first_value(id) OVER () AS root_bulletpoint_id
		FROM web.bulletpoints
		WHERE id = ANY(grouped.bulletpoint_id || grouped.root_bulletpoint_id)
	) new_groups ON TRUE
	WHERE new_groups.bulletpoint_id != new_groups.root_bulletpoint_id;
END;
$BODY$ LANGUAGE plpgsql VOLATILE;
