<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain;

use Klapuch\Iterator;

final class PublicBulletpoints implements Bulletpoints {
	private Bulletpoints $origin;

	public function __construct(Bulletpoints $origin) {
		$this->origin = $origin;
	}

	public function add(array $bulletpoint): void {
		$this->origin->add($bulletpoint);
	}

	public function all(): \Iterator {
		return new Iterator\Mapped(
			$this->origin->all(),
			static fn(Bulletpoint $bulletpoint): Bulletpoint => new PublicBulletpoint($bulletpoint),
		);
	}

	public function count(): int {
		return $this->origin->count();
	}
}
