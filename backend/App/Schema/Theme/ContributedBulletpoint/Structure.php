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
					],
					'required' => ['link', 'type'],
				],
				'content' => [
					'type' => 'string',
					'minLength' => 1,
					'maxLength' => 255,
				],
				'theme_id' => ['type' => 'number'],
			],
			'required' => ['id', 'source', 'content', 'theme_id'],
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
			],
			'required' => ['content', 'source'],
			'type' => 'object',
		];
	}
}
