<?php
declare(strict_types = 1);

namespace Bulletpoint\Constraint\Bulletpoint;

use Klapuch\Storage;
use Klapuch\Validation;

/**
 * Number of references in text matching with passed ID
 */
final class ReferenceRule implements Validation\Rule {
	/** @var \Klapuch\Storage\Connection */
	private $connection;

	/** @var int */
	private $count;

	public function __construct(Storage\Connection $connection, int $count) {
		$this->connection = $connection;
		$this->count = $count;
	}

	/**
	 * @param mixed $subject
	 * @return bool
	 */
	public function satisfied($subject): bool {
		return (new Storage\TypedQuery(
			$this->connection,
			'SELECT number_of_references(?)',
			[$subject],
		))->field() === $this->count;
	}

	/**
	 * @param mixed $subject
	 * @throws \UnexpectedValueException
	 * @return string
	 */
	public function apply($subject): string {
		if ($this->satisfied($subject)) {
			return $subject;
		}
		throw new \UnexpectedValueException('Number of references is not matching');
	}
}
