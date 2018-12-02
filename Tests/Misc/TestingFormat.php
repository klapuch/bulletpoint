<?php
declare(strict_types = 1);

namespace Bulletpoint\Misc;

use Klapuch\Output;

final class TestingFormat implements Output\Format {
	/** @var \Klapuch\Output\Format */
	private $origin;

	public function __construct(Output\Format $origin) {
		$this->origin = $origin;
	}

	public function with($tag, $content = null): Output\Format {
		return $this->origin->with($tag, $content);
	}

	public function serialization(): string {
		return $this->origin->serialization();
	}

	public function adjusted($tag, callable $adjustment): Output\Format {
		return $this->origin->adjusted($tag, $adjustment);
	}

	public function raw(): array {
		return json_decode($this->serialization(), true);
	}
}
