<?php
declare(strict_types = 1);

namespace Bulletpoint\Constraint;

use Klapuch\Validation;

/**
 * Rule for bulletpoint
 */
final class BulletpointRule implements Validation\Rule {
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
				'source' => [
					'link' => $subject['source']['type'] === 'web'
						? (new Validation\FriendlyRule(
							new UrlRule(),
							t('bulletpoint.source.link.not.valid'),
						))->apply($subject['source']['link'])
						: $subject['source']['link'],
				],
			],
			$subject,
		);
	}
}
