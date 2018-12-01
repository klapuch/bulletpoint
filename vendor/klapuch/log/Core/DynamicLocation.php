<?php
declare(strict_types = 1);
namespace Klapuch\Log;

/**
 * Location with a dynamic filename
 */
final class DynamicLocation implements Location {
	private $location;

	public function __construct(Location $location) {
		$this->location = $location;
	}

	public function path(): string {
		return $this->location->path() . DIRECTORY_SEPARATOR . $this->filename();
	}

	/**
	 * Unique and dynamic filename
	 * @return string
	 */
	private function filename(): string {
		return substr(
			md5(uniqid() . base64_encode(random_bytes(5))),
			0,
			20
		) . date('Y-m-d--H-i');
	}
}