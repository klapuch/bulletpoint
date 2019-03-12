<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain\Access;

use Klapuch\Http;
use Klapuch\Sql;
use Klapuch\Storage;
use Nette\Utils\Json;

/**
 * Secure entrance for entering users to the system via oauth providers
 */
final class OAuthEntrance implements Entrance {
	/** @var \Klapuch\Storage\Connection */
	private $connection;

	/** @var Http\Request */
	private $request;

	public function __construct(Storage\Connection $connection, Http\Request $request) {
		$this->connection = $connection;
		$this->request = $request;
	}

	/**
	 * @param array $credentials
	 * @throws \UnexpectedValueException
	 * @return \Bulletpoint\Domain\Access\User
	 */
	public function enter(array $credentials): User {
		['id' => $id, 'email' => $email] = $this->credentials();
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

	private function credentials(): array {
		$response = $this->request->send();
		return Json::decode($response->body(), Json::FORCE_ARRAY);
	}

	public function exit(): User {
		return new Guest();
	}
}
