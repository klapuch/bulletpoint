<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain\Access;

use Klapuch\Sql\Clause;
use Klapuch\Sql\Expression;
use Klapuch\Sql\Statement\Select;
use Klapuch\Storage;

/**
 * User which can be publicly shown
 */
final class PublicUser implements User {
	private int $id;

	private Storage\Connection $connection;

	public function __construct(int $id, Storage\Connection $connection) {
		$this->id = $id;
		$this->connection = $connection;
	}

	public function id(): string {
		return (string) $this->id;
	}

	public function properties(): array {
		return (new Storage\BuiltQuery(
			$this->connection,
			(new Select\Query())
				->select(new Expression\Select(['username', 'avatar_filename' => 'filesystem.files$images.filename']))
				->from(new Expression\From(['users']))
				->join(new Clause\Join('filesystem.files$images', 'users.avatar_filename_id = filesystem.files$images.id'))
				->where(new Expression\Where('users.id', $this->id())),
		))->row();
	}

	public function edit(array $properties): void {
	}
}
