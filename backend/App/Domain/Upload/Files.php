<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain\Upload;

use Klapuch\Sql\Expression;
use Klapuch\Sql\Statement\Select;
use Klapuch\Storage;
use Nette\Http\FileUpload;

abstract class Files {
	protected const PATH = __DIR__ . '/../../../data';

	/** @var string */
	protected $namespace;

	/** @var \Nette\Http\FileUpload */
	protected $upload;

	/** @var \Klapuch\Storage\Connection */
	protected $connection;

	public function __construct(string $namespace, FileUpload $upload, Storage\Connection $connection) {
		$this->namespace = $namespace;
		$this->upload = $upload;
		$this->connection = $connection;
	}

	final protected function sequence(): int {
		return (new Storage\BuiltQuery(
			$this->connection,
			(new Select\Query())
				->select(new Expression\Select(['nextval(:sequence)'], ['sequence' => 'filesystem.files$images_id_seq'])),
		))->field();
	}

	final protected static function filename(int $id, string $extension): string {
		$path = chunk_split(str_pad((string) $id, 12, '0', STR_PAD_LEFT), 3, '/') . $id;
		return sprintf('%s-%s.%s', $path, bin2hex(random_bytes(15)), $extension);
	}

	abstract public function save(): int;
}
