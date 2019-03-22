<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain\Image;

use Bulletpoint\Domain\Access;
use Klapuch\Application;
use Klapuch\Sql;
use Klapuch\Storage;
use Nette\Utils\Image;

final class UploadedAvatars implements Avatars {
	private const BASE_PATH = 'images/avatars';
	private const PATH = __DIR__ . '/../../../data/' . self::BASE_PATH;

	/** @var \Bulletpoint\Domain\Access\User */
	private $user;

	/** @var \Klapuch\Application\Request */
	private $request;

	/** @var \Klapuch\Storage\Connection */
	private $connection;

	public function __construct(Access\User $user, Application\Request $request, Storage\Connection $connection) {
		$this->user = $user;
		$this->request = $request;
		$this->connection = $connection;
	}

	public function save(): void {
		(new Storage\Transaction($this->connection))->start(function (): void {
			$filename = self::filename($this->user->id(), 'png');
			(new Storage\BuiltQuery(
				$this->connection,
				(new Sql\AnsiUpdate('users'))
					->set(['avatar_filename' => '?'], [self::BASE_PATH . DIRECTORY_SEPARATOR . $filename])
					->where('id = ?', [$this->user->id()]),
			))->execute();
			Image::fromString($this->request->body()->serialization())
				->save(self::PATH . DIRECTORY_SEPARATOR . $filename);
		});
	}

	private static function filename(string $id, string $extension): string {
		return sprintf('%s.%s', bin2hex(random_bytes(15)) . $id, $extension);
	}
}
