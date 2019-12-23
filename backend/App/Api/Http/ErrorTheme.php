<?php
declare(strict_types = 1);

namespace Bulletpoint\Api\Http;

use Bulletpoint\Domain;
use Klapuch\Output;

final class ErrorTheme implements Domain\Theme {
	private int $status;

	private \Bulletpoint\Domain\Theme $origin;

	public function __construct(int $status, Domain\Theme $origin) {
		$this->status = $status;
		$this->origin = $origin;
	}

	/**
	 * @param \Klapuch\Output\Format $format
	 * @throws \UnexpectedValueException
	 * @return \Klapuch\Output\Format
	 */
	public function print(Output\Format $format): Output\Format {
		try {
			return $this->origin->print($format);
		} catch (\UnexpectedValueException $e) {
			throw new \UnexpectedValueException($e->getMessage(), $this->status, $e);
		}
	}

	/**
	 * @throws \UnexpectedValueException
	 * @param mixed[] $theme
	 */
	public function change(array $theme): void {
		try {
			$this->origin->change($theme);
		} catch (\UnexpectedValueException $e) {
			throw new \UnexpectedValueException($e->getMessage(), $this->status, $e);
		}
	}

	public function star(): void {
		try {
			$this->origin->star();
		} catch (\UnexpectedValueException $e) {
			throw new \UnexpectedValueException($e->getMessage(), $this->status, $e);
		}
	}

	public function unstar(): void {
		try {
			$this->origin->unstar();
		} catch (\UnexpectedValueException $e) {
			throw new \UnexpectedValueException($e->getMessage(), $this->status, $e);
		}
	}
}
