<?php
declare(strict_types = 1);
namespace Klapuch\Routing;

use Klapuch\Uri;

/**
 * Default root for Route
 */
final class DefaultRoute implements Route {
	private const SEPARATOR = '/';
	private $source;
	private $uri;

	public function __construct(string $source, Uri\Uri $uri) {
		$this->source = $source;
		$this->uri = $uri;
	}

	public function parameters(): array {
		return $this->query($this->source, $this->uri) + $this->path($this->source, $this->uri);
	}

	private function path(string $source, Uri\Uri $uri): array {
		$sources = explode(self::SEPARATOR, parse_url($source, PHP_URL_PATH));
		$parameters = array_diff(explode(self::SEPARATOR, $uri->path()), $sources);
		return array_combine(
			preg_replace(
				'~{|}|\s\S+~',
				'',
				array_intersect_key($sources, $parameters)
			),
			$parameters
		);
	}

	private function query(string $source, Uri\Uri $uri): array {
		parse_str((string) parse_url($source, PHP_URL_QUERY), $query);
		return $uri->query() + array_map(
			function(string $part): string {
				return substr($part, 1, -1);
			},
			preg_grep('~^\(.*\)$~', $query)
		) + $query;
	}
}