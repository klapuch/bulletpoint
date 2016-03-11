<?php
namespace Bulletpoint\Fake;

use Bulletpoint\Core\Access;

final class Comparison implements Access\Comparison {
	public function areSame(string $origin, string $passed): bool {
		return $origin === $passed;
	}
}