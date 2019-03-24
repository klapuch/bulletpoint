<?php
declare(strict_types = 1);

namespace Bulletpoint\Schema\User\Tag;

final class Structure {
	public function get(): array {
		return [
			'$schema' => 'http://json-schema.org/draft-04/schema#',
			'additionalProperties' => false,
			'properties' => [
				'id' => ['type' => 'number'],
				'name' => ['type' => 'string'],
				'reputation' => ['type' => 'number'],
				'rank' => ['type' => 'number'],
			],
			'required' => ['name', 'id', 'rank', 'reputation'],
			'type' => 'object',
		];
	}
}
