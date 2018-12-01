<?php declare(strict_types = 1);

namespace Bulletpoint\Domain;

use Klapuch\Output;
use Klapuch\Storage;

final class ExistingTheme implements Theme {
	/** @var int */
	private $id;

	/** @var Storage\Connection */
	private $connection;

	/** @var Theme */
	private $origin;

	public function __construct(Theme $origin, int $id, Storage\Connection $connection) {
		$this->id = $id;
		$this->connection = $connection;
		$this->origin = $origin;
	}

	/**
	 * @param Output\Format $format
	 * @throws \UnexpectedValueException
	 * @return Output\Format
	 */
	public function print(Output\Format $format): Output\Format {
		if (!$this->exists($this->id))
			throw new \UnexpectedValueException(sprintf('Theme %d does not exist', $this->id));
		return $this->origin->print($format);
	}

	private function exists(int $id): bool {
		return (new Storage\TypedQuery(
			$this->connection,
			'SELECT EXISTS(SELECT 1 FROM themes WHERE id = :id)',
			['id' => $id]
		))->field();
	}
}