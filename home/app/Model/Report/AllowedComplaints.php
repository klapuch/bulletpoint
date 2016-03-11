<?php
namespace Bulletpoint\Model\Report;

final class AllowedComplaints implements Complaints {
	private $origin;
	const ALLOWED_REASONS = [
		'vulgarita',
		'spam'
	];
	const DEFAULT_REASON = 'Jiné';

	public function __construct(Complaints $origin) {
		$this->origin = $origin;
	}

	public function iterate(): \Iterator {
		return $this->origin->iterate();
	}

	public function settle(Target $target) {
		$this->origin->settle($target);
	}

	public function complain(Target $target, string $reason) {
		if(!$this->isReasonAllowed($reason))
			$reason = self::DEFAULT_REASON;
		$this->origin->complain($target, $reason);
	}

	private function isReasonAllowed(string $reason): bool {
		return in_array(
			mb_strtolower($reason),
			array_map('mb_strtolower', self::ALLOWED_REASONS)
		);
	}
}