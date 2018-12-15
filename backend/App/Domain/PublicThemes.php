<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain;

use Klapuch\Dataset;
use Klapuch\Iterator;

final class PublicThemes implements Themes {
	/** @var \Bulletpoint\Domain\Themes */
	private $origin;

	public function __construct(Themes $origin) {
		$this->origin = $origin;
	}

	public function create(array $theme): int {
		return $this->origin->create($theme);
	}

	public function all(Dataset\Selection $selection): \Iterator {
		return new Iterator\Mapped(
			$this->origin->all($selection),
			static function(Theme $theme): Theme {
				return new PublicTheme($theme);
			}
		);
	}

	public function count(Dataset\Selection $selection): int {
		return $this->origin->count($selection);
	}
}
