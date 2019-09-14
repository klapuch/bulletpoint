<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain\Upload;

use Klapuch\Sql\Clause;
use Klapuch\Sql\Statement\Insert;
use Klapuch\Storage;
use Nette\Http\FileUpload;
use Nette\Utils\FileSystem;

final class Images extends Files {
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

	private static function check(FileUpload $upload): FileUpload {
		if (!$upload->isOk())
			throw new \UnexpectedValueException(t('files.file.not.successfully.uploaded'));
		elseif (!$upload->isImage())
			throw new \UnexpectedValueException(t('files.file.not.image'));
		return $upload;
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
