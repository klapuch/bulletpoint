<?php
declare(strict_types = 1);

namespace Bulletpoint\Endpoint\StarredTags;

use Bulletpoint\Domain;
use Bulletpoint\Response;
use Klapuch\Application;
use Klapuch\Output;
use Klapuch\Storage;

final class Get implements Application\View {
	/** @var \Klapuch\Storage\Connection */
	private $connection;

	/** @var \Bulletpoint\Domain\Access\User */
	private $user;

	public function __construct(Storage\Connection $connection, Domain\Access\User $user) {
		$this->connection = $connection;
		$this->user = $user;
	}

	public function response(array $parameters): Application\Response {
		return new Response\JsonResponse(
			new Application\PlainResponse(
				new Output\Json(
					(new Domain\StarredTags(
						new Domain\StoredTags($this->connection),
						$this->connection,
						$this->user,
					))->all(),
				),
			),
		);
	}
}
