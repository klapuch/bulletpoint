<?php
declare(strict_types = 1);

namespace Bulletpoint\Api\Endpoint\Bulletpoint;

use Bulletpoint\Constraint;
use Bulletpoint\Domain;
use Bulletpoint\Domain\Access;
use Klapuch\Application;
use Klapuch\Storage;
use Klapuch\Validation;
use Nette\Utils\Json;

final class Put implements Application\View {
	public const SCHEMA = __DIR__ . '/schema/put.json';
	private Application\Request $request;
	private Storage\Connection $connection;

	public function __construct(Application\Request $request, Storage\Connection $connection) {
		$this->connection = $connection;
		$this->request = $request;
	}

	/**
	 * @throws \UnexpectedValueException
	 */
	public function response(array $parameters): Application\Response {
		(new Domain\ExistingBulletpoint(
			new Domain\StoredBulletpoint($parameters['id'], $this->connection, new Access\FakeUser()),
			$parameters['id'],
			$this->connection,
		))->edit(
			(new Validation\ChainedRule(
				new Constraint\StructuredJson(new \SplFileInfo(self::SCHEMA)),
				new Constraint\Bulletpoint\Rule($this->connection),
			))->apply(Json::decode($this->request->body()->serialization())),
		);
		return new Application\EmptyResponse();
	}
}
