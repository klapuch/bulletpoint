<?php
declare(strict_types = 1);
namespace Klapuch\Internal;

final class HeaderExtension implements Extension {
	private $headers;

	public function __construct(array $headers) {
		$this->headers = $headers;
	}

	public function improve(): void {
		$clear = array_filter(array_map('trim', $this->headers), 'strlen');
		(new RawHeaderExtension(
			array_map(
				function(string $field, string $value): string {
					return sprintf('%s:%s', $field, $value);
				},
				array_keys($clear),
				$clear
			)
		))->improve();
		$this->remove(array_keys(array_diff($this->headers, $clear)));
	}

	private function remove(array $headers): void {
		foreach ($headers as $header)
			header_remove($header);
	}
}