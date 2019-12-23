<?php
declare(strict_types = 1);

namespace Bulletpoint\Api\Endpoint\RefreshTokens;

use Bulletpoint\Api\Http;
use Bulletpoint\Constraint;
use Bulletpoint\Domain\Access;
use Bulletpoint\Response;
use Klapuch\Application;
use Klapuch\Output;
use Nette\Utils\Json;

final class Post implements Application\View {
	private const SCHEMA = __DIR__ . '/schema/post.json';

	private Application\Request $request;

	public function __construct(Application\Request $request) {
		$this->request = $request;
	}

	/**
	 * @throws \UnexpectedValueException
	 */
	public function response(array $parameters): Application\Response {
		$user = (new Http\ErrorEntrance(
			HTTP_FORBIDDEN,
			new Access\RefreshingEntrance(),
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
