<?php
declare(strict_types = 1);
namespace Klapuch\Internal;

interface Extension {
	/**
	 * Improve the current setting
	 * @return void
	 */
	public function improve(): void;
}