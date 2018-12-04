<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain;

final class FakeUser implements User {
	/** @var int|null */
	private $id;

	/** @var mixed[]|null */
	private $properties;

	public function __construct(?int $id = null, ?array $properties = null) {
		$this->id = $id;
		$this->properties = $properties;
	}

	public function id(): int {
		return $this->id;
	}

	public function properties(): array {
		return $this->properties;
	}
}
