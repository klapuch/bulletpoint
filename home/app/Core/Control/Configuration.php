<?php
namespace Bulletpoint\Core\Control;

interface Configuration {
	public function setting(): array;
	public function toSection(string $section);
	public function __get(string $key);
}