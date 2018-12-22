<?php
declare(strict_types = 1);

namespace Bulletpoint\Schema\Bulletpoint\Rating;

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
				'point' => [
					'type' => 'number',
					'enum' => (new Schema\PostgresConstant('bulletpoint_ratings_point_range', $this->connection))->values(),
				],
			],
			'required' => ['point'],
			'type' => 'object',
		];
	}
}
