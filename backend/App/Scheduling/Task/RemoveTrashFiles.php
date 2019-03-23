<?php
declare(strict_types = 1);

namespace Bulletpoint\Scheduling\Task;

use Klapuch\Scheduling;
use Klapuch\Storage;
use Nette\Utils;

final class RemoveTrashFiles implements Scheduling\Job {
	private const SIZES = ['w50h50'];

	/** @var \Klapuch\Storage\Connection */
	private $connection;

	public function __construct(Storage\Connection $connection) {
		$this->connection = $connection;
	}

	public function fulfill(): void {
		(new Storage\Transaction($this->connection))->start(function (): void {
			$filenames = array_column(
				(new Storage\TypedQuery(
					$this->connection,
					'DELETE FROM filesystem.trash RETURNING filename',
				))->rows(),
				'filename'
			);
			foreach (array_merge($filenames, self::cache($filenames)) as $filename) {
				Utils\FileSystem::delete(__DIR__ . '/../../../data/' . $filename);
			}
		});
	}

	private static function cache(array $filenames): array {
		$sizes = [];
		foreach ($filenames as $filename) {
			foreach (self::SIZES as $size) {
				$sizes[] = sprintf('resize_%s/%s', $size, $filename);
			}
		}
		return $sizes;
	}

	public function name(): string {
		return 'RemoveTrashFiles';
	}
}
