<?php
declare(strict_types = 1);
namespace Klapuch\Routing;

use Klapuch\Uri;

/**
 * Routes matching only path parts
 */
final class PathRoutes implements Routes {
	private $origin;
	private $uri;

	public function __construct(Routes $origin, Uri\Uri $uri) {
		$this->origin = $origin;
		$this->uri = $uri;
	}

	public function matches(): array {
		$matches = array_filter(
			$this->patterns($this->origin->matches()),
			function(string $source): bool {
				return (bool) preg_match(
					sprintf('~^%s$~i', strtok($source, ' ')),
					$this->uri->path()
				);
			},
			ARRAY_FILTER_USE_KEY
		);
		return array_intersect_key(
			$this->origin->matches(),
			array_flip(
				array_filter(
					array_filter(
						array_keys($this->origin->matches()),
						function(string $match) use ($matches): bool {
							return array_search($match, $matches) !== false;
						}
					)
				)
			)
		);
	}

	/**
	 * All the variable placeholders replaced by patterns
	 * @param array $matches
	 * @return array
	 */
	private function patterns(array $matches): array {
		return array_combine(
			array_map([$this, 'filling'], array_keys($matches)),
			array_keys($matches)
		);
	}

	// @codingStandardsIgnoreStart Used by array_map
	/**
	 * Filling variable placeholders
	 * @param string $match
	 * @return string
	 */
	private function filling(string $match): string {
		return implode(
			'/',
			array_map(
				[$this, 'pattern'],
				array_map(
					function(string $part): string {
						return parse_url($part, PHP_URL_PATH);
					},
					explode('/', $match)
				)
			)
		);
	}

	/**
	 * Placeholder replaced by pattern
	 * @param string $part
	 * @return string
	 */
	private function pattern(string $part): string {
		return preg_replace(
			'~{.+}~',
			rtrim(explode(' ', $part)[1] ?? '[\w\d]+', '}'),
			$part
		);
	} // @codingStandardsIgnoreEnd
}
