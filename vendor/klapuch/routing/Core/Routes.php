<?php
declare(strict_types = 1);
namespace Klapuch\Routing;

interface Routes {
	/**
	 * The matched routes
	 * @return array
	 */
	public function matches(): array;
}