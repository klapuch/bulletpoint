<?php
declare(strict_types = 1);
namespace Klapuch\Routing;

/**
 * Route handling correctly with types
 */
final class TypedRoute implements Route {
	private $origin;

	public function __construct(Route $origin) {
		$this->origin = $origin;
	}

	public function parameters(): array {
		$parameters = $this->origin->parameters();
		return array_map('intval', array_filter($parameters, 'is_numeric')) + $parameters;
	}
}