<?php
declare(strict_types = 1);

namespace Bulletpoint\Misc;

interface Callback {
	/**
	 * Invoke the given callback
	 * @param callable $action
	 * @param array $args
	 * @return mixed
	 */
	public function invoke(callable $action, array $args = []);
}
