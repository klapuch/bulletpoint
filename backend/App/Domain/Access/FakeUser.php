<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain\Access;

final class FakeUser implements User {
	/** @var string|null */
	private $id;

	/** @var mixed[]|null */
	private $properties;

	public function __construct(?string $id = null, ?array $properties = null) {
		$this->id = $id;
		$this->properties = $properties;
	}

	public function id(): string {
		return $this->id;
	}

	public function properties(): array {
		return $this->properties;
	}

	public function edit(array $properties): void {
	}
}
