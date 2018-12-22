<?php
declare(strict_types = 1);

namespace Bulletpoint\Schema\Theme;

final class Structure {
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
				'name' => ['type' => 'string'],
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
		return [
			'$schema' => 'http://json-schema.org/draft-04/schema#',
			'additionalProperties' => false,
			'properties' => [
				'tags' => [
					'type' => 'array',
					'items' => [
						'type' => 'number',
					],
				],
				'name' => ['type' => 'string'],
				'reference' => [
					'type' => 'object',
					'properties' => ['url' => ['type' => 'string']],
					'required' => ['url'],
				],
			],
			'required' => ['tags', 'name', 'reference'],
			'type' => 'object',
		];
	}
}
