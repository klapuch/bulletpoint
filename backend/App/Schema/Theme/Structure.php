<?php
declare(strict_types = 1);

namespace Bulletpoint\Schema\Theme;

final class Structure {
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
				'name' => [
					'type' => 'string',
				],
				'reference' => [
					'type' => 'object',
					'properties' => [
						'url' => [
							'type' => 'string',
						],
					],
					'required' => ['url'],
				],
			],
			'required' => ['tags', 'name', 'reference'],
			'type' => 'object',
		];
	}
}
