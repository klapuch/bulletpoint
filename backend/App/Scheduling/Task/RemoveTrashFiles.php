<?php
declare(strict_types = 1);

namespace Bulletpoint\Scheduling\Task;

use Klapuch\Scheduling;
use Klapuch\Storage;
use Nette\Utils;

final class RemoveTrashFiles implements Scheduling\Job {
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
				'filename',
			);
			self::removeOriginals($filenames);
			self::removeResizes($filenames);
		});
	}

	private static function removeOriginals(array $filenames): void {
		foreach ($filenames as $filename) {
			Utils\FileSystem::delete(__DIR__ . '/../../../data/' . $filename);
		}
	}

	private function removeResizes(array $filenames): void {
		foreach (glob(__DIR__ . '/../../../data/cache/resize_*/images/**/*.*') ?: [] as $cachedFilename) {
			foreach ($filenames as $filename) {
				if (Utils\Strings::contains($cachedFilename, $filename)) {
					Utils\FileSystem::delete($cachedFilename);
				}
			}
		}
	}

	public function name(): string {
		return 'RemoveTrashFiles';
	}
}
