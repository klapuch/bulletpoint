<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain\Contribution;

use Bulletpoint\Domain;
use Bulletpoint\Domain\Access;
use Klapuch\Output;
use Klapuch\Sql\Expression;
use Klapuch\Sql\Statement\Delete;
use Klapuch\Sql\Statement\Select;
use Klapuch\Sql\Statement\Update;
use Klapuch\Storage;

final class StoredBulletpoint implements Domain\Bulletpoint {
	private int $id;
	private Access\User $user;
	private Storage\Connection $connection;

	public function __construct(int $id, Access\User $user, Storage\Connection $connection) {
		$this->id = $id;
		$this->user = $user;
		$this->connection = $connection;
	}

	public function print(Output\Format $format): Output\Format {
		$row = (new Storage\BuiltQuery(
			$this->connection,
			(new Select\Query())
				->select(new Expression\Select([
					'id',
					'theme_id',
					'referenced_theme_id',
					'compared_theme_id',
					'source_link',
					'source_type',
					'source_is_broken',
					'content',
					'root_bulletpoint_id',
				]))->from(new Expression\From(['web.contributed_bulletpoints']))
				->where(new Expression\Where('id', $this->id))
				->where(new Expression\Where('user_id', $this->id)),
		))->row();
		return new Output\FilledFormat(
			$format,
			[
				'id' => $row['id'],
				'theme_id' => $row['theme_id'],
				'referenced_theme_id' => $row['referenced_theme_id'],
				'compared_theme_id' => $row['compared_theme_id'],
				'content' => $row['content'],
				'group' => [
					'root_bulletpoint_id' => $row['root_bulletpoint_id'],
				],
				'source' => [
					'link' => $row['source_link'],
					'type' => $row['source_type'],
					'is_broken' => $row['source_is_broken'],
				],
			],
		);
	}

	public function edit(array $bulletpoint): void {
		(new Storage\BuiltQuery(
			$this->connection,
			(new Update\Query())
				->update('web.contributed_bulletpoints')
				->set(new Expression\Set(['referenced_theme_id' => new Expression\PgArray($bulletpoint['referenced_theme_id'], 'int')]))
				->set(new Expression\Set(['compared_theme_id' => new Expression\PgArray($bulletpoint['compared_theme_id'], 'int')]))
				->set(new Expression\Set(['source_link' => $bulletpoint['source']['link']]))
				->set(new Expression\Set(['source_type' => $bulletpoint['source']['type']]))
				->set(new Expression\Set(['root_bulletpoint_id' => $bulletpoint['group']['root_bulletpoint_id']]))
				->where(new Expression\Where('id', $this->id))
				->where(new Expression\Where('user_id', $this->user->id())),
		))->execute();
	}

	public function delete(): void {
		(new Storage\BuiltQuery(
			$this->connection,
			(new Delete\Query())
				->from('contributed_bulletpoints')
				->where(new Expression\Where('id', [$this->id]))
				->where(new Expression\Where('user_id', [$this->user->id()])),
		))->execute();
	}

	public function rate(int $point): void {
		throw new \UnexpectedValueException('Rating for contributed bulletpoint is not allowed');
	}
}
