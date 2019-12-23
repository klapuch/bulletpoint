<?php
declare(strict_types = 1);

namespace Bulletpoint\View;

use Bulletpoint\Http;
use Klapuch\Application;
use Klapuch\Output;

/**
 * View showing response by used role
 */
final class AuthenticatedView implements Application\View {
	private Application\View $origin;
	private Http\Role $role;

	public function __construct(Application\View $origin, Http\Role $role) {
		$this->origin = $origin;
		$this->role = $role;
	}

	public function response(array $input): Application\Response {
		if ($this->role->allowed())
			return $this->origin->response($input);
		return new class implements Application\Response {
			public function body(): Output\Format {
				return new Output\Json(['message' => t('response.not.allowed')]);
			}

			public function headers(): array {
				return ['Content-Type' => 'application/json; charset=utf8'];
			}

			public function status(): int {
				return HTTP_FORBIDDEN;
			}
		};
	}
}
