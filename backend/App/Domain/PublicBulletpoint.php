<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain;

use Klapuch\Output;

final class PublicBulletpoint implements Bulletpoint {
	private Bulletpoint $origin;

	public function __construct(Bulletpoint $origin) {
		$this->origin = $origin;
	}

	/**
	 * @param \Klapuch\Output\Format $format
	 * @throws \UnexpectedValueException
	 * @return \Klapuch\Output\Format
	 */
	public function print(Output\Format $format): Output\Format {
		return $this->origin->print($format)
			->adjusted('created_at', static fn(string $datetime): string => (new \DateTime($datetime))->format(\DateTime::ATOM));
	}

	public function edit(array $bulletpoint): void {
		$this->origin->edit($bulletpoint);
	}

	public function delete(): void {
		$this->origin->delete();
	}

	public function rate(int $point): void {
		$this->origin->rate($point);
	}
}
