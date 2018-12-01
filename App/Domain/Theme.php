<?php declare(strict_types = 1);

namespace Bulletpoint\Domain;

use Klapuch\Output;

interface Theme {
	public function print(Output\Format $format): Output\Format;
}