<?php
namespace Bulletpoint\Core\Text;

final class AdaptableCorrection implements Correction {
	private $correction;

	public function __construct(Correction $correction) {
		$this->correction = $correction;
	}

	public function replacement($origin) {
		if(is_string($origin))
			return $this->correction->replacement($origin);
		elseif((array)$origin === $origin) // is_array
			foreach($origin as $key => $value)
				$origin[$key] = $this->replacement($value);
		return $origin;
	}
}
