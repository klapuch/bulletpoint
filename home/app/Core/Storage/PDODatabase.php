<?php
namespace Bulletpoint\Core\Storage;

final class PDODatabase implements Database {
	private $connection;
	const OPTIONS = [
		\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
		\PDO::ATTR_EMULATE_PREPARES => false,
		\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
	];

	public function __construct(
		string $host,
		string $user,
		string $pass,
		string $name
	) {
		try {
			$this->connection = new \PDO(
				"mysql:host=$host;dbname=$name;charset=utf8",
				$user,
				$pass,
				self::OPTIONS
			);
		} catch (\PDOException $ex) {
			throw new \RuntimeException(
				'Connection to database was not successful',
				503,
				$ex
			);
		}
	}

	public function fetch(string $query, array $parameters = []) {
		return $this->query($query, $parameters)->fetch();
	}

	public function fetchAll(string $query, array $parameters = []) {
		return $this->query($query, $parameters)->fetchAll();
	}

	public function fetchColumn(string $query, array $parameters = []) {
		return $this->query($query, $parameters)->fetchColumn();
	}

	public function query(string $query, array $parameters = []) {
		$statement = $this->connection->prepare($query);
		$statement->execute(array_values($parameters));
		return $statement;
	}

	public function exec(string $query) {
		$statement = $this->connection->exec($query);
		return $statement;
	}
}