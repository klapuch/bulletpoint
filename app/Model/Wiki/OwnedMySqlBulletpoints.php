<?php
namespace Bulletpoint\Model\Wiki;

use Bulletpoint\Model\{
    Storage, Access
};

final class OwnedMySqlBulletpoints implements Bulletpoints {
    private $owner;
    private $database;
    private $origin;

    public function __construct(
        Access\Identity $owner,
        Storage\Database $database,
        Bulletpoints $origin
    ) {
        $this->database = $database;
        $this->owner = $owner;
        $this->origin = $origin;
    }

    public function iterate(): \Iterator {
        $rows = $this->database->fetchAll(
            'SELECT ID, user_id, created_at, content, information_source_id
			FROM bulletpoints
			WHERE user_id = ?
			ORDER BY bulletpoints.created_at DESC',
            [$this->owner->id()]
        );
        foreach($rows as $row) {
            yield new ConstantBulletpoint(
                $this->owner,
                $row['content'],
                new \DateTime($row['created_at']),
                    new MySqlInformationSource(
                        $row['information_source_id'],
                        $this->database
                    ),
                new MySqlBulletpoint($row['ID'], $this->database)
            );
        }
    }

    public function add(string $content, InformationSource $source) {
        $this->origin->add($content, $source);
    }
}