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

	public function post(): array {
		return [
			'$schema' => 'http://json-schema.org/draft-04/schema#',
			'additionalProperties' => false,
			'properties' => [
				'content' => [
					'type' => 'string',
				],
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
			],
			'required' => ['content', 'source'],
			'type' => 'object',
		];
	}
}
