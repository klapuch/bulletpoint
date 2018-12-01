<?php
declare(strict_types = 1);
namespace Klapuch\Internal;

final class InternationalExtension implements Extension {
	private $timezone;

	public function __construct(string $timezone) {
		$this->timezone = $timezone;
	}

	public function improve(): void {
		mb_internal_encoding('UTF-8');
		if (@date_default_timezone_set($this->timezone) === false) {
			throw new \InvalidArgumentException(
				sprintf('Timezone "%s" is invalid', $this->timezone)
			);
		}
	}
}