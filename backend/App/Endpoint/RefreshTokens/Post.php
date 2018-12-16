<?php
declare(strict_types = 1);

namespace Bulletpoint\Endpoint\RefreshTokens;

use Bulletpoint\Domain\Access;
use Bulletpoint\Misc;
use Bulletpoint\Response;
use Klapuch\Application;
use Klapuch\Internal;
use Klapuch\Output;

final class Post implements Application\View {
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
		))->enter((new Internal\DecodedJson($this->request->body()->serialization()))->values());
		return new Response\JsonResponse(
			new Response\PlainResponse(
				new Output\Json(['token' => $seeker->id(), 'expiration' => $seeker->properties()['expiration']]),
				[],
				HTTP_CREATED
			)
		);
	}
}
