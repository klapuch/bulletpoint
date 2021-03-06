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

	/**
	 * @param mixed[] $theme
	 */
	public function change(array $theme): void;

	/**
	 * @throws \UnexpectedValueException
	 */
	public function star(): void;

	/**
	 * @throws \UnexpectedValueException
	 */
	public function unstar(): void;
}
