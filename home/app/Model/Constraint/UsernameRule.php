<?php
namespace Bulletpoint\Model\Constraint;

use Bulletpoint\Exception;

final class UsernameRule implements Rule {
	public function isSatisfied($input) {
		if(!(bool)preg_match('~^[a-z0-9]{3,30}\z~i', $input)) {
			throw new Exception\FormatException(
				'Přezdívka se smí skládat z písmen nebo číslic od 3 do 30 znaků'
			);
		}
	}
}