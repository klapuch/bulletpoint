<?php
declare(strict_types = 1);

namespace Bulletpoint\Constraint\Bulletpoint;

use Klapuch\Storage;
use Klapuch\Validation;

/**
 * Rule for bulletpoint
 */
final class Rule implements Validation\Rule {
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
		return array_replace_recursive(
			$subject,
			[
				'source' => (new SourceRule())->apply($subject),
				'content' => (new Validation\FriendlyRule(
					new ReferenceRule($this->connection, count($subject['referenced_theme_id'])),
					'Number of references in text do not match with count of referenced_theme_id',
				))->apply($subject['content']),
			],
		);
	}
}
