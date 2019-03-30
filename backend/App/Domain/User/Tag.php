<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain\User;

use Klapuch\Output;

interface Tag {
	public function print(Output\Format $format): Output\Format;
}
