<?php
declare(strict_types = 1);

namespace Bulletpoint\Constraint;

use Klapuch\Validation;

/**
 * Only non-mobile URL
 */
final class NonMobileUrlRule implements Validation\Rule {
	/**
	 * @param string|mixed $subject
	 * @return bool
	 */
	public function satisfied($subject): bool {
		return true;
	}

	/**
	 * @param string|mixed $subject
	 * @return string
	 */
	public function apply($subject): string {
		if (preg_match('~^https://(\w+)\.m\.wikipedia\.org~i', $subject, $match) === 1) {
			return str_ireplace(
				sprintf('https://%s.m.wikipedia.org', $match[1]),
				sprintf('https://%s.wikipedia.org', $match[1]),
				$subject,
			);
		}
		return $subject;
	}
}
