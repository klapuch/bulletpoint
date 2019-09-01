<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain;

use Klapuch\Output;
use Klapuch\Sql\Clause;
use Klapuch\Sql\Expression;
use Klapuch\Sql\Statement\Delete;
use Klapuch\Sql\Statement\Insert;
use Klapuch\Sql\Statement\Select;
use Klapuch\Sql\Statement\Update;
use Klapuch\Storage;

final class StoredBulletpoint implements Bulletpoint {
	/** @var int */
	private $id;

	/** @var \Klapuch\Storage\Connection */
	private $connection;

	/** @var \Bulletpoint\Domain\Access\User */
	private $user;

	public function __construct(int $id, Storage\Connection $connection, Access\User $user) {
		$this->id = $id;
		$this->connection = $connection;
		$this->user = $user;
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
					'total_rating',
					'up_rating',
					'down_rating',
					'user_rating',
					'user_id',
					'created_at',
					'root_bulletpoint_id',
				]))->from(new Expression\From(['web.bulletpoints']))
				->where(new Expression\Where('id', $this->id)),
		))->row();
		return new Output\FilledFormat(
			$format,
			[
				'id' => $row['id'],
				'theme_id' => $row['theme_id'],
				'referenced_theme_id' => $row['referenced_theme_id'],
				'compared_theme_id' => $row['compared_theme_id'],
				'content' => $row['content'],
				'created_at' => $row['created_at'],
				'group' => [
					'root_bulletpoint_id' => $row['root_bulletpoint_id'],
				],
				'rating' => [
					'up' => $row['up_rating'],
					'down' => $row['down_rating'],
					'total' => $row['total_rating'],
					'user' => $row['user_rating'],
				],
				'user_id' => $row['user_id'],
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
				->update('web.bulletpoints')
				->set(new Expression\Set(['referenced_theme_id' => new Expression\PgArray($bulletpoint['referenced_theme_id'], 'int')]))
				->set(new Expression\Set(['compared_theme_id' => new Expression\PgArray($bulletpoint['compared_theme_id'], 'int')]))
				->set(new Expression\Set(['source_link' => $bulletpoint['source']['link']]))
				->set(new Expression\Set(['source_type' => $bulletpoint['source']['type']]))
				->set(new Expression\Set(['content' => $bulletpoint['content']]))
				->set(new Expression\Set(['root_bulletpoint_id' => $bulletpoint['group']['root_bulletpoint_id']]))
				->where(new Expression\Where('id', $this->id)),
		))->execute();
	}

	public function delete(): void {
		(new Storage\BuiltQuery(
			$this->connection,
			(new Delete\Query())
				->from('public_bulletpoints')
				->where(new Expression\Where('id', $this->id)),
		))->execute();
	}

	public function rate(int $point): void {
		(new Storage\BuiltQuery(
			$this->connection,
			(new Insert\Query())
				->insertInto(new Clause\InsertInto('bulletpoint_ratings', ['point' => $point, 'user_id' => $this->user->id(), 'bulletpoint_id' => $this->id]))
				->onConflict(new Clause\OnConflict(['user_id', 'bulletpoint_id']))
				->doUpdate()
				->set(new Expression\RawSet('point = EXCLUDED.point')),
		))->execute();
	}
}
