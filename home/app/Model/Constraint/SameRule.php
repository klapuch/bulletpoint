<?php
namespace Bulletpoint\Model\Constraint;

use Bulletpoint\Exception;

final class SameRule implements Rule {
	private $message;
	private $toCheck;

	public function __construct(string $message, $toCheck) {
		$this->message = $message;
		$this->toCheck = $toCheck;
	}

	public function isSatisfied($input) {
		if($input !== $this->toCheck)
			throw new Exception\FormatException($this->message);
	}
}