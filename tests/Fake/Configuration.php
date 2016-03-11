<?php
namespace Bulletpoint\Fake;

use Bulletpoint\Core\Control;

final class Configuration implements Control\Configuration {
	private $configuration;
	private $section;

	public function __construct(array $configuration) {
		$this->configuration = $configuration;
	}

	public function toSection(string $section): self {
		$this->section = $section;
		return $this;
	}

	public function setting(): array {
		return $this->configuration[$this->section];
	}

	public function __get(string $key) {
		if($this->section && !isset($this->configuration[$this->section][$key]))
			throw new \RuntimeException('Key does not exist');
		elseif($this->section)
			return $this->configuration[$this->section][$key];
		return $this->configuration[$key];
	}
}