<?php
declare(strict_types = 1);

namespace Bulletpoint\Api\Endpoint\Bulletpoint;

use Bulletpoint\Domain;
use Bulletpoint\Domain\Access;
use Klapuch\Application;
use Klapuch\Storage;

final class Delete implements Application\View {
	/** @var \Klapuch\Storage\Connection */
	private $connection;

	public function __construct(Storage\Connection $connection) {
		$this->connection = $connection;
	}

	/**
	 * @throws \UnexpectedValueException
	 */
	public function response(array $parameters): Application\Response {
		(new Domain\StoredBulletpoint($parameters['id'], $this->connection, new Access\FakeUser()))->delete();
		return new Application\EmptyResponse();
	}
}
