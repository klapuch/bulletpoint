<?php
namespace Bulletpoint\Model\Constraint;

use Bulletpoint\Exception;

final class EmailRule implements Rule {
	public function isSatisfied($input) {
		if(!(bool)filter_var($input, FILTER_VALIDATE_EMAIL))
			throw new Exception\FormatException('Email je neplatný');
	}
}