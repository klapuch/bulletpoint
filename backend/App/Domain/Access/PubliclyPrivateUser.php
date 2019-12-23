<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain\Access;

use Klapuch\Sql\Clause;
use Klapuch\Sql\Expression;
use Klapuch\Sql\Statement\Select;
use Klapuch\Storage;

/**
 * User which can be publicly shown, but includes potential private data to owner
 */
final class PubliclyPrivateUser implements User {
	private User $origin;
	private Storage\Connection $connection;

	public function __construct(User $origin, Storage\Connection $connection) {
		$this->origin = $origin;
		$this->connection = $connection;
	}

	public function id(): string {
		return $this->origin->id();
	}

	public function properties(): array {
		return (new Storage\BuiltQuery(
			$this->connection,
			(new Select\Query())
				->select(new Expression\Select(['role', 'email', 'username', 'avatar_filename' => 'filesystem.files$images.filename']))
				->from(new Expression\From(['users']))
				->join(new Clause\Join('filesystem.files$images', 'users.avatar_filename_id = filesystem.files$images.id'))
				->where(new Expression\Where('users.id', $this->origin->id())),
		))->row();
	}

	public function edit(array $properties): void {
		$this->origin->edit($properties);
	}
}
