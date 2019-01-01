<?php
declare(strict_types = 1);

namespace Bulletpoint\Endpoint\Bulletpoint;

use Bulletpoint\Constraint;
use Bulletpoint\Domain;
use Klapuch\Application;
use Klapuch\Storage;
use Klapuch\Validation;
use Nette\Utils\Json;

final class Put implements Application\View {
	public const SCHEMA = __DIR__ . '/schema/put.json';

	/** @var \Klapuch\Application\Request */
	private $request;

	/** @var \Klapuch\Storage\Connection */
	private $connection;

	public function __construct(Application\Request $request, Storage\Connection $connection) {
		$this->connection = $connection;
		$this->request = $request;
	}

	/**
	 * @throws \UnexpectedValueException
	 */
	public function response(array $parameters): Application\Response {
		(new Domain\ExistingBulletpoint(
			new Domain\StoredBulletpoint($parameters['id'], $this->connection),
			$parameters['id'],
			$this->connection
		))->edit(
			(new Validation\ChainedRule(
				new Constraint\StructuredJson(new \SplFileInfo(self::SCHEMA)),
				new Constraint\BulletpointRule(),
			))->apply(Json::decode($this->request->body()->serialization()))
		);
		return new Application\EmptyResponse();
	}
}
