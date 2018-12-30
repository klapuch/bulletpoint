<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain\Contribution;

use Bulletpoint\Domain;
use Bulletpoint\Domain\Access;
use Klapuch\Output;
use Klapuch\Storage;

final class ExistingBulletpoint implements Domain\Bulletpoint {
	/** @var int */
	private $id;

	/** @var \Bulletpoint\Domain\Access\User */
	private $user;

	/** @var \Klapuch\Storage\Connection */
	private $connection;

	/** @var \Bulletpoint\Domain\Bulletpoint */
	private $origin;

	public function __construct(Domain\Bulletpoint $origin, Access\User $user, int $id, Storage\Connection $connection) {
		$this->id = $id;
		$this->user = $user;
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

	public function edit(array $bulletpoint): void {
		if (!$this->exists($this->id))
			throw new \UnexpectedValueException(sprintf('Bulletpoint %d does not exist', $this->id));
		$this->origin->edit($bulletpoint);
	}

	private function exists(int $id): bool {
		return (new Storage\TypedQuery(
			$this->connection,
			'SELECT EXISTS(SELECT 1 FROM contributed_bulletpoints WHERE id = :id AND user_id = :user_id)',
			['id' => $id, 'user_id' => $this->user->id()]
		))->field();
	}

	public function delete(): void {
		if (!$this->exists($this->id))
			throw new \UnexpectedValueException(sprintf('Bulletpoint %d does not exist', $this->id));
		$this->origin->delete();
	}
}