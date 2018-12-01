<?php
declare(strict_types = 1);
namespace Klapuch\Internal;

final class CspHeader {
	private $directives;
	private $nonce;

	public function __construct(array $directives) {
		$this->directives = $directives;
	}

	public function nonce(): string {
		if ($this->nonce === null)
			$this->nonce = base64_encode(random_bytes(16));
		return $this->nonce;
	}

	public function __toString(): string {
		if (!$this->directives)
			return '';
		return 'Content-Security-Policy: ' . $this->withNonce(
			$this->format($this->directives)
		);
	}

	private function format(array $directives): string {
		return implode(
			'; ',
			array_map(
				function(string $directive, string $constraint): string {
					return $directive . ' ' . $constraint;
				},
				array_keys($directives),
				$directives
			)
		);
	}

	private function withNonce(string $header): string {
		return str_replace('nonce', 'nonce-' . $this->nonce(), $header);
	}
}