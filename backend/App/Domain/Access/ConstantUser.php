<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain\Access;

final class ConstantUser implements User {
	private const SENSITIVE_COLUMNS = ['id', 'password'];

	/** @var int */
	private $id;

	/** @var mixed[] */
	private $properties;

	public function __construct(int $id, array $properties) {
		$this->id = $id;
		$this->properties = $properties;
	}

	public function id(): int {
		return $this->id;
	}

	public function properties(): array {
		return array_diff_ukey(
			$this->properties,
			array_flip(self::SENSITIVE_COLUMNS),
			'strcasecmp'
		);
	}
}
