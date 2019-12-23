<?php
declare(strict_types = 1);

namespace Bulletpoint\Schema\Theme\Bulletpoint;

use Bulletpoint\Schema;
use Klapuch\Storage;

final class Structure {
	private Storage\Connection $connection;

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
			'anyOf' => [
				['required' => ['rating']],
			],
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
						'link' => ['type' => ['string', 'null']],
						'type' => [
							'type' => 'string',
							'enum' => (new Schema\PostgresConstant('sources_type', $this->connection))->values(),
						],
						'is_broken' => ['type' => 'boolean'],
					],
					'required' => ['link', 'type', 'is_broken'],
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
				'created_at' => ['type' => 'string', 'format' => 'date-time'],
				'theme_id' => ['type' => 'number'],
				'referenced_theme_id' => [
					'type' => 'array',
					'items' => ['type' => 'number'],
				],
				'compared_theme_id' => [
					'type' => 'array',
					'items' => ['type' => 'number'],
				],
				'group' => [
					'type' => 'object',
					'properties' => [
						'root_bulletpoint_id' => ['type' => ['null', 'number']],
					],
					'required' => ['root_bulletpoint_id'],
				],
			],
			'required' => ['id', 'source', 'user_id', 'rating', 'content', 'theme_id', 'compared_theme_id', 'referenced_theme_id', 'created_at', 'group'],
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
				'source' => [
					'type' => 'object',
					'properties' => [
						'link' => $get['properties']['source']['properties']['link'],
						'type' => $get['properties']['source']['properties']['type'],
					],
				],
				'group' => $get['properties']['group'],
				'referenced_theme_id' => $get['properties']['referenced_theme_id'],
				'compared_theme_id' => $get['properties']['compared_theme_id'],
			],
			'required' => ['content', 'source', 'referenced_theme_id', 'compared_theme_id', 'group'],
			'type' => 'object',
		];
	}
}
