<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain;

use Klapuch\Sql;
use Klapuch\Storage;

final class ThemeBulletpoints implements Bulletpoints {
	/** @var \Klapuch\Storage\Connection */
	private $connection;

	/** @var int */
	private $theme;

	public function __construct(int $theme, Storage\Connection $connection) {
		$this->theme = $theme;
		$this->connection = $connection;
	}

	public function all(): \Iterator {
		$bulletpoints = (new Storage\BuiltQuery(
			$this->connection,
			(new Sql\AnsiSelect([
				'id',
				'text',
				'theme_id',
				'source_link',
				'source_type',
				'rating',
				'user_id',
			]))->from(['public_bulletpoints'])
				->where('theme_id = :theme_id', ['theme_id' => $this->theme])
		))->rows();
		foreach ($bulletpoints as $bulletpoint) {
			yield new StoredBulletpoint(
				$bulletpoint['id'],
				new Storage\MemoryConnection($this->connection, $bulletpoint)
			);
		}
	}

	public function add(array $bulletpoint): void {
		(new Storage\BuiltQuery(
			$this->connection,
			(new Sql\PgInsertInto(
				'public_bulletpoints',
				[
					'text' => ':text',
					'theme_id' => ':theme_id',
					'source_type' => ':source_type',
					'source_link' => ':source_link',
					'user_id' => ':user_id',
				],
				[
					'text' => $bulletpoint['text'],
					'theme_id' => $this->theme,
					'source_link' => $bulletpoint['source']['link'],
					'source_type' => $bulletpoint['source']['type'],
					'user_id' => $bulletpoint['user_id'],
				]
			))
		))->execute();
	}

	public function count(): int {
		return (new Storage\BuiltQuery(
			$this->connection,
			(new Sql\AnsiSelect(['count(*)']))
				->from(['bulletpoints'])
				->where('theme_id = :theme_id', ['theme_id' => $this->theme])
		))->field();
	}
}
