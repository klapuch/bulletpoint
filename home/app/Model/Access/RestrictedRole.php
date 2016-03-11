<?php
namespace Bulletpoint\Model\Access;

use Bulletpoint\Exception;

final class RestrictedRole implements Role {
	private $myself;
	private $origin;

	public function __construct(Identity $myself, Role $origin) {
		$this->myself = $myself;
		$this->origin = $origin;
	}

	public function degrade(): Role {
		$this->checkRestriction();
		return $this->origin->degrade();
	}

	public function promote(): Role {
		$this->checkRestriction();
		return $this->origin->promote();
	}

	public function __toString() {
		return (string)$this->origin;
	}

	public function rank(): int {
		return $this->origin->rank();
	}

	private function checkRestriction() {
		if(!$this->hasSufficientRank()) {
			throw new Exception\AccessDeniedException(
				'Nedostatečná role pro změnu'
			);
		}
	}

	private function hasSufficientRank(): bool {
		return $this->myself->role()->rank() > $this->rank();
	}
}