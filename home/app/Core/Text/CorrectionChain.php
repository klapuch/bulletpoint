<?php
namespace Bulletpoint\Core\Text;

final class CorrectionChain implements Correction {
	private $corrections;

	public function __construct(Correction ...$corrections) {
		$this->corrections = $corrections;
	}

	public function replacement($origin) {
		foreach($this->corrections as $correction)
			$origin = $correction->replacement($origin);
		return $origin;
	}
}
