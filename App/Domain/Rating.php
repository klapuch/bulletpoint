<?php declare(strict_types = 1);

namespace Bulletpoint\Domain;

interface Rating {
	public function rate(int $points): void;
}