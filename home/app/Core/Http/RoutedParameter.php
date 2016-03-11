<?php
namespace Bulletpoint\Core\Http;

final class RoutedParameter {
	const PARAMETERS = 2;
	private $url;

	public function __construct(Address $url) {
		$this->url = $url;
	}

	public function parameters(): array {
		return array_values(
			array_filter(
				array_slice(
					$this->url->pathname(),
					self::PARAMETERS
					), function($pathname) {
						return strlen(trim($pathname));
				}
			)
		);
	}
}