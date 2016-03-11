<?php
namespace Bulletpoint\Fake;

use Bulletpoint\Model\{Wiki};

final class InformationSource implements Wiki\InformationSource {
	private $id;
	private $place;
	private $year;
	private $author;

	public function __construct(
		int $id = null,
		string $place = null,
		$year = null,
		string $author = null
	) {
		$this->id = $id;
		$this->place = $place;
		$this->year = $year;
		$this->author = $author;
	}

	public function id(): int {
		return $this->id;
	}
	public function place(): string {
		return $this->place;
	}
	/**
	* @return int|null
	*/
	public function year() {
		return $this->year;
	}
	public function author(): string {
		return $this->author;
	}
	public function edit(string $place, $year, string $author) {

	}
}