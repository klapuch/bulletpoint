<?php
declare(strict_types = 1);

namespace Bulletpoint\Api\Endpoint\Users\Me;

use Bulletpoint\Domain\Access;
use Bulletpoint\Response;
use Klapuch\Application;
use Klapuch\Output;
use Klapuch\Storage;

final class Get implements Application\View {
	private Storage\Connection $connection;
	private Access\User $user;

	public function __construct(Storage\Connection $connection, Access\User $user) {
		$this->connection = $connection;
		$this->user = $user;
	}

	public function response(array $parameters): Application\Response {
		return new Response\JsonResponse(
			new Application\PlainResponse(
				new Output\Json(
					(new Access\PubliclyPrivateUser(
						$this->user,
						$this->connection,
					))->properties(),
				),
			),
		);
	}
}
