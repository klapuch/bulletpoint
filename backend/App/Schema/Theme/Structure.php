<?php
declare(strict_types = 1);

namespace Bulletpoint\Schema\Theme;

use Klapuch\Storage;

final class Structure {
	/** @var \Klapuch\Storage\Connection */
	private $connection;

	public function __construct(Storage\Connection $connection) {
		$this->connection = $connection;
	}

	public function get(): array {
		return [
			'$schema' => 'http://json-schema.org/draft-04/schema#',
			'additionalProperties' => false,
			'properties' => [
				'id' => ['type' => 'number'],
				'user_id' => ['type' => 'number'],
				'tags' => [
					'type' => 'array',
					'items' => [
						'type' => 'object',
						'properties' => [
							'id' => ['type' => 'number'],
							'name' => ['type' => 'string'],
						],
					],
				],
				'name' => [
					'type' => 'string',
					'minLength' => 1,
					'maxLength' => 255,
				],
				'reference' => [
					'type' => 'object',
					'properties' => ['url' => ['type' => 'string']],
					'required' => ['url'],
				],
				'created_at' => ['type' => 'string', 'format' => 'date-time'],
			],
			'required' => ['tags', 'name', 'reference'],
			'type' => 'object',
		];
	}

	public function post(): array {
		$get = $this->get();
		return [
			'$schema' => 'http://json-schema.org/draft-04/schema#',
			'additionalProperties' => false,
			'properties' => [
				'tags' => [
					'type' => 'array',
					'items' => ['type' => 'number'],
					'minItems' => 1,
					'maxItems' => (new Storage\NativeQuery($this->connection, 'SELECT constant.theme_tags_limit()'))->field(),
				],
				'name' => $get['properties']['name'],
				'reference' => $get['properties']['reference'],
			],
			'required' => ['tags', 'name', 'reference'],
			'type' => 'object',
		];
	}

	public function put(): array {
		return $this->post();
	}
}
