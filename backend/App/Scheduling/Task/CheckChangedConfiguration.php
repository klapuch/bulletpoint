<?php
declare(strict_types = 1);

namespace Bulletpoint\Scheduling\Task;

use Klapuch\Iterator;
use Klapuch\Scheduling;

final class CheckChangedConfiguration implements Scheduling\Job {
	private \SplFileInfo $destination;

	private Scheduling\Job $dependency;

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
					static fn (\SplFileInfo $file): bool => !$file->isDir(),
				),
				static fn (\SplFileInfo $file): string => (string) md5_file($file->getPathname()),
			),
		);
	}
}
