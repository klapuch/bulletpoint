<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain;

use Klapuch\Iterator;

final class PublicBulletpoints implements Bulletpoints {
	/** @var \Bulletpoint\Domain\Bulletpoints */
	private $origin;

	public function __construct(Bulletpoints $origin) {
		$this->origin = $origin;
	}

	public function add(array $bulletpoint): void {
		$this->origin->add($bulletpoint);
	}

	public function all(): \Iterator {
		return new Iterator\Mapped(
			$this->origin->all(),
			static function(Bulletpoint $bulletpoint): Bulletpoint {
				return new PublicBulletpoint($bulletpoint);
			},
		);
	}

	public function count(): int {
		return $this->origin->count();
	}
}
