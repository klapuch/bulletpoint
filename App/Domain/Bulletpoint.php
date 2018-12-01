<?php declare(strict_types = 1);

namespace Bulletpoint\Domain;

use Klapuch\Output;

interface Bulletpoint {
	public function print(Output\Format $format): Output\Format;
}