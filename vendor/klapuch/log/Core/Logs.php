<?php
declare(strict_types = 1);
namespace Klapuch\Log;

interface Logs {
	/**
	 * Put a new log
	 * @param \Klapuch\Log\Log $log
	 */
	public function put(Log $log): void;
}