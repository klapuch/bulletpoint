<?php
declare(strict_types = 1);

namespace Bulletpoint\Schema\Token\OAuth;

final class Structure {
	public function post(): array {
		return [
			'$schema' => 'http://json-schema.org/draft-04/schema#',
			'additionalProperties' => false,
			'properties' => [
				'login' => ['type' => 'string'],
			],
			'required' => ['login'],
			'type' => 'object',
		];
	}
}
