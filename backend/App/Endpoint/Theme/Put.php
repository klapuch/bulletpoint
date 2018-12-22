<?php
declare(strict_types = 1);

namespace Bulletpoint\Endpoint\Theme;

use Bulletpoint\Constraint;
use Bulletpoint\Domain;
use Bulletpoint\Response;
use Klapuch\Application;
use Klapuch\Internal;
use Klapuch\Storage;

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
		(new Domain\ExistingTheme(
			new Domain\StoredTheme($parameters['id'], $this->connection),
			$parameters['id'],
			$this->connection
		))->change(
			(new Constraint\StructuredJson(
				new \SplFileInfo(self::SCHEMA)
			))->apply((new Internal\DecodedJson($this->request->body()->serialization()))->values())
		);
		return new Response\EmptyResponse();
	}
}
