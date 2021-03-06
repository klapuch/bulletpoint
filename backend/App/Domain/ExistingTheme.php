<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain;

use Klapuch\Output;
use Klapuch\Sql\Expression;
use Klapuch\Sql\Statement\Select;
use Klapuch\Storage;

final class ExistingTheme implements Theme {
	private int $id;
	private Storage\Connection $connection;
	private Theme $origin;

	public function __construct(Theme $origin, int $id, Storage\Connection $connection) {
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
			throw new \UnexpectedValueException(sprintf('Theme %d does not exist', $this->id));
		return $this->origin->print($format);
	}

	/**
	 * @throws \UnexpectedValueException
	 * @param mixed[] $theme
	 */
	public function change(array $theme): void {
		if (!$this->exists($this->id))
			throw new \UnexpectedValueException(sprintf('Theme %d does not exist', $this->id));
		$this->origin->change($theme);
	}

	public function star(): void {
		if (!$this->exists($this->id))
			throw new \UnexpectedValueException(sprintf('Theme %d does not exist', $this->id));
		$this->origin->star();
	}

	public function unstar(): void {
		if (!$this->exists($this->id))
			throw new \UnexpectedValueException(sprintf('Theme %d does not exist', $this->id));
		$this->origin->unstar();
	}

	private function exists(int $id): bool {
		return (new Storage\BuiltQuery(
			$this->connection,
			(new Select\Query())
				->from(new Expression\From(['themes']))
				->where(new Expression\Where('id', $id))
				->exists(),
		))->field();
	}
}
