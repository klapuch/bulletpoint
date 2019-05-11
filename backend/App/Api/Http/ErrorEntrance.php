<?php
declare(strict_types = 1);

namespace Bulletpoint\Api\Http;

use Bulletpoint\Domain\Access;
use Bulletpoint\Domain\Access\User;

final class ErrorEntrance implements Access\Entrance {
	/** @var int */
	private $status;

	/** @var \Bulletpoint\Domain\Access\Entrance */
	private $origin;

	public function __construct(int $status, Access\Entrance $origin) {
		$this->status = $status;
		$this->origin = $origin;
	}

	public function enter(array $credentials): User {
		try {
			return $this->origin->enter($credentials);
		} catch (\UnexpectedValueException $e) {
			throw new \UnexpectedValueException($e->getMessage(), $this->status, $e);
		}
	}

	public function exit(): User {
		try {
			return $this->origin->exit();
		} catch (\UnexpectedValueException $e) {
			throw new \UnexpectedValueException($e->getMessage(), $this->status, $e);
		}
	}
}
