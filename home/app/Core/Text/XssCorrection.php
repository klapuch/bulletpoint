<?php
namespace Bulletpoint\Core\Text;

final class XssCorrection implements Correction {
	public function replacement($origin) {
		return htmlspecialchars($origin, ENT_QUOTES, 'UTF-8');
	}
}