<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain\Upload;

use Bulletpoint\Domain\Access;
use Klapuch\Sql\Expression;
use Klapuch\Sql\Statement\Update;
use Klapuch\Storage;
use Nette\Http\FileUpload;

final class Avatars {
	private const NAMESPACE = 'images/avatars';

	/** @var \Bulletpoint\Domain\Access\User */
	private $user;

	/** @var \Klapuch\Storage\Connection */
	private $connection;

	/** @var \Nette\Http\FileUpload */
	private $upload;

	public function __construct(FileUpload $upload, Access\User $user, Storage\Connection $connection) {
		$this->user = $user;
		$this->connection = $connection;
		$this->upload = $upload;
	}

	public function save(): void {
		(new Storage\Transaction($this->connection))->start(function (): void {
			$id = (new Images(self::NAMESPACE, $this->upload, $this->connection))->save();
			(new Storage\BuiltQuery(
				$this->connection,
				(new Update\Query())
					->update('users')
					->set(new Expression\Set(['avatar_filename_id' => $id]))
					->where(new Expression\Where('id', $this->user->id())),
			))->execute();
		});
	}
}
