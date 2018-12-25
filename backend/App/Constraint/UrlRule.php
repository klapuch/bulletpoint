<?php
declare(strict_types = 1);

namespace Bulletpoint\Constraint;

use Klapuch\Validation;
use Nette\Utils;

/**
 * Only valid URL
 */
final class UrlRule implements Validation\Rule {
	/**
	 * @param string $subject
	 * @return bool
	 */
	public function satisfied($subject): bool {
		return Utils\Validators::isUrl($subject);
	}

	/**
	 * @param string $subject
	 * @throws \UnexpectedValueException
	 * @return string
	 */
	public function apply($subject): string {
		if ($this->satisfied($subject))
			return $subject;
		throw new \UnexpectedValueException('Not valid URL');
	}
}
