<?php
declare(strict_types = 1);

namespace Bulletpoint\Endpoint\Tokens;

use Bulletpoint\Constraint;
use Bulletpoint\Domain\Access;
use Bulletpoint\Misc;
use Bulletpoint\Response;
use Klapuch\Application;
use Klapuch\Encryption;
use Klapuch\Output;
use Klapuch\Storage;
use Nette\Utils\Json;

final class Post implements Application\View {
	private const SCHEMA = __DIR__ . '/schema/post.json';

	/** @var \Klapuch\Application\Request */
	private $request;

	/** @var \Klapuch\Storage\Connection */
	private $connection;

	/** @var \Klapuch\Encryption\Cipher */
	private $cipher;

	public function __construct(
		Application\Request $request,
		Storage\Connection $connection,
		Encryption\Cipher $cipher
	) {
		$this->request = $request;
		$this->connection = $connection;
		$this->cipher = $cipher;
	}

	/**
	 * @throws \UnexpectedValueException
	 */
	public function response(array $parameters): Application\Response {
		$user = (new Access\HarnessedEntrance(
			new Access\TokenEntrance(
				new Access\VerifiedEntrance(
					$this->connection,
					new Access\SecureEntrance(
						$this->connection,
						$this->cipher,
					),
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
