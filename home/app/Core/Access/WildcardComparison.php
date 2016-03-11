<?php
namespace Bulletpoint\Core\Access;

final class WildcardComparison implements Comparison {
	const ALL = '*';
	const ONE = '?';

	public function areSame(string $origin, string $passed): bool {
		return (bool)preg_match(
			$this->toRegex($origin),
			$passed
		);
	}

	private function toRegex(string $origin): string {
		return sprintf(
			'~^%s\z~i',
			str_replace(
				self::ONE,
				'[\s\S]{0,1}',
				str_replace(
					self::ALL,
					'[\s\S]*',
					$origin
				)
			)
		);
	}
}