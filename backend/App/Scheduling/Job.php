<?php
declare(strict_types = 1);

namespace Bulletpoint\Scheduling;

interface Job {
	public function fulfill(): void;

	public function name(): string;
}
