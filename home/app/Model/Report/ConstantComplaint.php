<?php
namespace Bulletpoint\Model\Report;

use Bulletpoint\Model\Access;

final class ConstantComplaint implements Complaint {
	private $critic;
	private $target;
	private $reason;
	private $origin;

	public function __construct(
		Access\Identity $critic,
		Target $target,
		string $reason,
		Complaint $origin
	) {
		$this->critic = $critic;
		$this->target = $target;
		$this->reason = $reason;
		$this->origin = $origin;
	}

	public function id(): int {
		return $this->origin->id();
	}

	public function critic(): Access\Identity {
		return $this->critic;
	}

	public function target(): Target {
		return $this->target;
	}

	public function reason(): string {
		return $this->reason;
	}

	public function settle() {
		$this->origin->settle();
	}
}