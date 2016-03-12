<?php
namespace Bulletpoint\Model\Constraint;

use Bulletpoint\Model\Access;

interface Sins {
	public function iterate(): \Iterator;
	public function give(
		Access\Identity $sinner,
		\DateTime $expiration,
		string $reason = null
	);
	public function byIdentity(Access\Identity $identity): Sin;
}