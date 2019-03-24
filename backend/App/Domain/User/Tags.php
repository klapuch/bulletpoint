<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain\User;

interface Tags {
	public function all(): array;
}
