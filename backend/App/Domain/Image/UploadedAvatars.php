<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain\Image;

use Bulletpoint\Domain\Access;
use Klapuch\Sql;
use Klapuch\Storage;
use Nette\Http\FileUpload;

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
		(new Storage\Transaction($this->connection))->start(function (): void {
			$upload = self::check(new FileUpload($_FILES['avatar']));
			$filename = self::filename($this->user->id(), self::extension($upload));
			(new Storage\BuiltQuery(
				$this->connection,
				(new Sql\AnsiUpdate('users'))
					->set(['avatar_filename' => '?'], [self::BASE_PATH . DIRECTORY_SEPARATOR . $filename])
					->where('id = ?', [$this->user->id()]),
			))->execute();
			$upload->move(self::PATH . DIRECTORY_SEPARATOR . $filename);
		});
	}

	private function check(FileUpload $upload): FileUpload {
		if (!$upload->isOk())
			throw new \UnexpectedValueException(t('avatars.file.not.successfully.uploaded'));
		elseif (!$upload->isImage())
			throw new \UnexpectedValueException(t('avatars.file.not.image'));
		return $upload;
	}

	private static function filename(string $id, string $extension): string {
		return sprintf('%s.%s', bin2hex(random_bytes(15)) . $id, $extension);
	}

	private static function extension(FileUpload $upload): string {
		static $mimeExtensions = [
			'image/gif' => 'gif',
			'image/jpeg' => 'jpg',
			'image/png' => 'png',
			'image/webp' => 'webp',
		];
		$extension = $mimeExtensions[$upload->getContentType()] ?? null;
		if ($extension === null) {
			throw new \UnexpectedValueException(t('avatars.file.not.image'));
		}
		return $extension;
	}
}
