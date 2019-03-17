<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain\Access;

final class Guest implements User {
	public function id(): string {
		return '0';
	}

	public function properties(): array {
		return ['role' => 'guest'];
	}

	public function edit(array $properties): void {

	}
}
