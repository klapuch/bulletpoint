<?php
namespace Bulletpoint\Model\Constraint;

final class ChainRule implements Rule {
	private $rules;

	public function __construct(Rule ...$rules) {
		$this->rules = $rules;
	}

	public function isSatisfied($input) {
		foreach($this->rules as $rule) 
			$rule->isSatisfied($input);
	}
}