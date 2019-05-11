<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain;

use Klapuch\Dataset;

final class FakeThemes implements Themes {
	public function create(array $theme): int {
		return -1;
	}

	public function all(Dataset\Selection $selection): \Iterator {
		return new \ArrayIterator([]);
	}

	public function count(Dataset\Selection $selection): int {
		return -1;
	}
}
