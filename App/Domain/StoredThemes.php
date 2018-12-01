<?php declare(strict_types = 1);

namespace Bulletpoint\Domain;

use Klapuch\Output;
use Klapuch\Sql;
use Klapuch\Storage;

final class StoredThemes implements Themes {
	/** @var Storage\Connection */
	private $connection;

	public function __construct(Storage\Connection $connection) {
		$this->connection = $connection;
	}

	public function print(Output\Format $format): Output\Format {
		$row = (new Storage\BuiltQuery(
			$this->connection,
			(new Sql\AnsiSelect([
				'id',
				'name',
				'tags',
				'reference_url',
				'reference_name',
			]))->from(['public_themes'])
			->where('id = :id', ['id' => $this->id])
		))->row();
		return new Output\FilledFormat($format, $row);
	}
}