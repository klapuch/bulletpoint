<?php
namespace Bulletpoint\Core\Text;

final class FirstUpperCaseCorrection implements Correction {
	public function replacement($origin) {
		return ucfirst($origin);
	}
}