<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain;

use Klapuch\Output;
use Klapuch\Sql;
use Klapuch\Storage;

final class StoredBulletpoint implements Bulletpoint {
	/** @var int */
	private $id;

	/** @var \Klapuch\Storage\Connection */
	private $connection;

	public function __construct(int $id, Storage\Connection $connection) {
		$this->id = $id;
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
				'total_rating',
				'up_rating',
				'down_rating',
				'neutral_rating',
				'user_id',
			]))->from(['public_bulletpoints'])
				->where('id = :id', ['id' => $this->id])
		))->row();
		return new Output\FilledFormat(
			$format,
			[
				'id' => $row['id'],
				'theme_id' => $row['theme_id'],
				'content' => $row['content'],
				'rating' => [
					'up' => $row['up_rating'],
					'down' => $row['down_rating'],
					'neutral' => $row['neutral_rating'],
					'total' => $row['total_rating'],
				],
				'user_id' => $row['user_id'],
				'source' => [
					'link' => $row['source_link'],
					'type' => $row['source_type'],
				],
			]
		);
	}
}
