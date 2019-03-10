<?php
declare(strict_types = 1);

namespace Bulletpoint\Endpoint\FacebookTokens;

use Bulletpoint\Constraint;
use Bulletpoint\Domain\Access;
use Bulletpoint\Misc;
use Bulletpoint\Response;
use GuzzleHttp;
use Klapuch\Application;
use Klapuch\Output;
use Klapuch\Storage;
use Nette\Utils\Json;

final class Post implements Application\View {
	private const SCHEMA = __DIR__ . '/schema/post.json';

	/** @var \Klapuch\Application\Request */
	private $request;

	/** @var \Klapuch\Storage\Connection */
	private $connection;

	public function __construct(
		Application\Request $request,
		Storage\Connection $connection
	) {
		$this->request = $request;
		$this->connection = $connection;
	}

	/**
	 * @throws \UnexpectedValueException
	 */
	public function response(array $parameters): Application\Response {
		$user = (new Access\HarnessedEntrance(
			new Access\TokenEntrance(
				new Access\FacebookEntrance(
					$this->connection,
					new GuzzleHttp\Client(),
				),
			),
			new Misc\ApiErrorCallback(HTTP_FORBIDDEN),
		))->enter(
			(new Constraint\StructuredJson(
				new \SplFileInfo(self::SCHEMA),
			))->apply(Json::decode($this->request->body()->serialization())),
		);
		return new Response\JsonResponse(
			new Application\PlainResponse(
				new Output\Json(['token' => $user->id(), 'expiration' => $user->properties()['expiration']]),
				[],
				HTTP_CREATED,
			),
		);
	}
}
