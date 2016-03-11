<?php
namespace Bulletpoint\Model\Constraint;

use Bulletpoint\Exception;

final class FillRule implements Rule {
	private $message;

	public function __construct(string $message) {
		$this->message = $message;
	}

	public function isSatisfied($input) {
		if(strlen(trim($input)) === 0)
			throw new Exception\FormatException($this->message);
	}
}