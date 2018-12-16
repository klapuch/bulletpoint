<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain\Access;

use Bulletpoint\Misc;

/**
 * Entrance harnessed by callback
 */
final class HarnessedEntrance implements Entrance {
	/** @var \Bulletpoint\Domain\Access\Entrance */
	private $origin;

	/** @var \Bulletpoint\Misc\Callback */
	private $callback;

	public function __construct(Entrance $origin, Misc\Callback $callback) {
		$this->origin = $origin;
		$this->callback = $callback;
	}

	public function enter(array $credentials): User {
		return $this->callback->invoke([$this->origin, __FUNCTION__], func_get_args());
	}

	public function exit(): User {
		return $this->callback->invoke([$this->origin, __FUNCTION__], func_get_args());
	}
}
