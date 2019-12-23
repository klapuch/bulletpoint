<?php
declare(strict_types = 1);

namespace Bulletpoint\Api\Endpoint\Users\Me;

use Bulletpoint\Constraint;
use Bulletpoint\Domain\Access;
use Klapuch\Application;
use Klapuch\Storage;
use Nette\Utils\Json;

final class Put implements Application\View {
	public const SCHEMA = __DIR__ . '/../schema/put.json';
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
		(new Access\RegisteredUser(
			(int) $this->user->id(),
			$this->connection,
		))->edit(
			(new Constraint\StructuredJson(
				new \SplFileInfo(self::SCHEMA),
			))->apply(Json::decode($this->request->body()->serialization())),
		);
		return new Application\EmptyResponse();
	}
}
