<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain;

use Klapuch\Output;

final class PublicTheme implements Theme {
	private Theme $origin;

	public function __construct(Theme $origin) {
		$this->origin = $origin;
	}

	public function print(Output\Format $format): Output\Format {
		return $this->origin->print($format)
			->adjusted('created_at', static fn(string $datetime): string => (new \DateTime($datetime))->format(\DateTime::ATOM))
			->adjusted('starred_at', static function(?string $datetime): ?string {
				if ($datetime === null)
					return null;
				return (new \DateTime($datetime))->format(\DateTime::ATOM);
			});
	}

	public function change(array $theme): void {
		$this->origin->change($theme);
	}

	public function star(): void {
		$this->origin->star();
	}

	public function unstar(): void {
		$this->origin->unstar();
	}
}
