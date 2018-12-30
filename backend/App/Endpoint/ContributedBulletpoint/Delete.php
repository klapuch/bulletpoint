<?php
declare(strict_types = 1);

namespace Bulletpoint\Endpoint\ContributedBulletpoint;

use Bulletpoint\Domain\Access;
use Bulletpoint\Domain\Contribution;
use Bulletpoint\Response;
use Klapuch\Application;
use Klapuch\Storage;

final class Delete implements Application\View {
	/** @var \Klapuch\Storage\Connection */
	private $connection;

	/** @var Access\User */
	private $user;

	public function __construct(Storage\Connection $connection, Access\User $user) {
		$this->connection = $connection;
		$this->user = $user;
	}

	/**
	 * @throws \UnexpectedValueException
	 */
	public function response(array $parameters): Application\Response {
		(new Contribution\StoredBulletpoint($parameters['id'], $this->user, $this->connection))->delete();
		return new Response\EmptyResponse();
	}
}
