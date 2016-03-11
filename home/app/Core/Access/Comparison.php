<?php
namespace Bulletpoint\Core\Access;

interface Comparison {
	public function areSame(string $origin, string $passed): bool;
}