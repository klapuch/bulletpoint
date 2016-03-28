<?php
namespace Bulletpoint\Model\Wiki;

use Bulletpoint\Model\{
    Storage, Access
};

abstract class Bulletpoints {
    protected $database;

    public function __construct(Storage\Database $database) {
        $this->database = $database;
    }

    final protected function iterateBy(
        string $where,
        array $parameters
    ): \Iterator {
        $rows = $this->database->fetchAll(
            "SELECT users.ID, users.role, users.username,
			information_sources.ID AS source_id,
			information_sources.place,
			information_sources.`year`,
			information_sources.author,
			bulletpoints.ID AS bulletpoint_id,
			bulletpoints.created_at,
			bulletpoints.content
			FROM bulletpoints
			INNER JOIN information_sources
			ON information_sources.ID = bulletpoints.information_source_id
			INNER JOIN users
			ON users.ID = bulletpoints.user_id
			WHERE $where
			ORDER BY bulletpoints.created_at DESC, bulletpoints.document_id",
            $parameters
        );
        foreach($rows as $row) {
            yield new ConstantBulletpoint(
                new Access\ConstantIdentity(
                    $row['ID'],
                    new Access\ConstantRole(
                        $row['role'],
                        new Access\MySqlRole($row['ID'], $this->database)
                    ),
                    $row['username']
                ),
                $row['content'],
                new \DateTime($row['created_at']),
                new ConstantInformationSource(
                    $row['place'],
                    $row['year'],
                    $row['author'],
                    new MySqlInformationSource(
                        $row['source_id'],
                        $this->database
                    )
                ),
                new MySqlBulletpoint($row['bulletpoint_id'], $this->database)
            );
        }
    }

    public abstract function iterate(): \Iterator;
    public abstract function add(string $content, InformationSource $source);
}