<?php
declare(strict_types = 1);

namespace Bulletpoint\Scheduling\Task;

use Klapuch\Iterator;
use Klapuch\Scheduling;

final class CheckChangedConfiguration implements Scheduling\Job {
	/** @var \SplFileInfo */
	private $destination;

	/** @var \Klapuch\Scheduling\Job */
	private $dependency;

	public function __construct(\SplFileInfo $destination, Scheduling\Job $dependency) {
		$this->destination = $destination;
		$this->dependency = $dependency;
	}

	public function fulfill(): void {
		$before = $this->changes();
		$this->dependency->fulfill();
		if ($before !== $this->changes()) {
			echo sprintf('Job generated changed files');
			exit(1);
		}
	}

	public function name(): string {
		return 'CheckChangedConfiguration';
	}

	private function changes(): array {
		return iterator_to_array(
			new Iterator\Mapped(
				new \CallbackFilterIterator(
					new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->destination->getPathname())),
					static function (\SplFileInfo $file): bool {
						return !$file->isDir();
					}
				),
				static function (\SplFileInfo $file): string {
					return (string) md5_file($file->getPathname());
				},
			),
		);
	}
}
