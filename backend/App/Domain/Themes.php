<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain;

use Klapuch\Dataset;

interface Themes {
	public function create(array $theme): int;

	/**
	 * @param \Klapuch\Dataset\Selection $selection
	 * @return \Bulletpoint\Domain\Theme[]|\Iterator
	 */
	public function all(Dataset\Selection $selection): \Iterator;

	public function count(Dataset\Selection $selection): int;
}
