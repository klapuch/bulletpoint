<?php
declare(strict_types = 1);

namespace Bulletpoint\Api\Endpoint\Theme\ContributedBulletpoints;

use Bulletpoint\Domain;
use Bulletpoint\Domain\Access;
use Bulletpoint\Domain\Contribution;
use Bulletpoint\Response;
use Klapuch\Application;
use Klapuch\Output;
use Klapuch\Storage;

final class Get implements Application\View {
	public const SCHEMA = __DIR__ . '/schema/get.json';
	private Storage\Connection $connection;
	private Access\User $user;

	public function __construct(Storage\Connection $connection, Access\User $user) {
		$this->connection = $connection;
		$this->user = $user;
	}

	/**
	 * @throws \UnexpectedValueException
	 */
	public function response(array $parameters): Application\Response {
		$bulletpoints = new Contribution\ThemeBulletpoints(
			$parameters['theme_id'],
			$this->connection,
			$this->user,
		);
		return new Response\JsonResponse(
			new Application\PlainResponse(
				(new Output\JsonPrintedObjects(
					static fn (Domain\Bulletpoint $bulletpoint, Output\Format $format): Output\Format => $bulletpoint->print($format),
					...iterator_to_array($bulletpoints->all()),
				)),
				['X-Total-Count' => $bulletpoints->count()],
			),
		);
	}
}
