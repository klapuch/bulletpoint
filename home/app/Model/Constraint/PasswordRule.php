<?php
namespace Bulletpoint\Model\Constraint;

use Bulletpoint\Exception;

final class PasswordRule implements Rule {
	const MINIMUM = 6;

	public function isSatisfied($input) {
		if (mb_strlen($input) < self::MINIMUM) {
			throw new Exception\FormatException(
				sprintf(
					'Heslo musí mít aspoň %d znaků',
					self::MINIMUM
				)
			);
		}
	}
}