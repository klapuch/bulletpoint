<?php
declare(strict_types = 1);

namespace Bulletpoint\Misc;

use Predis;

final class FakeRedis implements Predis\ClientInterface {
	public function getProfile(): Predis\Profile\ProfileInterface {
	}

	public function getOptions(): Predis\Configuration\OptionsInterface {
	}

	public function connect(): void {
	}

	public function disconnect(): void {
	}

	public function getConnection(): Predis\Connection\ConnectionInterface {
	}

	/**
	 * @param string $method
	 * @param array $arguments
	 */
	public function createCommand($method, $arguments = []): Predis\Command\CommandInterface {
	}

	public function executeCommand(Predis\Command\CommandInterface $command): void {
	}

	/**
	 * @param string $method
	 * @param array $arguments
	 */
	public function __call($method, $arguments): void {
	}
}
