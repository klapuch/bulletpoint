<?php
namespace Bulletpoint\Fake;

use Bulletpoint\Core\Storage;

final class Database implements Storage\Database {
	private $fetch;
	private $fetchColumn;
	private $fetchAll;
	private $query;
	private $connection;

	public function __construct(
		$fetch = null,
		$fetchColumn = null,
		$fetchAll = null,
		$query = null,
		$connection = null
	) {
		$this->fetch = $fetch;
		$this->fetchColumn = $fetchColumn;
		$this->fetchAll = $fetchAll;
		$this->query = $query;
		$this->connection = $connection;
	}

	public function fetch(string $query, array $parameters = []) {
		return $this->fetch;
	}
	
	public function fetchAll(string $query, array $parameters = []) {
		return $this->fetchAll;
	}

	public function fetchColumn(string $query, array $parameters = []) {
		return $this->fetchColumn;
	}
	
	public function query(string $query, array $parameters = []) {
		return $this->query;
	}
}