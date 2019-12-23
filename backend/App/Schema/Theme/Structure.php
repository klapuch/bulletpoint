<?php
declare(strict_types = 1);

namespace Bulletpoint\Schema\Theme;

use Klapuch\Storage;

final class Structure {
	private Storage\Connection $connection;

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
				'is_starred' => ['type' => 'boolean'],
				'is_empty' => ['type' => 'boolean'],
				'starred_at' => ['type' => ['string', 'null'], 'format' => 'date-time'],
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
				'alternative_names' => [
					'type' => 'array',
					'items' => [
						'type' => 'string',
						'minLength' => 1,
						'maxLength' => 255,
					],
				],
				'reference' => [
					'type' => 'object',
					'properties' => [
						'url' => ['type' => 'string'],
						'is_broken' => ['type' => 'boolean'],
					],
					'required' => ['url', 'is_broken'],
				],
				'related_themes_id' => [
					'type' => 'array',
					'items' => ['type' => 'number'],
				],
				'created_at' => ['type' => 'string', 'format' => 'date-time'],
			],
			'required' => ['tags', 'name', 'reference', 'is_starred', 'starred_at', 'related_themes_id', 'is_empty'],
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
				'alternative_names' => $get['properties']['alternative_names'],
				'reference' => [
					'type' => 'object',
					'properties' => [
						'url' => $get['properties']['reference']['properties']['url'],
					],
				],
			],
			'required' => ['tags', 'name', 'reference'],
			'type' => 'object',
		];
	}

	public function put(): array {
		return $this->post();
	}

	public function patch(): array {
		$get = $this->get();
		return [
			'$schema' => 'http://json-schema.org/draft-04/schema#',
			'additionalProperties' => false,
			'properties' => [
				'is_starred' => $get['properties']['is_starred'],
			],
			'anyOf' => [
				['required' => ['is_starred']],
			],
			'type' => 'object',
		];
	}
}
