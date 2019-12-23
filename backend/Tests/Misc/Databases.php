<?php
declare(strict_types = 1);

namespace Bulletpoint\Misc;

use Klapuch\Storage;

interface Databases {
	/**
	 * Create a new database
	 */
	public function create(): Storage\Connection;

	/**
	 * Drop the database
	 * @return void
	 */
	public function drop(): void;
}
