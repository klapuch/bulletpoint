<?php
namespace Bulletpoint\Model\Constraint;

use Bulletpoint\Exception;

final class YearRule implements Rule {
	public function isSatisfied($input) {
		if(!strlen($input))
			return;
		if(!$this->isNumber($input) || $input > date('Y') || $input < 1) {
			throw new Exception\FormatException(
				'Rok musí být celé číslo menší než rok aktuální'
			);
		}
	}

	private function isNumber($input): bool {
		return ctype_digit(strval($input));
	}
}