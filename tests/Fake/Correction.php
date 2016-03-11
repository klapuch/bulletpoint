<?php
namespace Bulletpoint\Fake;

use Bulletpoint\Core\Text;

final class Correction implements Text\Correction {
	private $function;

	public function __construct($function = null) {
		$this->function = $function;
	}

	public function replacement($origin) {
		if($this->function && function_exists($this->function))
			return call_user_func($this->function, $origin);
		return $origin;
	}
}