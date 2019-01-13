<?php
declare(strict_types = 1);

namespace Bulletpoint\Endpoint\Theme;

use Bulletpoint\Domain;
use Bulletpoint\Domain\Access;
use Bulletpoint\Response;
use Klapuch\Application;
use Klapuch\Output;
use Klapuch\Storage;

final class Get implements Application\View {
	public const SCHEMA = __DIR__ . '/schema/get.json';

	/** @var \Klapuch\Storage\Connection */
	private $connection;

	public function __construct(Storage\Connection $connection) {
		$this->connection = $connection;
	}

	/**
	 * @throws \UnexpectedValueException
	 */
	public function response(array $parameters): Application\Response {
		return new Response\JsonResponse(
			new Application\PlainResponse(
				(new Domain\ExistingTheme(
					new Domain\PublicTheme(
						new Domain\StoredTheme(
							$parameters['id'],
							$this->connection,
							new Access\FakeUser(),
						)),
					$parameters['id'],
					$this->connection
				))->print(new Output\Json())
			)
		);
	}
}
