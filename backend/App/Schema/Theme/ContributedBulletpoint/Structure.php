<?php
declare(strict_types = 1);

namespace Bulletpoint\Schema\Theme\ContributedBulletpoint;

use Bulletpoint\Schema;
use Klapuch\Storage;

final class Structure {
	/** @var \Klapuch\Storage\Connection */
	private $connection;

	public function __construct(Storage\Connection $connection) {
		$this->connection = $connection;
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
						'is_broken' => ['type' => 'boolean'],
					],
					'required' => ['link', 'type', 'is_broken'],
				],
				'content' => [
					'type' => 'string',
					'minLength' => 1,
					'maxLength' => 255,
				],
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
			'required' => ['id', 'source', 'content', 'theme_id', 'referenced_theme_id', 'compared_theme_id', 'group'],
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
				'referenced_theme_id' => $get['properties']['referenced_theme_id'],
				'compared_theme_id' => $get['properties']['compared_theme_id'],
				'group' => $get['properties']['group'],
			],
			'required' => ['content', 'source', 'referenced_theme_id', 'compared_theme_id', 'group'],
			'type' => 'object',
		];
	}
}
