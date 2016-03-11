<?php
namespace Bulletpoint\Model\Constraint;

use Bulletpoint\Exception;

final class TitleRule implements Rule {
	public function isSatisfied($input) {
		if($this->isNumber($input))
			throw new Exception\FormatException('Titulek musí být alfabetický');
	}

	private function isNumber($input): bool {
		return ctype_digit(strval($input));
	}
}