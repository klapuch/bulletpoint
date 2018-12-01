<?php
declare(strict_types = 1);
namespace Klapuch\Routing;

/**
 * Shortcut replacements for routes
 */
final class ShortcutRoutes implements Routes {
	private const SHORTCUTS = [
		':int' => '\d+',
		':id' => '[1-9][0-9]*',
		':string' => '\w*\d*',
	];
	private $origin;

	public function __construct(Routes $origin) {
		$this->origin = $origin;
	}

	public function matches(): array {
		return $this->replacements($this->origin->matches());
	}

	private function replacements(array $matches): array {
		return array_combine(
			array_map(
				function(string $route): string {
					return str_replace(
						array_keys(self::SHORTCUTS),
						self::SHORTCUTS,
						$route
					);
				},
				array_keys($matches)
			),
			$matches
		);
	}
}
