<?php
declare(strict_types = 1);

namespace Bulletpoint\Schema\Theme\Bulletpoint;

use Bulletpoint\Schema;
use Klapuch\Storage;

final class Structure {
	/** @var \Klapuch\Storage\Connection */
	private $connection;

	public function __construct(Storage\Connection $connection) {
		$this->connection = $connection;
	}

	public function put(): array {
		return $this->post();
	}

	public function get(): array {
		return [
			'$schema' => 'http://json-schema.org/draft-04/schema#',
			'additionalProperties' => false,
			'properties' => [
				'id' => ['type' => 'number'],
				'source' => [
					'type' => 'object',
					'properties' => [
						'link' => ['type' => 'string'],
						'type' => [
							'type' => 'string',
							'enum' => (new Schema\PostgresConstant('sources_type', $this->connection))->values(),
						],
					],
					'required' => ['link', 'type'],
				],
				'user_id' => ['type' => 'number'],
				'rating' => [
					'type' => 'object',
					'properties' => [
						'up' => ['type' => 'number'],
						'down' => ['type' => 'number'],
						'total' => ['type' => 'number'],
						'user' => ['type' => 'number'],
					],
					'required' => ['up', 'down', 'total', 'user'],
				],
				'content' => ['type' => 'string'],
				'theme_id' => ['type' => 'number'],
			],
			'required' => ['id', 'source', 'user_id', 'rating', 'content', 'theme_id'],
			'type' => 'object',
		];
	}

	public function post(): array {
		return [
			'$schema' => 'http://json-schema.org/draft-04/schema#',
			'additionalProperties' => false,
			'properties' => [
				'content' => ['type' => 'string'],
				'source' => [
					'type' => 'object',
					'properties' => [
						'link' => ['type' => 'string'],
						'type' => [
							'type' => 'string',
							'enum' => (new Schema\PostgresConstant('sources_type', $this->connection))->values(),
						],
					],
					'required' => ['link', 'type'],
				],
			],
			'required' => ['content', 'source'],
			'type' => 'object',
		];
	}
}
