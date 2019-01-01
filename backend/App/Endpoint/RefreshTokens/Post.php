<?php
declare(strict_types = 1);

namespace Bulletpoint\Endpoint\RefreshTokens;

use Bulletpoint\Constraint;
use Bulletpoint\Domain\Access;
use Bulletpoint\Misc;
use Bulletpoint\Response;
use Klapuch\Application;
use Klapuch\Output;
use Nette\Utils\Json;

final class Post implements Application\View {
	private const SCHEMA = __DIR__ . '/schema/post.json';

	/** @var \Klapuch\Application\Request */
	private $request;

	public function __construct(Application\Request $request) {
		$this->request = $request;
	}

	/**
	 * @throws \UnexpectedValueException
	 */
	public function response(array $parameters): Application\Response {
		$seeker = (new Access\HarnessedEntrance(
			new Access\RefreshingEntrance(),
			new Misc\ApiErrorCallback(HTTP_FORBIDDEN)
		))->enter(
			(new Constraint\StructuredJson(
				new \SplFileInfo(self::SCHEMA)
			))->apply(Json::decode($this->request->body()->serialization()))
		);
		return new Response\JsonResponse(
			new Application\PlainResponse(
				new Output\Json(['token' => $seeker->id(), 'expiration' => $seeker->properties()['expiration']]),
				[],
				HTTP_CREATED
			)
		);
	}
}
