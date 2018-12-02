<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain;

interface User {
	public function id(): int;
	public function properties(): array;
}
