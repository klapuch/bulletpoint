<?php
declare(strict_types = 1);

namespace Bulletpoint\Schema\User;

use Klapuch\Storage;

final class Structure {
	/** @var \Klapuch\Storage\Connection */
	private $connection;

	public function __construct(Storage\Connection $connection) {
		$this->connection = $connection;
	}

	public function put(): array {
		return [
			'$schema' => 'http://json-schema.org/draft-04/schema#',
			'additionalProperties' => false,
			'properties' => [
				'username' => [
					'type' => 'string',
					'minLength' => (new Storage\NativeQuery($this->connection, 'SELECT constant.username_min_length()'))->field(),
					'maxLength' => (new Storage\NativeQuery($this->connection, 'SELECT constant.username_max_length()'))->field(),
				],
			],
			'required' => ['username'],
			'type' => 'object',
		];
	}
}
