<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain\Image;

interface Avatars {
	/**
	 * @throws \UnexpectedValueException
	 */
	public function save(): void;
}
