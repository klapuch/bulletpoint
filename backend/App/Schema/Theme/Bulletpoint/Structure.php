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

	public function patch(): array {
		$get = $this->get();
		return [
			'$schema' => 'http://json-schema.org/draft-04/schema#',
			'additionalProperties' => false,
			'properties' => [
				'rating' => [
					'type' => 'object',
					'properties' => [
						'user' => $get['properties']['rating']['properties']['user'],
					],
					'required' => ['user'],
				],
			],
			'type' => 'object',
			'required' => ['rating'],
		];
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
						'user' => [
							'type' => 'number',
							'enum' => (new Schema\PostgresConstant('bulletpoint_ratings_point_range', $this->connection))->values(),
						],
					],
					'required' => ['up', 'down', 'total', 'user'],
				],
				'content' => [
					'type' => 'string',
					'minLength' => 1,
					'maxLength' => 255,
				],
				'theme_id' => ['type' => 'number'],
				'referenced_theme_id' => ['type' => ['number', 'null']],
			],
			'required' => ['id', 'source', 'user_id', 'rating', 'content', 'theme_id'],
			'type' => 'object',
		];
	}

	public function post(): array {
		$get = $this->get();
		return [
			'$schema' => 'http://json-schema.org/draft-04/schema#',
			'additionalProperties' => false,
			'properties' => [
				'content' => $get['properties']['content'],
				'source' => $get['properties']['source'],
				'referenced_theme_id' => $get['properties']['referenced_theme_id'],
			],
			'required' => ['content', 'source', 'referenced_theme_id'],
			'type' => 'object',
		];
	}
}
