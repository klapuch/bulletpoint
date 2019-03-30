<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain\User;

use Klapuch\Dataset;

interface Tags {
	public function all(Dataset\Selection $selection): \Iterator;
}
