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

	/** @var \Bulletpoint\Domain\Access\User */
	private $user;

	public function __construct(int $theme, Storage\Connection $connection, Access\User $user) {
		$this->theme = $theme;
		$this->connection = $connection;
		$this->user = $user;
	}

	public function all(): \Iterator {
		$bulletpoints = (new Storage\BuiltQuery(
			$this->connection,
			(new Sql\AnsiSelect([
				'id',
				'content',
				'theme_id',
				'source_link',
				'source_type',
				'total_rating',
				'up_rating',
				'down_rating',
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
					'content' => ':content',
					'theme_id' => ':theme_id',
					'source_type' => ':source_type',
					'source_link' => ':source_link',
					'user_id' => ':user_id',
				],
				[
					'content' => $bulletpoint['content'],
					'theme_id' => $this->theme,
					'source_link' => $bulletpoint['source']['link'],
					'source_type' => $bulletpoint['source']['type'],
					'user_id' => $this->user->id(),
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
