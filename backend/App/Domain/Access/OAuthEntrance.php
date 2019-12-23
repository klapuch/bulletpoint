<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain\Access;

use Klapuch\Http;
use Klapuch\Storage;
use Nette\Utils\Json;

/**
 * Secure entrance for entering users to the system via oauth providers
 */
final class OAuthEntrance implements Entrance {
	private string $provider;
	private Storage\Connection $connection;
	private Http\Request $request;

	public function __construct(string $provider, Storage\Connection $connection, Http\Request $request) {
		$this->provider = $provider;
		$this->connection = $connection;
		$this->request = $request;
	}

	/**
	 * @param mixed[] $credentials
	 * @throws \UnexpectedValueException
	 */
	public function enter(array $credentials): User {
		['id' => $id, 'email' => $email] = Json::decode($this->request->send()->body(), Json::FORCE_ARRAY);
		$user = (new Storage\TypedQuery(
			$this->connection,
			'SELECT * FROM create_third_party_user(:provider, :id::text, :email) AS record',
			['provider' => $this->provider, 'id' => $id, 'email' => $email],
		))->row();
		return new ConstantUser((string) $user['id'], $user);
	}

	public function exit(): User {
		return new Guest();
	}
}
