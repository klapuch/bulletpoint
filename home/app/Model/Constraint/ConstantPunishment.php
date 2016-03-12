<?php
namespace Bulletpoint\Model\Constraint;

use Bulletpoint\Core\Storage;
use Bulletpoint\Model\Access;

final class ConstantPunishment implements Punishment {
	private $sinner;
	private $reason;
	private $expiration;
	private $origin;

	public function __construct(
		Access\Identity $sinner,
		string $reason,
		\DateTime $expiration,
		Punishment $origin
	) {
		$this->sinner = $sinner;
		$this->reason = $reason;
		$this->expiration = $expiration;
		$this->origin = $origin;
	}

	public function sinner(): Access\Identity {
		return $this->sinner;
	}

	public function id(): int {
		return $this->origin->id();
	}

	public function reason(): string {
		return $this->reason;
	}

	public function expiration(): \DateTime {
		return $this->expiration;
	}

	public function expired(): bool {
		return $this->origin->expired();
	}

	public function forgive() {
		$this->origin->forgive();
	}
}