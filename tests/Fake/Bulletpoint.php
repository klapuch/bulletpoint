<?php
namespace Bulletpoint\Fake;

use Bulletpoint\Model\{Access, Wiki};

final class Bulletpoint implements Wiki\Bulletpoint {
	private $id;
	private $document;

	public function __construct(int $id = null, Wiki\Document $document = null) {
		$this->id = $id;
		$this->document = $document;
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

	public function date(): \DateTime {
		
	}

	public function document(): Wiki\Document {
        return $this->document;
	}

	public function edit(string $content) {
		
	}
}