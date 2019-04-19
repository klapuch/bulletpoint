<?php
declare(strict_types = 1);

namespace Bulletpoint\Constraint;

use Klapuch\Storage;
use Klapuch\Validation;

/**
 * Rule for bulletpoint
 */
final class BulletpointRule implements Validation\Rule {
	/** @var \Klapuch\Storage\Connection */
	private $connection;

	public function __construct(Storage\Connection $connection) {
		$this->connection = $connection;
	}

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
		return (array) array_replace_recursive(
			[
				'source' => [
					'link' => $subject['source']['type'] === 'web'
						? (new Validation\FriendlyRule(
							new UrlRule(),
							t('bulletpoint.source.link.not.valid'),
						))->apply($subject['source']['link'])
						: $subject['source']['link'],
				],
				'content' => (new Validation\FriendlyRule(
					new TextReferenceRule($this->connection, count($subject['referenced_theme_id'])),
					'Number of references in text do not match with count of referenced_theme_id',
				))->apply($subject['content']),
			],
			$subject,
		);
	}
}
