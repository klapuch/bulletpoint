<?php
declare(strict_types = 1);

namespace Bulletpoint\Schema\Tag;

final class Structure {
	public function get(): array {
		return [
			'$schema' => 'http://json-schema.org/draft-04/schema#',
			'additionalProperties' => false,
			'properties' => [
				'id' => ['type' => 'number'],
				'name' => ['type' => 'string'],
			],
			'required' => ['name', 'id'],
			'type' => 'object',
		];
	}

	public function post(): array {
		$get = $this->get();
		unset($get['properties']['id'], $get['required'][1]);
		return $get;
	}
}
