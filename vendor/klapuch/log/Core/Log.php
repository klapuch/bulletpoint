<?php
declare(strict_types = 1);
namespace Klapuch\Log;

interface Log {
	/**
	 * Description of the log itself
	 * @return string
	 */
	public function description(): string;
}