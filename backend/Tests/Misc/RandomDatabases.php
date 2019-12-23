<?php
declare(strict_types = 1);

namespace Bulletpoint\Misc;

use Klapuch\Storage;
use Tester\FileMock;

final class RandomDatabases implements Databases {
	/** @var mixed[] */
	private array $credentials;

	private string $name;

	public function __construct(array $credentials) {
		$this->credentials = $credentials;
		$this->name = 'test_' . bin2hex(random_bytes(20));
	}

	public function create(): Storage\Connection {
		$this->database('postgres')->exec(
			sprintf(
				'CREATE DATABASE %s WITH TEMPLATE %s',
				$this->name,
				$this->credentials['template'],
			),
		);
		return $this->database($this->name);
	}

	public function drop(): void {
		if (getenv('TRAVIS') === 'true') // Travis slows down
			$this->database('postgres')->exec(sprintf('DROP DATABASE %s', $this->name));
	}

	private function database(string $name): Storage\Connection {
		return new Storage\CachedConnection(
			new Storage\PDOConnection(
				new Storage\SafePDO(
					sprintf($this->credentials['dsn'], $name),
					$this->credentials['user'],
					$this->credentials['password'],
				),
			),
			new \SplFileInfo(FileMock::create()),
		);
	}
}
