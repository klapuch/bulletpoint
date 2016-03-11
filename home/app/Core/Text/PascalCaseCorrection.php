<?php
namespace Bulletpoint\Core\Text;

final class PascalCaseCorrection implements Correction {
	public function replacement($origin) {
		return $this->withoutSpaces(
			$this->toWords(
				$this->withoutDashes(
					$origin
				)
			)
		);
	}

	private function withoutSpaces($origin) {
		return str_replace(' ', '', $origin);
	}

	private function toWords($origin) {
		return ucwords($origin);
	}

	private function withoutDashes($origin) {
		return mb_strtolower(str_replace('-', ' ', $origin));
	}
}