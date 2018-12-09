<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain;

interface Themes {
	public function create(array $theme): int;
}
