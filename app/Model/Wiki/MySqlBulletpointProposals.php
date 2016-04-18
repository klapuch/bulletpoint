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
			bulletpoint_proposals.content,
			bulletpoint_proposals.author AS proposal_author,
			bulletpoint_proposals.proposed_at,
			bulletpoint_proposals.document_id,
			documents.title,
			documents.description,
			documents.information_source_id AS document_source_id,
			documents.user_id AS document_author,
			documents.created_at AS document_date,
			bulletpoint_proposals.information_source_id
			FROM bulletpoint_proposals
			LEFT JOIN documents
			ON bulletpoint_proposals.document_id = documents.ID
			WHERE decision = "0"
			ORDER BY proposed_at DESC'
        );
        foreach($rows as $row) {
            yield new ConstantBulletpointProposal(
                new Access\MySqlIdentity(
                    $row['proposal_author'],
                    $this->database
                ),
                new \DateTimeImmutable($row['proposed_at']),
                new MySqlInformationSource(
                    $row['information_source_id'], $this->database
                ),
                $row['content'],
                new MySqlBulletpointProposal(
                    $row['proposal_id'],
                    $this->myself,
                    $this->database
                ),
                new ConstantDocument(
                    $row['title'],
                    $row['description'],
                    new Access\MySqlIdentity(
                        $row['document_author'],
                        $this->database
                    ),
                    new \DateTimeImmutable($row['document_date']),
                    new MySqlInformationSource(
                        $row['document_source_id'],
                        $this->database
                    ),
                    new MySqlDocument($row['document_id'], $this->database)
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