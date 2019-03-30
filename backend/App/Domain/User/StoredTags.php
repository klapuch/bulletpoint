<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain\User;

use Bulletpoint\Domain\Access;
use Klapuch\Dataset;
use Klapuch\Output;
use Klapuch\Sql;
use Klapuch\Storage;

final class StoredTags implements Tags {
	/** @var \Klapuch\Storage\Connection */
	private $connection;

	/** @var \Bulletpoint\Domain\Access\User */
	private $user;

	public function __construct(Storage\Connection $connection, Access\User $user) {
		$this->connection = $connection;
		$this->user = $user;
	}

	public function all(Dataset\Selection $selection): \Iterator {
		$tags = (new Storage\BuiltQuery(
			$this->connection,
			new Dataset\SelectiveStatement(
				(new Sql\AnsiSelect(['tag_id', 'name', 'reputation', 'rank']))
					->from(['user_tag_rank_reputations'])
					->where('user_id = :user_id', ['user_id' => $this->user->id()]),
				$selection,
			),
		))->rows();
		foreach ($tags as $tag) {
			yield new class ($tag) implements Tag {
				/** @var mixed[] */
				private $tag;

				public function __construct(array $tag) {
					$this->tag = $tag;
				}

				public function print(Output\Format $format): Output\Format {
					return new Output\FilledFormat($format, $this->tag);
				}
			};
		}
	}
}
