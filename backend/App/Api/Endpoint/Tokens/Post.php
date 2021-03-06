<?php
declare(strict_types = 1);

namespace Bulletpoint\Api\Endpoint\Tokens;

use Bulletpoint\Api\Http;
use Bulletpoint\Constraint;
use Bulletpoint\Domain\Access;
use Bulletpoint\Response;
use Klapuch\Application;
use Klapuch\Encryption;
use Klapuch\Output;
use Klapuch\Storage;
use Nette\Utils\Json;

final class Post implements Application\View {
	private const FACEBOOK_PROVIDER = 'facebook';
	private const GOOGLE_PROVIDER = 'google';
	private const SCHEMA = __DIR__ . '/schema/post.json';
	private const OAUTH_SCHEMA = __DIR__ . '/OAuth/schema/post.json';
	private const SCHEMAS = [
		self::FACEBOOK_PROVIDER => self::OAUTH_SCHEMA,
		self::GOOGLE_PROVIDER => self::OAUTH_SCHEMA,
	];
	private Application\Request $request;
	private Storage\Connection $connection;
	private Encryption\Cipher $cipher;

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
		$provider = $parameters['provider'] ?? null;
		$credentials = (new Constraint\StructuredJson(
			new \SplFileInfo(self::SCHEMAS[$provider] ?? self::SCHEMA),
		))->apply(Json::decode($this->request->body()->serialization()));
		if (in_array($provider, [self::FACEBOOK_PROVIDER, self::GOOGLE_PROVIDER], true)) {
			$entrance = new Access\OAuthEntrance(
				$provider,
				$this->connection,
				new Access\OAuthRequest($provider, $credentials['login']),
			);
		} else {
			$entrance = new Access\SecureEntrance($this->connection, $this->cipher);
		}
		$user = (new Http\ErrorEntrance(
			HTTP_FORBIDDEN,
			new Access\TokenEntrance($entrance),
		))->enter($credentials);
		return new Response\JsonResponse(
			new Application\PlainResponse(
				new Output\Json(['token' => $user->id(), 'expiration' => $user->properties()['expiration']]),
				[],
				HTTP_CREATED,
			),
		);
	}
}
