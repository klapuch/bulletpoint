<?php
declare(strict_types = 1);

namespace Bulletpoint\Api\Endpoint\ContributedBulletpoint;

use Bulletpoint\Constraint;
use Bulletpoint\Domain\Access;
use Bulletpoint\Domain\Contribution;
use Klapuch\Application;
use Klapuch\Storage;
use Klapuch\Validation;
use Nette\Utils\Json;

final class Put implements Application\View {
	public const SCHEMA = __DIR__ . '/schema/put.json';
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
		(new Contribution\ExistingBulletpoint(
			new Contribution\StoredBulletpoint($parameters['id'], $this->user, $this->connection),
			$this->user,
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
