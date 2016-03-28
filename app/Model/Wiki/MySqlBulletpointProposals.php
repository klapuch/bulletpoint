<?php
namespace Bulletpoint\Model\Wiki;

use Bulletpoint\Model\{
    Storage, Access
};
use Bulletpoint\Exception;

final class MySqlBulletpointProposals implements BulletpointProposals {
    private $myself;
    private $database;

    public function __construct(
        Access\Identity $myself,
        Storage\Database $database
    ) {
        $this->myself = $myself;
        $this->database = $database;
    }

    public function iterate(): \Iterator {
        $rows = $this->database->fetchAll(
            'SELECT bulletpoint_proposals.ID AS proposal_id,
			content,
			bulletpoint_proposals.author AS proposal_author,
			bulletpoint_proposals.proposed_at,
			bulletpoint_proposals.document_id,
			users.role,
			users.username,
			information_sources.ID AS source_id,
			information_sources.place,
			information_sources.`year`,
			information_sources.author
			FROM bulletpoint_proposals
			INNER JOIN users
			ON users.ID = bulletpoint_proposals.author
			INNER JOIN information_sources
			ON information_sources.ID = bulletpoint_proposals.information_source_id
			WHERE decision = "0"
			ORDER BY proposed_at DESC'
        );
        foreach($rows as $row) {
            yield new ConstantBulletpointProposal(
                new Access\ConstantIdentity(
                    $row['proposal_author'],
                    new Access\ConstantRole(
                        $row['role'],
                        new Access\MySqlRole(
                            $row['proposal_author'],
                            $this->database
                        )
                    ),
                    $row['username']
                ),
                new \DateTime($row['proposed_at']),
                new ConstantInformationSource(
                    $row['place'],
                    $row['year'],
                    $row['author'],
                    new MySqlInformationSource(
                        $row['source_id'],
                        $this->database
                    )
                ),
                $row['content'],
                new MySqlDocument($row['document_id'], $this->database),
                new MySqlBulletpointProposal(
                    $row['proposal_id'],
                    $this->myself,
                    $this->database
                )
            );
        }
    }

    public function propose(
        Document $document,
        string $content,
        InformationSource $source
    ) {
        if($this->isDuplicate($document, $content))
            throw new Exception\DuplicateException('Bulletpoint již existuje');
        $this->database->query(
            'INSERT INTO bulletpoint_proposals
			(document_id, content, author, information_source_id)
			VALUES (?, ?, ?, ?)',
            [
                $document->id(),
                $content,
                $this->myself->id(),
                $source->id(),
            ]
        );
    }

    private function isDuplicate(Document $document, string $content) {
        return (bool)$this->database->fetch(
            'SELECT 1 FROM bulletpoints WHERE document_id = ? AND content = ?',
            [$document->id(), $content]
        );
    }
}