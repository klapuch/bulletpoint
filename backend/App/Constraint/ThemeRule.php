<?php
declare(strict_types = 1);

namespace Bulletpoint\Constraint;

use Klapuch\Validation;

/**
 * Rule for theme
 */
final class ThemeRule implements Validation\Rule {
	/**
	 * @param mixed $subject
	 * @return bool
	 */
	public function satisfied($subject): bool {
		return false; // not used
	}

	/**
	 * @param mixed $subject
	 * @throws \UnexpectedValueException
	 * @return array
	 */
	public function apply($subject): array {
		return array_replace_recursive(
			[
				'reference' => [
					'url' => (new Validation\FriendlyRule(
						new UrlRule(),
						t('theme.reference.url.not.valid')
					))->apply($subject['reference']['url']),
				],
			],
			$subject
		);
	}
}
