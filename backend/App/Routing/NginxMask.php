<?php
declare(strict_types = 1);

namespace Bulletpoint\Routing;

use Klapuch\Routing;

/**
 * Mask from nginx
 */
final class NginxMask implements Routing\Mask {
	public function parameters(): array {
		$result = [];
		if (isset($_SERVER['ROUTE_PARAM_QUERY'])) {
			parse_str($_SERVER['ROUTE_PARAM_QUERY'], $result);
		}
		return $result;
	}
}
