<?php
namespace Bulletpoint\Core\Text;

final class PrefixCorrection implements Correction {
	private $prefix;

	public function __construct(string $prefix) {
		$this->prefix = $prefix;
	}

	public function replacement($origin) {
		return $this->prefix . $origin;
	}		
}