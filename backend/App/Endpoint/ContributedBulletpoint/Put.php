<?php
declare(strict_types = 1);

namespace Bulletpoint\Endpoint\ContributedBulletpoint;

use Bulletpoint\Constraint;
use Bulletpoint\Domain\Access;
use Bulletpoint\Domain\Contribution;
use Bulletpoint\Response;
use Klapuch\Application;
use Klapuch\Internal;
use Klapuch\Storage;
use Klapuch\Validation;

final class Put implements Application\View {
	public const SCHEMA = __DIR__ . '/schema/put.json';

	/** @var \Klapuch\Application\Request */
	private $request;

	/** @var \Klapuch\Storage\Connection */
	private $connection;

	/** @var \Bulletpoint\Domain\Access\User */
	private $user;

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
				new Constraint\BulletpointRule(),
			))->apply((new Internal\DecodedJson($this->request->body()->serialization()))->values())
		);
		return new Response\EmptyResponse();
	}
}
