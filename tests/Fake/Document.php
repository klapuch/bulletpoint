<?php
namespace Bulletpoint\Fake;

use Bulletpoint\Model\{Access, Wiki};

final class Document implements Wiki\Document {
	private $id;
	private $description;

	public function __construct(int $id, string $description = '') {
		$this->id = $id;
		$this->description = $description;
	}

	public function author(): Access\Identity {
        return new Identity();
	}

	public function description(): string {
        return $this->description;
	}

	public function title(): string {
        return 'title method';
	}

	public function source(): Wiki\InformationSource {
        return new InformationSource();
	}

	public function id(): int {
		return $this->id;
	}

	public function date(): \DateTimeImmutable {
		return new \DateTimeImmutable();
	}

	public function edit(string $title, string $description) {
	}
}