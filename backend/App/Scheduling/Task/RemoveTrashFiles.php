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
		(new Storage\Transaction($this->connection))->start(function () {
			$filenames = (new Storage\TypedQuery(
				$this->connection,
				'DELETE FROM filesystem.trash RETURNING filename',
			))->rows();
			foreach (
				array_merge(
					$filenames,
					array_map(static function (string $filename): string {
						return sprintf('resize_w50h50/%s', $filename);
					}, $filenames)
				) as $filename) {
				Utils\FileSystem::delete(__DIR__ . '/../../../data/' . $filename);
			}
		});
	}

	public function name(): string {
		return 'RemoveTrashFiles';
	}
}
