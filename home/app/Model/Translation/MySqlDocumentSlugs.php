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

	public function add(int $origin, string $slug) {
		$webalizedSlug = $this->correction->replacement($slug);
		if($this->exists($slug)) {
			throw new Exception\DuplicateException(
				sprintf(
					'Slug "%s" již existuje',
					$webalizedSlug
				)
			);
		}
		$this->database->query(
			'INSERT INTO document_slugs (slug, origin) VALUES (?, ?)',
			[$webalizedSlug, $origin]
		);
	}

	private function exists(string $slug): bool {
		return (bool)$this->database->fetch(
			'SELECT 1 FROM document_slugs WHERE slug = ?',
			[$slug]
		);
	}
}