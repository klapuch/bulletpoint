<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain\Contribution;

use Bulletpoint\Domain;
use Bulletpoint\Domain\Access;
use Klapuch\Output;
use Klapuch\Sql\Expression;
use Klapuch\Sql\Statement\Select;
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

	public function delete(): void {
		if (!$this->exists($this->id))
			throw new \UnexpectedValueException(sprintf('Bulletpoint %d does not exist', $this->id));
		$this->origin->delete();
	}

	public function rate(int $point): void {
		if (!$this->exists($this->id))
			throw new \UnexpectedValueException(sprintf('Bulletpoint %d does not exist', $this->id));
		$this->origin->rate($point);
	}

	private function exists(int $id): bool {
		return (new Storage\BuiltQuery(
			$this->connection,
			(new Select\Query())
				->from(new Expression\From(['contributed_bulletpoints']))
				->where(new Expression\Where('id', $id))
				->where(new Expression\Where('user_id', $this->user->id()))
				->exists(),
		))->field();
	}
}
