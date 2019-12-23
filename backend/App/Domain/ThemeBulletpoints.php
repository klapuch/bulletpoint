<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain;

use Klapuch\Sql\Clause;
use Klapuch\Sql\Expression;
use Klapuch\Sql\Statement\Insert;
use Klapuch\Sql\Statement\Select;
use Klapuch\Storage;

final class ThemeBulletpoints implements Bulletpoints {
	private Storage\Connection $connection;

	private int $theme;

	private Access\User $user;

	public function __construct(int $theme, Storage\Connection $connection, Access\User $user) {
		$this->theme = $theme;
		$this->connection = $connection;
		$this->user = $user;
	}

	public function all(): \Iterator {
		$bulletpoints = (new Storage\BuiltQuery(
			$this->connection,
			(new Select\Query())
				->select(new Expression\Select([
					'id',
					'content',
					'theme_id',
					'referenced_theme_id',
					'compared_theme_id',
					'source_link',
					'source_type',
					'source_is_broken',
					'total_rating',
					'up_rating',
					'down_rating',
					'user_rating',
					'root_bulletpoint_id',
					'user_id',
					'created_at',
				]))->from(new Expression\From(['web.bulletpoints']))
				->where(new Expression\Where('theme_id', $this->theme)),
		))->rows();
		foreach ($bulletpoints as $bulletpoint) {
			yield new StoredBulletpoint(
				$bulletpoint['id'],
				new Storage\MemoryConnection($this->connection, $bulletpoint),
				$this->user,
			);
		}
	}

	public function add(array $bulletpoint): void {
		(new Storage\BuiltQuery(
			$this->connection,
			(new Insert\Query())
				->insertInto(
					new Clause\InsertInto(
						'web.bulletpoints',
						[
							'content' => $bulletpoint['content'],
							'theme_id' => $this->theme,
							'referenced_theme_id' => new Expression\PgArray($bulletpoint['referenced_theme_id'], 'int'),
							'compared_theme_id' => new Expression\PgArray($bulletpoint['compared_theme_id'], 'int'),
							'source_link' => $bulletpoint['source']['link'],
							'source_type' => $bulletpoint['source']['type'],
							'user_id' => $this->user->id(),
							'root_bulletpoint_id' => $bulletpoint['group']['root_bulletpoint_id'],
						],
					),
				),
		))->execute();
	}

	public function count(): int {
		return (new Storage\BuiltQuery(
			$this->connection,
			(new Select\Query())
				->select(new Expression\Select(['count(*)']))
				->from(new Expression\From(['public_bulletpoints']))
				->where(new Expression\Where('theme_id', $this->theme)),
		))->field();
	}
}
