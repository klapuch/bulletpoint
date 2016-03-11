<?php
namespace Bulletpoint\Core\Control;

final class IniConfiguration implements Configuration {
	private $setting;
	private $section;
	const WITH_SECTIONS = true;
	const PRESERVE_TYPES = INI_SCANNER_TYPED;

	public function __construct(string $file) {
		$this->setting = parse_ini_file(
			$file,
			self::WITH_SECTIONS,
			self::PRESERVE_TYPES
		);
	}

	public function toSection(string $section): self {
		$this->section = mb_strtoupper($section);
		return $this;
	}

	public function setting(): array {
		if($this->section)
			return $this->setting[$this->section];
		return $this->setting;
	}

	public function __get(string $key) {
		if($this->settingExists($key))
			return $this->setting[$this->section][$key];
		throw new \RuntimeException(
			sprintf(
				'%s is undefined setting',
				$key
			)
		);
	}

	private function settingExists(string $key): bool {
		if($this->section)
			return isset($this->setting[$this->section][$key]);
		return isset($this->setting[$key]);
	}
}