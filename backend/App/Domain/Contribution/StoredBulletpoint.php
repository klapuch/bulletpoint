<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain\Contribution;

use Bulletpoint\Domain;
use Bulletpoint\Domain\Access;
use Klapuch\Output;
use Klapuch\Sql;
use Klapuch\Storage;
use Nette\Utils\Json;

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
				'referenced_theme_id',
				'compared_theme_id',
				'source_link',
				'source_type',
				'source_is_broken',
				'content',
				'root_bulletpoint_id',
			]))->from(['web.contributed_bulletpoints'])
				->where('id = :id', ['id' => $this->id])
				->where('user_id = :user_id', ['user_id' => $this->id]),
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
			(new Sql\PreparedUpdate(
				new Sql\AnsiUpdate('web.contributed_bulletpoints'),
			))->set([
				'referenced_theme_id' => Json::encode($bulletpoint['referenced_theme_id']), // TODO: use array
				'compared_theme_id' => Json::encode($bulletpoint['compared_theme_id']), // TODO: use array
				'source_link' => $bulletpoint['source']['link'],
				'source_type' => $bulletpoint['source']['type'],
				'root_bulletpoint_id'  => $bulletpoint['group']['root_bulletpoint_id'],
			])->where('id = :id', ['id' => $this->id])
				->where('user_id = :user_id', ['user_id' => $this->user->id()]),
		))->execute();
	}

	public function delete(): void {
		(new Storage\TypedQuery(
			$this->connection,
			'DELETE FROM contributed_bulletpoints WHERE id = :id AND user_id = :user_id',
			['id' => $this->id, 'user_id' => $this->user->id()],
		))->execute();
	}

	public function rate(int $point): void {
		throw new \UnexpectedValueException('Rating for contributed bulletpoint is not allowed');
	}
}
