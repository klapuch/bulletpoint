<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain\Access;

use GuzzleHttp;
use Klapuch\Sql;
use Klapuch\Storage;
use Nette\Utils\Json;

/**
 * Secure entrance for entering users to the system via oauth providers
 */
final class OAuthEntrance implements Entrance {
	private const GOOGLE_ENDPOINT = 'https://www.googleapis.com/oauth2/v3/userinfo';
	private const FACEBOOK_ENDPOINT = 'https://graph.facebook.com/v2.3/me';
	private const FIELDS = ['email'];

	/** @var \Klapuch\Storage\Connection */
	private $connection;

	/** @var \GuzzleHttp\Client */
	private $http;

	public function __construct(Storage\Connection $connection, GuzzleHttp\Client $http) {
		$this->connection = $connection;
		$this->http = $http;
	}

	/**
	 * @param array $credentials
	 * @throws \UnexpectedValueException
	 * @return \Bulletpoint\Domain\Access\User
	 */
	public function enter(array $credentials): User {
		['id' => $id, 'email' => $email] = $this->credentials($credentials['login']);
		$user = (new Storage\BuiltQuery(
			$this->connection,
			(new Sql\PgInsertInto(
				'users',
				['facebook_id' => ':facebook_id', 'email' => ':email'],
				['facebook_id' => $id, 'email' => $email],
			))
				->onConflict(['facebook_id'])
				->doUpdate(['email' => ':email'])
				->returning(['*']),
		))->row();
		return new ConstantUser((string) $user['id'], $user);
	}

	private function credentials(string $accessToken): array {
		$response = $this->http->request(
			'GET',
			self::FACEBOOK_ENDPOINT,
			['query' => ['fields' => implode(',', self::FIELDS), 'access_token' => $accessToken]],
		);
		$body = (string) $response->getBody();
		if ($response->getStatusCode() !== HTTP_OK) {
			throw new \UnexpectedValueException('Error during retrieving Facebook token.', 0, new \Exception($body));
		}
		return Json::decode($body, Json::FORCE_ARRAY);
	}

	public function exit(): User {
		return new Guest();
	}
}
