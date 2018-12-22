<?php
declare(strict_types = 1);

namespace Bulletpoint\Schema\Token;

final class Structure {
	public function post(): array {
		return [
			'$schema' => 'http://json-schema.org/draft-04/schema#',
			'additionalProperties' => false,
			'properties' => [
				'email' => ['type' => 'string'],
				'password' => ['type' => 'string'],
			],
			'required' => ['email', 'password'],
			'type' => 'object',
		];
	}
}
