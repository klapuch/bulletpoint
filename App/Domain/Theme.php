<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain;

use Klapuch\Output;

interface Theme {
	/**
	 * @param \Klapuch\Output\Format $format
	 * @throws \UnexpectedValueException
	 * @return \Klapuch\Output\Format
	 */
	public function print(Output\Format $format): Output\Format;
}
