<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain;

use Klapuch\Output;
use Klapuch\Storage;

final class ExistingBulletpoint implements Bulletpoint {
	/** @var int */
	private $id;

	/** @var \Klapuch\Storage\Connection */
	private $connection;

	/** @var \Bulletpoint\Domain\Bulletpoint */
	private $origin;

	public function __construct(Bulletpoint $origin, int $id, Storage\Connection $connection) {
		$this->id = $id;
		$this->connection = $connection;
		$this->origin = $origin;
	}

	/**
	 * @param \Klapuch\Output\Format $format
	 * @throws \UnexpectedValueException
	 * @return \Klapuch\Output\Format
	 */
	public function print(Output\Format $format): Output\Format {
		if (!$this->exists($this->id))
			throw new \UnexpectedValueException(sprintf('Bulletpoint %d does not exist', $this->id));
		return $this->origin->print($format);
	}

	private function exists(int $id): bool {
		return (new Storage\TypedQuery(
			$this->connection,
			'SELECT EXISTS(SELECT 1 FROM bulletpoints WHERE id = :id)',
			['id' => $id]
		))->field();
	}
}