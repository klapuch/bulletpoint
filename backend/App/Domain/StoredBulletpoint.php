<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain;

use Klapuch\Output;
use Klapuch\Sql;
use Klapuch\Storage;
use Nette\Utils\Json;

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
			(new Sql\AnsiSelect([
				'id',
				'theme_id',
				'referenced_theme_id',
				'source_link',
				'source_type',
				'content',
				'total_rating',
				'up_rating',
				'down_rating',
				'user_rating',
				'user_id',
			]))->from(['web.bulletpoints'])
				->where('id = :id', ['id' => $this->id]),
		))->row();
		return new Output\FilledFormat(
			$format,
			[
				'id' => $row['id'],
				'theme_id' => $row['theme_id'],
				'referenced_theme_id' => $row['referenced_theme_id'],
				'content' => $row['content'],
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
				],
			],
		);
	}

	public function edit(array $bulletpoint): void {
		(new Storage\BuiltQuery(
			$this->connection,
			(new Sql\PreparedUpdate(
				new Sql\AnsiUpdate('web.bulletpoints'),
			))->set([
				'referenced_theme_id' => Json::encode($bulletpoint['referenced_theme_id']), // TODO: use array
				'source_link' => $bulletpoint['source']['link'],
				'source_type' => $bulletpoint['source']['type'],
				'content' => $bulletpoint['content'],
			])->where('id = :id', ['id' => $this->id]),
		))->execute();
	}

	public function delete(): void {
		(new Storage\TypedQuery(
			$this->connection,
			'DELETE FROM public_bulletpoints WHERE id = :id',
			['id' => $this->id],
		))->execute();
	}

	public function rate(int $point): void {
		(new Storage\BuiltQuery(
			$this->connection,
			(new Sql\PgInsertInto(
				'bulletpoint_ratings',
				['point' => ':point', 'user_id' => ':user_id', 'bulletpoint_id' => ':bulletpoint_id'],
				['point' => $point, 'user_id' => $this->user->id(), 'bulletpoint_id' => $this->id],
			))->onConflict(['user_id', 'bulletpoint_id'])
				->doUpdate(['point' => 'EXCLUDED.point']),
		))->execute();
	}
}
