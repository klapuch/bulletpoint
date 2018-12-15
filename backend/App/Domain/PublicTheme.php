<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain;

use Klapuch\Output;

final class PublicTheme implements Theme {
	/** @var \Bulletpoint\Domain\Theme */
	private $origin;

	public function __construct(Theme $origin) {
		$this->origin = $origin;
	}

	public function print(Output\Format $format): Output\Format {
		return $this->origin->print($format)
			->adjusted('created_at', static function(string $datetime): string {
				return (new \DateTime($datetime))->format(\DateTime::ATOM);
			});
	}
}
