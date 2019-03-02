<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain;

use Klapuch\Storage;

final class UniqueTags implements Tags {
	/** @var \Bulletpoint\Domain\Tags */
	private $origin;

	/** @var \Klapuch\Storage\Connection */
	private $connection;

	public function __construct(Tags $origin, Storage\Connection $connection) {
		$this->origin = $origin;
		$this->connection = $connection;
	}

	public function all(): array {
		return $this->origin->all();
	}

	/**
	 * @throws \UnexpectedValueException
	 * @param string $name
	 */
	public function add(string $name): void {
		if ($this->exists($name)) {
			throw new \UnexpectedValueException(t('tag.already.exists', $name));
		}
		$this->origin->add($name);
	}

	private function exists(string $name): bool {
		return (new Storage\TypedQuery(
			$this->connection,
			'SELECT EXISTS(SELECT 1 FROM tags WHERE name = :name)',
			['name' => $name],
		))->field();
	}
}
