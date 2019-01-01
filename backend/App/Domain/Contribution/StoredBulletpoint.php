<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain\Contribution;

use Bulletpoint\Domain;
use Bulletpoint\Domain\Access;
use Klapuch\Output;
use Klapuch\Sql;
use Klapuch\Storage;

final class StoredBulletpoint implements Domain\Bulletpoint {
	/** @var int */
	private $id;

	/** @var \Bulletpoint\Domain\Access\User */
	private $user;

	/** @var \Klapuch\Storage\Connection */
	private $connection;

	public function __construct(int $id, Access\User $user, Storage\Connection $connection) {
		$this->id = $id;
		$this->user = $user;
		$this->connection = $connection;
	}

	public function print(Output\Format $format): Output\Format {
		$row = (new Storage\BuiltQuery(
			$this->connection,
			(new Sql\AnsiSelect([
				'id',
				'theme_id',
				'source_link',
				'source_type',
				'content',
			]))->from(['web.contributed_bulletpoints'])
				->where('id = :id', ['id' => $this->id])
				->where('user_id = :user_id', ['user_id' => $this->id])
		))->row();
		return new Output\FilledFormat(
			$format,
			[
				'id' => $row['id'],
				'theme_id' => $row['theme_id'],
				'content' => $row['content'],
				'source' => [
					'link' => $row['source_link'],
					'type' => $row['source_type'],
				],
			]
		);
	}

	public function edit(array $bulletpoint): void {
		(new Storage\BuiltQuery(
			$this->connection,
			(new Sql\PreparedUpdate(
				new Sql\AnsiUpdate('web.contributed_bulletpoints'),
			))->set([
				'source_link' => $bulletpoint['source']['link'],
				'source_type' => $bulletpoint['source']['type'],
				'content' => $bulletpoint['content'],
			])->where('id = :id', ['id' => $this->id])
				->where('user_id = :user_id', ['user_id' => $this->user->id()])
		))->execute();
	}

	public function delete(): void {
		(new Storage\TypedQuery(
			$this->connection,
			'DELETE FROM contributed_bulletpoints WHERE id = :id AND user_id = :user_id',
			['id' => $this->id, 'user_id' => $this->user->id()],
		))->execute();
	}
}
