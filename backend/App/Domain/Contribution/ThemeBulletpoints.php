<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain\Contribution;

use Bulletpoint\Domain;
use Bulletpoint\Domain\Access;
use Klapuch\Sql\Clause;
use Klapuch\Sql\Expression;
use Klapuch\Sql\Statement\Insert;
use Klapuch\Sql\Statement\Select;
use Klapuch\Storage;

final class ThemeBulletpoints implements Domain\Bulletpoints {
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
					'root_bulletpoint_id',
					'source_link',
					'source_type',
					'source_is_broken',
				]))->from(new Expression\From(['web.contributed_bulletpoints']))
				->where(new Expression\Where('theme_id', $this->theme))
				->where(new Expression\Where('user_id', $this->user->id())),
		))->rows();
		foreach ($bulletpoints as $bulletpoint) {
			yield new StoredBulletpoint(
				$bulletpoint['id'],
				$this->user,
				new Storage\MemoryConnection($this->connection, $bulletpoint),
			);
		}
	}

	public function add(array $bulletpoint): void {
		(new Storage\BuiltQuery(
			$this->connection,
			(new Insert\Query())
				->insertInto(new Clause\InsertInto('web.contributed_bulletpoints', [
					'content' => $bulletpoint['content'],
					'theme_id' => $this->theme,
					'referenced_theme_id' => new Expression\PgArray($bulletpoint['referenced_theme_id'], 'int'),
					'compared_theme_id' => new Expression\PgArray($bulletpoint['compared_theme_id'], 'int'),
					'source_link' => $bulletpoint['source']['link'],
					'source_type' => $bulletpoint['source']['type'],
					'root_bulletpoint_id' => $bulletpoint['group']['root_bulletpoint_id'],
					'user_id' => $this->user->id(),
				])),
		))->execute();
	}

	public function count(): int {
		return (new Storage\BuiltQuery(
			$this->connection,
			(new Select\Query())
				->select(new Expression\Select(['count(*)']))
				->from(new Expression\From(['contributed_bulletpoints']))
				->where(new Expression\Where('theme_id', $this->theme))
				->where(new Expression\Where('user_id', $this->user->id())),
		))->field();
	}
}
