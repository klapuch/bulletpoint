<?php
namespace Bulletpoint\TestCase;

use Bulletpoint\Core\Storage;

abstract class Database extends Mockery {
	private $database;

	protected function setUp() {
		parent::setUp();
		\Tester\Environment::lock('database', __DIR__ . '/../temp');
	}

	protected function connection() {
		if($this->database === null) {
			$credentials = parse_ini_file(__DIR__ . '/.database.ini');
			$this->database = new Storage\PDODatabase(
				$credentials['host'],
				$credentials['user'],
				$credentials['pass'],
				$credentials['name']
			);
		}
		return $this->database;
	}
}