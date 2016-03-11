<?php
namespace Bulletpoint\Model\Wiki;

use Bulletpoint\Core\Storage;

final class MySqlInformationSources implements InformationSources {
	private $database;

	public function __construct(Storage\Database $database) {
		$this->database = $database;
	}

	public function create(
		string $place,
		$year,
		string $author
	): InformationSource {
		$this->database->query(
			'INSERT INTO information_sources
			(place, `year`, author)
			VALUES(?, ?, ?)',
			[$place, $year ?: null, $author]
		);
		return new ConstantInformationSource(
			$place,
			$year ?: null,
			$author,
			new MySqlInformationSource(
				$this->database->fetchColumn('SELECT LAST_INSERT_ID()'),
				$this->database
			)
		);
	}
}