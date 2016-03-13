<?php
namespace Bulletpoint\Model\Translation;

use Bulletpoint\Core\{Storage, Text};
use Bulletpoint\Exception;

final class MySqlDocumentSlugs implements Slugs {
	private $database;
	private $correction;

	public function __construct(
		Storage\Database $database,
		Text\Correction $correction
	) {
		$this->database = $database;
		$this->correction = $correction;
	}

	public function add(int $origin, string $plain): Slug {
		$slug = $this->correction->replacement($plain);
		if($this->isDuplicate($slug)) {
			throw new Exception\DuplicateException(
				sprintf(
					'Slug "%s" již existuje',
					$slug
				)
			);
		}
		$this->database->query(
			'INSERT INTO document_slugs (slug, origin) VALUES (?, ?)',
			[$slug, $origin]
		);
		return new MySqlDocumentSlug(
			$this->database->fetchColumn('SELECT LAST_INSERT_ID()'),
			$this->database
		);
	}

	private function isDuplicate(string $slug): bool {
		return (bool)$this->database->fetch(
			'SELECT 1 FROM document_slugs WHERE slug = ?',
			[$slug]
		);
	}
}