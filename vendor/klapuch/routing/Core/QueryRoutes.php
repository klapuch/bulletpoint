<?php
declare(strict_types = 1);
namespace Klapuch\Routing;

use Klapuch\Uri;

/**
 * Routes matching only query parts
 */
final class QueryRoutes implements Routes {
	private $origin;
	private $uri;

	public function __construct(Routes $origin, Uri\Uri $uri) {
		$this->origin = $origin;
		$this->uri = $uri;
	}

	public function matches(): array {
		return array_filter(
			$this->origin->matches(),
			function(string $match): bool {
				parse_str((string) parse_url($match, PHP_URL_QUERY), $query);
				return $this->includes($this->uri, $query, $this->defaults($query));
			},
			ARRAY_FILTER_USE_KEY
		);
	}

	/**
	 * Does the URI includes given query within defaults?
	 * @param \Klapuch\Uri\Uri $uri
	 * @param array $query
	 * @param array $defaults
	 * @return bool
	 */
	private function includes(Uri\Uri $uri, array $query, array $defaults): bool {
		return array_intersect_assoc($defaults + $uri->query(), $defaults + $query) == $defaults + $query; // == intentionally because of order
	}

	/**
	 * Default values extracted from query - everything in brace
	 * @param array $query
	 * @return array
	 */
	private function defaults(array $query): array {
		return array_map(
			function(string $parameter): string {
				return substr($parameter, 1, -1);
			},
			preg_grep('~\(.+\)$~', $query)
		);
	}
}
