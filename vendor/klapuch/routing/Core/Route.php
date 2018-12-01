<?php
declare(strict_types = 1);
namespace Klapuch\Routing;

interface Route {
	/**
	 * The parameters e.g. [1, read]
	 * @return array
	 */
	public function parameters(): array;
}