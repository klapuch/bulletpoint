<?php
namespace Bulletpoint\Model\Constraint;

use Bulletpoint\Core\Storage;
use Bulletpoint\Model\Access;
use Bulletpoint\Exception;

final class MySqlBans implements Bans {
	private $myself;
	private $database;

	public function __construct(
		Access\Identity $myself,
		Storage\Database $database
	) {
		$this->myself = $myself;
		$this->database = $database;
	}

	public function iterate(): \Iterator {
		$rows = $this->database->fetchAll(
			'SELECT banned_users.ID,
			reason,
			expiration,
			user_id,
			users.role,
			users.username
			FROM banned_users
			INNER JOIN users
			ON users.ID = banned_users.user_id
			WHERE canceled = 0 AND NOW() < expiration
			ORDER BY expiration ASC'
		);
		foreach($rows as $row) {
			yield new ConstantBan(
				new Access\ConstantIdentity(
					$row['user_id'],
					new Access\ConstantRole(
						$row['role'],
						new Access\MySqlRole($row['user_id'], $this->database)
					),
					$row['username']
				),
				$row['reason'],
				new \DateTime($row['expiration']),
				new MySqlBan($row['ID'], $this->database)
			);
		}
	}

	public function give(
		Access\Identity $sinner,
		\DateTime $expiration,
		string $reason = null
	) {
		if($sinner->id() === $this->myself->id())
			throw new \LogicException('Ban nemůžeš udělit sám sobě.');
		elseif($this->isPast($expiration))
			throw new \LogicException('Ban můžeš dát pouze na budoucí období.');
		$this->database->query(
			'INSERT INTO banned_users (user_id, reason, expiration, author_id)
			VALUES (?, ?, ?, ?)',
			[
				$sinner->id(),
				$reason,
				current($expiration),
				$this->myself->id()
			]
		);
	}

	public function byIdentity(Access\Identity $identity): Ban {
		$ban = $this->database->fetch(
			'SELECT ID, reason, expiration, user_id
			FROM banned_users
			WHERE user_id = ? AND canceled = 0 AND expiration > NOW()
			ORDER BY expiration DESC
			LIMIT 1',
			[$identity->id()]
		);
		return new ConstantBan(
			new Access\MySqlIdentity((int)$ban['user_id'], $this->database),
			(string)$ban['reason'],
			new \DateTime($ban['expiration']),
			new MySqlBan((int)$ban['ID'], $this->database)
		);
	}

	private function isPast(\DateTime $expiration): bool {
		return $expiration < new \DateTime;
	}
}