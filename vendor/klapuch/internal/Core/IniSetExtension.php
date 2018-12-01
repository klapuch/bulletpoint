<?php
declare(strict_types = 1);
namespace Klapuch\Internal;

final class IniSetExtension implements Extension {
	private $settings;

	public function __construct(array $settings) {
		$this->settings = $settings;
	}

	public function improve(): void {
		foreach ($this->settings as $name => $value)
			ini_set($name, (string) $value);
	}
}