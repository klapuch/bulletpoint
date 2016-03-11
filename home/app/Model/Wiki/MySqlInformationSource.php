<?php
namespace Bulletpoint\Model\Wiki;

use Bulletpoint\Core\Storage;

final class MySqlInformationSource implements InformationSource {
	private $id;
	private $database;

	public function __construct(int $id, Storage\Database $database) {
		$this->id = $id;
		$this->database = $database;
	}

	public function id(): int {
		return $this->id;
	}

	public function place(): string {
		return $this->database->fetchColumn(
			'SELECT place FROM information_sources WHERE ID = ?',
			[$this->id]
		);
	}

	public function year() {
		return $this->database->fetchColumn(
			'SELECT `year` FROM information_sources WHERE ID = ?',
			[$this->id]
		);
	}

	public function author(): string {
		return $this->database->fetchColumn(
			'SELECT author FROM information_sources WHERE ID = ?',
			[$this->id]
		);
	}

	public function edit(string $place, $year, string $author) {
		$this->database->query(
			'UPDATE information_sources
			SET place = ?, `year` = ?, author = ?
			WHERE ID = ?',
			[$place, strlen($year) ? $year : null, $author, $this->id]
		);
	}
}