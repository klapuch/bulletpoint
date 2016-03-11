<?php
namespace Bulletpoint\Core\Text;

final class SuffixCorrection implements Correction {
	private $suffix;

	public function __construct(string $suffix) {
		$this->suffix = $suffix;
	}

	public function replacement($origin) {
		return $origin . $this->suffix;
	}		
}