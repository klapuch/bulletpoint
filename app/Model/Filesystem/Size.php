<?php
namespace Bulletpoint\Model\Filesystem;

final class Size {
	private $width;
	private $height;

	public function __construct(int $width, int $height) {
		$this->width = $width;
		$this->height = $height;
	}

	public function width(): int {
		return $this->width;
	}

	public function height(): int {
		return $this->height;
	}

	public function __toString() {
		return sprintf(
			'height=%d width=%d',
			$this->height,
			$this->width
		);
	}
}