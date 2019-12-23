<?php
declare(strict_types = 1);

namespace Bulletpoint\Api\Endpoint\Bulletpoint;

use Bulletpoint\Domain;
use Bulletpoint\Domain\Access;
use Bulletpoint\Response;
use Klapuch\Application;
use Klapuch\Output;
use Klapuch\Storage;

final class Get implements Application\View {
	public const SCHEMA = __DIR__ . '/schema/get.json';

	private Storage\Connection $connection;

	public function __construct(Storage\Connection $connection) {
		$this->connection = $connection;
	}

	/**
	 * @throws \UnexpectedValueException
	 */
	public function response(array $parameters): Application\Response {
		return new Response\JsonResponse(
			new Application\PlainResponse(
				(new Domain\PublicBulletpoint(
					new Domain\ExistingBulletpoint(
						new Domain\StoredBulletpoint($parameters['id'], $this->connection, new Access\FakeUser()),
						$parameters['id'],
						$this->connection,
					),
				))->print(new Output\Json()),
			),
		);
	}
}
