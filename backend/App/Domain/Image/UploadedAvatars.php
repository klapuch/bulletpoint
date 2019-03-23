<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain\Image;

use Bulletpoint\Domain\Access;
use Klapuch\Sql;
use Klapuch\Storage;
use Nette\Utils\Image;

final class UploadedAvatars implements Avatars {
	private const BASE_PATH = 'images/avatars';
	private const PATH = __DIR__ . '/../../../data/' . self::BASE_PATH;

	/** @var \Bulletpoint\Domain\Access\User */
	private $user;

	/** @var \Klapuch\Storage\Connection */
	private $connection;

	public function __construct(Access\User $user, Storage\Connection $connection) {
		$this->user = $user;
		$this->connection = $connection;
	}

	public function save(): void {
		try {
			(new Storage\Transaction($this->connection))->start(function (): void {
				$filename = self::filename($this->user->id(), self::extension($_FILES['avatar']['tmp_name']));
				(new Storage\BuiltQuery(
					$this->connection,
					(new Sql\AnsiUpdate('users'))
						->set(['avatar_filename' => '?'], [self::BASE_PATH . DIRECTORY_SEPARATOR . $filename])
						->where('id = ?', [$this->user->id()]),
				))->execute();
				Image::fromFile($_FILES['avatar']['tmp_name'])->save(self::PATH . DIRECTORY_SEPARATOR . $filename);
			});
		} catch(\Nette\InvalidArgumentException | \Nette\Utils\ImageException $e) {
			throw new \UnexpectedValueException($e->getMessage(), 0, $e);
		}
	}

	private static function filename(string $id, string $extension): string {
		return sprintf('%s.%s', bin2hex(random_bytes(15)) . $id, $extension);
	}

	private function extension(string $filename): string {
		static $mimeExtensions = [
			'image/gif' => 'gif',
			'image/jpeg' => 'jpg',
			'image/png' => 'png',
			'image/webp' => 'webp',
		];
		$extension = $mimeExtensions[finfo_file(finfo_open(FILEINFO_MIME_TYPE), $filename)] ?? null;
		if ($extension !== null) {
			return $extension;
		}
		throw new \UnexpectedValueException('File is not an image');
	}
}
