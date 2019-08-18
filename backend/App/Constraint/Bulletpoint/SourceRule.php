<?php
declare(strict_types = 1);

namespace Bulletpoint\Constraint\Bulletpoint;

use Bulletpoint\Constraint;
use Klapuch\Validation;

/**
 * Rule for source
 */
final class SourceRule implements Validation\Rule {
	/**
	 * @param mixed $subject
	 * @return bool
	 */
	public function satisfied($subject): bool {
		if ($subject['source']['type'] === 'web')
			return (new Constraint\UrlRule())->satisfied($subject['source']['link']);
		return $subject['source']['link'] === null;
	}

	/**
	 * @param mixed $subject
	 * @throws \UnexpectedValueException
	 * @return mixed[]
	 */
	public function apply($subject): array {
		if ($subject['source']['type'] === 'web') {
			return ['link' => self::web($subject)];
		} elseif ($subject['source']['type'] === 'head' && $subject['source']['link'] !== null) {
			throw new \UnexpectedValueException('Link to head source must be empty');
		}
		return ['link' => $subject];
	}

	private static function web(array $subject): string
	{
		return (new Validation\FriendlyRule(
			new Constraint\UrlRule(),
			t('bulletpoint.source.link.not.valid'),
		))->apply($subject['source']['link']);
	}
}
