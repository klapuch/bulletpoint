<?php
namespace Bulletpoint\Fake;

use Bulletpoint\Model\{Access, Wiki};

final class Bulletpoint implements Wiki\Bulletpoint {
	private $id;

	public function __construct(int $id) {
		$this->id = $id;
	}

	public function author(): Access\Identity {

	}

	public function content(): string {

	}

	public function source(): Wiki\InformationSource {

	}

	public function id(): int {
		return $this->id;
	}

	public function date(): \Datetime {
		
	}

	public function document(): Wiki\Document {
		
	}

	public function edit(string $content) {
		
	}
}