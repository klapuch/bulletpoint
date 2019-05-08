<?php
declare(strict_types = 1);

namespace Bulletpoint\Api\Endpoint\ContributedBulletpoint;

use Bulletpoint\Domain\Access;
use Bulletpoint\Domain\Contribution;
use Bulletpoint\Response;
use Klapuch\Application;
use Klapuch\Output;
use Klapuch\Storage;

final class Get implements Application\View {
	public const SCHEMA = __DIR__ . '/schema/get.json';

	/** @var \Klapuch\Storage\Connection */
	private $connection;

	/** @var \Bulletpoint\Domain\Access\User */
	private $user;

	public function __construct(Storage\Connection $connection, Access\User $user) {
		$this->connection = $connection;
		$this->user = $user;
	}

	/**
	 * @throws \UnexpectedValueException
	 */
	public function response(array $parameters): Application\Response {
		return new Response\JsonResponse(
			new Application\PlainResponse(
				(new Contribution\ExistingBulletpoint(
					new Contribution\StoredBulletpoint($parameters['id'], $this->user, $this->connection),
					$this->user,
					$parameters['id'],
					$this->connection,
				))->print(new Output\Json()),
			),
		);
	}
}
