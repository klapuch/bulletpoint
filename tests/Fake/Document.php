<?php
namespace Bulletpoint\Fake;

use Bulletpoint\Model\{Access, Wiki};

final class Document implements Wiki\Document {
	private $id;

	public function __construct(int $id) {
		$this->id = $id;
	}

	public function author(): Access\Identity {

	}

	public function description(): string {

	}

	public function title(): string {

	}

	public function source(): Wiki\InformationSource {

	}

	public function id(): int {
		return $this->id;
	}

	public function date(): \Datetime {
		
	}
	public function edit(string $title, string $description) {
		
	}
}