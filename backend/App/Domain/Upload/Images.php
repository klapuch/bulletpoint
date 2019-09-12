<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain\Upload;

use Klapuch\Sql\Clause;
use Klapuch\Sql\Expression;
use Klapuch\Sql\Statement\Insert;
use Klapuch\Sql\Statement\Select;
use Klapuch\Storage;
use Nette\Http\FileUpload;
use Nette\Utils\FileSystem;

final class Images extends Files {
	/** @var string */
	private $namespace;

	/** @var \Nette\Http\FileUpload */
	private $upload;

	/** @var \Klapuch\Storage\Connection */
	private $connection;

	public function __construct(string $namespace, FileUpload $upload, Storage\Connection $connection) {
		$this->namespace = $namespace;
		$this->upload = $upload;
		$this->connection = $connection;
	}

	public function save(): int {
		$upload = self::check($this->upload);
		$id = $this->sequence();
		$filename = self::filename($id, self::extension($upload));
		$image = $upload->toImage();
		return (new Storage\Transaction($this->connection))->start(function () use ($filename, $upload, $image, $id): int {
			$id = (new Storage\BuiltQuery(
				$this->connection,
				(new Insert\Query())
					->insertInto(
						new Clause\InsertInto(
							'filesystem.files$images',
							[
								'id' => $id,
								'width' => $image->getWidth(),
								'height' => $image->getHeight(),
								'mime_type' => $upload->getContentType(),
								'size_bytes' => $upload->getSize(),
								'filename' => $this->namespace . DIRECTORY_SEPARATOR . $filename,
							],
						),
					)
					->returning(new Clause\Returning(['id'])),
			))->field();
			FileSystem::createDir(self::PATH . DIRECTORY_SEPARATOR . $this->namespace . DIRECTORY_SEPARATOR . dirname($filename), 0755);
			$upload->move(self::PATH . DIRECTORY_SEPARATOR . $this->namespace . DIRECTORY_SEPARATOR . $filename);
			return $id;
		});
	}

	private function sequence(): int {
		return (new Storage\BuiltQuery(
			$this->connection,
			(new Select\Query())
				->select(new Expression\Select(['nextval(:sequence)'], ['sequence' => 'filesystem.files$images_id_seq'])),
		))->field();
	}

	private static function check(FileUpload $upload): FileUpload {
		if (!$upload->isOk())
			throw new \UnexpectedValueException(t('files.file.not.successfully.uploaded'));
		elseif (!$upload->isImage())
			throw new \UnexpectedValueException(t('files.file.not.image'));
		return $upload;
	}

	private static function filename(int $id, string $extension): string {
		$path = chunk_split(str_pad((string) $id, 12, '0', STR_PAD_LEFT), 3, '/') . $id;
		return sprintf('%s-%s.%s', $path, bin2hex(random_bytes(15)), $extension);
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
			throw new \UnexpectedValueException(t('files.file.not.image'));
		}
		return $extension;
	}
}
