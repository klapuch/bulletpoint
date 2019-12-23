<?php
declare(strict_types = 1);

namespace Bulletpoint\Api\Endpoint\Bulletpoint;

use Bulletpoint\Constraint;
use Bulletpoint\Domain;
use Bulletpoint\Domain\Access;
use Klapuch\Application;
use Klapuch\Storage;
use Nette\Utils\Json;

final class Patch implements Application\View {
	public const SCHEMA = __DIR__ . '/schema/patch.json';
	private Application\Request $request;
	private Storage\Connection $connection;
	private Access\User $user;

	public function __construct(Application\Request $request, Storage\Connection $connection, Access\User $user) {
		$this->connection = $connection;
		$this->request = $request;
		$this->user = $user;
	}

	/**
	 * @throws \UnexpectedValueException
	 */
	public function response(array $parameters): Application\Response {
		$bulletpoint = new Domain\ExistingBulletpoint(
			new Domain\StoredBulletpoint($parameters['id'], $this->connection, $this->user),
			$parameters['id'],
			$this->connection,
		);
		$payload = (new Constraint\StructuredJson(
			new \SplFileInfo(self::SCHEMA),
		))->apply(Json::decode($this->request->body()->serialization()));
		if (isset($payload['rating']['user'])) {
			$bulletpoint->rate($payload['rating']['user']);
		}
		return new Application\EmptyResponse();
	}
}
