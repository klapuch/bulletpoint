<?php
namespace Bulletpoint\Model\Report;

final class ConstantTarget implements Target {
	private $id;
	private $complaints;

	public function __construct(int $id, \Iterator $complaints) {
		$this->id = $id;
		$this->complaints = $complaints;
	}

	public function id(): int {
		return $this->id;
	}

	public function complaints(): \Iterator {
		return $this->complaints;
	}
}