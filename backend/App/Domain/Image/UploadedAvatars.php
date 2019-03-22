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

	/**
	 * @var Access\User
	 */
	private $user;

	/**
	 * @var Application\Request
	 */
	private $request;

	/**
	 * @var Storage\Connection
	 */
	private $connection;

	public function __construct(Access\User $user, Application\Request $request, Storage\Connection $connection) {
		$this->user = $user;
		$this->request = $request;
		$this->connection = $connection;
	}

	public function save(): void {
		(new Storage\Transaction($this->connection))->start(function (): void {
			$path = self::path($this->user->id(), 'png');
			(new Storage\BuiltQuery(
				$this->connection,
				(new Sql\AnsiUpdate('users'))
					->set(['avatar_path' => '?'], [self::BASE_PATH . DIRECTORY_SEPARATOR . $path])
					->where('id = ?', [$this->user->id()])
				))->execute();
			Image::fromString($this->request->body()->serialization())
				->save(self::PATH . DIRECTORY_SEPARATOR . $path);
		});
	}

	private static function path(string $id, string $extension): string {
		return sprintf('%s.%s', bin2hex(random_bytes(15)) . $id, $extension);
	}
}
