<?php
declare(strict_types = 1);

namespace Bulletpoint\Scheduling;

final class SerialJobs implements Job {
	/** @var \Bulletpoint\Scheduling\Job[] */
	private $origins;

	public function __construct(Job ...$origins) {
		$this->origins = $origins;
	}

	public function fulfill(): void {
		foreach ($this->origins as $origin)
			$origin->fulfill();
	}

	public function name(): string {
		return '';
	}
}
