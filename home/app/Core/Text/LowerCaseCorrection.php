<?php
namespace Bulletpoint\Core\Text;

final class LowerCaseCorrection implements Correction {
	public function replacement($origin) {
		return mb_strtolower($origin);
	}
}